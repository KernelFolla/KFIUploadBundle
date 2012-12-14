<?php

namespace KFI\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


/**
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class EntityUpload
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     *  @ORM\OneToOne(targetEntity="KFI\UploadBundle\Entity\Upload", cascade={"persist"} )
     *  @ORM\JoinColumn(name="upload_id", nullable=false,
     *      referencedColumnName="id", onDelete="CASCADE")
     */
    private $upload;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return EntityUpload
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $p = $this->getParent();
        $u = $this->getUpload();
        $u->setParentClass(get_class($p));
        if(method_exists($p, 'getId'))
            $u->setParentId($p->getId());
//        else{
//            throw new Exception('can\t get id');
//        }
    }

    /**
     * Set upload
     *
     * @param Upload $upload
     * @return EntityUpload
     */
    public function setUpload(Upload $upload)
    {
        $this->upload = $upload;

        return $this;
    }

    /**
     * Get upload
     *
     * @return Upload
     */
    public function getUpload()
    {
        return $this->upload;
    }

    abstract public function setParent($parent);
    abstract public function getParent();

    public function pushOnCollection($parent, Collection $collection){
        $this->setParent($parent);
        $this->setPosition($collection->count());
        $collection->add($this);
    }
}