<?php
/**
 * Created by PhpStorm.
 * User: JeffreyvanOostrom-St
 * Date: 27/01/2017
 * Time: 16:51
 */

namespace AppBundle\Controller\Admin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function indexAction(Request $request)
    {
        $render = $this->render('admin/home.html.twig');

        return $render;
    }

}