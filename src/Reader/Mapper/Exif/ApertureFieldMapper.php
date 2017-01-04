<?php
/**
 * Mapper for mapping data between raw input and an Aperture VO
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

use PHPExif\Common\Data\ExifInterface;
use PHPExif\Common\Data\ValueObject\Aperture;
use PHPExif\Common\Mapper\FieldMapper;
use PHPExif\Common\Mapper\GuardInvalidArgumentsForExifTrait;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Exiftool
 */
class ApertureFieldMapper implements FieldMapper
{
    use GuardInvalidArgumentsForExifTrait;
    use ValidKeysTrait;

    /**
     * @var array
     */
    private $validKeys = [
        'composite:aperture',
        'exififd:fnumber',
        'exififd:aperturevalue',
    ];

    /**
     * {@inheritDoc}
     */
    public function getSupportedFields()
    {
        return array(
            Aperture::class,
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

            $aperture = new Aperture($input[$key]);
            $output = $output->withAperture($aperture);
        }
    }
}
