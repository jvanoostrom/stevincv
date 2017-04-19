<?php


namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class TagController extends Controller
{
    /**
     * @Route("/admin/tag", name="admin_tag")
     *
     */
    public function indexAction(Request $request)
    {

        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();

        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);

        return $this->render('admin/tag.html.twig', array(
            'tags' => $tags,
            'form' => $form->createView(),
            'route' => 'index'
        ));

    }

    /**
     * @Route("/admin/tag/add", name="admin_tag_add")
     *
     */
    public function addAction(Request $request)
    {


        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();

        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'notice',
                'De competentie is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('admin_tag');
        }


        return $this->render('admin/tag.html.twig', array(
            'form' => $form->createView(),
            'tags' => $tags,
            'route' => 'add'
        ));

    }

    /**
     * @Route("/admin/tag/edit/{tagId}", name="admin_tag_edit")
     *
     */
    public function editAction(Request $request, $tagId)
    {

        $em = $this->getDoctrine()->getManager();

        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();

        $tag = $em->getRepository('AppBundle:Tag')
            ->findOneBy(array('id' => $tagId));

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();

            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('admin_tag');
        }

        return $this->render('admin/tag.html.twig', array(
            'form' => $form->createView(),
            'tags' => $tags,
            'route' => 'edit',
            'tagId' => $tagId
        ));

    }

    /**
     * @Route("/admin/tag/delete/{tagId}", name="admin_tag_delete")
     *
     */
    public function deleteAction(Request $request, $tagId)
    {

        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneBy(
            array(
                'id' => $tagId
            )
        );

        try
        {
            $em->remove($tag);
            $em->flush();
        }
        catch(\Doctrine\DBAL\DBALException $e) {
            if ($e->getErrorCode() != 1451) {
                throw $e;
            }

            $this->addFlash(
                'error',
                'Deze tag is geassocieerd met een CV. Verwijder de tag eerst van het CV.'
            );

            return $this->redirectToRoute('admin_tag');
        }

        $this->addFlash(
            'notice',
            'De tag is succesvol verwijderd.'
        );

        return $this->redirectToRoute('admin_tag');

    }

}