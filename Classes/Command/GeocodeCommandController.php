<?php
namespace Codappix\CdxFeuserLocations\Command;

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

use Codappix\CdxFeuserLocations\Service\Geocode;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class GeocodeCommandController extends CommandController
{
    /**
     * @var Geocode
     */
    protected $geocode;

    /**
     * @var ConnectionPool
     */
    protected $connection;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    public function __construct(Geocode $geocode, ConnectionPool $connection, \TYPO3\CMS\Core\Log\LogManager $logManager)
    {
        $this->geocode = $geocode;
        $this->connection = $connection;
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    /**
     * Geocode all fe_users entries if possible.
     */
    public function allFeUserCommand()
    {
        $this->geocodeFeUsers($this->getFeUsers());
    }

    /**
     * Geocode all fe_users entries with no lat or no lon, if possible.
     */
    public function missingFeUserCommand()
    {
        $this->geocodeFeUsers($this->getFeUsers("lat = '' OR lat is null OR lng = '' OR lng is null"));
    }

    protected function geocodeFeUsers(array $feUsers)
    {
        $this->logger->info(sprintf(
            'Adding geocoding information to %u fe_users.',
            count($feUsers)
        ));

        foreach ($feUsers as $user) {
            $this->logger->info(sprintf(
                'Geocoding fe_user "%s" with address "%s".',
                $user['username'],
                $this->geocode->getAddress($user)
            ));

            try {
                $geoInformation = $this->geocode->getGeoinformationForUser($user);
                $this->connection->getConnectionForTable('fe_users')->update('fe_users', [
                    'lat' => $geoInformation['geometry']['location']['lat'],
                    'lng' => $geoInformation['geometry']['location']['lng'],
                ], ['uid' => $user['uid']]);

                $this->logger->info(sprintf(
                    'Updated fe_user "%s" with lat: %f lng: %f.',
                    $user['username'],
                    $geoInformation['geometry']['location']['lat'],
                    $geoInformation['geometry']['location']['lng']
                ));
            } catch (\UnexpectedValueException $e) {
                $this->logger->warning(sprintf(
                    'Could not geocode fe_user "%s", reason: %s',
                    $user['username'],
                    $e->getMessage()
                ));
            }
        }
    }

    protected function getFeUsers(string $where = '1=1') : array
    {
        return $this->connection
            ->getQueryBuilderForTable('fe_users')
            ->select('*')
            ->where($where)
            ->from('fe_users')
            ->execute()
            ->fetchAll();
    }
}
