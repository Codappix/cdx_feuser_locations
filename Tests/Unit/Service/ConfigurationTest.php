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
    protected $subject;

    public function setUp()
    {
        $configurationManager = $this->getMock(
            'TYPO3\CMS\Extbase\Configuration\ConfigurationManager',
            ['getConfiguration']
        );
        $configurationManager
            ->expects($this->once())
            ->method('getConfiguration')
            ->will(self::returnValue([
                'googleApiKey' => 'testKeyValue',
            ]));
        $this->subject = new \WebVision\WvFeuserLocations\Service\Configuration;
        $this->subject->injectConfigurationManager($configurationManager);
    }

    /**
     * @test
     */
    public function canFetchGoogleApiKey()
    {
        $this->assertEquals(
            'testKeyValue',
            $this->subject->getGoogleApiKey(),
            'Google API has not the expected length. Mostly the key is not valid.'
        );
    }
}
