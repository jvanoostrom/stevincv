<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Personalia;
use AppBundle\Entity\User;
use AppBundle\Form\Admin\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

class RatecardController extends Controller
{
    /**
     * @Route("/{userId}/ratecard", name="ratecard")
     */
    public function indexAction(Request $request, $userId)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll(
        );

        return $this->render('index/ratecard.html.twig', array(
            'users' => $users,
            'userId' => $userId
        ));

    }

}