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

        if(!$this->container->get('app.zzpaccess')->canView($this->getUser(), $userId)) {

            $this->addFlash(
                'error',
                'Je kunt geen gegevens van andere consultants bekijken.'
            );
            return $this->redirectToRoute('profile_index', array('userId' => $this->getUser()->getId()));

        }

        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profile')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
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

        if(!$this->container->get('app.zzpaccess')->canView($this->getUser(), $userId)) {

            $this->addFlash(
                'error',
                'Je kunt geen gegevens van andere consultants bekijken.'
            );
            return $this->redirectToRoute('profile_index', array('userId' => $this->getUser()->getId()));

        }

        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('AppBundle:Profile')
            ->findOneBy(array('id' => $profileId));

        return $this->render('show/profile_show.html.twig', array(
            'userId' => $userId,
            'profile' => $profile
        ));
    }

    /**
     * @Route("/{userId}/profile/copy/{profileId}", name="profile_copy")
     *
     */
    public function copyAction(Request $request, $userId, $profileId)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $profile = $em->getRepository('AppBundle:Profile')
            ->findOneBy(array('id' => $profileId));

        $newProfile = clone $profile;
        $shortDescription = $profile->getShortDescription();

        if(strpos(strtoupper($shortDescription),strtoupper("- Kopie")) !== false)
        {
            $shortDescription = substr($shortDescription,0,strpos($shortDescription,"-")-1);
        }

        $qb->select(array('u.shortDescription')) // string 'u' is converted to array internally
        ->from('AppBundle:Profile', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('u.user', ':userId'),
                $qb->expr()->like('u.shortDescription', ':shortDescription')
            ))
            ->orderBy('u.shortDescription', 'DESC')
            ->setParameter('shortDescription', $shortDescription."%")
            ->setParameter('userId', $userId);

        $maxDescription = $qb->getQuery()->getResult();
        $maxDescription = $maxDescription[0]['shortDescription'];

        $copyNumber = substr($maxDescription,-1);
        $copyNumber = $copyNumber + 1;
        $newProfile->setShortDescription($shortDescription." - Kopie ".$copyNumber);
        $newProfile->setUpdatedAt(new \DateTime());

        $em->persist($newProfile);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het profiel is succesvol gekopieërd.'
        );

        return $this->redirectToRoute('profile_index', array('userId' => $userId));
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

        $this->serializeTags();

        $profile = new Profile();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('id' => $userId));

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $this->serializeTags();

        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('AppBundle:Profile')
            ->findOneBy(array('id' => $profileId));

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        try{
            $em->remove($profile);
            $em->flush();
        }
        catch(\Doctrine\DBAL\DBALException $e) {
            if ($e->getErrorCode() != 1451) {
                throw $e;
            }

            $this->addFlash(
                'error',
                'Dit profiel is geassocieerd met een CV. Verwijder dit profiel eerst van het CV.'
            );

            return $this->redirectToRoute('profile_index', array('userId' => $userId));
        }

        $this->addFlash(
            'notice',
            'Het profiel is succesvol verwijderd.'
        );

        return $this->redirectToRoute('profile_index', array('userId' => $userId));

    }

    public function serializeTags()
    {
        // Obtain Tags
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $count = count($tags);
        $i=0;
        $content = '[';
        foreach($tags as $tag)
        {
            $content .= '"'.$tag->getTagText() .'"';
            if(++$i != $count)
            {
                $content .=',';
            }
        }
        $content .= ']';
        $fs = new Filesystem();
        $fs->dumpFile('json/tags.json', $content);

    }
}