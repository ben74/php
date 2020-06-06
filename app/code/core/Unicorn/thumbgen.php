<?php
namespace Unicorn;

class thumbgen extends \baseObject
{
    static function main($params)
    {
        #thumbgen(compact('filename','target','h','w'));
        global $debug;
        $ts = 2;
        $quality = 70;#jpeg
        $pngq = 9; //0 : no compression, 9 :best
        $owidth = $oheight = $filename = $h = $w = $ext = $posy = $posx = $srcx = $srcy = null;

        #if(c('CLI'))print_r($params);
        extract($params);
        $s = '#';
        if (strpos($filename, $s)) {
            $filename = explode($s, $filename);
            $filename = reset($filename);
        }
        if (!is_file($filename)) {
            return "!not file : $filename";
        }
        $info = getimagesize($filename);
        if (!$info) {
            return '!image error - no mime';
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $ext = 'jpg';
                break;
            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $ext = 'png';
                $quality = $pngq;
                break;
            case 'image/webp':
                $image_create_func = 'imagecreatefromwebp';
                $image_save_func = 'imagewebp';
                $ext = 'webp';
                break;                
            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $ext = 'gif';
                break;
            default:
                return '!Unknown image type.';
        }

        $img = $image_create_func($filename);
        list($cuwidth, $cuheight) = getimagesize($filename);
        $oheight = $cuheight;
        $owidth = $cuwidth;
        $ratio = $cuwidth / $cuheight;
        if ($h and !$w) {
            $w = $h * $ratio;
        }
        if ($w and !$h) {
            $h = $w / $ratio;
        }
        if($w > $cuwidth){$h=ceil($cuwidth*$h/$w);$w=$cuwidth;}#do not enlarge
        $width = $w;
        $height = $h;
        $tmp = imagecreatetruecolor($width, $height);
        $posx = $posy = 0;

        if (isset($cropping)) {
            if (isset($resize)) { #cropped from middle
                if ($ratio >= 1) {
                    $srcy = 0;
                    $srcx = ($cuwidth - $cuheight) / 2;
                    $cuwidth = $cuheight;
                } else {
                    $srcx = 0;
                    $srcy = ($cuheight - $cuwidth) / 2;
                    $cuheight = $cuwidth;
                }
            } else {
                $srcx = ($cuwidth - $width) / 2;
                $srcy = ($cuheight - $height) / 2;
                $cuwidth = $width;
                $cuheight = $height;
            }
            #print_r(compact('ratio','posx','posy','srcx','srcy','width','height','cuwidth','cuheight'));
            #imagecopyresampled($tmp, $img, $posx, $posy, $srcx, $srcy, $width, $height, $cuwidth, $cuheight);$image_save_func($tmp, $target, $quality);
        } else {
            if (isset($vertical_center) or isset($horizontal_center)) {
                if (isset($background_color)) {
                    $hex2rgb = $this->hex2rgb2($background_color);
                    $color = imagecolorallocate($tmp, $hex2rgb[0], $hex2rgb[1], $hex2rgb[2]); //filled in white
                    imagefilledrectangle($tmp, 0, 0, $width, $height, $color);
                }
                if (isset($vertical_center)) {
                    $height = ($cuheight / $cuwidth) * $width;
                }
                if (isset($horizontal_center)) {
                    $width = ($cuwidth / $cuheight) * $height;
                }
                /** ne pas dépasser ni les dimensions spécifiées, ni celle de l'image source -> cumul des deux centrages */
                $height = ($height > $cuheight) ? $cuheight : (($height > $oheight) ? $oheight : $height);
                $width = ($width > $cuwidth) ? $cuwidth : (($width > $owidth) ? $owidth : $width);
            }
            /** sinon la déformation est explicite*/
        }

        if (in_array($ext,['png','webp'])) {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            /**rempli de la couleur transparente le fond puis*/
            imagefilledrectangle($tmp, 0, 0, $width, $height, $transparent);
#imagecopyresampled($tmp, $img, $posx, $posy, $srcx, $srcy, $width, $height,$cuwidth, $cuheight);
        }

        imagecopyresampled($tmp, $img, $posx, $posy, $srcx, $srcy, $width, $height, $cuwidth, $cuheight);

        if (strpos($target, 'thumb/') and 0) { #salomon-retry until less than 30% of the thumb is white
            $count = getPixelCountByColor($tmp, 16777215);
            while (($count[0] / $count[1]) > 0.3) {
                $w *= 2;
                $h *= 2;
                $cuwidth *= 2;
                $cuheight *= 2; #417x1000px
                $srcx = ($owidth - $w) / 2;
                $srcy = ($oheight - $h) / 2;
                if ($srcx + $cuwidth > $owidth or $srcy + $cuheight > $oheight) {
                    break;
                }
                imagecopyresampled($tmp, $img, $posx, $posy, $srcx, $srcy, $width, $height, $cuwidth, $cuheight);
                #dst,src,dtsx,dsty,srcx,srcy,destw,desth,srcw,srch
                $count = getPixelCountByColor($tmp, 16777215);
            }
            #kill([__line__]+compact('filename','target','w','h','posx','posy','srcx','srcy','width','height','cuwidth','cuheight'));
        }

        if (isset($grayscale)) {
            imagefilter($tmp, IMG_FILTER_GRAYSCALE);
        }

        if (!isset($target)) {
            $image_save_func($tmp);
            imagedestroy($tmp);
            return "!no target";
        }
        #makereps($target);
        $success = 1;
        try {
            $success = @touch($target);
            #if(c('CLI'))echo" ..try to touch target";
        } catch (Exception $e) {
            $success = "#no writable:$target";
            if (c('J9')) {
                $success .= '!' . $e->getMessage();
            }
            return $success;
        }
        if (!$success) {
            #if(c('CLI'))echo" .. not a success";
            return;
        }

        if ($ext == 'jpg') {#progressive
            imageinterlace($tmp, true);
        }
        
        #can't write this file ..
        $image_save_func($tmp, $target, $quality); # . $ext
        #else $success='!target not writable';
        #touch($target,$ts);#resized
        imagedestroy($tmp);
        return $success; /*1*/
    }
} #fun::generateThumbnail()

