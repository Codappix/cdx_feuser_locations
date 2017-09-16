<?php
namespace Codappix\CdxFeuserLocations\Tca\FieldRendering;

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

use Codappix\CdxFeuserLocations\Service\Configuration;

/**
 * Google Map rendering for TCA field.
 *
 * Will render a Google Map as TCA field.
 */
class MapFieldRendering
{
    /**
     * Render a Map as TCA field.
     *
     * @param array $configuration The configuration of the field.
     *
     * @return string
     */
    public function render(array &$configuration)
    {
        return $this->getInlineJs($configuration['row']['lat'], $configuration['row']['lng']) .
            $this->getInlineCss() .
            $this->getHtml() .
            $this->getJsIncludes();
    }

    /**
     * Get js includes for rendering.
     *
     * @return string
     */
    protected function getJsIncludes()
    {
        return '<script src="https://maps.googleapis.com/maps/api/js?key=' .
             $this->getConfigurationService()->getGoogleApiKey() .
            '&callback=wv.initGoogleMap" async defer></script>';
    }

    /**
     * Get the inline css for rendering.
     *
     * @return string
     */
    protected function getInlineCss()
    {
        return '<style type="text/css" media="all">
            #wvGoogleMap {
                height: 250px;
            }
        </style>';
    }

    /**
     * Get the html for rendering.
     *
     * @return string
     */
    protected function getHtml()
    {
        return '<div id="wvGoogleMap"></div>';
    }

    /**
     * Get inline js to configure Google map and position marker.
     *
     * @param float $lat The latitude of the marker.
     * @param float $lng The longitude of the marker.
     *
     * @return string
     */
    protected function getInlineJs($lat, $lng)
    {
        return '<script type="text/javascript" charset="utf-8">
            window.wv = window.wv || {};
            window.wv.initGoogleMap = function() {
                var map = new google.maps.Map(document.getElementById("wvGoogleMap"), {
                        center: {lat: ' . $lat . ', lng: ' . $lng . '},
                        zoom: 13
                    }),
                    marker = new google.maps.Marker({
                        position: {lat: ' . $lat . ', lng: ' . $lng . '},
                        map: map
                    });
            };
        </script>';
    }

    /**
     * Get configuration service providing configuration.
     *
     * @codeCoverageIgnore Just wraps TYPO3 API.
     *
     * @return \Codappix\CdxFeuserLocations\Service\Configuration
     */
    protected function getConfigurationService()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')
            ->get('Codappix\CdxFeuserLocations\Service\Configuration');
    }
}
