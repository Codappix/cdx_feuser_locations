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

use TYPO3\CMS\Core\Tests\UnitTestCase;
use WebVision\WvFeuserLocations\Tests\Unit\TestCase;

/**
 * Test different kinds of calls where the hook get's executed.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class DataMapHookExecutedTest extends TestCase
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
            ->setMethods(['getDatabaseConnection', 'getGoogleGeocode'])
            ->getMock();
        $this->subject->expects($this->once())
            ->method('getDatabaseConnection')
            ->will(self::returnValue($dbConnection));
        $this->subject->expects($this->once())
            ->method('getGoogleGeocode')
            ->with('An der Eickesmühle 38 41238 Mönchengladbach Germany')
            ->will(self::returnValue(
                json_encode([
                    'status' => 'OK',
                    'results' => [
                        [
                            'geometry' => [
                                'location' => [
                                    'lat' => 18.23,
                                    'lng' => 1.23,
                                ]
                            ]
                        ]
                    ]
                ])
            ));
    }

    /**
     * @test
     * @dataProvider jsonFile
     *
     * @param array $expectedResult The expected state of $modifiedFields after calling hook.
     * @param array $modifiedFields The modified fields from backend.
     * @param string $action The action performed in backend.
     * @param string $table The table affected by the action.
     * @param int $uid The uid of the record affected by the action.
     */
    public function updateRecordWithGeocode(
        array $expectedResult,
        array $modifiedFields,
        $action,
        $table,
        $uid
    ) {
        $this->subject->processDatamap_postProcessFieldArray(
            $action,
            $table,
            $uid,
            $modifiedFields
        );

        $this->assertEquals(
            $expectedResult,
            $modifiedFields,
            'Did not update modified fields with geocoding information for persistence in DB, triggered with new address.'
        );
    }
}
