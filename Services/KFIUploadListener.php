<?php

namespace KFI\UploadBundle\Services;

use Doctrine\ORM\Event\LifecycleEventArgs;
use KFI\UploadBundle\Entity\Upload;

class KFIUploadListener
{
    protected $manager;

    public function __construct(KFIUploadManager $manager)
    {
        $this->manager = $manager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity        = $args->getEntity();
        if (!($entity instanceof Upload))
            return;

        $entity->setType(Mime::getType($entity->getPath()));
        $entity->setMimeType(Mime::getMimeType($entity->getPath()));
        $title = $entity->getTitle();
        if(empty($title))
            $entity->setTitle(basename($entity->getPath()));
        if (strpos($entity->getPath(), '/temp/') === 0) {
            $baseFolder = $this->getBase();
            $tempPath   = $baseFolder . $entity->getPath();
            $filename   = basename($tempPath);

            $destFolder     = '/'.strtolower($entity->getType()) . '/' . date("Y/m/d/");
            $fullDestFolder = $baseFolder . $destFolder;
            if (!is_dir($fullDestFolder)) {
                mkdir($fullDestFolder, 0777, true);
            }
            $filename  = File::getUniqueFilename(
                $fullDestFolder,
                $filename
            );
            $finalPath = $fullDestFolder . $filename;
            if (file_exists($tempPath)) {
                if (rename($tempPath, $finalPath)) {
                    $entity->setPath($destFolder . $filename);
                    $this->removeThumbnails($entity);
                }
            } else {
                //usually when you try to refresh the page...
                throw new \Exception('uploaded file don\'t exists');
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!($entity instanceof Upload))
            return;

        $this->removeThumbnails($entity);
        unlink($this->getBase().$entity->getPath());
    }

    private function removeThumbnails(Upload $entity){
        $fullPath = $this->getBase().$entity->getPath();
        foreach($this->getThumbsByFile($fullPath) as $thumbPath)
            unlink($thumbPath);
    }

    private function getThumbsByFile($url) {
        $dir = dirname($url);
        $filename = basename($url);
        $results = array();

        // create a handler for the directory
        $handler = opendir($dir);

        // open directory and walk through the filenames
        while ($file = readdir($handler)) {
            if ($file != "." && $file != ".." &&
                strpos($file, $filename)!== false &&
                $file != $filename
            ) {
                $results[] = $file;
            }
        }
        // tidy up: close the handler
        closedir($handler);
        // done!
        return $results;
    }

    protected function getBase(){
        return $this->manager->getLocalPath();
    }
}
