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
 * Test different circumstances in which the hook should not be executed.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class DataMapHookNotExecutedTest extends TestCase
{
    protected $subject;

    public function setUp()
    {
        $this->subject = new \WebVision\WvFeuserLocations\Hook\DataMapHook;
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
    public function dontProcessForeignTables(
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
            'Processed hook which should not happen with the given parameter.'
        );
    }
}
