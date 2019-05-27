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

use TYPO3\CMS\Core\Tests\UnitTestCase;

class TestCase extends UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Disable db cache backend for unit tests.

        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Cache\CacheManager::class
        )->setCacheConfigurations([
            'extbase_object' => [
                'backend' => \TYPO3\CMS\Core\Cache\Backend\NullBackend::class,
            ],
            'extbase_datamapfactory_datamap' => [
                'backend' => \TYPO3\CMS\Core\Cache\Backend\NullBackend::class,
            ],
        ]);
    }

    /**
     * Autoloading dataprovider for json files per test class.
     *
     * @return JsonFileIterator
     */
    public function jsonFile()
    {
        return new JsonFileIterator(get_class($this));
    }
}
