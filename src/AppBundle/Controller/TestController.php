<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test/", name="test")
     */
    public function redirectAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => 1));

        $user = $cv->getUser();

        return $this->render('test.html.twig', array(
        'cv' => $cv,
        ));
    }



}
