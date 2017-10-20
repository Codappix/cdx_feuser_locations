<?php
namespace Codappix\CdxFeuserLocations\Form\Element;

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

use Codappix\CdxFeuserLocations\Service\Configuration;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

/**
 * Renders a google map.
 */
class MapElement extends AbstractFormElement
{
    public function render() : array
    {
        $config = $this->data['parameterArray']['fieldConf']['config'];
        $resultArray = $this->initializeResultArray();

        $resultArray['html'] = '';

        $lat = $this->data['databaseRow'][$config['fields']['lat']];
        $lng = $this->data['databaseRow'][$config['fields']['lng']];

        if (!$lat || !$lng) {
            return $resultArray;
        }

        $resultArray['html'] = $this->getInlineCss()
            . $this->getHtml()
            . $this->getInlineJs($lat, $lng)
            ;

        return $resultArray;
    }

    protected function getJsIncludes() : string
    {
        return '<script src=\"https://maps.googleapis.com/maps/api/js?key=' .
             $this->getConfigurationService()->getGoogleApiKey() .
            '&callback=cdx.initGoogleMap\"></" + "script>';
    }

    protected function getInlineCss() : string
    {
        return '<style type="text/css" media="all">
            #cdxGoogleMap {
                height: 250px;
            }
        </style>';
    }

    protected function getHtml() : string
    {
        return '<div id="cdxGoogleMapJsContainer"></div><div id="cdxGoogleMap"></div>';
    }

    protected function getInlineJs(float $lat, float $lng) : string
    {
        return '<script type="text/javascript" charset="utf-8">
            require(["jquery", "TYPO3/CMS/Backend/Tabs"], function($) {
                $(function() {
                    var active = false,
                        activate = function () {
                            active = true;
                            window.cdx = window.cdx || {};
                            window.cdx.initGoogleMap = function() {
                                var map = new google.maps.Map(document.getElementById("cdxGoogleMap"), {
                                        center: {lat: ' . (float) $lat . ', lng: ' . (float) $lng . '},
                                        zoom: 13
                                    }),
                                    marker = new google.maps.Marker({
                                        position: {lat: ' . (float) $lat . ', lng: ' . (float) $lng . '},
                                        map: map
                                    });
                            };
                            $("#cdxGoogleMap").append("' . $this->getJsIncludes() . '");
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

    /**
     * @codeCoverageIgnore Just wraps TYPO3 API.
     */
    protected function getConfigurationService() : Configuration
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')
            ->get(Configuration::class);
    }
}
