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


        // Me
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $personalia = $user->getPersonalia();

//        $profileAvatarName = $personalia->getProfileAvatarName();
        $profileAvatarName = '';

        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($personalia, 'profileImageFile');

        $child = $personalia->getFirstName().' '.$personalia->getLastName();
        $menu->addChild($child, array(
            'uri' => $user->getId(),
            'extras' => array(
                'img' => $profileAvatarName,
                'userId' => $user->getId()
            ),
        ));

        $menu[$child]->setLinkAttribute('class', 'collection-item avatar');

        // The rest
        $users = $em->getRepository('AppBundle:User')->findBy(array('enabled' => true));
        foreach($users as $user) {
            $personalia = $user->getPersonalia();

//            $profileAvatarName = $personalia->getProfileAvatarName();
            $profileAvatarName = '';

            $child = $personalia->getFirstName().' '.$personalia->getLastName();
            $menu->addChild($child, array(
                'uri' => $user->getId(),
                'extras' => array(
                    'img' => $profileAvatarName,
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
        $personalia = $user->getPersonalia();

        $profileAvatarName = $personalia->getProfileAvatarName();

        $child = $personalia->getFirstName().' '.$personalia->getLastName();
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