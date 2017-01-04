<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ApertureFieldMapper;
use PHPExif\Common\Data\Exif;
use PHPExif\Common\Data\ValueObject\Aperture;

/**
 * Class: ApertureFieldMapperTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\ApertureFieldMapper
 * @covers ::<!public>
 */
class ApertureFieldMapperTest extends BaseFieldMapperTest
{
    /**
     * {@inheritdoc}
     */
    protected $fieldMapperClass = ApertureFieldMapper::class;

    /**
     * {@inheritdoc}
     */
    protected $supportedFields = [
        Aperture::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $validInput = [
        'composite:aperture' => 5.0,
    ];

    /**
     * {@inheritdoc}
     */
    protected $outputAccessor = 'getAperture';

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
            Aperture::class,
            $newData
        );

        $this->assertEquals(
            'f/' . $this->validInput['composite:aperture'],
            (string) $newData
        );
    }
}
