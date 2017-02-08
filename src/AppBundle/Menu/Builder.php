<?php
// src/AppBundle/Menu/Builder.php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('AppBundle:User');
        $users = $repository->findAll();

        foreach($users as $user) {
            $personalia = $user->getPersonalia();

            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $path = $helper->asset($personalia, 'profileImageFile');

            $child = $personalia->getFirstName().' '.$personalia->getLastName();
            $menu->addChild($child, array(
                'uri' => $user->getId(),
                'extras' => array(
                    'img' => $path,
                    'userId' => $user->getId()
                ),
            ));

            $menu[$child]->setLinkAttribute('class', 'collection-item avatar');
        }

        return $menu;
    }

    public function topMenu(FactoryInterface $factory, array $options)
    {

        $menu = $factory->createItem('root');

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->container->get('doctrine')->getManager();
        $userPersonalia = $em->getRepository('AppBundle:Personalia')
                                ->findOneBy( array('user' => $user->getId()));

        $profileAvatarName = $userPersonalia->getProfileAvatarName();

        $child = $userPersonalia->getFirstName().' '.$userPersonalia->getLastName();
        $menu->addChild($child, array(
            'uri' => '#!',
            'extras' => array(
                'img' => $profileAvatarName,
                'userId' => $user->getId()
            ),
        ));

        return $menu;
    }
}