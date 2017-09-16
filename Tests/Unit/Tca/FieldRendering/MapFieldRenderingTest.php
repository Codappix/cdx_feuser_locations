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

        $this->subject = $this->getMockBuilder(\Codappix\CdxFeuserLocations\Tca\FieldRendering\MapFieldRendering::class)
            ->setMethods(['getConfigurationService'])
            ->getMock();
    }

    /**
     * @test
     */
    public function getRenderedGoogleMap()
    {
        $configurationService = $this->getMockBuilder(\Codappix\CdxFeuserLocations\Service\Configuration::class)
            ->setMethods(['getGoogleApiKey'])
            ->getMock();
        $configurationService
            ->expects($this->atLeastOnce())
            ->method('getGoogleApiKey')
            ->will(static::returnValue('testKey'));
        $this->subject
            ->expects($this->atLeastOnce())
            ->method('getConfigurationService')
            ->will(static::returnValue($configurationService));

        $configuration = [
            'row' => [
                'lat' => 54.3243432,
                'lng' => -1.2,
            ],
        ];

        $this->assertRegExp(
            '/id="cdxGoogleMap"/',
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

    /**
     * @test
     */
    public function googleMapIsNotRenderedIfNoLatOrLngExist()
    {
        $configuration = [
            'row' => [
                'lat' => '',
                'lng' => '',
            ],
        ];

        $this->assertSame(
            '',
            $this->subject->render($configuration),
            'Something was returned, when it should not.'
        );
    }
}
