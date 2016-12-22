<?php

// Include all necessary PHPPresentation files
require_once 'lib/PHPPresentation/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();

require_once 'lib/PHPOffice/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');

use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Slide\Background\Image as BackgroundImage;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

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
echo date('H:i:s') . ' Create new PHPPresentation object'.EOL;
$objPHPPresentation = new PhpPresentation();
$oMasterSlide = $objPHPPresentation->getAllMasterSlides()[0];
$oSlideLayout = $oMasterSlide->getAllSlideLayouts()[0];

// Set size
echo date('H:i:s') . ' Set document layout size'.EOL;
$oDocumentLayout = $objPHPPresentation->getLayout();
$oDocumentLayout->setDocumentLayout(DocumentLayout::LAYOUT_A4, true);

// Set properties
echo date('H:i:s') . ' Set document properties'.EOL;
$oDocumentProperties = $objPHPPresentation->getDocumentProperties();
$oDocumentProperties->setCreator('Jeffrey van Oostrom');
$oDocumentProperties->setCompany('Stevin Technology Consultants');
$oDocumentProperties->setTitle('CV Jeffrey van Oostrom');
$oDocumentProperties->setDescription('Curriculum Vitae');
$oDocumentProperties->setLastModifiedBy('Jeffrey van Oostrom');
$oDocumentProperties->setKeywords('cv, jeffrey, van, oostrom');

/* --------------------------
 *  First slide
 * -------------------------- 
 */

echo date('H:i:s') . ' Create first slide'.EOL;
$oSlide1 = $objPHPPresentation->getActiveSlide();
$oSlide1->setSlideLayout($oSlideLayout);

// Add background image
$oBackgroundImage = new BackgroundImage();
$oBackgroundImage->setPath('./img/cv/cvbackground.jpg');
$oSlide1->setBackground($oBackgroundImage);

// Add placeholder picture
$oPlaceholder = new Drawing\File();
$oPlaceholder->setPath('./img/cv/placeholder.png')
->setHeight(0)
->setWidth(0)
->setOffsetX(0)
->setOffsetY(0);
$oSlide1->addShape($oPlaceholder);

