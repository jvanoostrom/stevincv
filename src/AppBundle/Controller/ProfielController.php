<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Profiel;
use AppBundle\Form\ProfielType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class ProfielController extends Controller
{
    /**
     * @Route("/{userId}/profiel/", name="profiel_view")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $profiles = $this->getDoctrine()->getRepository('AppBundle:Profiel')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit profiel wilt verwijderen?'
        );

        return $this->render('index/profiel.html.twig', array(
            'profiles' => $profiles,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profiel/add", name="profiel_add")
     *
     */
    public function addAction(Request $request, $userId)
    {
        //$this->serializeTags();
        $profiel = new Profiel();

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('id' => $userId));

        $form = $this->createForm(ProfielType::class, $profiel);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $profiel = $form->getData();

            $profiel->setUser($user);

            $em->persist($profiel);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het profiel is succesvol aangemaakt.'
            );

            return $this->redirectToRoute('profiel_view', array('userId' => $userId));
        }


        return $this->render('form/profiel_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profiel/edit/{profileId}", name="profiel_edit")
     *
     */
    public function editAction(Request $request, $userId, $profileId)
    {
        $em = $this->getDoctrine()->getManager();

        $profiel = $em->getRepository('AppBundle:Profiel')
            ->findOneBy(array('id' => $profileId));

        $form = $this->createForm(ProfielType::class, $profiel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $profiel = $form->getData();

            $em->persist($profiel);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('profiel_view', array('userId' => $userId));
        }

        return $this->render('form/profiel_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/profiel/delete/{profileId}", name="profiel_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $profileId)
    {

        $em = $this->getDoctrine()->getManager();
        $profiel = $em->getRepository('AppBundle:Profiel')->findOneBy(
            array(
                'id' => $profileId
            )
        );

        $em->remove($profiel);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het profiel is succesvol verwijderd.'
        );

        return $this->redirectToRoute('profiel_view', array('userId' => $userId));

    }

    public function serializeTags()
    {
        // Initialize encoder, normaliser and serializer
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('id'));
        $serializer = new Serializer(array($normalizer), array($encoder));

        // Obtain Tags
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $count = count($tags);
        $i=0;
        $content = '[';
        $content .= "\r\n";
        foreach($tags as $tag)
        {
            $content .= "  ";
            $content .= '"'.$tag->getTagText() .'"';
            if(++$i != $count)
            {
                $content .=',';
            }
            $content .= "\r\n";
        }
        $content .= ']';
        $jsonContent = $serializer->serialize($tags, 'json');
        $fs = new Filesystem();
        //$fs->dumpFile('json/tags.json', $content);

    }


}