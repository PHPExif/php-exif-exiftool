<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ExposureTimeFieldMapper;
use PHPExif\Common\Data\Exif;
use PHPExif\Common\Data\ValueObject\ExposureTime;

/**
 * Class: ExposureTimeFieldMapperTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ExposureTimeFieldMapper
 * @covers ::<!public>
 */
class ExposureTimeFieldMapperTest extends BaseFieldMapperTest
{
    /**
     * FQCN of the fieldmapper being tested
     *
     * @var mixed
     */
    protected $fieldMapperClass = ExposureTimeFieldMapper::class;

    /**
     * List of supported fields
     *
     * @var array
     */
    protected $supportedFields = [
        ExposureTime::class,
    ];

    /**
     * Valid input data
     *
     * @var array
     */
    protected $validInput = [
        'exififd:exposuretime' => '1/60',
        'composite:shutterspeed' => '1/80',
    ];

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldHasDataInOutput()
    {
        $field = reset($this->supportedFields);
        $output = new Exif;
        $mapper = new $this->fieldMapperClass();

        $originalData = $output->getExposureTime();
        $mapper->mapField($field, $this->validInput, $output);
        $newData = $output->getExposureTime();

        $this->assertNotSame($originalData, $newData);

        $this->assertInstanceOf(
            ExposureTime::class,
            $newData
        );

        $this->assertEquals(
            $this->validInput['exififd:exposuretime'],
            $newData
        );
    }

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldTraversesSetOfKeys()
    {
        $field = reset($this->supportedFields);
        $output = new Exif;
        $mapper = new $this->fieldMapperClass();

        $keys = array_keys($this->validInput);
        foreach ($this->validInput as $key => $value) {
            unset($this->validInput[$key]);
            array_shift($keys);

            if (count($keys) === 0) {
                break;
            }

            $newKey = $keys[0];

            $originalData = $output->getExposureTime();
            $mapper->mapField($field, $this->validInput, $output);
            $newData = $output->getExposureTime();

            $this->assertNotSame($originalData, $newData);

            $this->assertInstanceOf(
                ExposureTime::class,
                $newData
            );

            $this->assertEquals(
                $this->validInput[$newKey],
                $newData
            );
        }
    }

    /**
     * @covers ::getValidKeys
     * @group mapper
     *
     * @return void
     */
    public function testGetValidKeysReturnsArray()
    {
        $mapper = new $this->fieldMapperClass();
        $this->assertInternalType(
            'array',
            $mapper->getValidKeys()
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
        $mapper = new $this->fieldMapperClass();
        $data = [
            'foo', 'bar', 'baz',
        ];

        $this->assertNotEquals($data, $mapper->getValidKeys());
        $mapper->setValidKeys($data);
        $this->assertEquals($data, $mapper->getValidKeys());
    }
}
