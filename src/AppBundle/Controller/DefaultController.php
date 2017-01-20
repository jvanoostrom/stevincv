<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="base")
     */
    public function redirectAction(Request $request)
    {
        $userId = $this->getUser()->getId();

        return $this->redirectToRoute('homepage', array('userId' => $userId));
    }

    /**
     * @Route("/{userId}", name="homepage")
     */
    public function indexAction(Request $request, $userId)
    {

        $render = $this->render('index/home.html.twig',
            array('userId' => $userId)
        );

        return $render;
    }

}