// Add profile picture
$oProfileImg = new Drawing\File();
$oProfileImg->setPath('./img/cv/voorpaginaJeffreyVanOostrom.jpg')
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
$oNameTextRun = $oNameText->createTextRun('JEFFREY VAN OOSTROM');
$oNameTextRun->getFont()
			 ->setCSpacing(3)
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
$oQuoteTextRun = $oQuoteText->createTextRun('SEEK FIRST TO UNDERSTAND, THEN TO BE UNDERSTOOD');
$oQuoteTextRun->getFont()
			  ->setCSpacing(2)
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
$oProfileTextRun = $oProfileText->createTextRun('Ontwikkeling staat bij mij centraal. Niet alleen persoonlijke ontwikkeling, maar ook die van anderen. Door deze blik ben ik erg leergierig. Ik zorg dat ik de benodigde kennis heb, dan wel in huis haal.

Tijdens mijn studie Aerospace Engineering heb ik een breed scala aan technische kennis en kunde opgebouwd. In verschillende projecten heb ik laten zien in staat te zijn om in groepen te werken en mijn kennis snel en effectief toe te passen. Niet alleen vliegtuigkennis, maar ook programmeren, systems engineering en het ontwikkelen van analytische vaardigheden waren onderdeel van het curriculum.

Ik heb bij KPN de problematiek rondom assetmanagement van het mobiele netwerk mij eigen gemaakt. Door goed te luisteren en de juiste mensen met elkaar te verbinden heb ik een verbeterslag kunnen maken in de processen en systemen, om zo een onderhoudbaar en toekomstbestendig mobiel netwerk te realiseren.

Bij Ordina heb ik een team van engineers begeleid in het Agile werken, door gezamenlijk een innovatieve continuous delivery pipeline te ontwikkelen, waarbij gebruik gemaakt wordt van de nieuwste technieken en tools.

Ik heb technische kennis en communicatieve vaardigheden, een goede combinatie om elk probleem aan te pakken.');
$oProfileTextRun->getFont()
				->setCSpacing(0.5)
			 	->setName('Open Sans')
			 	->setSize(8)
			 	->setColor($darkGrey);

 
/* --------------------------
 *  Second slide LEFT
 * --------------------------
 */

echo date('H:i:s') . ' Create second slide'.EOL;
$oSlide2 = $objPHPPresentation->createSlide();
$oSlide2->setSlideLayout($oSlideLayout);

// Add Background image
$oBackgroundImageSlide2 = new BackgroundImage();
$oBackgroundImageSlide2->setPath('./img/cv/cvBackgroundExperience.png');
$oSlide2->setBackground($oBackgroundImageSlide2);

// Add placeholder picture
$oPlaceholder = new Drawing\File();
$oPlaceholder->setPath('./img/cv/placeholder.png')
->setHeight(0)
->setWidth(0)
->setOffsetX(0)
->setOffsetY(0);
$oSlide2->addShape($oPlaceholder);

// Add profile image
$oProfileImage = new Drawing\File();
$oProfileImage->setPath('./img/cv/profielFotoJeffreyVanOostrom.png')
->setWidthAndHeight(95,95)
->setResizeProportional(false)
->setOffsetX($xOffsetLeft+10)
->setOffsetY(50);
$oSlide2->addShape($oProfileImage);

// Add name text box
// Add Date of Birth and Residency table
// Create a shape (table)
$oTableDate = $oSlide2->createTableShape(2);
$oTableDate->setHeight(55);
$oTableDate->setWidth(400);
$oTableDate->setOffsetX($xOffsetLeft+125);
$oTableDate->setOffsetY(55);
 
// Add row Name
$oRowName = $oTableDate->createRow();
$oRowName->setHeight(16);
$oCell12 = $oRowName->nextCell();
$oCell12->setColSpan(2);
$oCell12->setWidth(100);
$oCell12Text = $oCell12->createTextRun('Jeffrey van Oostrom');
$oCell12Text->getFont()
->setCSpacing(1.5)
->setName('Open Sans SemiBold')
->setSize(14)
->setColor($darkGrey);

// Add row Date of Birth
$oRowDate = $oTableDate->createRow();
$oRowDate->setHeight(10);
$oCell1 = $oRowDate->nextCell();
$oCell1Text = $oCell1->createTextRun('Geboortejaar:');
$oCell1Text->getFont()
->setSize(9)
->setCSpacing(0.5)
->setName('Open Sans')
->setColor($darkGrey);

$oCell2 = $oRowDate->nextCell();
$oCell2Text = $oCell2->createTextRun('1990');
$oCell2Text->getFont()
->setSize(9)
->setCSpacing(0.5)
->setName('Open Sans')
->setColor($darkGrey);

// Add row Residency
$oRowPlace = $oTableDate->createRow();
$oRowPlace->setHeight(10);
$oCell1 = $oRowPlace->nextCell();
$oCell1Text = $oCell1->createTextRun('Woonplaats:');
$oCell1Text->getFont()
->setSize(9)
->setCSpacing(0.5)
->setName('Open Sans')
->setColor($darkGrey);

$oCell2 = $oRowPlace->nextCell();
$oCell2Text = $oCell2->createTextRun('Voorburg');
$oCell2Text->getFont()
->setSize(9)
->setCSpacing(0.5)
->setName('Open Sans')
->setColor($darkGrey);

// Time to add education table
$oTableEducation = $oSlide2->createTableShape(2);
$oTableEducation->setHeight(300);
$oTableEducation->setWidth(430);
$oTableEducation->setOffsetX($xOffsetLeft);
$oTableEducation->setOffsetY(150);

$oRow = $oTableEducation->createRow();
$oRow->setHeight(5);
$oCell = $oRow->nextCell();
$oCell->setWidth(345);
$oCellText = $oCell->createTextRun('
OPLEIDING');
$oCellText->getFont()
->setSize(10)
->setCSpacing(0.5)
->setName('Open Sans SemiBold')
->setColor($red);
$oCell = $oRow->nextCell();
$oCell->setWidth(85);

$nEducation = 3;
$rowHeight = floor(300/$nEducation);

// Iteration over #education
//Edu 1
$oRow = $oTableEducation->createRow();
$oRow->setHeight($rowHeight);
$oCell = $oRow->nextCell();
$oCell->getActiveParagraph()->setLineSpacing(120);
$oCellText = $oCell->createTextRun('Master Aerospace Engineering,
specialisatie Aerodynamics and Wind Energy
Technische Universiteit Delft');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2011-2014');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

//Edu 2
$oRow = $oTableEducation->createRow();
$oRow->setHeight($rowHeight);
$oCell = $oRow->nextCell();
$oCell->getActiveParagraph()->setLineSpacing(120);
$oCellText = $oCell->createTextRun('Bachelor Aerospace Engineering,
Minor Educatie Wiskunde
Technische Universiteit Delft');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2008-2011');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

//Edu 3
$oRow = $oTableEducation->createRow();
$oRow->setHeight($rowHeight);
$oCell = $oRow->nextCell();
$oCell->getActiveParagraph()->setLineSpacing(120);
$oCellText = $oCell->createTextRun('VWO, Natuur & Techniek met muziek
Mgr. Frencken College Oosterhout');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2012-2008');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);


// Time to add extracurricular table
$oTableExtracurricular = $oSlide2->createTableShape(2);
$oTableExtracurricular->setHeight(200);
$oTableExtracurricular->setWidth(430);
$oTableExtracurricular->setOffsetX($xOffsetLeft);
$oTableExtracurricular->setOffsetY(500);

$oRow = $oTableExtracurricular->createRow();
$oRow->setHeight(12);
$oCell = $oRow->nextCell();
$oCell->setWidth(345);
$oCellText = $oCell->createTextRun('
NEVENACTIVITEITEN');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans SemiBold')
->setColor($red);
$oCell = $oRow->nextCell();
$oCell->setWidth(85);

//Iteration extracurriculair
// Extra 1
$oRow = $oTableExtracurricular->createRow();
$oRow->setHeight(10);
$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('Stagiair Technische Analyse DAF Trucks N.V.');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2013');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

// Extra 2
$oRow = $oTableExtracurricular->createRow();
$oRow->setHeight(10);
$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('Onderwijsassistent TU Delft (80 studenten)');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2012-2013');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

// Extra 3
$oRow = $oTableExtracurricular->createRow();
$oRow->setHeight(10);
$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2e-graads bevoegdheid docent Wiskunde');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

$oCell = $oRow->nextCell();
$oCellText = $oCell->createTextRun('2010-2011');
$oCellText->getFont()
->setSize(10)
->setName('Open Sans')
->setColor($darkGrey);

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


/* --------------------------
 *  Third+ Slide LEFT
 * --------------------------
 */

echo date('H:i:s') . ' Create third slide'.EOL;
$oSlide3 = $objPHPPresentation->createSlide();
$oSlide3->setSlideLayout($oSlideLayout);

// Add Background image
$oBackgroundImageSlide2 = new BackgroundImage();
$oBackgroundImageSlide2->setPath('./img/cv/cvBackgroundExperience.png');
$oSlide3->setBackground($oBackgroundImageSlide2);

// Add placeholder picture
$oPlaceholder = new Drawing\File();
$oPlaceholder->setPath('./img/cv/placeholder.png')
->setHeight(0)
->setWidth(0)
->setOffsetX(0)
->setOffsetY(0);
$oSlide3->addShape($oPlaceholder);

// Add Stevin Logo
$oStevinLogo = new Drawing\File();
$oStevinLogo->setPath('./img/cv/logostevin.png')
->setHeight(70)
->setOffsetX(35)
->setOffsetY(645);
$oSlide3->addShape($oStevinLogo);

// Add date range box
$oDateRange = $oSlide3->createRichTextShape()
->setHeight(35)
->setWidth(125)
->setOffsetX($xOffsetLeft+10)
->setOffsetY(45);
$oDateRange->getFill()
->setFillType(Fill::FILL_SOLID)
->setStartColor($red);
$oDateRange->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oDateRange->getActiveParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
$oDateRangeRun = $oDateRange->createTextRun('Aug 16 - Nov 16');
$oDateRangeRun->getFont()
->setBold(true)
->setName('Open Sans SemiBold')
->setSize(10)
->setColor($white);

// Add Role text
$oRoleText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetLeft)
->setOffsetY(100);
$oRoleText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oRoleTextRun = $oRoleText->createTextRun('PROJECTLEIDER IMPLEMENTATIE');
$oRoleTextRun->getFont()
->setCSpacing(0.5)
->setBold(true)
->setName('Open Sans SemiBold')
->setSize(10)
->setColor($darkGrey);

