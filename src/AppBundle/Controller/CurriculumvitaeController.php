<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Curriculumvitae;
use AppBundle\Entity\Curriculumvitae_Project;
use AppBundle\Form\CurriculumvitaeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CurriculumvitaeController extends Controller
{
    /**
     * @Route("/{userId}/cv/", name="cv_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $cvs = $this->getDoctrine()->getRepository('AppBundle:Curriculumvitae')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        return $this->render('index/curriculumvitae.html.twig', array(
            'cvs' => $cvs,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/add", name="cv_add")
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
                    'Je kunt geen CV\'s voor anderen consultants aanmaken.'
                );

                return $this->redirectToRoute('cv_index', array('userId' => $userId));
            }
        }

        $this->serializeTags();

        $cv = new Curriculumvitae();

        $form = $this->createForm(CurriculumvitaeType::class, $cv, array('userId' => $userId));

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $cv = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));

            $cv->setUser($user);

            foreach($cv->getCurriculumvitaeProjects() as $project)
            {
                $project->setCurriculumvitae($cv);
            }


            $em->persist($cv);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het cv is succesvol aangemaakt.'
            );

            return $this->redirectToRoute('cv_index', array('userId' => $userId));
        }


        return $this->render('form/curriculumvitae_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/edit/{cvId}", name="cv_edit")
     *
     */
    public function editAction(Request $request, $userId, $cvId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen CV\'s van anderen consultants aanpassen.'
                );

                return $this->redirectToRoute('cv_index', array('userId' => $userId));
            }
        }

        $this->serializeTags();

        $em = $this->getDoctrine()->getManager();

        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => $cvId));

        $originalProjects = new ArrayCollection();

        // Create an ArrayCollection of the current CurriculumvitaeProject objects in the database
        foreach ($cv->getCurriculumvitaeProjects() as $project) {
            $originalProjects->add($project);
        }

        $form = $this->createForm(CurriculumvitaeType::class, $cv, array('userId' => $userId));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cv = $form->getData();

            foreach ($originalProjects as $project) {
                if (false === $cv->getCurriculumvitaeProjects()->contains($project)) {
                    // Remove relation
                    $project->setCurriculumvitae(null);
                    $em->persist($project);

                    // Delete CurriculumvitaeProject entry
                    $em->remove($project);
                }
            }

            foreach($cv->getCurriculumvitaeProjects() as $project)
            {
                $project->setCurriculumvitae($cv);
            }

            try
            {
            $em->persist($cv);
            $em->flush();
            }
            catch(UniqueConstraintViolationException $e) {
                $this->addFlash(
                    'error',
                    'Je kan een project maar één keer toevoegen.'
                );

                return $this->redirectToRoute('cv_edit', array('userId' => $userId, 'cvId' => $cvId));
            }

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('cv_index', array('userId' => $userId));
        }

        return $this->render('form/curriculumvitae_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cv/delete/{cvId}", name="cv_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $cvId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen CV\'s van anderen consultants verwijderen.'
                );

                return $this->redirectToRoute('cv_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')->findOneBy(
            array(
                'id' => $cvId
            )
        );
        $projects = $cv->getCurriculumvitaeProjects();
        $certificates = $cv->getCertificates();
        $extracurricular = $cv->getExtracurricular();
        $publications = $cv->getPublications();
        $education = $cv->getEducation();
        $tags = $cv->getTags();

        foreach($projects as $project)
        {
            $cv->removeCurriculumvitaeProject($project);
            $project->setCurriculumvitae(null);
            $project->setProject(null);
            $em->remove($project);
        }

        foreach($certificates as $certificate)
        {
            $cv->removeCertificate($certificate);
        }

        foreach($extracurricular as $extra)
        {
            $cv->removeExtracurricular($extra);
        }

        foreach($publications as $publication)
        {
            $cv->removePublication($publication);
        }

        foreach($education as $edu)
        {
            $cv->removeEducation($edu);
        }

        foreach($tags as $tag)
        {
            $cv->removeTag($tag);
        }

        $cv->setProfile(null);
        $em->remove($cv);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het cv is succesvol verwijderd.'
        );

        return $this->redirectToRoute('cv_index', array('userId' => $userId));

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
        foreach($tags as $tag)
        {
            $content .= '"'.$tag->getTagText() .'"';
            if(++$i != $count)
            {
                $content .=',';
            }
        }
        $content .= ']';
        $jsonContent = $serializer->serialize($tags, 'json');
        $fs = new Filesystem();
        $fs->dumpFile('json/tags.json', $content);

    }

}