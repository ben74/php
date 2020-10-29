<?php
ini_set('display_errors',1);
if(!class_exists('Imagick')){die('Imagick not installed');}
echo"imagick installed, testing librairies<hr>";

$image = new Imagick();
$image->newImage(1, 1, new ImagickPixel('#ffffff'));
$image->setImageFormat('png');
$pngData = $image->getImagesBlob();
echo strpos($pngData, "\x89PNG\r\n\x1a\n") === 0 ? 'Ok' : 'Failed'; 

$text = 'dynamic text rendering for gif creation';
$width = 1200;
$height = 100;
$output='imagick-test.gif';

try{
$pixel = new ImagickPixel('lightblue');
$arcArray = array(360);
$draw1 = new ImagickDraw();
$draw1->setFont('Arial');
$draw1->setFontSize( 120 );
$draw1->setGravity( Imagick::GRAVITY_WEST );

$draw2 = new ImagickDraw();
$draw2->setFont('Arial');
$draw2->setFontSize( 120 );
$draw2->setGravity( Imagick::GRAVITY_EAST );

$im1 = new Imagick();
$im1->newImage($width, $height, $pixel);
$im1->annotateImage($draw1, 0, 0, 0, $text);
$im1->setImageVirtualPixelMethod( Imagick::VIRTUALPIXELMETHOD_WHITE );
$im1->distortImage( Imagick::DISTORTION_ARC, $arcArray, false );

$im2 = new Imagick();
$im2->newImage($width, $height, $pixel);
$im2->setImageVirtualPixelMethod( Imagick::VIRTUALPIXELMETHOD_WHITE );
$im2->annotateImage($draw2, 0, 0, 0, $text);#
$im2->distortImage( Imagick::DISTORTION_ARC, $arcArray, false );

$frame = new Imagick();
$im2->setImageFormat('gif');
$frame->readImageBlob($im2->getImageBlob());
for ($i = 1; $i < 3; ++$i) {
  $frame->addImage(${'im'.$i});
  $frame->setImageDelay(100);
}
$frame->setImageDispose(2);
$frame->setImageIterations(0);

$frame->writeImages($output, true);

}catch(Exception $e){
        echo $e->getMessage();
}