// Add company
$oCompanyText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetLeft)
->setOffsetY(125);
$oCompanyText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oCompanyTextRun = $oCompanyText->createTextRun('Ordina N.V.');
$oCompanyTextRun->getFont()
->setName('Open Sans')
->setSize(8)
->setColor($lightGrey);

// Add Executive
$oExecutiveText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetLeft)
->setOffsetY(145);
$oExecutiveText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oExecutiveTextRun = $oExecutiveText->createTextRun('OPDRACHTGEVER');
$oExecutiveTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add ExecutiveTextBox
$oExecutiveTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(90)
->setOffsetX($xOffsetLeft)
->setOffsetY(165);
$oExecutiveTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oExecutiveTextBox->getActiveParagraph()->setLineSpacing(120);
$oExecutiveTextBoxRun = $oExecutiveTextBox->createTextRun('Ordina is de grootste, onafhankelijke ICT-dienstverlener in de Benelux die streeft naar ICT die mensen echt verder helpt door samen met klanten duurzaam te innoveren.');
$oExecutiveTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);

// Add Tasks
$oTaskText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetLeft)
->setOffsetY(255);
$oTaskText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oTaskTextRun = $oTaskText->createTextRun('WERKZAAMHEDEN');
$oTaskTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add TaskTextBox
$oTaskTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(150)
->setOffsetX($xOffsetLeft)
->setOffsetY(275);
$oTaskTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oTaskTextBox->getActiveParagraph()->setLineSpacing(120);
$oTaskTextBoxRun = $oTaskTextBox->createTextRun('Bij de implementatie van een servicemanagementtool was ik projectleider testen en implementatie. Mijn taken waren het organiseren en faciliteren van (gebruikersacceptatie)testen, het registreren van bevindingen en het uitzetten van rework bij interne en externe leveranciers. In overleg met key users heb ik beslissingen genomen over het al dan niet live gaan met de geleverde functionaliteit.');
$oTaskTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);

