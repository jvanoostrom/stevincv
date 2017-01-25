<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Extracurricular;
use AppBundle\Form\ExtracurricularType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ExtracurricularController extends Controller
{
    /**
     * @Route("/{userId}/extra/", name="extra_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $extracurricular = $this->getDoctrine()->getRepository('AppBundle:Extracurricular')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je deze nevenactiviteit wilt verwijderen?'
        );

        return $this->render('index/extracurricular.html.twig', array(
            'extracurricular' => $extracurricular,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/extra/show/{extracurricularId}", name="extra_show")
     *
     */
    public function showAction(Request $request, $userId, $extracurricularId)
    {
        $em = $this->getDoctrine()->getManager();

        $extracurricular = $em->getRepository('AppBundle:Extracurricular')
            ->findOneBy(array('id' => $extracurricularId));

        return $this->render('show/extracurricular_show.html.twig', array(
            'userId' => $userId,
            'extracurricular' => $extracurricular
        ));
    }

    /**
     * @Route("/{userId}/extra/add", name="extra_add")
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
                    'Je kunt geen nevenactiviteit voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('extra_index', array('userId' => $userId));
            }
        }

        $extracurricular = new Extracurricular();

        $form = $this->createForm(ExtracurricularType::class, $extracurricular);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $extracurricular = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $extracurricular->setUser($user);

            $em->persist($extracurricular);
            $em->flush();

            $this->addFlash(
                'notice',
                'De nevenactiviteit is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('extra_index', array('userId' => $userId));
        }


        return $this->render('form/extracurricular_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/extra/edit/{extracurricularId}", name="extra_edit")
     *
     */
    public function editAction(Request $request, $userId, $extracurricularId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen nevenactiviteit van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('extra_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $extracurricular = $em->getRepository('AppBundle:Extracurricular')
            ->findOneBy(array('id' => $extracurricularId));

        $form = $this->createForm(ExtracurricularType::class, $extracurricular);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $extracurricular = $form->getData();

            $em->persist($extracurricular);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('extra_index', array('userId' => $userId));
        }

        return $this->render('form/extracurricular_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/extra/delete/{extracurricularId}", name="extra_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $extracurricularId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen nevenactiviteit van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('extra_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $extracurricular = $em->getRepository('AppBundle:Extracurricular')->findOneBy(
            array(
                'id' => $extracurricularId
            )
        );

        $em->remove($extracurricular);
        $em->flush();

        $this->addFlash(
            'notice',
            'De nevenactiviteit is succesvol verwijderd.'
        );

        return $this->redirectToRoute('extra_index', array('userId' => $userId));

    }

}