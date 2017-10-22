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
    public function geocodedInformationIsAddedToFeUsers()
    {
        $this->importDataSet(__DIR__ . '/../Fixture/GeocodeFeUser.xml');
        $subject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ObjectManager::class)
            ->get(GeocodeCommandController::class);
        $subject->feUserCommand();

        $users = $this->getConnectionPool()
            ->getQueryBuilderForTable('fe_users')
            ->select('*')
            ->from('fe_users')
            ->execute()
            ->fetchAll();

        foreach ($users as $index => $user) {
            $this->assertGreaterThan(0, $user['lat'], 'No latitude was assigned to user ' . $index);
            $this->assertGreaterThan(0, $user['lng'], 'No longitude was assigned to user ' . $index);
        }
    }
}
