<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LoginListener {

    private $securityAuthorization;
    private $router;
    private $dispatcher;


    public function __construct(AuthorizationCheckerInterface $securityAuthorization, Router $router, EventDispatcherInterface $dispatcher) {
        $this->securityAuthorization = $securityAuthorization;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        if ($this->securityAuthorization->isGranted( 'IS_AUTHENTICATED_FULLY' )) {
            $user = $event->getAuthenticationToken()->getUser ();

            if ($user->getLastLogin () === null) {
                $this->dispatcher->addListener ( KernelEvents::RESPONSE, array (
                    $this,
                    'onKernelResponse'
                ) );
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event) {
        $response = new RedirectResponse ( $this->router->generate ( 'change_password' ) );
        $event->setResponse ( $response );
    }
}
