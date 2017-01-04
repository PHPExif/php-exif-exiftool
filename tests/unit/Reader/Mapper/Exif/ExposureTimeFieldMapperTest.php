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
     * {@inheritdoc}
     */
    protected $fieldMapperClass = ExposureTimeFieldMapper::class;

    /**
     * {@inheritdoc}
     */
    protected $supportedFields = [
        ExposureTime::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $validInput = [
        'exififd:exposuretime' => '1/60',
        'composite:shutterspeed' => '1/80',
    ];

    /**
     * {@inheritdoc}
     */
    protected $outputAccessor = 'getExposureTime';

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

        $originalData = $output->{$this->outputAccessor}();
        $mapper->mapField($field, $this->validInput, $output);
        $newData = $output->{$this->outputAccessor}();

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
}
