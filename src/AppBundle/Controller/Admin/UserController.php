<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Personalia;
use AppBundle\Entity\User;
use AppBundle\Form\Admin\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

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

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $form->getData();
            $user->setUsernameCanonical(strtolower($user->getUsername()));
            $user->setEmail($user->getUsername());
            $user->setEmailCanonical($user->getUsernameCanonical());
            $user->setCountThreeMonthsEmail(0);

            // Generate random password
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $randompass = substr($tokenGenerator->generateToken(), 0, 8);
            $user->setPlainPassword($randompass);

            $user->getPersonalia()->setProfileImageName('bassie_'.$user->getPersonalia()->getLastName().'.jpg');
            $user->getPersonalia()->setProfileAvatarName('bassie_'.$user->getPersonalia()->getLastName().'_circle.png');

            $user->getPersonalia()->setUser($user);
            $fs = new Filesystem();
            $dir = $this->container->getParameter('kernel.root_dir');
            $fs->copy($dir.'/../web/img/bassie.jpg',$dir.'/../web/img/profile/'.$user->getPersonalia()->getProfileImageName());
            $fs->copy($dir.'/../web/img/bassie_circle.png',$dir.'/../web/img/profile/'.$user->getPersonalia()->getProfileAvatarName());

            // Send e-mail with login details
            $message = \Swift_Message::newInstance()
                ->setSubject('Welkom bij SteVee!')
                ->setFrom(array('vanoostrom@stevin.com' => 'Jeffrey van Oostrom'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'admin/email/new_user.html.twig',
                        array(
                            'first_name' => $user->getPersonalia()->getFirstName(),
                            'username' => $user->getUsername(),
                            'password' => $randompass
                        )

                    )
                )
                ->setContentType("text/html");

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->get('mailer')->send($message);

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

        $user->getPersonalia()->setUser(null);
        $user->setPersonalia(null);
        $em->flush();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'De consultant is succesvol verwijderd.'
        );

        return $this->redirectToRoute('admin_user');

    }

    /**
     * @Route("/admin/user/edit/{userId}", name="admin_user_edit")
     */
    public function editAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(
            array(
                'id' => $userId
            )
        );
        $personalia = $user->getPersonalia();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $form->getData();
            $user->setUsernameCanonical(strtolower($user->getUsername()));
            $user->setEmail($user->getUsername());
            $user->setEmailCanonical($user->getUsernameCanonical());

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'De consultant is succesvol aangepast.'
            );

            return $this->redirectToRoute('admin_user');
        }


        return $this->render('admin/user_form.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/admin/user/active/{userId}", name="admin_user_toggle_active")
     */
    public function toggleActiveAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(
            array(
                'id' => $userId
            )
        );

        if($user->isEnabled()) {
            $user->setEnabled(false);
            $text = 'gedeactiveerd';
        }
        else {
            $user->setEnabled(true);
            $text = 'geactiveerd';
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'notice',
            $user->getPersonalia()->getFirstName().' '.$user->getPersonalia()->getLastName().' is succesvol '.$text.'.'
        );

        return $this->redirectToRoute('admin_user');

    }

}