function thumbgen($params)
{
    thumbgen::main($params);
}

/**
 * get dimensions from thumbnail final url
 * list($w,$h)=whFromTnPath($x);
 * @param $x
 * @return array
 */
function whFromTnPath($x)
{
    $w = $h = null;
    if (preg_match('~tn-([0-9]+)-([0-9]+)~', $x, $m) and $m[1]) {
        $h = $m[2];
        $w = $m[1];
    } #$filename='tn-$w-$h-';
    elseif (preg_match('~tn-h([0-9]+)~', $x, $m) and $m[1]) {
        $h = $m[1];
    } elseif (preg_match('~tn-w([0-9]+)~', $x, $m) and $m[1]) {
        $w = $m[1];
    } elseif (false and 'oldWay' and preg_match('~tn-([0-9]+)~', $x, $m) and $m[1]) {
        $w = $m[1];
        $h = $m[2];
    }
    return [$w,$h];#compact('w', 'h'); ==> needs extract not list
}


#$ cu https://aws2.127.0.0.1.xip.io/a/tn/tn-_w400-_h200-DSC00456.JPG
function thumbPath($filename, $w = 0, $h = 0)
{
    if(!$w && !$h){
        return $filename;
    }#not a thumb then
    $target=explode('.',$filename);$ext=array_pop($target);$target=implode('.',$target);
    $target=explode('/',$target);$end=array_pop($target);$target=implode('/',$target).'/tn-';#end filename
    if($w){$target.='-w'.$w;}
    if($h){$target.='-h'.$h;}
    $target.='-'.$end.'.'.$ext;
    $target=preg_replace('~-+~','-',$target);
    return $target;
}
#cu https://aws2.127.0.0.1.xip.io/a/tn/tn-_-w400_-h200-DSC00456.JPG
function thumb2params($filename){
    $w=$h=0;
if('newer'){    
    preg_match('~_-w([0-9]+)-?~', $filename, $m);
    if ($m && $m[1] && (int)$m[1]) {
        $filename = str_replace($m[0], '', $filename);
        $w = (int)$m[1];
    }
    preg_match('~_-h([0-9]+)-?~', $filename, $m);
    if ($m && $m[1] && (int)$m[1]) {
        $filename = str_replace($m[0], '', $filename);
        $h = (int)$m[1];
    }
}    
if(!$w and !$h and 'old formats'){
    preg_match('~-h([0-9]+)-?~', $filename, $m);
    if ($m && $m[1] && (int)$m[1]) {
        $filename = str_replace('h'.$m[1].'-', '', $filename);#strip out
        $h = (int)$m[1];
    }
    preg_match('~-w([0-9]+)-?~', $filename, $m);
    if ($m && $m[1] && (int)$m[1]) {
        $filename = str_replace('w'.$m[1].'-', '', $filename);#strip out
        $w = (int)$m[1];
    }
}
    #$filename=preg_replace('~/tn-+~','/',$filename);
    $filename=preg_replace('~tn-+~','',$filename);
    return compact('filename','w','h');
}


