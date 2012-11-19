<?php
namespace KFI\UploadBundle\Services;

use Gedmo\Sluggable\Util\Urlizer;

class File
{
    static public function getUniqueFilename($dir, $filename)
    {
        $filename = strtolower($filename);
        // separate the filename into a name and extension
        $info = pathinfo($filename);
        $ext  = $info['extension'];


        $number = '';

        if (empty($ext)) {
            $ext = '';
        } else {
            $ext = strtolower(".$ext");
        }

        $filename = str_replace($ext, '', $filename);
        $filename = str_replace('%', '', Urlizer::urlize($filename)) . $ext;

        while (file_exists($dir . "/$filename")) {
            if ('' == "$number$ext") {
                $filename = $filename . ++$number . $ext;
            } else {
                $filename = str_replace("$number$ext", ++$number . $ext, $filename);
            }
        }

        return $filename;
    }
}
