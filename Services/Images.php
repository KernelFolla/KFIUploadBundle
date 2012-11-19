<?php

namespace KFI\UploadBundle\Services;

use \Exception;

class Images
{

    const ALIGN_LEFT   = 0;
    const ALIGN_RIGHT  = 1;
    const ALIGN_CENTER = 2;
    const ALIGN_TOP    = 3;
    const ALIGN_BOTTOM = 4;
    const ALIGN_MIDDLE = 5;


    /**
     * Converte una stringa di colore esadecimale in una terna RGB
     *
     * @param $color : colore in input in formato esadecimale
     * @author Domenico Renna
     */
    public static function hex2rgb($color)
    {
        if ($color[0] == '#')
                {
                    $color = substr($color, 1);
                }

        if (strlen($color) == 6)
            list($r, $g, $b) = array(
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]
            );
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]); else
            return false;

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return array($r, $g, $b);
    }

    public static function fixedThumb(
        $filename,
        $w,
        $h,
        $align = null,
        $valign = null,
        $color = null,
        $saveto = null,
        $clear = false
    ) {
        if (!isset($color))
            $color = "#FFFFFF";
        if (!isset($valign))
            $valign = self::ALIGN_TOP;
        if (!isset($align))
            $align = self::ALIGN_LEFT;

        if (!isset($filename))
            return false;
        if (!isset($saveto))
            $saveto = "$filename.fix.$w.$h.jpg";
        if (defined('LOCALE'))
            return basename($saveto);
        if ($clear || defined('CLEAR_THUMBS'))
            unlink($saveto);
        if (!file_exists($saveto)) {
            if (!file_exists($filename)) {
                return null;
            }
            self::checkDir($saveto);

            // NUOVE DIMENSIONI
            list($width, $height, $mime) = getimagesize($filename);

            $ratio1 = $width / $w;
            $ratio2 = $height / $h;
            if (($ratio1 > 1) || ($ratio2 > 1)) {
                if ($ratio1 > $ratio2) {
                    $new_width  = $w;
                    $new_height = intval($height / $ratio1);
                } else {
                    $new_height = $h;
                    $new_width  = intval($width / $ratio2);
                }
            } else {
                $new_width  = $width;
                $new_height = $height;
            }

            $image_p = imagecreatetruecolor($w, $h);
            $rgb     = self::hex2rgb($color);
            $color   = imageColorAllocate($image_p, $rgb[0], $rgb[1], $rgb[2]);
            ImageFilledRectangle($image_p, 0, 0, $w, $h, $color);

            $dest_y = ($h - $new_height) / 2;
            $dest_x = ($w - $new_width) / 2;
            if ($mime == 1)
                $image = imagecreatefromgif($filename);
            else if ($mime == 2)
                $image = imagecreatefromjpeg($filename);
            else if ($mime == 3)
                $image = imagecreatefrompng($filename);
            else
                throw new Exception('try to resize a bad file ' . $filename);

            if ($mime == 3 || $mime == 1) { // png
                imagefill($image, 0, 0, $color);
                imagefill($image, 0, $height - 1, $color);
                imagefill($image, $width - 1, 0, $color);
                imagefill($image, $width - 1, $height - 1, $color);
            }

            imagecopyresampled($image_p, $image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_p, $saveto, 100);
        }

        return basename($saveto);
    }


    public static function thumbnail($filename, $w, $h, $saveto = null, $clear = false)
    {
        if (!isset($filename))
            return 'empty';
        if (!isset($saveto))
            $saveto = "$filename.$w.$h.jpg";

        if (defined('LOCALE'))
            return basename($saveto);
        if ($clear)
            unlink($saveto);
        if (!file_exists($saveto)) {
            if (!file_exists($filename))
                return 'notexists_' . $filename;
            self::checkDir($saveto);

            list($width, $height, $mime) = getimagesize($filename);
            if ($width < $w)
                return basename($filename);


            $ratio1 = $width / $w;
            $ratio2 = $height / $h;
            if ($ratio1 > $ratio2) {
                $new_width  = $w;
                $new_height = intval($height / $ratio1);
            } else {
                $new_height = $h;
                $new_width  = intval($width / $ratio2);
            }
            $image_p = imagecreatetruecolor($new_width, $new_height);
            if ($mime == 3) { // png
                $background = imagecolorallocate($image_p, 255, 255, 255);
                imagecolortransparent($image_p, $background);
                imagealphablending($image_p, false);
                imagesavealpha($image_p, true);
            } elseif ($mime == 1) {
                $background = imagecolorallocate($image_p, 255, 255, 255);
                imagecolortransparent($image_p, $background);
            }

            $dest_y = 0;
            $dest_x = 0; //-$h/2;
            static $first = 0;
            if ($mime == 1)
                $image = imagecreatefromgif($filename);
            else if ($mime == 2)
                $image = imagecreatefromjpeg($filename);
            else if ($mime == 3)
                $image = imagecreatefrompng($filename);
            else
                throw new Exception('try to resize a bad file ' . $filename);

            imagecopyresampled($image_p, $image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_p, $saveto, 100);
        }

        return basename($saveto);
    }

    public static function crop($filename, $w, $h, $saveto = null, $clear = false)
    {
        //KF controllo che non esista il file
        if (!isset($saveto))
            $saveto = "$filename.$w.$h.c.jpg";
        //eturn basename($saveto);
        if (defined('LOCALE'))
            return basename($saveto);
        if ($clear && file_exists($saveto))
            unlink($saveto);
        if (!file_exists($saveto)) {
            if (!file_exists($filename))
                return null;
            self::checkDir($saveto);

            $image_p = imagecreatetruecolor($w, $h);
            // NUOVE DIMENSIONI
            list($width, $height, $mime) = getimagesize($filename);

            $dest_x = 0;
            $dest_y = 0;

            $ratio1 = $width / $w;
            $ratio2 = $height / $h;
            if ($ratio1 < $ratio2) {
                $new_width  = $w;
                $new_height = intval($height / $ratio1);

                if ($h > $new_height)
                    $dest_y = -($h - $new_height) / 3;
                else
                    $dest_y = -($new_height - $h) / 3;
            } else {
                $new_height = $h;
                $new_width  = intval($width / $ratio2);
                $dest_y     = 0;
                if ($w < $new_width)
                    $dest_x = -($new_width - $w) / 3;
                else
                    $dest_x = -($w - $new_width) / 3;
            }
            // RIDIMENSIONA
            if ($mime == 1)
                $image = imagecreatefromgif($filename);
            else if ($mime == 2)
                $image = imagecreatefromjpeg($filename);
            else if ($mime == 3)
                $image = imagecreatefrompng($filename);
            else
                throw new Exception('try to resize a bad file ' . $filename);
            imagecopyresampled($image_p, $image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $width, $height);
            //SALVA
            imagejpeg($image_p, $saveto, 75);
            //	}
        }

        return basename($saveto);
    }

    private static function checkDir($fullName)
    {
        $dir = dirname($fullName);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
