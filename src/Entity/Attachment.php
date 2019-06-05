<?php

namespace Lle\AttachmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Attachment
 *
 * @ORM\Table(name="lle_attachment", indexes={
 *     @ORM\Index(name="attachment_type_idx", columns={"type"}),
 *     @ORM\Index(name="attachment_objclass_idx", columns={"object_id","object_class"}),
 *     @ORM\Index(name="attachment_objid_idx", columns={"object_id"}),
 *     @ORM\Index(name="attachment_class_idx", columns={"object_class"})
 * })
 * @ORM\Entity
 */
class Attachment
{
	use BlameableEntity;
	use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true, options={"default"="other"})
     */
    private $type = 'other';

    /**
     * @ORM\Column(name="path", type="string", length=512)
     */
    private $path;

    /**
     * name of the file
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=false)
     */
    private $filename;

    /**
     * file mime type
     * @var string
     *
     * @ORM\Column(name="mimetype", type="string", length=255, nullable=true)
     */
    private $mimetype;

    /**
     * @ORM\Column(name="size", type="decimal")
     */
    private $size;

    /**
     * @var int|null
     *
     * @ORM\Column(name="object_id", type="bigint", nullable=true)
     */
    private $objectId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="object_class", type="string", length=255, nullable=true)
     */
    private $objectClass;

    /**
     * @var string|null
     *
     * @ORM\Column(name="object_field", type="string", length=255, nullable=true)
     */
    private $objectField;


    public function upload(UploadedFile $media, $root, $id)
    {

    }



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Attachment
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Attachment
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return Attachment
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set type.
     *
     * @param string|null $type
     *
     * @return Attachment
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set objectId.
     *
     * @param int|null $objectId
     *
     * @return Attachment
     */
    public function setObjectId($objectId = null)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId.
     *
     * @return int|null
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectClass.
     *
     * @param string|null $objectClass
     *
     * @return Attachment
     */
    public function setObjectClass($objectClass = null)
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    /**
     * Get objectClass.
     *
     * @return string|null
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }
    /**
     * @return string|null
     */
    public function getObjectField(): ?string
    {
        return $this->objectField;
    }

    /**
     * @param string|null $objectField
     * @return Attachment
     */
    public function setObjectField(?string $objectField): Attachment
    {
        $this->objectField = $objectField;
        return $this;
    }
    
    /**
     * Get the value of filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @return  self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the value of size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of size
     *
     * @return  self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get file mime type
     *
     * @return  string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set file mime type
     *
     * @param  string  $mimetype  file mime type
     *
     * @return  self
     */
    public function setMimetype(string $mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }
}
