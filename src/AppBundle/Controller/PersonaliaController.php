<?php


namespace AppBundle\Controller;

use AppBundle\Form\PersonaliaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\HttpFoundation\Request;


class PersonaliaController extends Controller
{
    /**
     * @Route("/{userId}/personalia/", name="personalia")
     *
     */
    public function showAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        // Retrieve User object and pass it to Personalia for association
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('id' => $userId));
        $personalia = $em->getRepository('AppBundle:Personalia')
            ->findOneBy(array('user' => $user));

        $profileImageName = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($personalia, 'profileImageFile');

        $form = $this->createForm(PersonaliaType::class, $personalia);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $personalia = $form->getData();

            // Remove old avatar file
            $fs = new Filesystem();
            try {
                $fs->remove($this->container->getParameter('profile_image_directory').'/'.$personalia->getProfileAvatarName());
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while removing your file at ".$e->getPath();
            }

            $em->persist($personalia);
            $em->flush();

            // Create avatar and save in DB.
            $profileImageName_tmp = $personalia->getProfileImageName();
            $profileAvatarName = $this->createAvatar($profileImageName_tmp);
            $personalia->setProfileAvatarName($profileAvatarName);

            $em->persist($personalia);
            $em->flush();

            $this->addFlash(
                'notice',
                'De wijzigingen zijn opgeslagen.'
            );

            return $this->redirectToRoute('personalia', array('userId' => $userId));
        }

        return $this->render('show/personalia.html.twig', array(
            'form' => $form->createView(),
            'profileImage' => $profileImageName,
            'userId' => $userId
        ));

    }

    public function createAvatar($picture_url)
    {
        // Check filetype
        $profile_dir = $this->container->getParameter('profile_image_directory');
        $picture_url = $profile_dir.'/'.$picture_url;
        if(exif_imagetype($picture_url) == IMAGETYPE_JPEG)
        {
            $picture = imagecreatefromjpeg($picture_url);
            $basename = basename($picture_url, '.jpg');
        }
        elseif(exif_imagetype($picture_url) == IMAGETYPE_PNG)
        {
            $picture = imagecreatefrompng($picture_url);
            $basename = basename($picture_url, '.png');
        }
        else
        {
            die('Picture type not accepted.');
        }


        // Set width and height of image
        $picture_width = imagesx($picture);
        $picture_height =imagesy($picture);

        // Crop to rectangle
        $picture_size = min($picture_height, $picture_width);
        $picture_cropped = imagecrop($picture, ['x' => 0, 'y' => 0, 'width' => $picture_size, 'height' => $picture_size]);

        // Draw a square with white fill
        $square = imagecreatetruecolor($picture_size, $picture_size);
        $square_bg = imagecolorallocate($square, 255, 255, 255);
        imagefill($square, 0, 0, $square_bg);

        // Draw a circle with transparant fill
        $square_bg_transparant = imagecolorallocate($square, 0, 0, 0);
        imagefilledellipse($square, ($picture_size/2), ($picture_size/2), $picture_size, $picture_size, $square_bg_transparant);
        imagecolortransparent($square, $square_bg_transparant);

        // Merge images
        imagecopymerge($picture_cropped, $square, 0, 0, 0, 0, $picture_size, $picture_size, 100);
        imagecolortransparent($picture_cropped, $square_bg_transparant);

        // Export
        $filename = $basename.'_circle.png';
        $filepath = $profile_dir.'/'.$filename;

        imagepng($picture_cropped, $filepath);

        imagedestroy($picture);
        imagedestroy($picture_cropped);
        imagedestroy($square);

        return $filename;

    }

}