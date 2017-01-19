<?php
/**
 * Created by PhpStorm.
 * User: JeffreyvanOostrom-St
 * Date: 16/01/2017
 * Time: 12:11
 */

namespace AppBundle\Controller;


use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PasswordController extends Controller
{
    /**
     * @Route("/password", name="change_password")
     *
     */
    public function indexAction(Request $request)
    {
        $user = $request->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->get('session')->setFlash('notice', 'Password changed succesfully');

            return $this->redirect($this->generateUrl('change_password'));
        }

        return $this->render('security/password.html.twig', ['form' => $form->createView()]);
    }
}