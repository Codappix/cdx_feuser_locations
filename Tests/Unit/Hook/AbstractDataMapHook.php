<?php
namespace WebVision\WvFeuserLocations\Tests\Unit\Hook;

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

use WebVision\WvFeuserLocations\Tests\Unit\TestCase;

/**
 * Test exceptions within hook.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class AbstractDataMapHook extends TestCase
{
    protected $subject;

    public function setUp()
    {
        $dbConnection = $this->getMock(
            '\TYPO3\CMS\Core\Database\DatabaseConnection',
            ['exec_SELECTgetSingleRow']
        );
        $dbConnection->expects($this->once())
            ->method('exec_SELECTgetSingleRow')
            ->will(static::returnValue([
                'address' => 'An der Eickesmühle 38',
                'zip' => '41238',
                'city' => 'Mönchengladbach',
                'country' => 'Germany',
            ]));
        $this->subject = $this
            ->getMockBuilder('\WebVision\WvFeuserLocations\Hook\DataMapHook')
            ->setMethods(['getDatabaseConnection', 'getGoogleGeocode'])
            ->getMock();
        $this->subject->expects($this->once())
            ->method('getDatabaseConnection')
            ->will(static::returnValue($dbConnection));
    }
}
