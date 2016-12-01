<?php
/**
 * Mapper for mapping data between raw input and Data classes
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool\Reader;

use PHPExif\Common\Adapter\MapperInterface;
use PHPExif\Common\Data\MetadataInterface;
use PHPExif\Common\Mapper\ArrayMapper;
use PHPExif\Common\Mapper\FieldMapperTrait;

/**
 * Mapper
 *
 * Maps the array of exif & iptc data onto the
 * correct fields of given MetadataInterface object
 *
 * @category    PHPExif
 * @package     Exiftool
 */
class Mapper implements MapperInterface, ArrayMapper
{
    use FieldMapperTrait;

    /**
     * {@inheritDoc}
     */
    public function map(array $input, MetadataInterface &$output)
    {
        $this->mapArray($input, $output);

        $output->setRawData($input);
    }

    /**
     * {@inheritDoc}
     */
    public function mapArray(array $input, &$output)
    {
        $mappers = $this->getFieldMappers();

        foreach ($mappers as $field => $mapper) {
            $mapper->mapField($field, $input, $output);
        }
    }
}
