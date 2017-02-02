<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Personalia;
use AppBundle\Entity\User;
use AppBundle\Form\Admin\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/admin/user", name="admin_user")
     */
    public function indexAction(Request $request)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll(
        );

        return $this->render('admin/user.html.twig', array(
            'users' => $users,
        ));

    }

    /**
     * @Route("/admin/user/add", name="admin_user_add")
     */
    public function addAction(Request $request)
    {

        $user = new User();
        $personalia = new Personalia();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $form->getData();
            $user->setUsernameCanonical(strtolower($user->getUsername()));
            $user->setEmail($user->getUsername());
            $user->setEmailCanonical($user->getUsernameCanonical());
            //$user->setRoles(array($form['roles']->getData()));

            $personalia->setFirstName($form['firstName']->getData());
            $personalia->setLastName($form['lastName']->getData());
            $personalia->setPlaceOfResidence($form['placeOfResidence']->getData());
            $personalia->setDateOfBirth($form['dateOfBirth']->getData());

            $personalia->setUser($user);




            $user->setPersonalia($personalia);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'De consultant is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('admin_user');
        }


        return $this->render('admin/user_form.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/admin/user/delete/{userId}", name="admin_user_delete")
     */
    public function deleteAction(Request $request, $userId)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(
            array(
                'id' => $userId
            )
        );


        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'De consultant is succesvol verwijderd.'
        );

        return $this->redirectToRoute('edu_index', array('userId' => $userId));

    }


}