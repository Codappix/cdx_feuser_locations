<?php
namespace WebVision\WvFeuserLocations\Tests\Unit\Tca\FieldRendering;

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
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class MapFieldRenderingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getRenderedGoogleMap()
    {
        $mapFieldRendering = new \WebVision\WvFeuserLocations\Tca\FieldRendering\MapFieldRendering;
        $configuration = [
            'row' => [
                'lat' => 54.3243432,
                'lng' => -1.2,
            ],
        ];

        $this->assertRegExp(
            '/id="wvGoogleMap"/',
            $mapFieldRendering->render($configuration),
            'No HTML Element with necessary id returned.'
        );
        $this->assertRegExp(
            '/center\: {lat: 54\.3243432, lng: -1\.2\}/',
            $mapFieldRendering->render($configuration),
            'Center of map is not set properly.'
        );
        $this->assertRegExp(
            '/position\: {lat: 54\.3243432, lng: -1\.2\}/',
            $mapFieldRendering->render($configuration),
            'Position of marker is not set properly.'
        );
    }
}
