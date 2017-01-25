<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Certificate;
use AppBundle\Form\CertificateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CertificateController extends Controller
{
    /**
     * @Route("/{userId}/cert/", name="cert_index")
     *
     */
    public function indexAction(Request $request, $userId)
    {

        $certificates = $this->getDoctrine()->getRepository('AppBundle:Certificate')->findBy(
            array('user' => $userId),
            array('updatedAt' => 'DESC')
        );

        $this->addFlash(
            'delete',
            'Weet je zeker dat je dit certificaat wilt verwijderen?'
        );

        return $this->render('index/certificate.html.twig', array(
            'certificates' => $certificates,
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cert/show/{certificateId}", name="cert_show")
     *
     */
    public function showAction(Request $request, $userId, $certificateId)
    {
        $em = $this->getDoctrine()->getManager();

        $certificate = $em->getRepository('AppBundle:Certificate')
            ->findOneBy(array('id' => $certificateId));

        return $this->render('show/certificate_show.html.twig', array(
            'userId' => $userId,
            'certificate' => $certificate
        ));
    }

    /**
     * @Route("/{userId}/cert/add", name="cert_add")
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
                    'Je kunt geen certificaat voor andere consultants toevoegen.'
                );

                return $this->redirectToRoute('cert_index', array('userId' => $userId));
            }
        }

        $certificate = new Certificate();

        $form = $this->createForm(CertificateType::class, $certificate);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $certificate = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $userId));
            $certificate->setUser($user);

            $em->persist($certificate);
            $em->flush();

            $this->addFlash(
                'notice',
                'Het certificaat is succesvol toegevoegd.'
            );

            return $this->redirectToRoute('cert_index', array('userId' => $userId));
        }


        return $this->render('form/certificate_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cert/edit/{certificateId}", name="cert_edit")
     *
     */
    public function editAction(Request $request, $userId, $certificateId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen certificaat van andere consultants aanpassen.'
                );

                return $this->redirectToRoute('cert_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $certificate = $em->getRepository('AppBundle:Certificate')
            ->findOneBy(array('id' => $certificateId));

        $form = $this->createForm(CertificateType::class, $certificate);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $certificate = $form->getData();

            $em->persist($certificate);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('cert_index', array('userId' => $userId));
        }

        return $this->render('form/certificate_form.html.twig', array(
            'form' => $form->createView(),
            'userId' => $userId
        ));

    }

    /**
     * @Route("/{userId}/cert/delete/{certificateId}", name="cert_delete")
     *
     */
    public function deleteAction(Request $request, $userId, $certificateId)
    {

        // If not correct user
        $roles = $this->getUser()->getRoles();
        if($userId != $this->getUser()->getId())
        {
            if(!in_array('ROLE_ADMIN', $roles))
            {
                $this->addFlash(
                    'error',
                    'Je kunt geen certificaat van andere consultants verwijderen.'
                );

                return $this->redirectToRoute('cert_index', array('userId' => $userId));
            }
        }

        $em = $this->getDoctrine()->getManager();
        $certificate = $em->getRepository('AppBundle:Certificate')->findOneBy(
            array(
                'id' => $certificateId
            )
        );

        $em->remove($certificate);
        $em->flush();

        $this->addFlash(
            'notice',
            'Het certificaat is succesvol verwijderd.'
        );

        return $this->redirectToRoute('cert_index', array('userId' => $userId));

    }

}