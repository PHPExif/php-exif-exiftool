<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\DateTimeFieldMapper;
use PHPExif\Common\Data\Exif;
use \DateTimeImmutable;

/**
 * Class: DateTimeFieldMapperTest
 *
 * @see \PHPUnit_Framework_TestCase
 * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader\Mapper\Exif\DateTimeFieldMapper
 * @covers ::<!public>
 */
class DateTimeFieldMapperTest extends BaseFieldMapperTest
{
    /**
     * {@inheritdoc}
     */
    protected $fieldMapperClass = DateTimeFieldMapper::class;

    /**
     * {@inheritdoc}
     */
    protected $supportedFields = [
        DateTimeImmutable::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $validInput = [
        'system:filemodifydate' => '2016-11-17 20:00:00',
        'composite:subsecdatetimeoriginal' => '2016-11-17 20:01:00',
        'composite:subsecmodifydate' => '2016-11-17 20:02:00',
        'exififd:datetimeoriginal' => '2016-11-17 20:03:00',
        'exififd:createdate' => '2016-11-17 20:04:00',
        'ifd0:modifydate' => '2016-11-17 20:05:00',
    ];

    /**
     * {@inheritdoc}
     */
    protected $outputAccessor = 'getCreationDate';

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
            DateTimeImmutable::class,
            $newData
        );

        $this->assertEquals(
            $this->validInput['system:filemodifydate'],
            $newData->format('Y-m-d H:i:s')
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

            $originalData = $output->{$this->outputAccessor}();
            $mapper->mapField($field, $this->validInput, $output);
            $newData = $output->{$this->outputAccessor}();

            $this->assertNotSame($originalData, $newData);

            $this->assertInstanceOf(
                DateTimeImmutable::class,
                $newData
            );

            $this->assertEquals(
                $this->validInput[$newKey],
                $newData->format('Y-m-d H:i:s')
            );
        }
    }
}
