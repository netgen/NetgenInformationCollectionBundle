<?php

namespace Netgen\Bundle\InformationCollectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EzInfoCollection.
 *
 * @ORM\Table(name="ezinfocollection", indexes={@ORM\Index(name="ezinfocollection_co_id_created", columns={"contentobject_id", "created"})})
 * @ORM\Entity(repositoryClass="Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionRepository")
 */
class EzInfoCollection
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="contentobject_id", type="integer", nullable=false, options={"default"=0})
     */
    private $contentObjectId;

    /**
     * @var int
     *
     * @ORM\Column(name="created", type="integer", nullable=false, options={"default"=0})
     */
    private $created;

    /**
     * @var int
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=false, options={"default"=0})
     */
    private $creatorId;

    /**
     * @var int
     *
     * @ORM\Column(name="modified", type="integer", nullable=true, options={"default"=0})
     */
    private $modified;

    /**
     * @var string
     *
     * @ORM\Column(name="user_identifier", type="string", length=34, nullable=true)
     */
    private $userIdentifier;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->contentObjectId;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->contentObjectId = $contentObjectId;
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @param int $creatorId
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * @param int $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @param string $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * @return int
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }
}
