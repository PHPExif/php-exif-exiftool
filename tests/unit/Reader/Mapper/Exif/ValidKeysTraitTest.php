<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ValidKeysTrait;

/**
 * Class: ValidKeysTraitTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ValidKeysTrait
 * @covers ::<!public>
 */
class ValidKeysTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getValidKeys
     * @group mapper
     *
     * @return void
     */
    public function testGetValidKeysReturnsArray()
    {
        $mock = $this->getMockBuilder(ValidKeysTrait::class)
            ->getMockForTrait();

        $this->assertInternalType(
            'array',
            $mock->getValidKeys()
        );
    }

    /**
     * @covers ::getValidKeys
     * @group mapper
     *
     * @return void
     */
    public function testGetValidKeysReturnsEmptyArray()
    {
        $mock = $this->getMockBuilder(ValidKeysTrait::class)
            ->getMockForTrait();

        $this->assertCount(
            0,
            $mock->getValidKeys()
        );
    }

    /**
     * @covers ::setValidKeys
     * @group mapper
     *
     * @return void
     */
    public function testSetValidKeysSetsCorrectData()
    {
        $mock = $this->getMockBuilder(ValidKeysTrait::class)
            ->getMockForTrait();
        $data = [
            'foo', 'bar', 'baz',
        ];

        $this->assertNotEquals($data, $mock->getValidKeys());
        $mock->setValidKeys($data);
        $this->assertEquals($data, $mock->getValidKeys());
    }
}
