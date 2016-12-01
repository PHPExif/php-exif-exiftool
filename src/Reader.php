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

namespace PHPExif\Adapter\Native;

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
    private $numeric = true;

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
        ];
        $config = array_replace($defaults, $config);

        $this->binary = $config[self::BIN];
        $this->path = $config[self::PATH];

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

        $data = json_decode($result, true);

        if (false === $data) {
            throw NoExifDataException::fromFile($filePath);
        }

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
     * Returns the output from given cli command
     *
     * @param string $command
     *
     * @throws RuntimeException If the command can't be executed
     *
     * @return string
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
            throw new RuntimeException(
                'Could not open a resource to the exiftool binary'
            );
        }
        $result = stream_get_contents($pipes[1]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        return $result;
    }
}
