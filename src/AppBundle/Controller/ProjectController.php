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
     * @Route("/{userId}/project/", name="project_view")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit project wilt verwijderen?'
        );

        return $this->render('index/project.html.twig', array(
            'projects' => $projects,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/project/add", name="project_add")
     *
     */
    public function addAction(Request $request, $userId)
    {

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
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

            return $this->redirectToRoute('project_view', array('userId' => $userId));
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
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('AppBundle:Project')
            ->findOneBy(array('id' => $projectId));

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $project = $form->getData();

            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('project_view', array('userId' => $userId));
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

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('AppBundle:Project')->findOneBy(
            array(
                'id' => $projectId
            )
        );

        $em->remove($project);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het project is succesvol verwijderd.'
        );

        return $this->redirectToRoute('project_view', array('userId' => $userId));

    }

}