<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use AppBundle\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class PublicationController extends Controller
{
    /**
     * @Route("/{userId}/pub/", name="pub_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $publications = $this->getDoctrine()->getRepository('AppBundle:Publication')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit publicatie wilt verwijderen?'
        );

        return $this->render('index/publication.html.twig', array(
            'publications' => $publications,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/pub/show/{publicationId}", name="pub_show")
     *
     */
    public function showAction(Request $request, $userId, $publicationId)
    {
        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository('AppBundle:Publication')
            ->findOneBy(array('id' => $publicationId));

        return $this->render('show/publication_show.html.twig', array(
            'userId' => $userId,
            'publication' => $publication
        ));
    }

    /**
     * @Route("/{userId}/pub/add", name="pub_add")
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
                    'Je kunt geen publicatie voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('pub_index', array('userId' => $userId));
            }
        }

        $publication = new Publication();

        $form = $this->createForm(PublicationType::class, $publication);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $publication = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $publication->setUser($user);

            $em->persist($publication);
            $em->flush();

            $this->addFlash(
                'notice',
                'De publicatie is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('pub_index', array('userId' => $userId));
        }


        return $this->render('form/publication_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/pub/edit/{publicationId}", name="pub_edit")
     *
     */
    public function editAction(Request $request, $userId, $publicationId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen publicatie van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('pub_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository('AppBundle:Publication')
            ->findOneBy(array('id' => $publicationId));

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $publication = $form->getData();

            $em->persist($publication);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('pub_index', array('userId' => $userId));
        }

        return $this->render('form/publication_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/pub/delete/{publicationId}", name="pub_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $publicationId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen publicatie van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('pub_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository('AppBundle:Publication')->findOneBy(
            array(
                'id' => $publicationId
            )
        );

        $em->remove($publication);
        $em->flush();

        $this->addFlash(
            'notice',
            'De publicatie is succesvol verwijderd.'
        );

        return $this->redirectToRoute('pub_index', array('userId' => $userId));

    }

}