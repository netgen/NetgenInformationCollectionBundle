<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\Value;

use PHPUnit_Framework_TestCase;
use Netgen\Bundle\InformationCollectionBundle\Value\LegacyData;

class LegacyDataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider legacyDataProvider
     */
    public function testGetters($contentClassAttributeId, $dataFloat, $dataInt, $dataText)
    {
        $legacyData = new LegacyData($contentClassAttributeId, $dataFloat, $dataInt, $dataText);

        $this->assertEquals($contentClassAttributeId, $legacyData->getContentClassAttributeId());
        $this->assertEquals($dataFloat, $legacyData->getDataFloat());
        $this->assertEquals($dataInt, $legacyData->getDataInt());
        $this->assertEquals($dataText, $legacyData->getDataText());
    }
    public function legacyDataProvider()
    {
        return [
            [123456, 2.67, 34, 'text'],
            [31221312, 3.14, null, 'some text'],
            [4234234, null, 12, ''],
            [4234324, null, 35, 'weee'],
        ];
    }
}