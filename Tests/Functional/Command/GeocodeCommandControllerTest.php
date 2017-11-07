<?php
namespace Codappix\CdxFeuserLocations\Tests\Functional\Command;

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

use Codappix\CdxFeuserLocations\Command\GeocodeCommandController;
use Codappix\CdxFeuserLocations\Service\Geocode;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class GeocodeCommandControllerTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/cdx_feuser_locations'];

    /**
     * @test
     */
    public function geocodedInformationIsAddedToAllFeUsers()
    {
        $this->markTestIncomplete('We do not get the test to be working all the time.');

        $this->importDataSet(__DIR__ . '/../Fixture/GeocodeAllFeUser.xml');
        $subject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(GeocodeCommandController::class);
        $subject->allFeUserCommand();

        $users = $this->getConnectionPool()
            ->getQueryBuilderForTable('fe_users')
            ->select('*')
            ->from('fe_users')
            ->execute()
            ->fetchAll();

        foreach ($users as $user) {
            $this->assertGreaterThan(0, (float) $user['lat'], 'No latitude was assigned to user "' . $user['username'] . '".');
            $this->assertGreaterThan(0, (float) $user['lng'], 'No longitude was assigned to user "' . $user['username'] . '".');
        }
    }

    /**
     * @test
     */
    public function geocodedInformationIsAddedToMissingFeUsers()
    {
        $this->markTestIncomplete('We do not get the test to be working all the time.');

        $this->importDataSet(__DIR__ . '/../Fixture/GeocodeMissingFeUser.xml');
        $subject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(GeocodeCommandController::class);
        $subject->missingFeUserCommand();

        $user = $this->getConnectionPool()
            ->getQueryBuilderForTable('fe_users')
            ->select('*')
            ->where('uid = 1')
            ->from('fe_users')
            ->execute()
            ->fetchAll();

        $this->assertSame(51.0, (float) $user[0]['lat'], 'Latitude was changed for user, even if it should not be touched.');
        $this->assertSame(56.0, (float) $user[0]['lng'], 'Longitude changed for user, even if it should not be touched.');

        $user = $this->getConnectionPool()
            ->getQueryBuilderForTable('fe_users')
            ->select('*')
            ->where('uid = 2')
            ->from('fe_users')
            ->execute()
            ->fetchAll();

        $this->assertGreaterThan(0, (float) $user[0]['lat'], 'No latitude was assigned to user.');
        $this->assertGreaterThan(0, (float) $user[0]['lng'], 'No longitude was assigned to user.');
    }
}
