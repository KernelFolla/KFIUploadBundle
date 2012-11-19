<?php

namespace KFI\UploadBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

use KFI\UploadBundle\Entity\Upload;

class SingleUploadTransformer implements DataTransformerInterface
{
    /** @var ObjectRepository */
    private $repo;

    public static function bind(FormBuilderInterface $builder, ObjectRepository $repo)
    {
        $builder->addViewTransformer(new self($repo));
    }

    private function __construct(ObjectRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * create a fake array with one item
     *
     * @param mixed $item
     * @return array|mixed
     */
    public function transform($item)
    {
        return (!$item) ?
            array()
            : array($item);
    }

    /**
     * transform an array of id in array of object
     *
     * @param mixed $items
     * @return Upload
     * @throws TransformationFailedException
     */
    public function reverseTransform($items)
    {
        if (!$items) {
            return null;
        }
        $item   = array_pop($items);
        $ret = isset($item['id']) ?
            $this->bindEntityByID($item['id'], $item['title'])
            : $this->bindNewEntity($item);
        $ret->setTitle($item['title']);

        return $ret;
    }

    /**
     * @param $id
     * @return Upload
     * @throws TransformationFailedException
     */
    protected function bindEntityByID($id)
    {
        $ret = $this->repo->find($id);
        if (!isset($ret)) {
            throw new TransformationFailedException(sprintf(
                'l\'oggetto con id %s non esiste!',
                $id
            ));
        }

        return $ret;
    }

    /**
     * @param array $item
     * @return Upload
     */
    protected function bindNewEntity($item)
    {
        $ret = new Upload();
        $ret->setPath($item['url']);
        $ret->setType($item['type']);
        $ret->setRemote($item['remote']);

        return $ret;
    }
}