<?php

namespace AppBundle\Controller\Admin;

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
     * @Route("/admin/ratecard", name="admin_ratecard")
     */
    public function indexAction(Request $request)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        return $this->render('admin/ratecard.html.twig', array(
            'users' => $users,
        ));

    }

    /**
     * @Route("/admin/ratecard/edit/{userId}", name="admin_ratecard_edit")
     */
    public function editAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(
            array(
                'id' => $userId
            )
        );

        $user->setRateTariff($request->request->get('user')['rateTariff']);

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'De consultant is succesvol aangepast.'
        );

            return $this->redirectToRoute('admin_ratecard');
    }

}