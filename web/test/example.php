<?php

// Include all necessary PHPPresentation files
require_once 'PHPPresentation/src/PhpPresentation/Autoloader.php';
\PhpOffice\PhpPresentation\Autoloader::register();

require_once 'PHPOffice/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Slide\Background\Image as BackgroundImage;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

// Start PPT
$objPHPPresentation = new PhpPresentation();

// Add Slide
$oSlide1 = $objPHPPresentation->getActiveSlide();
$oNameText = $oSlide1->createRichTextShape()
->setHeight(35)
->setWidth(535)
->setOffsetX(450)
->setOffsetY(110);
$oNameText->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
$oNameTextRun = $oNameText->createTextRun('MyText');
$oNameTextRun->getFont()
->setSize(16);

// Add another slide
$oSlide2 = $objPHPPresentation->createSlide();

// Image causes problems
$oImage = new Drawing\File();
$oImage->setPath('./image.png')
->setHeight(50)
->setWidth(50)
->setOffsetX(500)
->setOffsetY(300);
$oSlide2->addShape($oImage);

$oWriterPPTX = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
$oWriterPPTX->save(__DIR__ . "example.pptx");
$oWriterODP = IOFactory::createWriter($objPHPPresentation, 'ODPresentation');
$oWriterODP->save(__DIR__ . "example.odp");

?>