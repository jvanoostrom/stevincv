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
        $roles = $this->getUser()->getRoles();
        if(in_array('ROLE_ZZP', $roles))
        {
            $this->addFlash(
                'error',
                'Je kunt geen ratecards bekijken.'
            );
            return $this->redirectToRoute('cv_index', array('userId' => $this->getUser()->getId()));
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        return $this->render('index/ratecard.html.twig', array(
            'users' => $users,
            'userId' => $userId
        ));

    }

}