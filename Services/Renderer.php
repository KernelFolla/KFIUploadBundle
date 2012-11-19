<?php

namespace KFI\UploadBundle\Services;

use KFI\UploadBundle\Entity\Upload;

class Renderer
{
    protected static $modes = array(
        'webpath'        => 'getWebPath',
        'fixedthumbpath' => 'getFixedThumbPath',
        'thumbpath'      => 'getThumbPath',
        'croppath'       => 'getCropPath',
        'fixedthumbimg'  => 'getFixedThumbImg',
        'thumbimg'       => 'getThumbImg',
        'cropimg'        => 'getCropImg',
        'img'            => 'getImg',
        'link'           => 'getLink'
    );

    /** @var KFIUploadManager */
    protected $manager;
    /** @var Upload */
    protected $upload;

    protected $width;
    protected $height;
    protected $mode;

    protected $extra;

    public function __construct(KFIUploadManager $manager, Upload $upload)
    {
        $this->manager = $manager;
        $this->upload  = $upload;
    }

    protected function getClone()
    {
        return new Renderer($this->manager, $this->upload);
    }

    public function getAlt()
    {
        return htmlentities($this->upload->getTitle());
    }

    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getWebPath()
    {
        return $this->manager->getWebPath() . $this->upload->getPath();
    }

    public function getLocalPath()
    {
        $u = $this->upload;

        return $this->manager->getLocalPath() . $u->getPath();
    }

    public function getImg($extra = null)
    {
        $extra = isset($extra) ? $extra : $this->extra;

        return sprintf(
            '<img src="%s" alt="%s" %s />',
            $this->getWebPath(),
            $this->getAlt(),
            $extra
        );
    }

    public function getLink($text = null, $extra = null, $alt = null)
    {
        if (empty($text)) {
            $text = $this->upload->getTitle();
        }
        if (!$alt) {
            $alt = $this->getAlt();
        }

        return sprintf(
            '<a title="%s" href="%s" %s>%s</a>',
            $alt,
            $this->getWebPath(),
            $extra,
            $text
        );
    }

    public function getThumbImg($extra)
    {
        $extra = isset($extra) ? $extra : $this->extra;

        return sprintf(
            '<img src="%s" alt="%s" %s />',
            $this->getThumbPath(),
            $this->getAlt(),
            $extra
        );
    }

    public function getCropImg($extra = null)
    {
        return sprintf(
            '<img src="%s" width="%s" height="%s" alt="%s" %s />',
            $this->getCropPath(),
            $this->getWidth(),
            $this->getHeight(),
            $this->getAlt(),
            $extra
        );
    }

    public function getFixedThumbImg($extra = null, $align = null, $valign = null, $color = null)
    {
        return sprintf(
            '<img src="%s" width="%s" height="%s" alt="%s" %s />',
            $this->getFixedThumbPath($align, $valign, $color),
            $this->getWidth(),
            $this->getHeight(),
            $this->getAlt(),
            $extra
        );
    }

    public function getThumbPath()
    {
        $u = $this->upload;

        $add = '/thumb/w' . $this->width . '/h' . $this->height;
        Images::thumbnail(
            $this->getLocalPath() . $u->getPath(),
            $this->width,
            $this->height,
            $this->getLocalPath() . $add . $u->getPath()
        );

        return $this->getWebPath() . $add . $u->getPath();
    }

    public function getCropPath()
    {
        $u = $this->upload;

        $add = '/crop/w' . $this->width . '/h' . $this->height;
        Images::crop(
            $this->getLocalPath(),
            $this->width,
            $this->height,
            $this->manager->getLocalPath() . $add . $u->getPath()
        );

        return $this->manager->getWebPath() . $add . $u->getPath();
    }

    public function getFixedThumbPath($align = null, $valign = null, $color = null)
    {
        $u = $this->upload;

        $add = '/fthumb/w' . $this->width
            . '/h' . $this->height
            . '/a' . $align
            . '/v' . $valign
            . '/c' . $color;

        Images::fixedThumb(
            $this->getLocalPath(),
            $this->width,
            $this->height,
            $align, $valign, $color,
            $this->manager->getLocalPath() . $add . $u->getPath()
        );

        return $this->manager->getWebPath() . $add . $u->getPath();
    }


    public function __call($method, $args)
    {
        throw new \Exception("unknown method [$method]");
    }
}
