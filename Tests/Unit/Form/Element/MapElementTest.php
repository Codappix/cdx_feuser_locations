<?php
namespace Codappix\CdxFeuserLocations\Tests\Unit\Form\Element;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\CdxFeuserLocations\Form\Element\MapElement;
use Codappix\CdxFeuserLocations\Service\Configuration;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MapElementTest extends TestCase
{
    /**
     * @var array
     */
    protected $originalSingletons = [];

    public function setUp()
    {
        parent::setUp();

        $this->originalSingletons = GeneralUtility::getSingletonInstances();
    }

    public function tearDown()
    {
        GeneralUtility::resetSingletonInstances($this->originalSingletons);

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider mapElementData
     */
    public function htmlOutputIsAsExpected(string $apiKey, $lat, $lng, string $expectedHtml)
    {
        $configurationMock = $this->getMockBuilder(Configuration::class)->getMock();
        $configurationMock
            ->method('getGoogleApiKey')
            ->willReturn($apiKey);
        GeneralUtility::setSingletonInstance(Configuration::class, $configurationMock);
        $subject = new MapElement(GeneralUtility::makeInstance(NodeFactory::class), [
            'parameterArray' => [
                'fieldConf' => [
                    'config' => [
                        'fields' => [
                            'lat' => 'lat',
                            'lng' => 'lng',
                        ],
                    ],
                ],
            ],
            'databaseRow' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ]);
        $result = $subject->render();
        $this->assertSame($expectedHtml, $result['html'], 'Generated HTML for map element was not as expected.');
    }

    public function mapElementData() : array
    {
        return [
            'No lat' => [
                'apiKey' => 'GoogleMapsApiKey',
                'lat' => '',
                'lng' => 23.343,
                'expectedHtml' => '',
            ],
            'No lng' => [
                'apiKey' => 'GoogleMapsApiKey',
                'lat' => 23.343,
                'lng' => '',
                'expectedHtml' => '',
            ],
            'Both, lat and lng' => [
                'apiKey' => 'GoogleMapsApiKey',
                'lat' => 23.343,
                'lng' => 23.232,
                'expectedHtml' => sprintf(
                    $this->getHtmlTemplateString(),
                    23.343,
                    23.232,
                    23.343,
                    23.232,
                    'GoogleMapsApiKey'
                ),
            ],
        ];
    }

    protected function getHtmlTemplateString() : string
    {
        return '<style type="text/css" media="all">
            #cdxGoogleMap {
                height: 250px;
            }
        </style><div id="cdxGoogleMapJsContainer"></div><div id="cdxGoogleMap"></div><script type="text/javascript" charset="utf-8">
            require(["jquery", "TYPO3/CMS/Backend/Tabs"], function($) {
                $(function() {
                    var active = false,
                        activate = function () {
                            active = true;
                            window.cdx = window.cdx || {};
                            window.cdx.initGoogleMap = function() {
                                var map = new google.maps.Map(document.getElementById("cdxGoogleMap"), {
                                        center: {lat: %s, lng: %s},
                                        zoom: 13
                                    }),
                                    marker = new google.maps.Marker({
                                        position: {lat: %s, lng: %s},
                                        map: map
                                    });
                            };
                            $("#cdxGoogleMap").append("<script src=\"https://maps.googleapis.com/maps/api/js?key=%s&callback=cdx.initGoogleMap\"></" + "script>");
                        };

                    if ($($(".t3js-tabs .t3js-tabmenu-item.active:first a").attr("href")).find("#cdxGoogleMap").length > 0) {
                        activate();
                    }

                    $(".t3js-tabs").on("shown.bs.tab", function (e) {
                        if (! active && $($(e.target).attr("href")).find("#cdxGoogleMap").length > 0) {
                            activate();
                        }
                    });
                });
            });
        </script>';
    }
}
