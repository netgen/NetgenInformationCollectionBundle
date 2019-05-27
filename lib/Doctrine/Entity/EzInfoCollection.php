<?php

declare(strict_types=1);

namespace Netgen\InformationCollection\Doctrine\Entity;

class EzInfoCollection
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $contentObjectId;

    /**
     * @var int
     */
    private $created;

    /**
     * @var int
     */
    private $creatorId;

    /**
     * @var int
     */
    private $modified;

    /**
     * @var string
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
