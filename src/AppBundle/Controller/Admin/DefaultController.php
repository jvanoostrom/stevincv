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



    /**
     * @Route("/admin/mailtest", name="admin_mailtest")
     */
    public function mailtestAction(Request $request)
    {
        $boundaryDate = date('Y-m-d',strtotime('- 1 day'));
        $em = $this->getDoctrine()->getManager();

        $sql = "SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" AND curriculumvitae.user_id = 1 GROUP BY curriculumvitae.user_id";
        //$sql = "SELECT curriculumvitae.user_id, fos_user.email, personalia.first_name, personalia.last_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"".$boundaryDate."\" GROUP BY curriculumvitae.user_id";
        $query = $em->getConnection()->prepare($sql);
        $query->execute();
        $users = $query->fetchAll();

        foreach($users as $user) {
            $userid = $user['user_id'];
            $sql = "SELECT curriculumvitae.curriculumvitae_name FROM curriculumvitae JOIN fos_user on curriculumvitae.user_id=fos_user.id join personalia on fos_user.personalia_id=personalia.id WHERE curriculumvitae.updated_at < \"" . $boundaryDate . "\" AND curriculumvitae.user_id = $userid";
            $query = $em->getConnection()->prepare($sql);
            $query->execute();
            $cvs = $query->fetchAll();

            $message = \Swift_Message::newInstance()
                ->setSubject('Je hebt verouderde CV\'s')
                ->setFrom(array('vanoostrom@stevin.com' => 'Jeffrey van Oostrom'))
                ->setTo($user['email'])
                ->setBody(
                    $this->renderView(
                        'admin/verouderde_cvs_email.html.twig',
                        array(
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'cvs' => $cvs,
                        )

                    )
                )
                ->setContentType("text/html");

            $this->container->get('mailer')->send($message);


        }




        $render = $this->render('admin/home.html.twig');

        return $render;
    }

}