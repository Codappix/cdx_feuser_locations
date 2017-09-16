<?php
namespace Codappix\CdxFeuserLocations\Tests\Unit\Tca\FieldRendering;

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

class MapFieldRenderingTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    protected $subject;

    public function setUp()
    {
        $configurationService = $this->getMock(
            'Codappix\CdxFeuserLocations\Service\Configuration',
            ['getGoogleApiKey']
        );
        $configurationService
            ->expects($this->atLeastOnce())
            ->method('getGoogleApiKey')
            ->will(static::returnValue('testKey'));

        $this->subject = $this->getMock(
            'Codappix\CdxFeuserLocations\Tca\FieldRendering\MapFieldRendering',
            ['getConfigurationService']
        );
        $this->subject
            ->expects($this->atLeastOnce())
            ->method('getConfigurationService')
            ->will(static::returnValue($configurationService));
    }

    /**
     * @test
     */
    public function getRenderedGoogleMap()
    {
        $configuration = [
            'row' => [
                'lat' => 54.3243432,
                'lng' => -1.2,
            ],
        ];

        $this->assertRegExp(
            '/id="wvGoogleMap"/',
            $this->subject->render($configuration),
            'No HTML Element with necessary id returned.'
        );
        $this->assertRegExp(
            '/center\: {lat: 54\.3243432, lng: -1\.2\}/',
            $this->subject->render($configuration),
            'Center of map is not set properly.'
        );
        $this->assertRegExp(
            '/position\: {lat: 54\.3243432, lng: -1\.2\}/',
            $this->subject->render($configuration),
            'Position of marker is not set properly.'
        );
    }
}
