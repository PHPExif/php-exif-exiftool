<?php
/**
 * Mapper for mapping data between raw input and a datetime object
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use PHPExif\Common\Data\ExifInterface;
use PHPExif\Common\Mapper\FieldMapper;
use PHPExif\Common\Mapper\GuardInvalidArgumentsForExifTrait;
use \DateTimeImmutable;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Common
 */
class DateTimeFieldMapper implements FieldMapper
{
    use GuardInvalidArgumentsForExifTrait;

    /**
     * {@inheritDoc}
     */
    public function getSupportedFields()
    {
        return array(
            DateTimeImmutable::class,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function mapField($field, array $input, &$output)
    {
        $this->guardInvalidArguments($field, $input, $output);

        $validKeys = [
            'system:filemodifydate',
            'composite:subsecdatetimeoriginal',
            'composite:subsecmodifydate',
            'exififd:datetimeoriginal',
            'exififd:createdate',
            'ifd0:modifydate',
        ];

        while ($key = array_shift($validKeys)) {
            if (!array_key_exists($key, $input)) {
                continue;
            }

            $datetimeOriginal = new DateTimeImmutable($input[$key]);
            $output = $output->withCreationDate($datetimeOriginal);
            break;
        }
    }
}
