<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Education;
use AppBundle\Form\EducationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class EducationController extends Controller
{
    /**
     * @Route("/{userId}/edu/", name="edu_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $education = $this->getDoctrine()->getRepository('AppBundle:Education')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        return $this->render('index/education.html.twig', array(
            'education' => $education,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/edu/show/{educationId}", name="edu_show")
     *
     */
    public function showAction(Request $request, $userId, $educationId)
    {
        $em = $this->getDoctrine()->getManager();

        $education = $em->getRepository('AppBundle:Education')
            ->findOneBy(array('id' => $educationId));

        return $this->render('show/education_show.html.twig', array(
            'userId' => $userId,
            'education' => $education
        ));
    }

    /**
     * @Route("/{userId}/edu/add", name="edu_add")
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
                    'Je kunt geen opleiding voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('edu_index', array('userId' => $userId));
            }
        }

        $education = new Education();

        $form = $this->createForm(EducationType::class, $education);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $education = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $education->setUser($user);

            $em->persist($education);
            $em->flush();

            $this->addFlash(
                'notice',
                'De opleiding is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('edu_index', array('userId' => $userId));
        }


        return $this->render('form/education_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/edu/edit/{educationId}", name="edu_edit")
     *
     */
    public function editAction(Request $request, $userId, $educationId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen opleiding van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('edu_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $education = $em->getRepository('AppBundle:Education')
            ->findOneBy(array('id' => $educationId));

        $form = $this->createForm(EducationType::class, $education);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $education = $form->getData();

            $em->persist($education);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('edu_index', array('userId' => $userId));
        }

        return $this->render('form/education_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/edu/delete/{educationId}", name="edu_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $educationId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen opleiding van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('edu_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $education = $em->getRepository('AppBundle:Education')->findOneBy(
            array(
                'id' => $educationId
            )
        );

        $em->remove($education);
        $em->flush();

        $this->addFlash(
            'notice',
            'De opleiding is succesvol verwijderd.'
        );

        return $this->redirectToRoute('edu_index', array('userId' => $userId));

    }

}