// Add Results
$oResultsText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetLeft)
->setOffsetY(425);
$oResultsText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oResultsTextRun = $oResultsText->createTextRun('RESULTAAT');
$oResultsTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add ResultsTextBox
$oResultsTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(150)
->setOffsetX($xOffsetLeft)
->setOffsetY(445);
$oResultsTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oResultsTextBox->getActiveParagraph()->setLineSpacing(120);
$oResultsTextBoxRun = $oResultsTextBox->createTextRun('De (gebruikersacceptatie)testen zijn volledig doorlopen voor de verschillende onderdelen binnen het pakket en er was een duidelijk overzicht van de bevindingen richting de key users, stuurgroep en leveranciers. Rework naar aanleiding van bevindingen is correct en tijdig uitgevoerd. Er is een kwalitatief hoogstaande servicemanagement-tool gerealiseerd die organisatiebreed is uitgerold. Verzoeken als toegang, leaseaanvragen of autorisatie worden nu via de servicemanagementtool afgehandeld in plaats van via telefoon of e-mail. Hiermee is sturing op onderhanden werk, doorlooptijden en kwaliteit mogelijk gemaakt.');
$oResultsTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);



/* --------------------------
 *  Third+ Slide RIGHT
 * --------------------------
 */

// Add date range box
$oDateRange = $oSlide3->createRichTextShape()
->setHeight(35)
->setWidth(125)
->setOffsetX($xOffsetRight+10)
->setOffsetY(45);
$oDateRange->getFill()
->setFillType(Fill::FILL_SOLID)
->setStartColor($red);
$oDateRange->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oDateRange->getActiveParagraph()->getAlignment()->setVertical( Alignment::VERTICAL_CENTER );
$oDateRangeRun = $oDateRange->createTextRun('Aug 16 - Nov 16');
$oDateRangeRun->getFont()
->setBold(true)
->setName('Open Sans SemiBold')
->setSize(10)
->setColor($white);

// Add Role text
$oRoleText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetRight)
->setOffsetY(100);
$oRoleText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oRoleTextRun = $oRoleText->createTextRun('PROJECTLEIDER IMPLEMENTATIE');
$oRoleTextRun->getFont()
->setCSpacing(0.5)
->setBold(true)
->setName('Open Sans SemiBold')
->setSize(10)
->setColor($darkGrey);

// Add company
$oCompanyText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetRight)
->setOffsetY(125);
$oCompanyText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oCompanyTextRun = $oCompanyText->createTextRun('Ordina N.V.');
$oCompanyTextRun->getFont()
->setName('Open Sans')
->setSize(8)
->setColor($lightGrey);

// Add Executive
$oExecutiveText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetRight)
->setOffsetY(145);
$oExecutiveText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oExecutiveTextRun = $oExecutiveText->createTextRun('OPDRACHTGEVER');
$oExecutiveTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add ExecutiveTextBox
$oExecutiveTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(90)
->setOffsetX($xOffsetRight)
->setOffsetY(165);
$oExecutiveTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oExecutiveTextBox->getActiveParagraph()->setLineSpacing(120);
$oExecutiveTextBoxRun = $oExecutiveTextBox->createTextRun('Ordina is de grootste, onafhankelijke ICT-dienstverlener in de Benelux die streeft naar ICT die mensen echt verder helpt door samen met klanten duurzaam te innoveren.');
$oExecutiveTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);

