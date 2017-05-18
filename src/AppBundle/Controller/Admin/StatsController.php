<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StatsController extends Controller
{
    /**
     * @Route("/admin/stats", name="admin_stats")
     */
    public function indexAction(Request $request)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        foreach($users as $user)
        {
            $cvs[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Curriculumvitae')->findBy(array('user' => $user)));
            $profiles[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Profile')->findBy(array('user' => $user)));
            $projects[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Project')->findBy(array('user' => $user)));
            $education[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Education')->findBy(array('user' => $user)));
            $certificates[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Certificate')->findBy(array('user' => $user)));
            $extracurricular[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Extracurricular')->findBy(array('user' => $user)));
            $publications[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(array('user' => $user)));
            $skills[$user->getUsername()] = count($this->getDoctrine()->getRepository('AppBundle:Skill')->findBy(array('user' => $user)));
        }

        $counts = [
            'cvs' => $cvs,
            'profiles' => $profiles,
            'projects' => $projects,
            'education' => $education,
            'certificates' => $certificates,
            'extracurricular' => $extracurricular,
            'publications' => $publications,
            'skills' => $skills,
            ];

        return $this->render('admin/stats.html.twig', array(
            'users' => $users,
            'counts' => $counts
        ));

    }
}