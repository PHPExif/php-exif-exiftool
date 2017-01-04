<?php
/**
 * Mapper for mapping data between raw input and a Filename VO
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use PHPExif\Common\Data\ExifInterface;
use PHPExif\Common\Data\ValueObject\Filename;
use PHPExif\Common\Mapper\FieldMapper;
use PHPExif\Common\Mapper\GuardInvalidArgumentsForExifTrait;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Exiftool
 */
class FilenameFieldMapper implements FieldMapper
{
    use GuardInvalidArgumentsForExifTrait;
    use ValidKeysTrait;

    /**
     * @var array
     */
    private $validKeys = [
        'sourcefile',
        'system:filename',
    ];

    /**
     * {@inheritDoc}
     */
    public function getSupportedFields()
    {
        return array(
            Filename::class,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function mapField($field, array $input, &$output)
    {
        $this->guardInvalidArguments($field, $input, $output);

        foreach ($this->validKeys as $key) {
            if (!array_key_exists($key, $input)) {
                continue;
            }

            $filename = new Filename($input[$key]);
            $output = $output->withFilename($filename);
            break;
        }
    }
}
