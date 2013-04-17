<?php

namespace Fpn\ApiClient\Pictures\Tests;

use Fpn\ApiClient\Pictures\Preset;

class PresetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->preset = new Preset();
    }

    public function tearDown()
    {
        unset($this->preset);

        parent::tearDown();
    }

    public function testCanFetchOnePreset()
    {
        $expected = json_decode('{ "id": 3 }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected));

        $this->preset
            ->setApiClient($apiClient)
            ->fetch(3);

        $this->assertEquals(3, $this->preset->getId());
    }

    public function testCanFetchMultiplePresets()
    {
        $expected = json_decode('{ "items": [ { "id": 1 }, { "id": 2 }, { "id": 3 } ] }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected));

        $presets = $this->preset
            ->setApiClient($apiClient)
            ->fetchAll()
            ;

        foreach ($presets as $preset) {
            $this->assertTrue($preset instanceof Preset);
        }
    }

    public function testCanCreateNewPreset()
    {
        $expected = json_decode('{ "id": 1, "name": "Created Preset", "posId": 147 }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected));

        $this->preset
            ->setApiClient($apiClient)
            ->setName('Created Preset')
            ->setPosId(147)
            ->save()
            ;

        $preset = new Preset();
        $preset
            ->setApiClient($apiClient)
            ->fetch($this->preset->getId())
            ;

        $this->assertEquals($this->preset, $preset);
    }

    public function testCanUpdatePreset()
    {
        $expected = json_decode('{ "id": 1, "name": "Updated Preset", "posId": 147 }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($expected));

        $this->preset
            ->setApiClient($apiClient)
            ->fetch(3)
            ->setName('Updated Preset')
            ->save()
            ;

        $preset = new Preset();
        $preset
            ->setApiClient($apiClient)
            ->fetch(3)
            ;

        $this->assertEquals($this->preset, $preset);
    }

    /**
     * @expectedException \Exception
     */
    public function testCanDeletePreset()
    {
        $toDelete = json_decode('{ "id": 3 }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls($toDelete, $toDelete, $this->throwException(new \Exception())))
            ;

        $this->preset
            ->setApiClient($apiClient)
            ->fetch(3)
            ->delete()
            ;

        $preset = new Preset();
        $preset
            ->setApiClient($apiClient)
            ->fetch(3)
            ;
    }

    public function testCanApplyPreset()
    {
        $preset = json_decode('{ "id": 3 }');
        $image  = json_decode('{ "image": "/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAJYAyADAREAAhEBAxEB/8QAHwAAAQMFAQEBAAAAAAAAAAAAAAIDBgEEBQcICQoL/8QAbRAAAQMCBQMCAwQECAsDBAIjAQIDBAURAAYHEiEIEzEiQQkUURUyYXEjQoGRFhgaaKGosegKFyQzUliXwdHW8CVi4SY0NTdDcoK2JzZT8RkoOEZ3hoemxkRHSFZjc5KyVGZndHaDiKKjpMLH/8QAHgEBAAICAwEBAQAAAAAAAAAAAAIDAQQFBggHCQr/xACAEQABAQYDAwcHAwkPDAoPAg8BAgADBAURIQYS..." }');

        $apiClient = $this->getMock('Fpn\ApiClient\Core\ApiClient', array('request'));
        $apiClient
            ->expects($this->any())
            ->method('request')
            ->will($this->onConsecutiveCalls($preset, $image))
            ;

        $this->preset
            ->setApiClient($apiClient)
            ->fetch(3)
            ->apply('/tmp/foo.png', '/tmp/foo.jpg')
            ;

        $this->assertEquals(base64_decode($image->image), file_get_contents('/tmp/foo.jpg'));

        unlink('/tmp/foo.jpg');
    }
}
