<?php

namespace KFI\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class EntityHasUploads
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
     * @return EntityHasUploads
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
        $u->setParentId($p->getId());
    }

    /**
     * Set upload
     *
     * @param Upload $upload
     * @return EntityHasUploads
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
}