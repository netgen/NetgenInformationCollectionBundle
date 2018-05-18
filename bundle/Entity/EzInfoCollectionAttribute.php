<?php

namespace Netgen\Bundle\InformationCollectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EzInfoCollectionAttribute.
 *
 * @ORM\Table(
 *     name="ezinfocollection_attribute",
 *     indexes={
 *          @ORM\Index(name="ezinfocollection_attr_cca_id", columns={"contentclass_attribute_id"}),
 *          @ORM\Index(name="ezinfocollection_attr_co_id", columns={"contentobject_id"}),
 *          @ORM\Index(name="ezinfocollection_attr_coa_id", columns={"contentobject_attribute_id"}),
 *          @ORM\Index(name="ezinfocollection_attr_ic_id", columns={"informationcollection_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Netgen\Bundle\InformationCollectionBundle\Repository\EzInfoCollectionAttributeRepository")
 */
class EzInfoCollectionAttribute
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
     * @ORM\Column(name="contentclass_attribute_id", type="integer", nullable=false, options={"default"=0})
     */
    private $contentClassAttributeId;

    /**
     * @var int
     *
     * @ORM\Column(name="contentobject_attribute_id", type="integer", nullable=true)
     */
    private $contentObjectAttributeId;

    /**
     * @var int
     *
     * @ORM\Column(name="contentobject_id", type="integer", nullable=true)
     */
    private $contentObjectId;

    /**
     * @var float
     *
     * @ORM\Column(name="data_float", type="float", nullable=true)
     */
    private $dataFloat;

    /**
     * @var int
     *
     * @ORM\Column(name="data_int", type="integer", nullable=true)
     */
    private $dataInt;

    /**
     * @var string
     *
     * @ORM\Column(name="data_text", type="text", nullable=true)
     */
    private $dataText;

    /**
     * @var int
     *
     * @ORM\Column(name="informationcollection_id", type="integer", nullable=false, options={"default"=0})
     */
    private $informationCollectionId;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $contentClassAttributeId
     */
    public function setContentClassAttributeId($contentClassAttributeId)
    {
        $this->contentClassAttributeId = $contentClassAttributeId;
    }

    /**
     * @param int $contentObjectAttributeId
     */
    public function setContentObjectAttributeId($contentObjectAttributeId)
    {
        $this->contentObjectAttributeId = $contentObjectAttributeId;
    }

    /**
     * @param int $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->contentObjectId = $contentObjectId;
    }

    /**
     * @param float $dataFloat
     */
    public function setDataFloat($dataFloat)
    {
        $this->dataFloat = $dataFloat;
    }

    /**
     * @param int $dataInt
     */
    public function setDataInt($dataInt)
    {
        $this->dataInt = $dataInt;
    }

    /**
     * @param string $dataText
     */
    public function setDataText($dataText)
    {
        $this->dataText = $dataText;
    }

    /**
     * @param int $informationCollectionId
     */
    public function setInformationCollectionId($informationCollectionId)
    {
        $this->informationCollectionId = $informationCollectionId;
    }

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
    public function getContentClassAttributeId()
    {
        return $this->contentClassAttributeId;
    }

    /**
     * @return int
     */
    public function getContentObjectAttributeId()
    {
        return $this->contentObjectAttributeId;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->contentObjectId;
    }

    /**
     * @return float
     */
    public function getDataFloat()
    {
        return $this->dataFloat;
    }

    /**
     * @return int
     */
    public function getDataInt()
    {
        return $this->dataInt;
    }

    /**
     * @return string
     */
    public function getDataText()
    {
        return $this->dataText;
    }

    /**
     * @return int
     */
    public function getInformationCollectionId()
    {
        return $this->informationCollectionId;
    }

    public function getValue()
    {
        if (!empty($this->dataText)) {
            return $this->dataText;
        }

        if (!empty($this->dataInt)) {
            return $this->dataInt;
        }

        if (!empty($this->dataFloat)) {
            return $this->dataFloat;
        }

        return '';
    }
}
