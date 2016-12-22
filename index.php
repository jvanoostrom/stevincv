<?php

require_once 'PHPPresentation/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();

require_once 'PHPOffice/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide\Background\Image;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\Drawing;

echo date('H:i:s') . ' Create new PHPPresentation object'.EOL;
$objPHPPresentation = new PhpPresentation();
$oMasterSlide = $objPHPPresentation->getAllMasterSlides()[0];
$oSlideLayout = $oMasterSlide->getAllSlideLayouts()[0];

echo date('H:i:s') . ' Set slide size'.EOL;
$layout = $objPHPPresentation->getLayout();
$layout->setDocumentLayout(DocumentLayout::LAYOUT_A4, true);


// Powerpoint information
echo date('H:i:s') . ' Set properties'.EOL;
$properties = $objPHPPresentation->getProperties();
$properties->setCreator('Jeffrey van Oostrom');
$properties->setCompany('Stevin Technology Consultants');
$properties->setTitle('CV Jeffrey van Oostrom');
$properties->setDescription('Curriculum Vitae');
$properties->setLastModifiedBy('Jeffrey van Oostrom');
$properties->setCreated(mktime(0, 0, 0, 12, 20, 2016));
$properties->setModified(mktime(0, 0, 0, 12, 20, 2016));
$properties->setSubject('CV Jeffrey van Oostrom');
$properties->setKeywords('cv, jeffrey, van, oostrom');


// Create slide
echo date('H:i:s') . ' Create first slide'.EOL;
$currentSlide = $objPHPPresentation->getActiveSlide();
$currentSlide->setSlideLayout($oSlideLayout);

// Background image
echo date('H:i:s') . ' Add background image'.EOL;
$oBkgImage = new Image();
$oBkgImage->setPath('./img/cv/cvbackground.jpg');
$currentSlide->setBackground($oBkgImage);

// Create a shape (drawing)
echo date('H:i:s') . ' Add custom image'.EOL;
$shape1 = $currentSlide->createDrawingShape();
$shape1->setPath('./img/cv/foto.png');
$shape1->setHeight(200)
->setOffsetX(10)
->setOffsetY(50);

// Create a shape (drawing)
echo date('H:i:s') . ' Add custom image'.EOL;
$oProfileImg = new Drawing\File();
$oProfileImg->setPath('./img/cv/foto.png')
->setHeight(36)
->setOffsetX(10)
->setOffsetY(50);
$currentSlide->addShape($oProfileImg);

// Create a shape (text)
echo date('H:i:s') . ' Add text'.EOL;
$shape = $currentSlide->createRichTextShape()
	->setHeight(300)
	->setWidth(600)
	->setOffsetX(170)
	->setOffsetY(180);
$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
$textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
$textRun->getFont()->setBold(true)
	->setSize(60)
	->setColor( new Color( 'FFE06B20' ) );

echo date('H:i:s') . ' Export'.EOL;
$oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . "/CVJVO.pptx");
$oWriterODP = IOFactory::createWriter($objPHPPresentation, 'ODPresentation');
$oWriterODP->save(__DIR__ . "/CVJVO.odp");

echo "Succes!";

?>