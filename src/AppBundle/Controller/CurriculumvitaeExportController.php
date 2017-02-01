<?php


namespace AppBundle\Controller;

use PhpOffice\PhpPresentation\Shape\Table\Cell;
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


class CurriculumvitaeExportController extends Controller
{
    /**
     * @Route("/{userId}/cv/export/{cvId}", name="cv_export")
     *
     */
    public function exportAction(Request $request, $userId, $cvId)
    {

        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:Curriculumvitae')
            ->findOneBy(array('id' => $cvId));

        $user = $cv->getUser();
        $personalia = $em->getRepository('AppBundle:Personalia')
            ->findOneBy(array('user' => $user));
        $profile = $cv->getProfile();
        $projects = $cv->getProjects();
        $education = $cv->getEducation();
        $certificates = $cv->getCertificates();
        $extracurricular = $cv->getExtracurricular();
        $publications = $cv->getPublications();
        $skills = $cv->getSkills();

        $outputDirectory = $this->container->getParameter('curriculumvitae_output_directory');
        $profileImageDirectory = $this->container->getParameter('profile_image_directory');
        $outputFileName = $cv->getCurriculumvitaeName().'-'.$personalia->getFirstName().' '. $personalia->getLastName().'-'.date('d-m-Y').".pptx";
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
        $oDocumentProperties->setCreator($personalia->getFirstName().' '. $personalia->getLastName());
        $oDocumentProperties->setCompany('Stevin Technology Consultants');
        $oDocumentProperties->setTitle('CV '. $personalia->getFirstName().' '. $personalia->getLastName());
        $oDocumentProperties->setDescription('Curriculum Vitae');
        $oDocumentProperties->setLastModifiedBy($personalia->getFirstName().' '. $personalia->getLastName());
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
            ->setHeight(390)
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
        $oNameText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $oNameTextRun = $oNameText->createTextRun(strtoupper($personalia->getFirstName().' '. $personalia->getLastName()));
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
        $oQuoteText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $oQuoteTextRun = $oQuoteText->createTextRun(strtoupper($profile->getQuoteLine()));
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
        $oProfileText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $oProfileTextRun = $oProfileText->createTextRun($profile->getProfileText());
        $oProfileTextRun->getFont()
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setSize(8)
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
            ->setWidthAndHeight(95,95)
            ->setResizeProportional(false)
            ->setOffsetX($xOffsetLeft+10)
            ->setOffsetY(50);
        $oSlide2->addShape($oProfileImage);

        // Add name text box
        // Add Date of Birth and Residency table
        // Create a shape (table)
        $oTable = $oSlide2->createTableShape(2);
        $oTable->setHeight(55);
        $oTable->setWidth(400);
        $oTable->setOffsetX($xOffsetLeft+125);
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
        $oCellText = $oCell->createTextRun('Geboortejaar:');
        $oCellText->getFont()
            ->setSize(9)
            ->setCharacterSpacing(0.5)
            ->setName('Open Sans')
            ->setColor($darkGrey);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCellText = $oCell->createTextRun(date_format($personalia->getDateOfBirth(),'Y'));
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
        $oCellText = $oCell->createTextRun('Woonplaats:');
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
        $oCellText = $oCell->createTextRun('
OPLEIDING');
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
        foreach($education as $edu) {
            $oRow = $oTable->createRow();
            $oRow->setHeight(60);
            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->setLineSpacing(120);

            if(
                (strpos(strtolower($edu->getEducationName()), 'master') !== false)
                ||
                (strpos(strtolower($edu->getEducationName()), 'msc') !== false)
            )
            {
                $oCellText = $oCell->createTextRun($edu->getEducationName().',
'.$edu->getEducationSpecialisation().'
'.$edu->getEducationInstitute());
            }
            else
            {
                $oCellText = $oCell->createTextRun($edu->getEducationName().',
'.$edu->getEducationInstitute());
            }


            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $oCellText = $oCell->createTextRun(
                date_format($edu->getStartDate(),'Y')
                .'-'.
                date_format($edu->getEndDate(),'Y')
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
        $oCellText = $oCell->createTextRun('
CERTIFICATEN');
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
        foreach($certificates as $certificate)
        {
            $oRow = $oTable->createRow();
            $oRow->setHeight(10);
            $oCell = $oRow->nextCell();
            $oCellText = $oCell->createTextRun($certificate->getCertificateName().' - '.$certificate->getCertificateInstitute());
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
            $oCellText = $oCell->createTextRun(date_format($certificate->getObtainedDate(),'Y'));
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
        $oCloud->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );

//        $skill_range = range(0,count($skills));
//        shuffle($skill_range);
        foreach($skills as $skill)
        //for($i=0; $i<count($skills); $i++)
        {
            $weight = $skill->getSkillWeight();
            if($weight < 1)
            {
                $fontSize = 8;
            }
            elseif($weight == 1)
            {
                $fontSize = 10;
            }
            elseif($weight == 2)
            {
                $fontSize = 12;
            }
            elseif($weight == 3)
            {
                $fontSize = 14;
            }
            elseif($weight == 4)
            {
                $fontSize = 16;
            }
            elseif($weight >= 5)
            {
                $fontSize = 18;
            }
            else {
                $fontSize = 10;
            }
            $oCloudText = $oCloud->createTextRun(strtolower($skill->getSkillText()).'  ');

            $oCloudText->getFont()
                ->setName('Open Sans')
                ->setSize($fontSize)
                ->setColor($darkGrey);
        }


        // Add important Projects table
        $oTable = $oSlide2->createTableShape(1);
        $oTable->setHeight(200);
        $oTable->setWidth(430);
        $oTable->setOffsetX($xOffsetRight);
        $oTable->setOffsetY(200);

        $oRow = $oTable->createRow();
        $oRow->setHeight(12);
        $oCell = $oRow->nextCell();
        $oCell->setWidth(345);
        $oCellText = $oCell->createTextRun('
BELANGRIJKSTE PROJECTEN');
        $oCellText->getFont()
            ->setSize(10)
            ->setName('Open Sans SemiBold')
            ->setColor($red);
        $this->setBorderStyle($oCell, 0);

        for($i = 0; $i<3; $i++)
        {
            $oRow = $oTable->createRow();
            $oRow->setHeight(10);
            $oCell = $oRow->nextCell();
            $oCellText = $oCell->createTextRun($projects[$i]->getFunctionTitle());
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans')
                ->setColor($darkGrey);
            $this->setBorderStyle($oCell, 0);
        }


        if(count($extracurricular) > 0)
        {

        // Add Extracurricular table
        $oTable = $oSlide2->createTableShape(2);
        $oTable->setHeight(200);
        $oTable->setWidth(430);
        $oTable->setOffsetX($xOffsetRight);
        $oTable->setOffsetY(400);

        $oRow = $oTable->createRow();
        $oRow->setHeight(12);
        $oCell = $oRow->nextCell();
        $oCell->setWidth(345);
        $oCellText = $oCell->createTextRun('
NEVENACTIVITEITEN');
        $oCellText->getFont()
            ->setSize(10)
            ->setName('Open Sans SemiBold')
            ->setColor($red);
        $this->setBorderStyle($oCell, 0);

        $oCell = $oRow->nextCell();
        $oCell->setWidth(90);
        $this->setBorderStyle($oCell, 0);

            foreach($extracurricular as $extra)
            {
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
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );

                if($extra->getEndDate())
                {
                    $text = date_format($extra->getStartDate(),'Y').'-'.date_format($extra->getEndDate(),'Y');
                }
                else
                {
                    $text = date_format($extra->getStartDate(),'Y').'- nu';
                }

                $oCellText = $oCell->createTextRun($text);
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);
            }

        }
        elseif(count($publications) > 0)
        {
            // Add Publications table
            $oTable = $oSlide2->createTableShape(2);
            $oTable->setHeight(200);
            $oTable->setWidth(430);
            $oTable->setOffsetX($xOffsetRight);
            $oTable->setOffsetY(300);

            $oRow = $oTable->createRow();
            $oRow->setHeight(12);
            $oCell = $oRow->nextCell();
            $oCell->setWidth(345);
            $oCellText = $oCell->createTextRun('
PUBLICATIES');
            $oCellText->getFont()
                ->setSize(10)
                ->setName('Open Sans SemiBold')
                ->setColor($red);
            $this->setBorderStyle($oCell, 0);

            $oCell = $oRow->nextCell();
            $oCell->setWidth(85);
            $this->setBorderStyle($oCell, 0);

            foreach($publications as $publication)
            {
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
                $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
                $oCellText = $oCell->createTextRun(date_format($publication->getPublishedDate(),'Y'));
                $oCellText->getFont()
                    ->setSize(10)
                    ->setName('Open Sans')
                    ->setColor($darkGrey);
                $this->setBorderStyle($oCell, 0);
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
        foreach($projects as $project)
        {
            if($project_i % 2 == 0)
            {
                $offset = $xOffsetRight;
            }
            else
            {
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
                ->setOffsetX($offset+10)
                ->setOffsetY(45);
            $oDateRange->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($red);
            $oDateRange->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oDateRange->getActiveParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
            $oDateRangeRun = $oDateRange->createTextRun(
                    $this->getTranslatedMonth($project->getStartDate()).' '. date_format($project->getStartDate(),'y')
                    .' - '.
                    $this->getTranslatedMonth($project->getEndDate()).' '. date_format($project->getEndDate(),'y')
                );
            $oDateRangeRun->getFont()
                ->setBold(true)
                ->setName('Open Sans SemiBold')
                ->setSize(10)
                ->setColor($white);

            // Add Role text
            $oRoleText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(100);
            $oRoleText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oRoleTextRun = $oRoleText->createTextRun(strtoupper($project->getFunctionTitle()));
            $oRoleTextRun->getFont()
                ->setCharacterSpacing(0.5)
                ->setBold(true)
                ->setName('Open Sans SemiBold')
                ->setSize(10)
                ->setColor($darkGrey);

            // Add company
            $oCompanyText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(125);
            $oCompanyText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oCompanyTextRun = $oCompanyText->createTextRun($project->getCustomerName());
            $oCompanyTextRun->getFont()
                ->setName('Open Sans')
                ->setSize(8)
                ->setColor($lightGrey);

            // Add Executive
            $oExecutiveText = $oSlide->createRichTextShape()
                ->setWidth(450)
                ->setHeight(15)
                ->setOffsetX($offset)
                ->setOffsetY(145);
            $oExecutiveText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
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
                ->setOffsetY(165);
            $oExecutiveTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oExecutiveTextBox->getActiveParagraph()->setLineSpacing(120);
            $oExecutiveTextBoxRun = $oExecutiveTextBox->createTextRun($project->getSituationText());
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
                ->setOffsetY(255);
            $oTaskText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
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
                ->setOffsetY(275);
            $oTaskTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oTaskTextBox->getActiveParagraph()->setLineSpacing(120);
            $oTaskTextBoxRun = $oTaskTextBox->createTextRun($project->getTaskText());
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
                ->setOffsetY(425);
            $oResultsText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
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
                ->setOffsetY(445);
            $oResultsTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
            $oResultsTextBox->getActiveParagraph()->setLineSpacing(120);
            $oResultsTextBoxRun = $oResultsTextBox->createTextRun($project->getResultText());
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
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $outputFileName
        );
        $response->deleteFileAfterSend(true);

        return $response;

    }

    public function setBorderStyle(Cell $cell, $lineWidth)
    {
        $cell->getBorders()->getLeft()->setLineWidth($lineWidth);
        $cell->getBorders()->getRight()->setLineWidth($lineWidth);
        $cell->getBorders()->getTop()->setLineWidth($lineWidth);
        $cell->getBorders()->getBottom()->setLineWidth($lineWidth);
    }

    public function getTranslatedMonth(\Datetime $datetime)
    {
        $month = date_format($datetime,'M');

        switch($month)
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

        return $result;
    }

}