<?php
namespace Codappix\CdxFeuserLocations\Tests\Unit\Hook;

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
class DataMapHookExecutedTest extends AbstractDataMapHook
{
    public function setUp()
    {
        parent::setUp();

        $this->geocodeMock->expects($this->once())
            ->method('getGoogleGeocode')
            ->with('An der Eickesmühle 38 41238 Mönchengladbach Germany')
            ->willReturn(json_encode([
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
            ]));
    }

    /**
     * @test
     * @dataProvider jsonFile
     *
     * @param array $expectedResult The expected state after calling hook.
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
