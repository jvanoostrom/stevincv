<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;


class ProjectController extends Controller
{
    /**
     * @Route("/{userId}/project/", name="project_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        if(!$this->container->get('app.zzpaccess')->canView($this->getUser(), $userId)) {

            $this->addFlash(
                'error',
                'Je kunt geen gegevens van andere consultants bekijken.'
            );
            return $this->redirectToRoute('project_index', array('userId' => $this->getUser()->getId()));

        }

        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        return $this->render('index/project.html.twig', array(
            'projects' => $projects,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/project/show/{projectId}", name="project_show")
     *
     */
    public function showAction(Request $request, $userId, $projectId)
    {

        if(!$this->container->get('app.zzpaccess')->canView($this->getUser(), $userId)) {

            $this->addFlash(
                'error',
                'Je kunt geen gegevens van andere consultants bekijken.'
            );
            return $this->redirectToRoute('project_index', array('userId' => $this->getUser()->getId()));

        }

        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('AppBundle:Project')
            ->findOneBy(array('id' => $projectId));

        return $this->render('show/project_show.html.twig', array(
            'userId' => $userId,
            'project' => $project
        ));
    }

    /**
     * @Route("/{userId}/project/add", name="project_add")
     *
     */
    public function addAction(Request $request, $userId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen projecten voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('project_index', array('userId' => $userId));
            }
        }

        $this->serializeTags();

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $project->setUser($user);

            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het project is succesvol aangemaakt.'
            );

            return $this->redirectToRoute('project_index', array('userId' => $userId));
        }


        return $this->render('form/project_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/project/copy/{projectId}", name="project_copy")
     *
     */
    public function copyAction(Request $request, $userId, $projectId)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $project = $em->getRepository('AppBundle:Project')
            ->findOneBy(array('id' => $projectId));

        $newProject = clone $project;
        $projectName = $project->getProjectName();

        if(strpos(strtoupper($projectName),strtoupper("- Kopie")) !== false)
        {
            $projectName = substr($projectName,0,strpos($projectName,"-")-1);
        }

        $qb->select(array('u.projectName')) // string 'u' is converted to array internally
        ->from('AppBundle:Project', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('u.user', ':userId'),
                $qb->expr()->like('u.projectName', ':projectName')
            ))
            ->orderBy('u.projectName', 'DESC')
            ->setParameter('projectName', $projectName."%")
            ->setParameter('userId', $userId);

        $maxProjectName = $qb->getQuery()->getResult();
        $maxProjectName = $maxProjectName[0]['projectName'];

        $copyNumber = substr($maxProjectName,-1);
        $copyNumber = $copyNumber + 1;
        $newProject->setProjectName($projectName." - Kopie ".$copyNumber);
        $newProject->setUpdatedAt(new \DateTime());

        $em->persist($newProject);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het project is succesvol gekopieërd.'
        );

        return $this->redirectToRoute('project_index', array('userId' => $userId));
    }

    /**
     * @Route("/{userId}/project/edit/{projectId}", name="project_edit")
     *
     */
    public function editAction(Request $request, $userId, $projectId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen projecten van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('project_index', array('userId' => $userId));
            }
        }

        $this->serializeTags();

        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('AppBundle:Project')
            ->findOneBy(array('id' => $projectId));

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();

            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('project_index', array('userId' => $userId));
        }

        return $this->render('form/project_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/project/delete/{projectId}", name="project_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $projectId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen projecten van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('project_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('AppBundle:Project')->findOneBy(
            array(
                'id' => $projectId
            )
        );
        try
        {
        $em->remove($project);
        $em->flush();
        }
        catch(\Doctrine\DBAL\DBALException $e)
        {
            if($e->getErrorCode() != 1451) {
                throw $e;
            }

            $this->addFlash(
                'error',
                'Het project is geassocieerd met een CV. Verwijder het project eerst van het CV.'
            );

            return $this->redirectToRoute('project_index', array('userId' => $userId));

        }

        $this->addFlash(
            'notice',
            'Het project is succesvol verwijderd.'
        );

        return $this->redirectToRoute('project_index', array('userId' => $userId));

    }
    
    public function serializeTags()
    {
        // Obtain Tags
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $count = count($tags);
        $i=0;
        $content = '[';
        foreach($tags as $tag)
        {
            $content .= '"'.$tag->getTagText() .'"';
            if(++$i != $count)
            {
                $content .=',';
            }
        }
        $content .= ']';
        $fs = new Filesystem();
        $fs->dumpFile('json/tags.json', $content);

    }

}