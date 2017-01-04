<?php
/**
 * Reader which uses exiftool to read EXIF data
 *
 * @category    PHPExif
 * @copyright   Copyright (c) 2016 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/PHPExif/php-exif-exiftool/blob/master/LICENSE MIT License
 * @link        http://github.com/PHPExif/php-exif-exiftool for the canonical source repository
 * @package     Exiftool
 */

namespace PHPExif\Adapter\Exiftool;

use PHPExif\Common\Adapter\MapperInterface;
use PHPExif\Common\Adapter\ReaderInterface;
use PHPExif\Common\Data\Exif;
use PHPExif\Common\Data\Iptc;
use PHPExif\Common\Data\Metadata;
use PHPExif\Common\Exception\Reader\NoExifDataException;
use \RuntimeException;

/**
 * Reader
 *
 * Reads EXIF data
 *
 * @category    PHPExif
 * @package     Exiftool
 */
final class Reader implements ReaderInterface
{
    const PATH = 'path';
    const BIN = 'binary';
    const NUMERIC = 'numeric';

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @var string
     */
    private $binary;

    /**
     * @var bool
     */
    private $numeric;

    /**
     * @var string
     */
    private $path;

    /**
     * @param MapperInterface $mapper
     */
    public function __construct(
        MapperInterface $mapper,
        array $config = []
    ) {
        $defaults = [
            self::BIN => 'exiftool',
            self::PATH => '/usr/bin/env',
            self::NUMERIC => true,
        ];
        $config = array_replace($defaults, $config);

        $this->binary = $config[self::BIN];
        $this->path = $config[self::PATH];
        $this->numeric = $config[self::NUMERIC];

        $this->mapper = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFromFile($filePath)
    {
        $result = $this->getCliOutput(
            sprintf(
                '%1$s%3$s -j -a -G1 -c %4$s %2$s',
                $this->path,
                escapeshellarg($filePath),
                $this->numeric ? ' -n' : '',
                escapeshellarg('%d deg %d\' %.4f"')
            )
        );

        if (false === $result) {
            throw NoExifDataException::fromFile($filePath);
        }

        $data = json_decode($result, true)[0];
        $data = $this->normalizeArrayKeys($data);

        // map the data:
        $mapper = $this->getMapper();
        $metadata = new Metadata(
            new Exif,
            new Iptc
        );
        $mapper->map($data, $metadata);

        return $metadata;
    }

    /**
     * Lowercases the keys for given array
     *
     * @param array $data
     *
     * @return array
     */
    private function normalizeArrayKeys(array $data)
    {
        $keys = array_keys($data);
        $keys = array_map('strtolower', $keys);
        $values = array_values($data);
        $values = array_map(function ($value) {
            if (!is_array($value)) {
                return $value;
            }

            return $this->normalizeArrayKeys($value);
        }, $values);

        return array_combine(
            $keys,
            $values
        );
    }

    /**
     * Returns the output from given cli command
     *
     * @param string $command
     *
     * @return string|boolean
     */
    protected function getCliOutput($command)
    {
        $descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'a')
        );
        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            return false;
        }

        $result = stream_get_contents($pipes[1]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        return $result;
    }
}
