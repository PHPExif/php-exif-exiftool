<?php
/**
 * Trait with methods to manipulate the $validKeys property
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool\Reader\Mapper\Exif;

/**
 * Common methods to manipulate the validKeys property
 *
 * @category    PHPExif
 * @package     Exiftool
 */
trait ValidKeysTrait
{
    /**
     * Getter for validKeys
     *
     * @return array
     */
    public function getValidKeys()
    {
        return (!property_exists($this, 'validKeys')) ? [] : $this->validKeys;
    }

    /**
     * Setter for validKeys
     *
     * @param array $validKeys
     */
    public function setValidKeys(array $validKeys)
    {
        $this->validKeys = $validKeys;
    }
}
