<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Skill;
use AppBundle\Form\SkillType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SkillController extends Controller
{
    /**
     * @Route("/{userId}/skill/", name="skill_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $skills = $this->getDoctrine()->getRepository('AppBundle:Skill')->findBy(
            array('user' => $userId),
            array('skillText' => 'ASC')
        );

        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);

        return $this->render('index/skill.html.twig', array(
            'form' => $form->createView(),
            'skills' => $skills,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/skill/add", name="skill_add")
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
                    'Je kunt geen competenties voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('skill_index', array('userId' => $userId));
            }
        }

        $skill = new Skill();

        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $skill = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $skill->setUser($user);

            $em->persist($skill);
            $em->flush();

            $this->addFlash(
                'notice',
                'De competentie is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('skill_index', array('userId' => $userId));
        }


        return $this->render('index/skill.html.twig', array(
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/skill/edit/{skillId}", name="skill_edit")
     *
     */
    public function editAction(Request $request, $userId, $skillId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen competentie van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('skill_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $skill = $em->getRepository('AppBundle:Skill')
            ->findOneBy(array('id' => $skillId));

        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $skill = $form->getData();

            $em->persist($skill);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('skill_index', array('userId' => $userId));
        }

    }

    /**
     * @Route("/{userId}/skill/delete/{skillId}", name="skill_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $skillId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen competentie van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('skill_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $skill = $em->getRepository('AppBundle:Skill')->findOneBy(
            array(
                'id' => $skillId
            )
        );

        $em->remove($skill);
        $em->flush();

        $this->addFlash(
            'notice',
            'De competentie is succesvol verwijderd.'
        );

        return $this->redirectToRoute('skill_index', array('userId' => $userId));

    }

}