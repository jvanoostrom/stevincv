<?php


namespace AppBundle\Controller;

use PhpOffice\Common\File;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Shape\Table\Cell;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Bullet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Slide\Background\Image as BackgroundImage;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpPresentation\DocumentLayout;

class CurriculumvitaeExportController extends Controller
{
    /**
     * @Route("/{userId}/cv/export/2016/{cvId}", name="cv_export_2016")
     *
     */
    public function exportAction(Request $request, $userId, $cvId)
    {

        if(!$this->container->get('app.zzpaccess')->canView($this->getUser(), $userId)) {

            $this->addFlash(
                'error',
                'Je kunt geen gegevens van andere consultants bekijken.'
            );
            return $this->redirectToRoute('cv_index', array('userId' => $this->getUser()->getId()));

        }

        $locale = 'nl';
        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => $cvId));

        $user = $cv->getUser();
        $personalia = $user->getPersonalia();
        $profile = $cv->getProfile();

        $query = "SELECT * FROM 
                  (SELECT * FROM curriculumvitae_project WHERE curriculumvitae_project.cv_id = ".$cvId.") 
                  as cv_project INNER JOIN project ON cv_project.project_id=project.id 
                  ORDER BY cv_project.important DESC, project.end_date DESC, project.start_date DESC";
        $test = $em->getConnection()->prepare($query);
        $test->execute();
        $projects = $test->fetchAll();

        $education = $cv->getEducation();
        $certificates = $cv->getCertificates();
        $extracurricular = $cv->getExtracurricular();
        $publications = $cv->getPublications();
        $skills = $cv->getSkills();

        $outputDirectory = $this->container->getParameter('curriculumvitae_output_directory');
        $profileImageDirectory = $this->container->getParameter('profile_image_directory');
        $outputFileName = $cv->getCurriculumvitaeName().'-'.$personalia->getFirstName().' '.$personalia->getLastName(
            ).'-'.date('d-m-Y')."-".$locale.".pptx";
        $outputFileName = str_replace(' ', '', $outputFileName);
        $outputFilePath = $outputDirectory."/".$outputFileName;

        /* --------------------------
         *  Define variables
         * --------------------------
         */

        $darkGrey = new Color('FF264756');
        $lightGrey = new Color('FF8D9499');
        $red = new Color('FFCC3333');
        $white = new Color('FFFFFFFF');
        $black = new Color('FF000000');
        $xOffsetLeft = 55;
        $xOffsetRight = 555;

        /* --------------------------
         *  Start Presentation
         * --------------------------
         */

        // Create object
        $objPHPPresentation = new PhpPresentation();
        $oMasterSlide = $objPHPPresentation->getAllMasterSlides()[0];
        $oSlideLayout = $oMasterSlide->getAllSlideLayouts()[0];

        // Set size
        $oDocumentLayout = $objPHPPresentation->getLayout();
        $oDocumentLayout->setCX(9906120);
        $oDocumentLayout->setCY(6858000);

        // Set properties
        $oDocumentProperties = $objPHPPresentation->getDocumentProperties();
        $oDocumentProperties->setCreator($personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setCompany('Stevin Technology Consultants');
        $oDocumentProperties->setTitle('CV '.$personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setDescription('Curriculum Vitae');
        $oDocumentProperties->setLastModifiedBy($personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setKeywords('cv, '.$personalia->getFirstName().', '.$personalia->getLastName());

        /* --------------------------
         *  First slide
         * --------------------------
         */

        $oSlide1 = $objPHPPresentation->getActiveSlide();
        $oSlide1->setSlideLayout($oSlideLayout);

        // Add background image
        $oBackgroundImage = new BackgroundImage();
        $oBackgroundImage->setPath('./img/cv/cvbackground.jpg');
        $oSlide1->setBackground($oBackgroundImage);

        // Add profile picture
        $oProfileImg = new Drawing\File();
        $oProfileImg->setPath($profileImageDirectory.'/'.$personalia->getProfileImageName())
            //->setHeight(390)
            ->setWidth(260)
            ->setOffsetX(110)
            ->setOffsetY(165);
        $oSlide1->addShape($oProfileImg);

        // Add Stevin Logo
        $oStevinLogo = new Drawing\File();
        $oStevinLogo->setPath('./img/cv/logostevin.png')
            ->setHeight(90)
            ->setOffsetX(860)
            ->setOffsetY(10);
        $oSlide1->addShape($oStevinLogo);

        // Add name text box
        $oNameText = $oSlide1->createRichTextShape()
            ->setHeight(35)
            ->setWidth(535)
            ->setOffsetX(450)
            ->setOffsetY(110);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun(
            mb_strtoupper($personalia->getFirstName().' '.$personalia->getLastName())
        );
        $oNameTextRun->getFont()
            ->setCharacterSpacing(3)
            ->setBold(true)
            ->setName('Open Sans SemiBold')
            ->setSize(16)
            ->setColor($darkGrey);

        // Add quote
        $oQuoteText = $oSlide1->createRichTextShape()
            ->setHeight(35)
            ->setWidth(450)
            ->setOffsetX(450)
            ->setOffsetY(140);
        $oQuoteText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oQuoteTextRun = $oQuoteText->createTextRun(mb_strtoupper($profile->getQuoteLine()));
        $oQuoteTextRun->getFont()
            ->setCharacterSpacing(2)
            ->setBold(true)
            ->setItalic(true)
            ->setName('Open Sans SemiBold')
            ->setSize(11)
            ->setColor($lightGrey);

        // Add profile text
        $oProfileText = $oSlide1->createRichTextShape()
            ->setWidth(450)
            ->setHeight(500)
            ->setOffsetX(450)
            ->setOffsetY(210);
        $oProfileText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $oProfileTextRun = $oProfileText->createTextRun($profile->getProfileText());
        $oProfileTextRun->getFont()
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setSize(9)
            ->setColor($darkGrey);


        /* --------------------------
         *  Second slide LEFT
         * --------------------------
         */

        $oSlide2 = $objPHPPresentation->createSlide()
            ->setSlideLayout($oSlideLayout);

        // Add Background image
        $oBackgroundImageSlide2 = new BackgroundImage();
        $oBackgroundImageSlide2->setPath('./img/cv/cvBackgroundExperience.png');
        $oSlide2->setBackground($oBackgroundImageSlide2);

        // Add profile image
        $oProfileImage = new Drawing\File();
        $oProfileImage->setPath($profileImageDirectory.'/'.$personalia->getProfileAvatarName())
            ->setWidthAndHeight(95, 95)
            ->setResizeProportional(false)
            ->setOffsetX($xOffsetLeft + 10)
            ->setOffsetY(50);
        $oSlide2->addShape($oProfileImage);

        // Add name text box
        // Add Date of Birth and Residency table
        // Create a shape (table)
        $oTable = $oSlide2->createTableShape(2);
        $oTable->setHeight(55);
        $oTable->setWidth(400);
        $oTable->setOffsetX($xOffsetLeft + 125);
        $oTable->setOffsetY(55);

        // Add row Name
        $oRow = $oTable->createRow();
        $oRow->setHeight(16);
        $oCell = $oRow->nextCell();
        $oCell->setColSpan(2)
            ->setWidth(100);
        $oCellText = $oCell->createTextRun($personalia->getFirstName().' '.$personalia->getLastName());
        $oCellText->getFont()
            ->setCharacterSpacing(1.5)
            ->setName('Open Sans SemiBold')
            ->setSize(14)
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        // Add row Date of Birth
        $oRow = $oTable->createRow();
        $oRow->setHeight(10);
        $oCell = $oRow->nextCell();
        $oCellText = $oCell->createTextRun('Geboortejaar: ');
        $oCellText->getFont()
            ->setSize(9)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCellText = $oCell->createTextRun(date_format($personalia->getDateOfBirth(), 'Y'));
        $oCellText->getFont()
            ->setSize(9)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        // Add row Residency
        $oRow = $oTable->createRow();
        $oRow->setHeight(10);
        $oCell = $oRow->nextCell();
        $oCellText = $oCell->createTextRun('Woonplaats: ');
        $oCellText->getFont()
            ->setSize(9)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCellText = $oCell->createTextRun($personalia->getPlaceOfResidence());
        $oCellText->getFont()
            ->setSize(9)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        // Education table
        $oTable = $oSlide2->createTableShape(2)
            ->setHeight(300)
            ->setWidth(430)
            ->setOffsetX($xOffsetLeft)
            ->setOffsetY(150);

        $oRow = $oTable->createRow();
        $oRow->setHeight(5);
        $oCell = $oRow->nextCell();
        $oCell->setWidth(345);
        $oCellText = $oCell->createTextRun(
            '
OPLEIDING'
        );
        $oCellText->getFont()
            ->setSize(10)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans SemiBold')
            ->setColor($red);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCell->setWidth(85);
        $this->setBorderStyle($oCell, 0);

        // Iteration over #education
        //Edu 1
        foreach ($education as $edu) {
            $oRow = $oTable->createRow();
            $oRow->setHeight(60);
            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->setLineSpacing(120);

            if (
                (strpos(mb_strtolower($edu->getEducationName()), 'master') !== false)
                ||
                (strpos(mb_strtolower($edu->getEducationName()), 'msc') !== false)
            ) {
                $oCellText = $oCell->createTextRun(
                    $edu->getEducationName().',
'.$edu->getEducationSpecialisation().'
'.$edu->getEducationInstitute()
                );
            } else {
                $oCellText = $oCell->createTextRun(
                    $edu->getEducationName().',
'.$edu->getEducationInstitute()
                );
            }


            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $oCellText = $oCell->createTextRun(
                date_format($edu->getStartDate(), 'Y')
                .'-'.
                date_format($edu->getEndDate(), 'Y')
            );
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);
        }

        // Certificate table
        $oTable = $oSlide2->createTableShape(2);
        $oTable->setHeight(200);
        $oTable->setWidth(430);
        $oTable->setOffsetX($xOffsetLeft);
        $oTable->setOffsetY(440);

        $oRow = $oTable->createRow();
        $oRow->setHeight(12);
        $oCell = $oRow->nextCell();
        $oCell->setWidth(345);
        $oCellText = $oCell->createTextRun(
            '
CERTIFICATEN'
        );
        $oCellText->getFont()
            ->setSize(10)
            ->setName('Open Sans SemiBold')
            ->setColor($red);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCell->setWidth(90);
        $this->setBorderStyle($oCell, 0);

        //Iteration certificates
        // Extra 1
        foreach ($certificates as $certificate) {
            $oRow = $oTable->createRow();
            $oRow->setHeight(10);
            $oCell = $oRow->nextCell();
            $oCellText = $oCell->createTextRun(
                $certificate->getCertificateName().' - '.$certificate->getCertificateInstitute()
            );
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $oCellText = $oCell->createTextRun(date_format($certificate->getObtainedDate(), 'Y'));
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);
        }

        // Add Stevin Logo
        $oStevinLogo = new Drawing\File();
        $oStevinLogo->setPath('./img/cv/logostevin.png')
            ->setHeight(70)
            ->setOffsetX(35)
            ->setOffsetY(645);
        $oSlide2->addShape($oStevinLogo);

        /* --------------------------
         *  Second slide RIGHT
         * --------------------------
         */

        // Add Skill Cloud
        $oCloudHeader = $oSlide2->createRichTextShape()
            ->setHeight(35)
            ->setWidth(400)
            ->setOffsetX($xOffsetRight)
            ->setOffsetY(55);
        $oCloudHeaderText = $oCloudHeader->createTextRun('COMPETENTIES');
        $oCloudHeaderText->getFont()
            ->setSize(10)
            ->setName('Open Sans SemiBold')
            ->setColor($red);

        $oCloud = $oSlide2->createRichTextShape()
            ->setHeight(35)
            ->setWidth(450)
            ->setOffsetX($xOffsetRight)
            ->setOffsetY(80);
        $oCloud->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach ($skills as $skill) //for($i=0; $i<count($skills); $i++)
        {
            $weight = $skill->getSkillWeight();
            if ($weight < 1) {
                $fontSize = 8;
            } elseif ($weight == 1) {
                $fontSize = 10;
            } elseif ($weight == 2) {
                $fontSize = 12;
            } elseif ($weight == 3) {
                $fontSize = 14;
            } elseif ($weight == 4) {
                $fontSize = 16;
            } elseif ($weight >= 5) {
                $fontSize = 18;
            } else {
                $fontSize = 10;
            }
            $oCloudText = $oCloud->createTextRun(mb_strtolower($skill->getSkillText()).'  ');

            $oCloudText->getFont()
                ->setName('Open Sans')
                ->setSize($fontSize)
                ->setColor($darkGrey);
        }


        // Add important Projects table
        $oTable = $oSlide2->createTableShape(1);
        $oTable->setHeight(200);
        $oTable->setWidth(430);
        $oTable->setOffsetX($xOffsetRight + 10);
        $oTable->setOffsetY(240);

        $oRow = $oTable->createRow();
        $oRow->setHeight(12);
        $oCell = $oRow->nextCell();
        $oCell->setWidth(345);
        $oCellText = $oCell->createTextRun(
            '
BELANGRIJKSTE PROJECTEN'
        );
        $oCellText->getFont()
            ->setSize(10)
            ->setName('Open Sans SemiBold')
            ->setColor($red);
        $this->setBorderStyle($oCell, 0);

        for ($i = 0; $i < count($projects); $i++) {
            if ($projects[$i]['important'] == 1) {
                $oRow = $oTable->createRow();
                $oRow->setHeight(10);
                $oCell = $oRow->nextCell();
                $oCellText = $oCell->createTextRun($projects[$i]['function_title']);
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);
            }
        }


        if (count($extracurricular) > 0) {

            // Add Extracurricular table
            $oTable = $oSlide2->createTableShape(2);
            $oTable->setHeight(200);
            $oTable->setWidth(430);
            $oTable->setOffsetX($xOffsetRight + 10);
            $oTable->setOffsetY(440);

            $oRow = $oTable->createRow();
            $oRow->setHeight(12);
            $oCell = $oRow->nextCell();
            $oCell->setWidth(345);
            $oCellText = $oCell->createTextRun(
                '
NEVENACTIVITEITEN'
            );
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans SemiBold')
                ->setColor($red);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->setWidth(90);
            $this->setBorderStyle($oCell, 0);

            foreach ($extracurricular as $extra) {
                $oRow = $oTable->createRow();
                $oRow->setHeight(10);
                $oCell = $oRow->nextCell();
                $oCellText = $oCell->createTextRun($extra->getExtraCurricularName());
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);

                $oCell = $oRow->nextCell();
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                if ($extra->getEndDate()) {
                    $text = date_format($extra->getStartDate(), 'Y').'-'.date_format($extra->getEndDate(), 'Y');
                } else {
                    $text = date_format($extra->getStartDate(), 'Y').'- nu';
                }

                $oCellText = $oCell->createTextRun($text);
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);
            }

        } elseif (count($publications) > 0) {
            // Add Publications table
            $oTable = $oSlide2->createTableShape(2);
            $oTable->setHeight(200);
            $oTable->setWidth(430);
            $oTable->setOffsetX($xOffsetRight + 10);
            $oTable->setOffsetY(440);

            $oRow = $oTable->createRow();
            $oRow->setHeight(12);
            $oCell = $oRow->nextCell();
            $oCell->setWidth(345);
            $oCellText = $oCell->createTextRun(
                '
PUBLICATIES'
            );
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans SemiBold')
                ->setColor($red);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->setWidth(85);
            $this->setBorderStyle($oCell, 0);

            foreach ($publications as $publication) {
                $oRow = $oTable->createRow();
                $oRow->setHeight(10);
                $oCell = $oRow->nextCell();
                $oCellText = $oCell->createTextRun($publication->getPublicationTitle());
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $oCell->createBreak();
                $oCellText = $oCell->createTextRun($publication->getPublicationJournal());
                $oCellText->getFont()
                    ->setSize(10)
                    ->setItalic(true)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);

                $oCell = $oRow->nextCell();
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $oCellText = $oCell->createTextRun(date_format($publication->getPublishedDate(), 'Y'));
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);
            }
        } else {
            die('Geen nevenactiviteit OF publicatie toegevoegd aan het CV.');
        }

        /* --------------------------
         *  Project Slides
         * --------------------------
         */
        $project_i = 1;
        foreach ($projects as $project) {
            $projectStartDate = date_create($project['start_date']);
            $projectEndDate = date_create($project['end_date']);

            if ($project_i % 2 == 0) {
                $offset = $xOffsetRight;
            } else {
                $offset = $xOffsetLeft;

                $oSlide = $objPHPPresentation->createSlide();
                $oSlide->setSlideLayout($oSlideLayout);

                // Add Background image
                $oBackgroundImageSlide = new BackgroundImage();
                $oBackgroundImageSlide->setPath('./img/cv/cvBackgroundExperience.png');
                $oSlide->setBackground($oBackgroundImageSlide);

                // Add Stevin Logo
                $oStevinLogo = new Drawing\File();
                $oStevinLogo->setPath('./img/cv/logostevin.png')
                    ->setHeight(70)
                    ->setOffsetX(35)
                    ->setOffsetY(645);
                $oSlide->addShape($oStevinLogo);
            }
            $project_i++;

            // Add date range box
            $oDateRange = $oSlide->createRichTextShape()
                ->setHeight(35)
                ->setWidth(125)
                ->setOffsetX($offset + 10)
                ->setOffsetY(45);
            $oDateRange->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($red);
            $oDateRange->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oDateRange->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $oDateRangeRun = $oDateRange->createTextRun(
                $this->getTranslatedMonth($projectStartDate, $locale).' '.date_format($projectStartDate, 'y')
                .' - '.
                $this->getTranslatedMonth($projectEndDate, $locale).' '.date_format($projectEndDate, 'y')
            );

            $oDateRangeRun->getFont()
                ->setBold(true)
                ->setName('Open Sans SemiBold')
                ->setSize(10)
                ->setColor($white);

            // Add company
            $oCompanyText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(100);
            $oCompanyText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oCompanyTextRun = $oCompanyText->createTextRun($project['customer_name']);
            $oCompanyTextRun->getFont()
                ->setName('Open Sans')
                ->setSize(8)
                ->setColor($lightGrey);

            // Add Role text
            $oRoleText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(120);
            $oRoleText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oRoleTextRun = $oRoleText->createTextRun(mb_strtoupper($project['function_title']));
            $oRoleTextRun->getFont()
                ->setCharacterSpacing(0.5)
                ->setBold(true)
                ->setName('Open Sans SemiBold')
                ->setSize(10)
                ->setColor($darkGrey);

            // Add Situation
            $oExecutiveText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(155);
            $oExecutiveText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oExecutiveTextRun = $oExecutiveText->createTextRun('SITUATIE');
            $oExecutiveTextRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(10)
                ->setColor($red);

            // Add ExecutiveTextBox
            $oExecutiveTextBox = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(90)
                ->setOffsetX($offset)
                ->setOffsetY(175);
            $oExecutiveTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oExecutiveTextBox->getActiveParagraph()->setLineSpacing(120);
            $oExecutiveTextBoxRun = $oExecutiveTextBox->createTextRun($project['situation_text']);
            $oExecutiveTextBoxRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(9)
                ->setColor($darkGrey);

            // Add Tasks
            $oTaskText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(265);
            $oTaskText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTaskTextRun = $oTaskText->createTextRun('WERKZAAMHEDEN');
            $oTaskTextRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(10)
                ->setColor($red);

            // Add TaskTextBox
            $oTaskTextBox = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(150)
                ->setOffsetX($offset)
                ->setOffsetY(285);
            $oTaskTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oTaskTextBox->getActiveParagraph()->setLineSpacing(120);
            $oTaskTextBoxRun = $oTaskTextBox->createTextRun($project['task_text']);
            $oTaskTextBoxRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(9)
                ->setColor($darkGrey);

            // Add Results
            $oResultsText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(440);
            $oResultsText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oResultsTextRun = $oResultsText->createTextRun('RESULTAAT');
            $oResultsTextRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(10)
                ->setColor($red);

            // Add ResultsTextBox
            $oResultsTextBox = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(150)
                ->setOffsetX($offset)
                ->setOffsetY(460);
            $oResultsTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oResultsTextBox->getActiveParagraph()->setLineSpacing(120);
            $oResultsTextBoxRun = $oResultsTextBox->createTextRun($project['result_text']);
            $oResultsTextBoxRun->getFont()
                ->setName('Open Sans SemiBold')
                ->setCharacterSpacing(0.5)
                ->setSize(9)
                ->setColor($darkGrey);


        }

        /* --------------------------
         *  Output
         * --------------------------
         */

        $oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
        $oWriterPPTX->save($outputFilePath);

        $response = new BinaryFileResponse($outputFilePath);
        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $outputFileName
        );
        $response->deleteFileAfterSend(true);

        return $response;

    }

    /**
     * @Route("/{userId}/cv/export/2017/{cvId}/{locale}", name="cv_export_2017")
     *
     */
    public function exportNewAction(Request $request, $userId, $cvId, $locale)
    {
        if($locale == 'en')
        {
            $dateOfBirthHeader = "Year of Birth:";
            $placeOfResidenceHeader = "Residence:";
            $educationHeader = "EDUCATION";
            $certificateHeader = "CERTIFICATES";
            $skillsHeader = "SKILLS";
            $importantProjectsHeader = "IMPORTANT PROJECTS";
            $extracurricularHeader = "EXTRACURRICULAR";
            $publicationsHeader = "PUBLICATIONS";
            $situationHeader = "SITUATION";
            $tasksHeader = "TASKS";
            $resultsHeader = "RESULTS";
        }
        elseif($locale == 'nl')
        {
            $dateOfBirthHeader = "Geboortejaar:";
            $placeOfResidenceHeader = "Woonplaats:";
            $educationHeader = "OPLEIDINGEN";
            $certificateHeader = "CERTIFICATEN";
            $skillsHeader = "COMPETENTIES";
            $importantProjectsHeader = "BELANGRIJKE PROJECTEN";
            $extracurricularHeader = "NEVENACTIVITEITEN";
            $publicationsHeader = "PUBLICATIES";
            $situationHeader = "SITUATIE";
            $tasksHeader = "WERKZAAMHEDEN";
            $resultsHeader = "RESULTATEN";

        }


        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => $cvId));

        $user = $cv->getUser();
        $personalia = $user->getPersonalia();
        $profile = $cv->getProfile();

        // Split paragraphs
        $paragraphs = explode("\n", $profile->getProfileText());
        if(sizeof($paragraphs[1]) == 1)
        {
            $offsetSlice = 2;
        }
        else
        {
            $offsetSlice = 1;
        }

        $query = "SELECT * FROM 
                  (SELECT * FROM curriculumvitae_project WHERE curriculumvitae_project.cv_id = ".$cvId.") 
                  as cv_project INNER JOIN project ON cv_project.project_id=project.id 
                  ORDER BY cv_project.important DESC, project.end_date DESC, project.start_date DESC";
        $test = $em->getConnection()->prepare($query);
        $test->execute();
        $projects = $test->fetchAll();

        $education = $cv->getEducation();
        $certificates = $cv->getCertificates();
        $extracurricular = $cv->getExtracurricular();
        $publications = $cv->getPublications();
        $skills = $cv->getSkills();

        $outputDirectory = $this->container->getParameter('curriculumvitae_output_directory');
        $profileImageDirectory = $this->container->getParameter('profile_image_directory');
        $outputFileName = $cv->getCurriculumvitaeName().'-'.$personalia->getFirstName().' '.$personalia->getLastName(
            ).'-'.date('d-m-Y')."-".$locale.".pptx";
        $outputFileName = str_replace(' ', '', $outputFileName);
        $outputFilePath = $outputDirectory."/".$outputFileName;

        /*---------------------------
         *  Create profile image
         * --------------------------
         */

        $fs = new Filesystem();
        $profileImageSource = $profileImageDirectory.'/'.$personalia->getProfileImageName();
        $profileImageName = explode(".",$personalia->getProfileImageName());
        $profileImageCornerSource = $profileImageDirectory.'/'.$profileImageName[0].'_corner.png';
        if(!$fs->exists($profileImageCornerSource))
        {
            @imagepng($this->imageCreateCorners($profileImageSource,791, 1016,40),$profileImageCornerSource);
        }

        /* --------------------------
         *  Define variables
         * --------------------------
         */

        $darkGrey = new Color('FF264756');
        $blueGreen = new Color('FF214856');
        $lightGrey = new Color('FF8D9499');
        $stevinRed = new Color('FFd2333a');
        $red = new Color('FFCC3333');
        $white = new Color('FFFFFFFF');
        $black = new Color('FF000000');
        $xOffsetLeft = 48;
        $xOffsetRight = 590;

        /* --------------------------
         *  Start Presentation
         * --------------------------
         */

        // Create object
        $objPHPPresentation = new PhpPresentation();
        $oMasterSlide = $objPHPPresentation->getAllMasterSlides()[0];
        $oSlideLayout = $oMasterSlide->getAllSlideLayouts()[0];

        // Set size
        $oDocumentLayout = $objPHPPresentation->getLayout();
        $oDocumentLayout->setCX(29.7,DocumentLayout::UNIT_CENTIMETER);
        $oDocumentLayout->setCY(21,DocumentLayout::UNIT_CENTIMETER);

        // Set properties
        $oDocumentProperties = $objPHPPresentation->getDocumentProperties();
        $oDocumentProperties->setCreator($personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setCompany('Stevin Technology Consultants');
        $oDocumentProperties->setTitle('CV '.$personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setDescription('Curriculum Vitae');
        $oDocumentProperties->setLastModifiedBy($personalia->getFirstName().' '.$personalia->getLastName());
        $oDocumentProperties->setKeywords('cv, '.$personalia->getFirstName().', '.$personalia->getLastName());

        /* --------------------------
         *  First slide
         * --------------------------
         */

        $oSlide1 = $objPHPPresentation->getActiveSlide();
        $oSlide1->setSlideLayout($oSlideLayout);

        /*
         * Default per slide
         */
        // Add background image
        $oProfileImg = new Drawing\File();
        $oProfileImg->setPath('./img/cv/cvbackground_new.png')
            ->setHeight(546)
            ->setWidth(546)
            ->setOffsetX(665)
            ->setOffsetY(334);
        $oSlide1->addShape($oProfileImg);

        // Add Stevin Logo
        $oStevinLogo = new Drawing\File();
        $oStevinLogo->setPath('./img/cv/logostevin_new.png')
            ->setHeight(45.5)
            ->setOffsetX(48)
            ->setOffsetY(47);
        $oSlide1->addShape($oStevinLogo);

        // Add CV text box
        $oNameText = $oSlide1->createRichTextShape()
            ->setHeight(23)
            ->setWidth(215)
            ->setOffsetX(844)
            ->setOffsetY(35)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun('Curriculum Vitae');
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(20)
            ->setColor($blueGreen);

        // Add Page Number box
        $oNameText = $oSlide1->createRichTextShape()
            ->setHeight(25)
            ->setWidth(25)
            ->setOffsetX(48)
            ->setOffsetY(758)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun('1');
        $oNameTextRun->getFont()
            ->setName('Arial')
            ->setSize(9)
            ->setColor($blueGreen);

        /*
         * End Default per slide
         */

        // Add profile picture
        $oProfileImg = new Drawing\File();
        $oProfileImg->setPath($profileImageCornerSource)
            //->setHeight(390)
            ->setWidth(260)
            ->setOffsetX(742)
            ->setOffsetY(173)->setRotation(4);
        $oProfileImg->getShadow()
            ->setVisible(true)
            ->setDirection(45)
            ->setDistance(10);
        $oSlide1->addShape($oProfileImg);

        // Add Name text box
        $oNameText = $oSlide1->createRichTextShape()
            ->setHeight(32)
            ->setWidth(613)
            ->setOffsetX(48)
            ->setOffsetY(199)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun(
            mb_strtoupper($personalia->getFirstName().' '.$personalia->getLastName())
        );
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(22)
            ->setColor($stevinRed);

        // Add profile text
        // First paragraph
        $oProfileTextFirst = $oSlide1->createRichTextShape()
            ->setWidth(613)
            ->setHeight(84)
            ->setOffsetX(48)
            ->setOffsetY(251)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oProfileTextFirst->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $oProfileTextFirst->getActiveParagraph()->setLineSpacing(132);
        $oProfileTextFirstRun = $oProfileTextFirst->createTextRun($paragraphs[0]);
        $oProfileTextFirstRun->getFont()
            ->setName('Arial')
            ->setSize(12)
            ->setColor($blueGreen)
            ->setBold(true);

        // Other paragraphs
        $oProfileTextRest = $oSlide1->createRichTextShape()
            ->setWidth(613)
            ->setHeight(267)
            ->setOffsetX(48)
            ->setOffsetY(345)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oProfileTextRest->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $oProfileTextRest->getActiveParagraph()->setLineSpacing(132);
        $oProfileTextRestRun = $oProfileTextRest->createTextRun(implode("",array_slice($paragraphs, $offsetSlice)));
        $oProfileTextRestRun->getFont()
            ->setName('Arial')
            ->setSize(9)
            ->setColor($blueGreen);

        // Add quote
        $oQuoteText = $oSlide1->createRichTextShape()
            ->setHeight(45)
            ->setWidth(613)
            ->setOffsetX(48)
            ->setOffsetY(677)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0)
            ->setRotation(-2);
        $oQuoteText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oQuoteTextRun = $oQuoteText->createTextRun($profile->getQuoteLine());
        $oQuoteTextRun->getFont()
            ->setName('Marydale')
            ->setSize(21.6)
            ->setColor($stevinRed);


        /* --------------------------
         *  Second slide LEFT
         * --------------------------
         */

        $oSlide2 = $objPHPPresentation->createSlide()
            ->setSlideLayout($oSlideLayout);

        /*
         * Default per slide
         */
        // Add background image
        $oProfileImg = new Drawing\File();
        $oProfileImg->setPath('./img/cv/cvbackground_new.png')
            ->setHeight(546)
            ->setWidth(546)
            ->setOffsetX(665)
            ->setOffsetY(334);
        $oSlide2->addShape($oProfileImg);

        // Add Stevin Logo
        $oStevinLogo = new Drawing\File();
        $oStevinLogo->setPath('./img/cv/logostevin_new.png')
            ->setHeight(45.5)
            ->setOffsetX(48)
            ->setOffsetY(47);
        $oSlide2->addShape($oStevinLogo);

        // Add CV text box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(23)
            ->setWidth(215)
            ->setOffsetX(844)
            ->setOffsetY(35)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun('Curriculum Vitae');
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(20)
            ->setColor($blueGreen);

        // Add Page Number box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(25)
            ->setWidth(25)
            ->setOffsetX(48)
            ->setOffsetY(758)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun('2');
        $oNameTextRun->getFont()
            ->setName('Arial')
            ->setSize(9)
            ->setColor($blueGreen);

        /*
         * End Default per slide
         */

        // Add Name text box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(32)
            ->setWidth(613)
            ->setOffsetX(48)
            ->setOffsetY(199)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun(
            mb_strtoupper($personalia->getFirstName().' '.$personalia->getLastName())
        );
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(22)
            ->setColor($stevinRed);

        // Add DoB text box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(19)
            ->setWidth(106)
            ->setOffsetX(48)
            ->setOffsetY(254)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun($dateOfBirthHeader);
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(12)
            ->setColor($blueGreen);

       // Add DoB year box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(19)
            ->setWidth(46)
            ->setOffsetX(158)
            ->setOffsetY(254)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun(date_format($personalia->getDateOfBirth(), 'Y'));
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(12)
            ->setColor($blueGreen);

       // Add Residency text box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(19)
            ->setWidth(106)
            ->setOffsetX(240)
            ->setOffsetY(254)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun($placeOfResidenceHeader);
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(12)
            ->setColor($blueGreen);

       // Add Residency box
        $oNameText = $oSlide2->createRichTextShape()
            ->setHeight(19)
            ->setWidth(96)
            ->setOffsetX(340)
            ->setOffsetY(254)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oNameTextRun = $oNameText->createTextRun($personalia->getPlaceOfResidence());
        $oNameTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(12)
            ->setColor($blueGreen);

        /*
         * Education
         */
        // Add Education Text
        $oText = $oSlide2->createRichTextShape()
            ->setHeight(20)
            ->setWidth(96)
            ->setOffsetX(48)
            ->setOffsetY(305)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oTextRun = $oText->createTextRun($educationHeader);
        $oTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(9)
            ->setColor($stevinRed);

        // Add Education
        $iEdu = 0;
        foreach ($education as $edu) {

            if (
                (strpos(mb_strtolower($edu->getEducationName()), 'master') !== false)
                ||
                (strpos(mb_strtolower($edu->getEducationName()), 'msc') !== false)
            ) {
                $educationName = $edu->getEducationName().', '.$edu->getEducationSpecialisation();

            } else {
                $educationName = $edu->getEducationName();
            }

            $oText = $oSlide2->createRichTextShape()
                ->setHeight(17)
                ->setWidth(388)
                ->setOffsetX(48)
                ->setOffsetY(327 + 53 * $iEdu)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTextRun = $oText->createTextRun($educationName);
            $oTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            $oText = $oSlide2->createRichTextShape()
                ->setHeight(17)
                ->setWidth(388)
                ->setOffsetX(48)
                ->setOffsetY(348 + 53 * $iEdu)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTextRun = $oText->createTextRun($edu->getEducationInstitute());
            $oTextRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            $oText = $oSlide2->createRichTextShape()
                ->setHeight(17)
                ->setWidth(76)
                ->setOffsetX(470)
                ->setOffsetY(327 + 53 * $iEdu)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $oTextRun = $oText->createTextRun(
                date_format($edu->getStartDate(), 'Y')
                .'-'.
                date_format($edu->getEndDate(), 'Y')
            );
            $oTextRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            $iEdu = $iEdu + 1;
        }

        /*
         * Certificates
         */
        // Add Certificate Text
        $oText = $oSlide2->createRichTextShape()
            ->setHeight(20)
            ->setWidth(96)
            ->setOffsetX(48)
            ->setOffsetY(572)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oTextRun = $oText->createTextRun($certificateHeader);
        $oTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(9)
            ->setColor($stevinRed);

        // Add Certificates
        $iCert = 0;
        foreach ($certificates as $certificate) {

            $oText = $oSlide2->createRichTextShape()
                ->setHeight(17)
                ->setWidth(388)
                ->setOffsetX(48)
                ->setOffsetY(593 + 23 * $iCert)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTextRun = $oText->createTextRun($certificate->getCertificateName()." - ".$certificate->getCertificateInstitute());
            $oTextRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);


            $oText = $oSlide2->createRichTextShape()
                ->setHeight(17)
                ->setWidth(76)
                ->setOffsetX(470)
                ->setOffsetY(593 + 23 * $iCert)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $oTextRun = $oText->createTextRun(
                date_format($certificate->getObtainedDate(), 'Y')
            );
            $oTextRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            $iCert = $iCert + 1;
        }

        /* --------------------------
         *  Second slide RIGHT
         * --------------------------
         */

        /*
         * Skills
         */
        // Add Skills Text
        $oText = $oSlide2->createRichTextShape()
            ->setHeight(20)
            ->setWidth(219)
            ->setOffsetX(590)
            ->setOffsetY(305)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oTextRun = $oText->createTextRun($skillsHeader);
        $oTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(9)
            ->setColor($stevinRed);

        $oCloud = $oSlide2->createRichTextShape()
            ->setHeight(102)
            ->setWidth(370)
            ->setOffsetX(590)
            ->setOffsetY(327)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oCloud->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oCloud->getActiveParagraph()->setLineSpacing(132);

        foreach ($skills as $skill)
        {
            $oCloudCheck = $oCloud->createTextRun("");
            $oCloudCheck->getFont()
                ->setName('Wingdings')
                ->setSize(9)
                ->setColor($stevinRed);
            $oCloudText = $oCloud->createTextRun(' '.mb_strtolower($skill->getSkillText()).'    ');
            $oCloudText->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($darkGrey);
        }

        /*
         * Important Projects
         */
        // Add Important Projects Text
        $oText = $oSlide2->createRichTextShape()
            ->setHeight(20)
            ->setWidth(219)
            ->setOffsetX(590)
            ->setOffsetY(438)
            ->setInsetTop(0)
            ->setInsetBottom(0)
            ->setInsetLeft(0)
            ->setInsetRight(0);
        $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $oTextRun = $oText->createTextRun($importantProjectsHeader);
        $oTextRun->getFont()
            ->setBold(true)
            ->setName('Arial')
            ->setSize(9)
            ->setColor($stevinRed);

        // Add Important Projects
        $iImp = 0;
        for ($i = 0; $i < count($projects); $i++) {
            if ($projects[$i]['important'] == 1) {
                $oText = $oSlide2->createRichTextShape()
                    ->setHeight(17)
                    ->setWidth(344)
                    ->setOffsetX(590)
                    ->setOffsetY(460 + 23 * $iImp)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $oTextRun = $oText->createTextRun($projects[$i]['function_title']);
                $oTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);
                $iImp = $iImp + 1;
            }
        }

        /*
         * Extracurricular or Publications
         */

        if (count($extracurricular) > 0) {

            // Add Extracurricular Text
            $oText = $oSlide2->createRichTextShape()
                ->setHeight(20)
                ->setWidth(219)
                ->setOffsetX(590)
                ->setOffsetY(572)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTextRun = $oText->createTextRun($extracurricularHeader);
            $oTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($stevinRed);

            // Add Extracurricular
            $iExtra = 0;
            foreach ($extracurricular as $extra) {

                if ($extra->getEndDate()) {
                    $extraDate = date_format($extra->getStartDate(), 'Y').'-'.date_format($extra->getEndDate(), 'Y');
                } else {
                    $extraDate = date_format($extra->getStartDate(), 'Y').'- nu';
                }

                $oText = $oSlide2->createRichTextShape()
                    ->setHeight(17)
                    ->setWidth(388)
                    ->setOffsetX(590)
                    ->setOffsetY(593 + 23 * $iExtra)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $oTextRun = $oText->createTextRun($extra->getExtraCurricularName());
                $oTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);


                $oText = $oSlide2->createRichTextShape()
                    ->setHeight(17)
                    ->setWidth(76)
                    ->setOffsetX(1011)
                    ->setOffsetY(593 + 23 * $iExtra)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $oTextRun = $oText->createTextRun($extraDate);
                $oTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);

                $iExtra = $iExtra + 1;
            }



        } elseif (count($publications) > 0) {
            // Add Publications Text
            $oText = $oSlide2->createRichTextShape()
                ->setHeight(20)
                ->setWidth(219)
                ->setOffsetX(590)
                ->setOffsetY(572)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTextRun = $oText->createTextRun($publicationsHeader);
            $oTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($stevinRed);

            // Add Extracurricular
            $iPub = 0;
            foreach ($publications as $publication)
            {

                $oText = $oSlide2->createRichTextShape()
                    ->setHeight(17)
                    ->setWidth(388)
                    ->setOffsetX(590)
                    ->setOffsetY(593 + 23 * $iPub)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $oTextRun = $oText->createTextRun($publication->getPublicationTitle()." - ".$publication->getPublicationJournal());
                $oTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);


                $oText = $oSlide2->createRichTextShape()
                    ->setHeight(17)
                    ->setWidth(76)
                    ->setOffsetX(1011)
                    ->setOffsetY(593 + 23 * $iPub)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $oTextRun = $oText->createTextRun(date_format($publication->getPublishedDate(), 'Y'));
                $oTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);

                $iPub = $iPub + 1;
            }

        }
        else
        {
            die('Geen nevenactiviteit OF publicatie toegevoegd aan het CV.');
        }

        /* --------------------------
         *  Project Slides
         * --------------------------
         */
        $project_i = 1;
        $iProjectPage = 1;
        foreach ($projects as $project)
        {
            $projectStartDate = date_create($project['start_date']);
            $projectEndDate = date_create($project['end_date']);

            if ($project_i % 2 == 0)
            {
                $offset = $xOffsetRight;
            }
            else
            {
                $offset = $xOffsetLeft;

                $oSlide = $objPHPPresentation->createSlide();
                $oSlide->setSlideLayout($oSlideLayout);

                /*
                 * Default per slide
                 */
                // Add background image
                $oProfileImg = new Drawing\File();
                $oProfileImg->setPath('./img/cv/cvbackground_new.png')
                    ->setHeight(546)
                    ->setWidth(546)
                    ->setOffsetX(665)
                    ->setOffsetY(334);
                $oSlide->addShape($oProfileImg);

                // Add Stevin Logo
                $oStevinLogo = new Drawing\File();
                $oStevinLogo->setPath('./img/cv/logostevin_new.png')
                    ->setHeight(45.5)
                    ->setOffsetX(48)
                    ->setOffsetY(47);
                $oSlide->addShape($oStevinLogo);

                // Add CV text box
                $oNameText = $oSlide->createRichTextShape()
                    ->setHeight(23)
                    ->setWidth(215)
                    ->setOffsetX(844)
                    ->setOffsetY(35)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $oNameTextRun = $oNameText->createTextRun('Curriculum Vitae');
                $oNameTextRun->getFont()
                    ->setBold(true)
                    ->setName('Arial')
                    ->setSize(20)
                    ->setColor($blueGreen);

                // Add Page Number box
                $oNameText = $oSlide->createRichTextShape()
                    ->setHeight(25)
                    ->setWidth(25)
                    ->setOffsetX(48)
                    ->setOffsetY(758)
                    ->setInsetTop(0)
                    ->setInsetBottom(0)
                    ->setInsetLeft(0)
                    ->setInsetRight(0);
                $oNameText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $oNameTextRun = $oNameText->createTextRun($iProjectPage + 2);
                $oNameTextRun->getFont()
                    ->setName('Arial')
                    ->setSize(9)
                    ->setColor($blueGreen);

                /*
                 * End Default per slide
                 */
                $iProjectPage++;
            }
            $project_i++;

            // Add date range box
            $oDateRange = $oSlide->createRichTextShape()
                ->setHeight(17)
                ->setWidth(135)
                ->setOffsetX($offset)
                ->setOffsetY(201)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oDateRange->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oDateRangeRun = $oDateRange->createTextRun(
                $this->getTranslatedMonth($projectStartDate, $locale).' '.date_format($projectStartDate, 'y')
                .' - '.
                $this->getTranslatedMonth($projectEndDate, $locale).' '.date_format($projectEndDate, 'y')
            );

            $oDateRangeRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            // Add Function/role
            $oCompanyText = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(17)
                ->setOffsetX($offset)
                ->setOffsetY(238)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oCompanyText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oCompanyTextRun = $oCompanyText->createTextRun(mb_strtoupper($project['function_title']));
            $oCompanyTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(14)
                ->setColor($stevinRed);

            // Add company
            $oRoleText = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(17)
                ->setOffsetX($offset)
                ->setOffsetY(267)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oRoleText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oRoleTextRun = $oRoleText->createTextRun($project['customer_name']);
            $oRoleTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(14)
                ->setColor($blueGreen);

            // Add Situation
            $oExecutiveText = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(17)
                ->setOffsetX($offset)
                ->setOffsetY(315)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oExecutiveText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oExecutiveTextRun = $oExecutiveText->createTextRun($situationHeader);
            $oExecutiveTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($stevinRed);

            // Add ExecutiveTextBox
            $oExecutiveTextBox = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(48)
                ->setOffsetX($offset)
                ->setOffsetY(332)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oExecutiveTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oExecutiveTextBox->getActiveParagraph()->setLineSpacing(132);
            $oExecutiveTextBoxRun = $oExecutiveTextBox->createTextRun($project['situation_text']);
            $oExecutiveTextBoxRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            // Add Tasks
            $oTaskText = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(17)
                ->setOffsetX($offset)
                ->setOffsetY(401)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oTaskText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oTaskTextRun = $oTaskText->createTextRun($tasksHeader);
            $oTaskTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($stevinRed);

            // Add TaskTextBox
            $oTaskTextBox = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(118)
                ->setOffsetX($offset)
                ->setOffsetY(417)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oTaskTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oTaskTextBox->getActiveParagraph()->setLineSpacing(132);
            $oTaskTextBoxRun = $oTaskTextBox->createTextRun($project['task_text']);
            $oTaskTextBoxRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);

            // Add Results
            $oResultsText = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(17)
                ->setOffsetX($offset)
                ->setOffsetY(550)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oResultsText->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $oResultsTextRun = $oResultsText->createTextRun($resultsHeader);
            $oResultsTextRun->getFont()
                ->setBold(true)
                ->setName('Arial')
                ->setSize(9)
                ->setColor($stevinRed);

            // Add ResultsTextBox
            $oResultsTextBox = $oSlide->createRichTextShape()
                ->setWidth(498)
                ->setHeight(161)
                ->setOffsetX($offset)
                ->setOffsetY(566)
                ->setInsetTop(0)
                ->setInsetBottom(0)
                ->setInsetLeft(0)
                ->setInsetRight(0);
            $oResultsTextBox->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
            $oResultsTextBox->getActiveParagraph()->setLineSpacing(132);
            $oResultsTextBoxRun = $oResultsTextBox->createTextRun($project['result_text']);
            $oResultsTextBoxRun->getFont()
                ->setName('Arial')
                ->setSize(9)
                ->setColor($blueGreen);


        }

        /* --------------------------
         *  Output
         * --------------------------
         */

        $oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
        $oWriterPPTX->save($outputFilePath);

        $response = new BinaryFileResponse($outputFilePath);
        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $outputFileName
        );
        $response->deleteFileAfterSend(true);

        return $response;

    }

    public function setBorderStyle(Cell $cell, $lineWidth)
    {
        $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getRight()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getTop()->setLineStyle(Border::LINE_NONE);
        $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_NONE);
    }

    public function getTranslatedMonth(\DateTime $datetime, $locale)
    {
        $month = date_format($datetime, 'M');
        if ($locale == 'nl')
        {
            switch ($month)
            {
                case 'Mar':
                    $result = 'Mrt';
                    break;
                case 'Oct':
                    $result = 'Okt';
                    break;
                case 'May':
                    $result = 'Mei';
                    break;
                default:
                    $result = $month;
            }
        }
        else
        {
            $result = $month;
        }

        return $result;
    }

    public function imageCreateCorners($sourceImageFile, $width, $height, $radius)
    {
        $aspectRatio_new = $width / $height;
        $info = getimagesize($sourceImageFile);

        if (file_exists($sourceImageFile)) {
            $res = is_array($info);
        } else {
            $res = false;
        }

        if ($res) {
            $width_source = $info[0];
            $height_source = $info[1];
            $aspectRatio_source = $width_source / $height_source;
            switch ($info['mime']) {
                case 'image/jpeg':
                    $src = imagecreatefromjpeg($sourceImageFile);
                    break;
                case 'image/gif':
                    $src = imagecreatefromgif($sourceImageFile);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($sourceImageFile);
                    break;
                default:
                    $res = false;
            }
        }

        if ($res) {
            // Resize
            if ($aspectRatio_new > $aspectRatio_source) {
                $resized_width = $width;
            } else {
                $resized_width = $height * $aspectRatio_source;
            }

            $resized_image = imagescale($src, $resized_width);

            // Crop
            if ($resized_width > $width || $resized_width * $aspectRatio_source > $height) {
                $cropped_image = imagecrop(
                    $resized_image,
                    ['x' => ($resized_width - $width) / 2, 'y' => 0, 'width' => $width, 'height' => $height]
                );
            } else {
                $cropped_image = $resized_image;
            }

            $q = 1; # change this if you want
            $radius *= $q;

            # find unique color
            do {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
            } while (imagecolorexact($cropped_image, $r, $g, $b) < 0);

            $nw = $width * $q;
            $nh = $height * $q;

            $img = imagecreatetruecolor($nw, $nh);

            $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
            imagealphablending($img, false);
            imagesavealpha($img, true);
            imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

            imagefill($img, 0, 0, $alphacolor);
            imagecopyresampled($img, $cropped_image, 0, 0, 0, 0, $nw, $nh, $width, $height);

            imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
            imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
            imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
            imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
            imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
            imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
            imagealphablending($img, true);
            imagecolortransparent($img, $alphacolor);

            # resize image down
            $dest = imagecreatetruecolor($width, $height);
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            imagefilledrectangle($dest, 0, 0, $width, $height, $alphacolor);
            imagecopyresampled($dest, $img, 0, 0, 0, 0, $width, $height, $nw, $nh);

            # output image
            $res = $dest;
            imagedestroy($src);
            imagedestroy($img);
            imagedestroy($cropped_image);
            imagedestroy($resized_image);

        }

        return $res;

    }
}