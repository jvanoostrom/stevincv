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
        $personalia = $user->getPersonalia();

        $profileImageName = $this->container->get('vich_uploader.templating.helper.uploader_helper')->asset($personalia, 'profileImageFile');

        $form = $this->createForm(PersonaliaType::class, $personalia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $personalia = $form->getData();

            if(filesize($form['profileImageFile']->getData()) > 18000000)
            {
                $this->addFlash(
                    'error',
                    'De foto is te groot. Kies een foto van maximaal 15MB.'
                );

                return $this->redirectToRoute('personalia', array('userId' => $userId));
            }

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

        $allowed_types = array ('image/jpeg', 'image/png' );
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file( $fileInfo, $picture_url );
        if ( !in_array($detected_type, $allowed_types) ) {
            die ( 'Please upload a pdf or an image ' );
        }
        finfo_close( $fileInfo );

        $this->resizeImage($picture_url, $detected_type);

        if($detected_type == 'image/jpeg')
        {
            $picture = imagecreatefromjpeg($picture_url);
            $basename = basename($picture_url, '.jpg');
        }
        elseif($detected_type == 'image/png')
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

    public function resizeImage($filename, $filetype)
    {
        $filesize = filesize($filename) / 1000000;

        while($filesize > 2.0) {
            $percent = 0.5;

            // Get new dimensions
            list($width, $height) = getimagesize($filename);
            $new_width = round($width * $percent);
            $new_height = round($height * $percent);

            // Resample
            $image_p = imagecreatetruecolor($new_width, $new_height);

            // Content type
            if ($filetype == 'image/jpeg') {
                $image = imagecreatefromjpeg($filename);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_p, $filename, 100);
                imagedestroy($image);

            } elseif ($filetype == 'image/png') {
                $image = imagecreatefrompng($filename);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($image_p, $filename, 100);
                imagedestroy($image);
            }
            imagedestroy($image_p);
            clearstatcache();
            $filesize = filesize($filename) / 1000000;
        }

    }
}