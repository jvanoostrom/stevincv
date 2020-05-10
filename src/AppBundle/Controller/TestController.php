<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
//    /**
//     * @Route("/test/", name="test")
//     */
//    public function redirectAction(Request $request)
//    {
//
//        $em = $this->getDoctrine()->getManager();
//        $cv = $em->getRepository('AppBundle:Curriculumvitae')
//            ->findOneBy(array('id' => 1));
//
//        $user = $cv->getUser();
//
//        return $this->render('test.html.twig', array(
//        'cv' => $cv,
//        ));
//    }

    /**
     * @Route("/test/", name="test")
     */
    public function redirectAction(Request $request)
    {

        // Send e-mail with login details
        $message = \Swift_Message::newInstance()
            ->setSubject('Welkom bij SteVee!')
            ->setFrom(array('info@stevin.com' => 'Stevin Technology Consultants'))
            ->setTo('jvanoostrom@outlook.com')
            ->setBody(
                $this->renderView(
                    'admin/email/new_user.html.twig',
                    array(
                        'first_name' => 'Jeffrey',
                        'username' => 'jvanoostrom@outlook.com',
                        'password' => 'test123'
                    )

                )
            )
            ->setContentType("text/html");

        $this->get('mailer')->send($message);

        return new Response(
            '<html><body>Test</body></html>'
        );
    }




}
