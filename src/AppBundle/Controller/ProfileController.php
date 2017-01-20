<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Profile;
use AppBundle\Form\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class ProfileController extends Controller
{
    /**
     * @Route("/{userId}/profile/", name="profile_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profile')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit profiel wilt verwijderen?'
        );

        return $this->render('index/profile.html.twig', array(
            'profiles' => $profiles,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profile/show/{profileId}", name="profile_show")
     *
     */
    public function showAction(Request $request, $userId, $profileId)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('AppBundle:Profile')
            ->findOneBy(array('id' => $profileId));

        return $this->render('show/profile_show.html.twig', array(
            'userId' => $userId,
            'profile' => $profile
        ));
    }
    /**
     * @Route("/{userId}/profile/add", name="profile_add")
     *
     */
    public function addAction(Request $request, $userId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen profielen voor anderen consultants aanmaken.'
                );

                return $this->redirectToRoute('profile_index', array('userId' => $userId));
            }
        }

        $profile = new Profile();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('id' => $userId));

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $profile = $form->getData();

            $profile->setUser($user);

            $em->persist($profile);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het profiel is succesvol aangemaakt.'
            );

            return $this->redirectToRoute('profile_index', array('userId' => $userId));
        }


        return $this->render('form/profile_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profile/edit/{profileId}", name="profile_edit")
     *
     */
    public function editAction(Request $request, $userId, $profileId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen profielen van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('profile_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('AppBundle:Profile')
            ->findOneBy(array('id' => $profileId));

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $profile = $form->getData();

            $em->persist($profile);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('profile_index', array('userId' => $userId));
        }

        return $this->render('form/profile_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profile/delete/{profileId}", name="profile_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $profileId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen profielen van anderen consultants verwijderen.'
                );

                return $this->redirectToRoute('profile_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $profile = $em->getRepository('AppBundle:Profile')->findOneBy(
            array(
                'id' => $profileId
            )
        );

        $em->remove($profile);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het profiel is succesvol verwijderd.'
        );

        return $this->redirectToRoute('profile_index', array('userId' => $userId));

    }

//    public function serializeTags()
//    {
//        // Initialize encoder, normaliser and serializer
//        $encoder = new JsonEncoder();
//        $normalizer = new ObjectNormalizer();
//        $normalizer->setIgnoredAttributes(array('id'));
//        $serializer = new Serializer(array($normalizer), array($encoder));
//
//        // Obtain Tags
//        $em = $this->getDoctrine()->getManager();
//        $tags = $em->getRepository('AppBundle:Tag')->findAll();
//        $count = count($tags);
//        $i=0;
//        $content = '[';
//        $content .= "\r\n";
//        foreach($tags as $tag)
//        {
//            $content .= "  ";
//            $content .= '"'.$tag->getTagText() .'"';
//            if(++$i != $count)
//            {
//                $content .=',';
//            }
//            $content .= "\r\n";
//        }
//        $content .= ']';
//        $jsonContent = $serializer->serialize($tags, 'json');
//        $fs = new Filesystem();
//        //$fs->dumpFile('json/tags.json', $content);
//
//    }
//
//    public function checkUser($msg, $route, $userId)
//    {
//        // If not correct user
//        $roles = $this->getUser()->getRoles();
//        if($userId != $this->getUser()->getId())
//        {
//            if (!in_array('ROLE_ADMIN', $roles)) {
//                $this->addFlash(
//                    'error',
//                    $msg
//                );
//
//                return $this->redirectToRoute($route, array('userId' => $userId));
//            }
//        }
//    }

}