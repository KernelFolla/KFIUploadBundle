<?php

namespace KFI\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use \DateTime;

/**
 * KFI\UploadBundle\Entity\Upload
 *
 * @ORM\Table(name="kfi_upload")
 * @ORM\Entity(repositoryClass="KFI\UploadBundle\Entity\UploadRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Upload
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var string $mimeType
     *
     * @ORM\Column(name="mimeType", type="string", length=20)
     */
    private $mimeType;

    /**
     * @var boolean $remote
     *
     * @ORM\Column(name="remote", type="boolean")
     */
    private $remote;

    /**
     * @var integer $parentId
     *
     * @ORM\Column(name="parentId", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string $parentClass
     *
     * @ORM\Column(name="parentClass", type="string", length=255, nullable=true)
     */
    private $parentClass;

    /**
     * @var integer $parentPosition
     *
     * @ORM\Column(name="parentPosition", type="integer", nullable=true)
     */
    private $parentPosition;

    /**
     * @var string $parentGroup
     *
     * @ORM\Column(name="parentGroup", type="string", length=255, nullable=true)
     */
    private $parentGroup;

    /**
     * @var DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->title          = '';
        $this->type           = '';
        $this->mimeType       = '';
        $this->parentClass    = '';
        $this->parentPosition = '';
        $this->parentGroup    = '';
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Upload
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Upload
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Upload
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Upload
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set remote
     *
     * @param boolean $remote
     * @return Upload
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;

        return $this;
    }

    /**
     * Get remote
     *
     * @return boolean
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Upload
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set parentClass
     *
     * @param string $parentClass
     * @return Upload
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;

        return $this;
    }

    /**
     * Get parentClass
     *
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * Set parentPosition
     *
     * @param integer $parentPosition
     * @return Upload
     */
    public function setParentPosition($parentPosition)
    {
        $this->parentPosition = $parentPosition;

        return $this;
    }

    /**
     * Get parentPosition
     *
     * @return integer
     */
    public function getParentPosition()
    {
        return $this->parentPosition;
    }

    /**
     * Set parentGroup
     *
     * @param string $parentGroup
     * @return Upload
     */
    public function setParentGroup($parentGroup)
    {
        $this->parentGroup = $parentGroup;

        return $this;
    }

    /**
     * Get parentGroup
     *
     * @return string
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Upload
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Upload
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}