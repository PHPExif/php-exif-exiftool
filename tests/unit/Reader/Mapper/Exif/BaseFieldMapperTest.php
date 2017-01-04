<?php

namespace Tests\PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use Mockery as m;
use PHPExif\Common\Data\Exif;
use PHPExif\Common\Exception\Mapper\UnsupportedFieldException;
use PHPExif\Common\Exception\Mapper\UnsupportedOutputException;

/**
 * Class: BaseFieldMapperTest
 *
 * @see \PHPUnit_Framework_TestCase
 */
abstract class BaseFieldMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * FQCN of the fieldmapper being tested
     *
     * @var mixed
     */
    protected $fieldMapperClass;

    /**
     * List of supported fields
     *
     * @var array
     */
    protected $supportedFields = [];

    /**
     * Valid input data
     *
     * @var array
     */
    protected $validInput = [];

    /**
     * Which method should be used on the output to access the
     * mapped data?
     *
     * @var string
     */
    protected $outputAccessor;

    /**
     * @covers ::getSupportedFields
     * @group mapper
     *
     * @return void
     */
    public function testGetSupportedFieldsReturnsExpectedArray()
    {
        $mapper = new $this->fieldMapperClass();
        $actual = $mapper->getSupportedFields();

        $this->assertInternalType('array', $actual);

        $this->assertEquals($this->supportedFields, $actual);
    }

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldThrowsExceptionForUnsupportedField()
    {
        $this->setExpectedException(UnsupportedFieldException::class);

        $field = 'foo';
        $input = [];
        $output = new Exif;
        $mapper = new $this->fieldMapperClass();

        $mapper->mapField($field, $input, $output);
    }

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldThrowsExceptionForUnsupportedOutput()
    {
        $this->setExpectedException(UnsupportedOutputException::class);

        $field = reset($this->supportedFields);
        $input = [];
        $output = new \stdClass;
        $mapper = new $this->fieldMapperClass();

        $mapper->mapField($field, $input, $output);
    }

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldYieldsNewOutputForValidInput()
    {
        $field = reset($this->supportedFields);
        $output = new Exif;
        $mapper = new $this->fieldMapperClass();

        $originalHash = spl_object_hash($output);

        $mapper->mapField($field, $this->validInput, $output);

        $newHash = spl_object_hash($output);

        $this->assertNotEquals(
            $originalHash,
            $newHash
        );
    }

    /**
     * @covers ::mapField
     * @group mapper
     *
     * @return void
     */
    public function testMapFieldYieldsSameOutputForInvalidInput()
    {
        $field = reset($this->supportedFields);
        $output = new Exif;
        $mapper = new $this->fieldMapperClass();

        $originalHash = spl_object_hash($output);

        $mapper->mapField($field, [], $output);

        $newHash = spl_object_hash($output);

        $this->assertEquals(
            $originalHash,
            $newHash
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

            if (null !== $originalData) {
                $this->assertInstanceOf(
                    get_class($originalData),
                    $newData
                );
            }

            $this->assertEquals(
                $this->validInput[$newKey],
                $newData
            );
        }
    }
}
