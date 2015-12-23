<?php
namespace WebVision\WvFeuserLocations\Tests\Unit\Service;

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

/**
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class ConfigurationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @test
     */
    public function canFetchGoogleApiKey()
    {
        $this->assertEquals(
            39,
            strlen(\WebVision\WvFeuserLocations\Service\Configuration::getGoogleApiKey()),
            'Google API has not the expected length. Mostly the key is not valid.'
        );
    }
}
