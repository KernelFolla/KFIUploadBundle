<?php

namespace KFI\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use KFI\UploadBundle\Services\File;
use KFI\UploadBundle\Services\KFIUploadManager;

class DefaultController extends Controller
{
    /**
     * @Route("/ajax/upload/addfile", name="kfi_upload_addfile")
     */
    public function addfileAction()
    {
        if (!$this->getRequest()->files->has('Filedata')) {
            throw new \Exception('no file submitted');
        }

        /** @var $manager KFIUploadManager */
        $manager = $this->get('kfi.upload.manager');

        /** @var $file UploadedFile */
        $file = $this->getRequest()->files->get('Filedata');
//            $uid = Users::isLogged() ? Users::current()->getID() : '0';
        $uid        = 0;
        $targetPath = '/temp/' . $uid;
        $fullPath   = $manager->getLocalPath() . $targetPath;

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $targetFile = File::getUniqueFileName(
            $fullPath,
            $file->getClientOriginalName()
        );

        $file->move($fullPath, $targetFile);
        $newPath  = $targetPath . '/' . $targetFile;
        $response = new JsonResponse(array(
            'html' => $manager->getIcon($newPath),
            'file' => $newPath
        ));

        return $response;
    }
}