// Add Tasks
$oTaskText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetRight)
->setOffsetY(255);
$oTaskText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oTaskTextRun = $oTaskText->createTextRun('WERKZAAMHEDEN');
$oTaskTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add TaskTextBox
$oTaskTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(150)
->setOffsetX($xOffsetRight)
->setOffsetY(275);
$oTaskTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oTaskTextBox->getActiveParagraph()->setLineSpacing(120);
$oTaskTextBoxRun = $oTaskTextBox->createTextRun('Bij de implementatie van een servicemanagementtool was ik projectleider testen en implementatie. Mijn taken waren het organiseren en faciliteren van (gebruikersacceptatie)testen, het registreren van bevindingen en het uitzetten van rework bij interne en externe leveranciers. In overleg met key users heb ik beslissingen genomen over het al dan niet live gaan met de geleverde functionaliteit.');
$oTaskTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);

// Add Results
$oResultsText = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(15)
->setOffsetX($xOffsetRight)
->setOffsetY(425);
$oResultsText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oResultsTextRun = $oResultsText->createTextRun('RESULTAAT');
$oResultsTextRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(10)
->setColor($red);

// Add ResultsTextBox
$oResultsTextBox = $oSlide3->createRichTextShape()
->setWidth(450)
->setHeight(150)
->setOffsetX($xOffsetRight)
->setOffsetY(445);
$oResultsTextBox->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oResultsTextBox->getActiveParagraph()->setLineSpacing(120);
$oResultsTextBoxRun = $oResultsTextBox->createTextRun('De (gebruikersacceptatie)testen zijn volledig doorlopen voor de verschillende onderdelen binnen het pakket en er was een duidelijk overzicht van de bevindingen richting de key users, stuurgroep en leveranciers. Rework naar aanleiding van bevindingen is correct en tijdig uitgevoerd. Er is een kwalitatief hoogstaande servicemanagement-tool gerealiseerd die organisatiebreed is uitgerold. Verzoeken als toegang, leaseaanvragen of autorisatie worden nu via de servicemanagementtool afgehandeld in plaats van via telefoon of e-mail. Hiermee is sturing op onderhanden werk, doorlooptijden en kwaliteit mogelijk gemaakt.');
$oResultsTextBoxRun->getFont()
->setName('Open Sans SemiBold')
->setCSpacing(0.5)
->setSize(9)
->setColor($darkGrey);

/* --------------------------
 *  Values slide
 * --------------------------
 */

echo date('H:i:s') . ' Create values slide'.EOL;
$oSlideWaarden = $objPHPPresentation->createSlide();
$oSlideWaarden->setSlideLayout($oSlideLayout);

// Add Background image
$oBackgroundImageSlide = new BackgroundImage();
$oBackgroundImageSlide->setPath('./img/cv/cvBackgroundWaarden.jpg');
$oSlideWaarden->setBackground($oBackgroundImageSlide);

// Add placeholder picture
$oPlaceholder = new Drawing\File();
$oPlaceholder->setPath('./img/cv/placeholder.png')
->setHeight(0)
->setWidth(0)
->setOffsetX(0)
->setOffsetY(0);
$oSlideWaarden->addShape($oPlaceholder);

/* --------------------------
 *  Final slide
 * --------------------------
 */

echo date('H:i:s') . ' Create final slide'.EOL;
$oSlideEinde = $objPHPPresentation->createSlide();
$oSlideEinde->setSlideLayout($oSlideLayout);

// Add Background image
$oBackgroundImageSlide = new BackgroundImage();
$oBackgroundImageSlide->setPath('./img/cv/cvBackgroundEinde.jpg');
$oSlideEinde->setBackground($oBackgroundImageSlide);

// Add placeholder picture
$oPlaceholder = new Drawing\File();
$oPlaceholder->setPath('./img/cv/placeholder.png')
->setHeight(0)
->setWidth(0)
->setOffsetX(0)
->setOffsetY(0);
$oSlideEinde->addShape($oPlaceholder);

/* --------------------------
 *  Output
 * --------------------------
 */
echo date('H:i:s') . ' Export'.EOL;
$oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . "/out/CVJVO.pptx");
$oWriterODP = IOFactory::createWriter($objPHPPresentation, 'ODPresentation');
$oWriterODP->save(__DIR__ . "/out/CVJVO.odp");

// Success!
echo date('H:i:s') . ' Success!'.EOL;




?>