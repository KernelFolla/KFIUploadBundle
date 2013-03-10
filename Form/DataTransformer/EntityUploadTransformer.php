<?php

namespace KFI\UploadBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

use KFI\UploadBundle\Entity\EntityUpload;
use KFI\UploadBundle\Entity\Upload;

class EntityUploadTransformer implements DataTransformerInterface
{
    /** @var ObjectRepository */
    private $repo;

    public function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * transform array of objects in an array of id
     *
     * @param  object[] $collection
     * @return int[]
     */
    public function transform($collection)
    {
        $ret = array();
        if (!$collection) {
            return $ret;
        }
        /** @var $item EntityUpload */
        foreach ($collection as $item) {
            $ret[$item->getId()] = $item->getUpload();
        }

        return $ret;
    }

    /**
     * transform an array of id in array of object
     *
     * @param mixed $items
     * @return array|mixed
     * @throws TransformationFailedException
     */
    public function reverseTransform($items)
    {
        $ret = array();
        if (!$items) {
            return $ret;
        }
        $i = 0;
        foreach ($items as $item) {
            $entity = isset($item['id']) ?
                $this->bindEntityByID($item['id'], $item['title'])
                : $this->bindNewEntity($item);
            $entity->setPosition($i);
            $ret[] = $entity;
            $i++;
        }

        return $ret;
    }

    protected function bindEntityByID($id, $title)
    {
        /** @var $entity EntityUpload */
        $entity = $this->repo->find($id);
        if (!isset($entity)) {
            throw new TransformationFailedException(sprintf(
                'l\'oggetto con id %s non esiste!',
                $id
            ));
        }
        $entity->getUpload()->setTitle($title);

        return $entity;
    }

    /**
     * @param array $item
     * @return EntityUpload
     */
    protected function bindNewEntity($item)
    {
        $upload = new Upload();
        $upload->setPath($item['url']);
        $upload->setType($item['type']);
        $upload->setRemote($item['remote']);

        $className = $this->repo->getClassName();
        /** @var $ret EntityUpload */
        $ret = new $className();
        $ret->setUpload($upload);

        return $ret;
    }
}