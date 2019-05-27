<?php
namespace Codappix\CdxFeuserLocations\Tests\Unit;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Iterator;

/**
 * Parse json file wit htest data as DataProvider for PHPUnit.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class JsonFileIterator implements Iterator
{
    protected $jsonData;

    /**
     * @param string $className E.g. `get_class($this)` in test case.
     */
    public function __construct($className)
    {
        $file = $this->getFileName($className);

        $this->jsonData = json_decode(
            file_get_contents($file),
            true
        );
        if ($this->jsonData === null) {
            throw new \InvalidArgumentException(
                'Invalid json: "' . json_last_error_msg() . '".',
                1451045747
            );
        }
    }

    /**
     * Get absolute flie name based on class.
     *
     * @param string $className
     *
     * @return string
     */
    protected function getFileName($className)
    {
        $dataStorage = __DIR__ . '/Data/';
        $fileEnding = '.json';

        $file = $dataStorage . $this->fetchFileNameForClass($className) . $fileEnding;
        if (is_file($file)) {
            return $file;
        }

        throw new \InvalidArgumentException(
            'Could not autoload data file for PHPUnit Test. Last tried file was: "' . $file . '".',
            1451044703
        );
    }

    /**
     * PSR-4 File name for class.
     *
     * @param string $className
     *
     * @return string
     */
    protected function fetchFileNameForClass($className)
    {
        // Remove vendor, ext name and test framework.
        $classNameParts = explode('\\', $className);
        unset($classNameParts[0], $classNameParts[1], $classNameParts[2], $classNameParts[3]);
        $fileNameForClass = implode('/', $classNameParts);

        return str_replace('Test', '', $fileNameForClass);
    }

    public function rewind()
    {
        reset($this->jsonData);
    }

    public function valid()
    {
        return current($this->jsonData) !== false;
    }

    public function key()
    {
        $keys = array_keys(current($this->jsonData));
        if (count($keys) === 1) {
            return $keys[0];
        }

        return key($this->jsonData);
    }

    public function current()
    {
        $data = current($this->jsonData);
        $keys = array_keys(current($this->jsonData));
        if (count($keys) === 1) {
            return $data[$keys[0]];
        }

        return $data;
    }

    public function next()
    {
        next($this->jsonData);
    }
}
