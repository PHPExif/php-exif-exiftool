<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\FilenameFieldMapper;
use PHPExif\Common\Data\Exif;
use PHPExif\Common\Data\ValueObject\Filename;

/**
 * Class: FilenameFieldMapperTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\FilenameFieldMapper
 * @covers ::<!public>
 */
class FilenameFieldMapperTest extends BaseFieldMapperTest
{
    /**
     * {@inheritdoc}
     */
    protected $fieldMapperClass = FilenameFieldMapper::class;

    /**
     * {@inheritdoc}
     */
    protected $supportedFields = [
        Filename::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $validInput = [
        'sourcefile' => 'IMG_0001.JPG',
        'system:filename' => 'IMG_0002.JPG',
    ];

    /**
     * {@inheritdoc}
     */
    protected $outputAccessor = 'getFilename';

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
            Filename::class,
            $newData
        );

        $this->assertEquals(
            $this->validInput['sourcefile'],
            $newData
        );
    }
}
