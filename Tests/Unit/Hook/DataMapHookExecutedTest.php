<?php
namespace WebVision\Tests\Unit\Hook;

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
 * Test different kinds of calls where the hook get's executed.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class DataMapHookExecutedTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
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
            ->will(self::returnValue([
                'address' => 'An der Eickesmühle 38',
                'zip' => '41238',
                'city' => 'Mönchengladbach',
                'country' => 'Germany',
            ]));
        $this->subject = $this
            ->getMockBuilder('\WebVision\WvFeuserLocations\Hook\DataMapHook')
            ->setMethods(['getDatabaseConnection', 'getGeoinformation'])
            ->getMock();
        $this->subject->expects($this->once())
            ->method('getDatabaseConnection')
            ->will(self::returnValue($dbConnection));
        $this->subject->expects($this->once())
            ->method('getGeoinformation')
            ->with('An der Eickesmühle 38 41238 Mönchengladbach Germany')
            ->will(self::returnValue([
                'geometry' => [
                    'location' => [
                        'lat' => 18.23,
                        'lng' => 1.23,
                    ]
                ]
            ]));
    }

    /**
     * @test
     */
    public function updateRecordWithGeocodeOnUpdate()
    {
        $expectedResult = ['lat' => 18.23, 'lng' => 1.23];
        $modifiedFields = ['lat' => '', 'lng' => ''];

        $this->subject->processDatamap_postProcessFieldArray(
            'update',
            'fe_users',
            5,
            $modifiedFields
        );

        $this->assertEquals(
            $expectedResult,
            $modifiedFields,
            'Did not update modified fields with geocoding information for persistence in DB.'
        );
    }
}
