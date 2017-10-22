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

use Codappix\CdxFeuserLocations\Service\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class Geocode
{
    public function getGeoinformationForUser(array $userRecord) : array
    {
        return $this->getGeoinformation($this->getAddress($userRecord));
    }

    public function getAddress(array $record) : string
    {
        return implode(
            ' ',
            [$record['address'], $record['zip'], $record['city'], $record['country']]
        );
    }

    public function getGeoinformation(string $address) : array
    {
        $response = json_decode($this->getGoogleGeocode($address), true);

        if ($response['status'] === 'OK') {
            // Return first geocode result on success.
            return $response['results'][0];
        }

        throw new \UnexpectedValueException(
            'Could not geocode address: "' . $address . '". Return status was: "' . $response['status'] . '".',
            1450279414
        );
    }

    protected function getGoogleGeocode(string $address) : string
    {
        $googleApiKey = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(Configuration::class)
            ->getGoogleApiKey();

        return GeneralUtility::getUrl(
            'https://maps.googleapis.com/maps/api/geocode/json?address=' .
            urlencode($address) . '&key=' . $googleApiKey
        );
    }
}
