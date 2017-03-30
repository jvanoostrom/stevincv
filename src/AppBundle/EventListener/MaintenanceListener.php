<?php
/**
 * Created by PhpStorm.
 * User: JeffreyvanOostrom-St
 * Date: 20/03/2017
 * Time: 14:21
 */

namespace AppBundle\EventListener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = $this->container->hasParameter('maintenance') ? $this->container->getParameter('maintenance') : false;

        $debug = in_array($this->container->get('kernel')->getEnvironment(), array('test', 'dev'));

        if ($maintenance && !$debug) {
            $engine = $this->container->get('templating');
            $content = $engine->render('maintenance.html.twig');
            $event->setResponse(new Response($content, 503));
            $event->stopPropagation();
        }

    }
}