<?php
namespace AppBundle\Service;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ZZPAccess
{

    public function canView(User $user, $userId)
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();

        $roles = $user->getRoles();
        if($userId != $user->getId())
        {

            if(in_array('ROLE_ZZP', $roles))
            {
                return false;
            }
        }

        return true;
    }
}