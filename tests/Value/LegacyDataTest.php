<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;
use PHPUnit\Framework\TestCase;

class LegacyDataTest extends TestCase
{
    /**
     * @dataProvider legacyDataProvider
     *
     * @param mixed $contentClassAttributeId
     * @param mixed $dataFloat
     * @param mixed $dataInt
     * @param mixed $dataText
     */
    public function testGetters($contentClassAttributeId, $dataFloat, $dataInt, $dataText)
    {
        $legacyData = new LegacyData(
            array(
                'contentClassAttributeId' => $contentClassAttributeId,
                'dataFloat' => $dataFloat,
                'dataInt' => $dataInt,
                'dataText' => $dataText,
            )
        );

        $this->assertEquals($contentClassAttributeId, $legacyData->contentClassAttributeId);
        $this->assertEquals($dataFloat, $legacyData->dataFloat);
        $this->assertEquals($dataInt, $legacyData->dataInt);
        $this->assertEquals($dataText, $legacyData->dataText);
    }

    public function legacyDataProvider()
    {
        return array(
            array(123456, 2.67, 34, 'text'),
            array(31221312, 3.14, null, 'some text'),
            array(4234234, null, 12, ''),
            array(4234324, null, 35, 'weee'),
        );
    }
}
