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
use Codappix\CdxFeuserLocations\Tests\Unit\JsonFileIterator;

class TestCase extends UnitTestCase
{
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
