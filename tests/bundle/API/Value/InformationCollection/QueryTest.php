<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\API\Value\InformationCollection;

use Netgen\Bundle\InformationCollectionBundle\API\Value\InformationCollection\Query;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testWithContent()
    {
        $query = Query::withContent(12);

        $this->assertEquals(12, $query->contentId);
        $this->assertEquals(0, $query->offset);
        $this->assertEquals(10, $query->limit);
        $this->assertEmpty($query->fields);
    }

    public function testCount()
    {
        $query = Query::count();

        $this->assertEquals(0, $query->limit);
        $this->assertEquals(0, $query->offset);
    }

    public function testConstructor()
    {
        $values = [
            'offset' => 15,
            'limit' => 100,
            'contentId' => 55,
            'collectionId' => 14,
            'searchTest' => 'text',
            'contents' => [
                12, 34, 34
            ],
            'collections' => [
                56, 78, 45
            ],
            'fields' => [
                86, 324, 54
            ],
        ];

        $query = new Query($values);

        foreach ($values as $key => $value) {

            $this->assertEquals($value, $query->$key);

        }
    }
}
