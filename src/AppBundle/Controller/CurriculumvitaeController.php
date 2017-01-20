<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Curriculumvitae;
use AppBundle\Form\CurriculumvitaeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class CurriculumvitaeController extends Controller
{
    /**
     * @Route("/{userId}/cv/", name="cv_view")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $cvs = $this->getDoctrine()->getRepository('AppBundle:Curriculumvitae')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit cv wilt verwijderen?'
        );

        return $this->render('index/curriculumvitae.html.twig', array(
            'cvs' => $cvs,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/add", name="cv_add")
     *
     */
    public function addAction(Request $request, $userId)
    {

        $cv = new Curriculumvitae();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('id' => $userId));

        $form = $this->createForm(CurriculumvitaeType::class, $cv, array('userId' => $userId));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cv = $form->getData();

            // Set User Object Association

            $cv->setUser($user);

            $em->persist($cv);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het cv is succesvol aangemaakt.'
            );

            return $this->redirectToRoute('cv_view', array('userId' => $userId));
        }


        return $this->render('form/curriculumvitae_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/edit/{cvId}", name="cv_edit")
     *
     */
    public function editAction(Request $request, $userId, $cvId)
    {
        $em = $this->getDoctrine()->getManager();

        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => $cvId));

        $form = $this->createForm(CurriculumvitaeType::class, $cv, array('userId' => $userId));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cv = $form->getData();

            $em->persist($cv);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('cv_view', array('userId' => $userId));
        }

        return $this->render('form/curriculumvitae_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/delete/{cvId}", name="cv_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $cvId)
    {

        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')->findOneBy(
            array(
                'id' => $cvId
            )
        );

        $em->remove($cv);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het cv is succesvol verwijderd.'
        );

        return $this->redirectToRoute('cv_view', array('userId' => $userId));

    }

}