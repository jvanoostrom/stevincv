<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ProjectController extends Controller
{
    /**
     * @Route("/{userId}/project/", name="project_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

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

}