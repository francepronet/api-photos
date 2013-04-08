<?php

namespace Fpn\ApiClient\Tests;

use Fpn\ApiClient\Pictures\Filter;
use Fpn\ApiClient\Core\Utility\Caster;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->filter = new Filter();
        $this->filter->setPresetId(3);
    }

    public function tearDown()
    {
        unset($this->preset);

        parent::tearDown();
    }

    public function testCanFetchOneFilter()
    {
        $expected = json_decode('{ "id": 51, "type": "addBorder", "params": [ "c0f", 5 ] }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected))
            ;

        $this->filter
            ->setApiClient($apiClient)
            ->fetch(51)
            ;

        $this->assertEquals(51, $this->filter->getId());
    }

    public function testCanFetchMultipleFilters()
    {
        $expected = json_decode('{ "current_page_number": "1", "num_items_per_page": 20, "items": [ { "id": 51, "type": "addBorder", "params": [ "c0f", 5 ] }, { "id": 52, "type": "addWatermark", "params": [ "watermark.png" ] }, { "id": 53, "type": "roundCorners", "params": [ 10, 10 ] }, { "id": 54, "type": "addBanner", "params": [ "banner.jpg", "top" ] } ], "total_count": 4 }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected))
            ;

        $filters = $this->filter
            ->setApiClient($apiClient)
            ->fetchAll()
            ;

        foreach ($filters as $filter) {
            $this->assertTrue($filter instanceof Filter);
        }
    }

    public function testCanCreateAFilter()
    {
        $expected = json_decode('{ "id": 51, "type": "addBorder", "params": [ "c0f", 5 ] }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected))
            ;

        $this->filter
            ->setApiClient($apiClient)
            ->setType('addBorder')
            ->setParams(array('c0f', 5))
            ->save()
            ;

        $filter = new Filter();
        $filter
            ->setApiClient($apiClient)
            ->setPresetId(3)
            ;
        Caster::cast($expected, $filter);

        $this->assertEquals($filter, $this->filter);
    }

    public function testCanUpdateAFilter()
    {
        $fetched = json_decode('{ "id": 51, "type": "addBorder", "params": [ "c0f", 5 ] }');
        $updated = json_decode('{ "id": 51, "type": "addRoundedCorner", "params": [ 4, 4 ] }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls($fetched, $updated))
            ;

        $this->filter
            ->setApiClient($apiClient)
            ->fetch(51)
            ->setType('addRoundedCorner')
            ->setParams(array(4, 4))
            ->save()
            ;

        $filter = new Filter();
        $filter
            ->setApiClient($apiClient)
            ->setPresetId(3)
            ;
        Caster::cast($updated, $filter);

        $this->assertEquals($filter, $this->filter);
    }

    /**
     * @expectedException \Exception
     */
    public function testCanDeleteAFilter()
    {
        $deleted = json_decode('{ "id": 51, "type": "addBorder", "params": [ "c0f", 5 ] }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls($deleted, $deleted, $this->throwException(new \Exception())))
            ;

        $this->filter
            ->setApiClient($apiClient)
            ->fetch(51)
            ->delete()
            ->fetch(51)
            ;
    }
}
