<?php

namespace KFI\UploadBundle\Services;

use KFI\UploadBundle\Entity\EntityHasUploads;

class KFIUploadManager
{
    const THUMB_FORMAT = '<a href="%s" target="blank"><div class="uploadifyIcon" style="background: url(%s) white no-repeat center center"></div></a>';

    const IMAGE = 'image';
    const DOC   = 'doc';
    const VIDEO = 'video';

    protected $localPath;
    protected $webPath;

    public function __construct($options)
    {
        $this->localPath = $options['local_base_path'];
        $this->webPath   = $options['web_base_path'];
    }

    public function getLocalPath()
    {
        return $this->localPath;
    }

    public function getWebPath()
    {
        return $this->webPath;
    }

    public function getIcon($url, $remote = 0, $type = null)
    {
        if ($remote) {
            $link = $url;

//            switch($type) {
//                case 'image':
            //$thumb = $this->baseUrl."/".$dir."/".Images::thumbnail($url,64,64);
            return "<a href=\"$url\" target=\"blank\"'><div class=\"uploadifyIcon\" style='text-align: center; margin:  0 auto;'><img src='$url' style='max-height: 64px; max-width: 64px'/></div></a>";
//                    break;
//                    break;
//                case 'youtube':
//                    $icon = LIBURL."/KFCMS/includes/icons/youtube.png";
//                    break;
//                default:
//                    $icon = LIBURL."/KFCMS/includes/icons/remote.png";
//            }
        } else {
            $local = $this->getLocalPath();
            $web   = $this->getWebPath();
//            if(empty($url) || (!is_file($local.$url)) || (!file_exists($local.$url)))
//                return null;
            $dir  = dirname($url);
            $link = $web . "/" . $url;

//            $info = Mime::checkFiletype($url);
//            switch($info['ext']) {
//                case 'pdf':
//                    $icon = LIBURL."/KFCMS/includes/icons/pdf.png";
//                    break;
//                case 'doc':
//                case 'docx':
//                    $icon = LIBURL."/KFCMS/includes/icons/doc.png";
//                    break;
//                default:
            $icon = $web . "/" . $dir . "/" . Images::thumbnail($local . $url, 64, 64);
//            }
        }

        return sprintf(self::THUMB_FORMAT, $link, $icon);
    }

    /**
     * @param $upload
     * @return Renderer
     */
    public function getRenderer($upload){
        if($upload instanceof EntityHasUploads)
            $upload = $upload->getUpload();
        return new Renderer($this, $upload);
    }
}
