<?php

namespace KFI\UploadBundle\Services;

class UploadTwigExtension extends \Twig_Extension
{
    protected $manager;

    public function __construct(KFIUploadManager $manager)
    {
        $this->manager = $manager;
    }

    public function getFilters()
    {
        return array(
            'kfi_upload' => new \Twig_Filter_Method($this, 'filterUpload'),
        );
    }

    public function filterUpload($entity, $mode = 'img', $width = 0, $height = 0, $extra=null)
    {
        $renderer = $this->manager->getRenderer($entity);
        $call = 'get'.$mode;
        return $renderer
            ->setWidth($width)
            ->setHeight($height)
            ->$call($extra);
    }

    public function getName(){
        return 'kfi_upload_extension';
    }
}