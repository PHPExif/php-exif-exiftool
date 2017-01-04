<?php
namespace
{  
    $mockProcOpen = false;
    $mockStreamGetContents = false;
} 

namespace PHPExif\Adapter\Exiftool
{
    use Mockery as m;
    use PHPExif\Adapter\Exiftool\Reader;
    use PHPExif\Common\Adapter\MapperInterface;
    use PHPExif\Common\Data\Metadata;
    use PHPExif\Common\Exception\Reader\NoExifDataException;
    use \ReflectionProperty;

    // stub the function
    function proc_open($cmd, array $descriptorspec, &$pipes, $cwd = null, array $env = [], array $other_options = [])
    {
        global $mockProcOpen;

        switch ($mockProcOpen) {
            case -10:
                return false;
            case false:
            default:
                return \proc_open($cmd, $descriptorspec, $pipes, $cwd, $env, $other_options);
        }
    }

    function stream_get_contents($handle, $maxlength = -1, $offset = -1)
    {
        global $mockStreamGetContents;

        switch ($mockStreamGetContents) {
            case -10:
                return false;
            case -20:
                return '[{"foo":"bar"}]';
            case false:
            default:
                return call_user_func_array('stream_get_contents', func_get_args());
        }
    }


    /**
     * Class: ReaderTest
     *
     * @see \PHPUnit_Framework_TestCase
     * @coversDefaultClass \PHPExif\Adapter\Exiftool\Reader
     * @covers ::<!public>
     */
    class ReaderTest extends \PHPUnit_Framework_TestCase
    {
        public function setUp()
        {
            global $mockProcOpen;
            global $mockStreamGetContents;

            $mockProcOpen = false;
            $mockStreamGetContents = false;
        }

        /**
         * @covers ::__construct
         * @dataProvider defaultPropertyValues
         * @group reader
         *
         * @return void
         */
        public function testConstructorSetsDefaultConfiguration($propertyName, $expectedValue)
        {
            $mapper = m::mock(MapperInterface::class);
            $reader = new Reader($mapper);

            $reflProp = new ReflectionProperty(Reader::class, $propertyName);
            $reflProp->setAccessible(true);

            $this->assertEquals(
                $expectedValue,
                $reflProp->getValue($reader)
            );
        }

        /**
         * @return array
         */
        public function defaultPropertyValues()
        {
            return [
                [
                    'binary',
                    'exiftool'
                ],
                [
                    'numeric',
                    true
                ],
                [
                    'path',
                    '/usr/bin/env'
                ],
            ];
        }

        /**
         * @covers ::__construct
         * @dataProvider overrideDefaultPropertyValues
         * @group reader
         *
         * @return void
         */
        public function testConstructorCanOverrideDefaultConfiguration($propertyName, $defaultValue, $key, $newValue)
        {
            $mapper = m::mock(MapperInterface::class);
            $reader = new Reader($mapper);

            $reflProp = new ReflectionProperty(Reader::class, $propertyName);
            $reflProp->setAccessible(true);

            $this->assertEquals(
                $defaultValue,
                $reflProp->getValue($reader)
            );

            $reader = new Reader($mapper, [$key => $newValue]);

            $this->assertEquals(
                $newValue,
                $reflProp->getValue($reader)
            );
        }

        /**
         * @return array
         */
        public function overrideDefaultPropertyValues()
        {
            return [
                [
                    'binary',
                    'exiftool',
                    Reader::BIN,
                    'exiftool2',
                ],
                [
                    'numeric',
                    true,
                    Reader::NUMERIC,
                    false,
                ],
                [
                    'path',
                    '/usr/bin/env',
                    Reader::PATH,
                    '/dev/null',
                ],
            ];
        }

        /**
         * @covers ::getMapper
         * @group reader
         *
         * @return void
         */
        public function testGetMapperReturnsMapper()
        {
            $mapper = m::mock(MapperInterface::class);
            $reader = new Reader($mapper);

            $this->assertSame(
                $mapper,
                $reader->getMapper()
            );
        }

        /**
         * @covers ::getMetadataFromFile
         * @group reader
         *
         * @return void
         */
        public function testGetMetadataFromFileThrowsExceptionWhenNoProcOpen()
        {
            global $mockProcOpen;
            $mockProcOpen = -10;

            $this->setExpectedException(NoExifDataException::class);
            $mapper = m::mock(MapperInterface::class);
            $reader = new Reader($mapper);

            $reader->getMetadataFromFile('/dev/null');
        }

        /**
         * @covers ::getMetadataFromFile
         * @group reader
         *
         * @return void
         */
        public function testGetMetadataFromFileThrowsExceptionWhenNoData()
        {
            global $mockStreamGetContents;
            $mockStreamGetContents = -10;

            $this->setExpectedException(NoExifDataException::class);

            $mapper = m::mock(MapperInterface::class);
            $mapper->shouldReceive('map')
                ->andReturnNull();
            $reader = new Reader($mapper);
            $result = $reader->getMetadataFromFile('/tmp');

            $this->assertInstanceOf(
                Metadata::class,
                $result
            );
        }

        /**
         * @covers ::getMetadataFromFile
         * @group reader
         *
         * @return void
         */
        public function testGetMetadataFromFileReturnsMetadataInstance()
        {
            global $mockStreamGetContents;
            $mockStreamGetContents = -20;

            $mapper = m::mock(MapperInterface::class);
            $mapper->shouldReceive('map')
                ->andReturnNull();
            $reader = new Reader($mapper);
            $result = $reader->getMetadataFromFile('/tmp');

            $this->assertInstanceOf(
                Metadata::class,
                $result
            );
        }
    }
}
