<?php
namespace Codappix\CdxFeuserLocations\Service;

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

use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use Codappix\CdxFeuserLocations\Domain\GeoInformation;
use Codappix\CdxFeuserLocations\Domain\OpenStreetMapGeoInformation;
use function GuzzleHttp\Psr7\build_query;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// TODO: Finish implementation
class OpenStreetMap implements Geocode
{
    public function getGeoinformationForRecord(GeocodeableRecord $record): GeoInformation
    {
        return $this->getGeoinformation($this->getAddress($record));
    }

    public function getAddress(GeocodeableRecord $record) : string
    {
        return http_build_query([
            'street' => trim($record->getHouseNumber() . ' ' . $record->getStreet()),
            'city' => $record->getCity(),
            'country' => $record->getCountry(),
            'postalcode' => $record->getZip(),
        ]);
    }

    private function getGeoinformation(string $address) : OpenStreetMapGeoInformation
    {
        $response = json_decode($this->getGeocode($address), true);

        if ($response === []) {
            throw new \UnexpectedValueException(
                'Could not geocode address: "' . $address . '".',
                1450279414
            );
        }

        return new OpenStreetMapGeoInformation($response);
    }

    private function getGeocode(string $address) : string
    {
        return GeneralUtility::getUrl(
            'https://nominatim.openstreetmap.org/search?' .
            $address . '&format=json&limit=1'
        );
    }
}
