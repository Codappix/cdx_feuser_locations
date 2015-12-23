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
 * Test different circumstances in which the hook should not be executed.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class DataMapHookNotExecutedTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    protected $subject;

    public function setUp()
    {
        $this->subject = new \WebVision\WvFeuserLocations\Hook\DataMapHook;
    }

    /**
     * @test
     */
    public function dontProcessForeignTables()
    {
        $expectedResult = ['title' => 'test'];
        $modifiedFields = $expectedResult;

        $this->subject->processDatamap_postProcessFieldArray(
            'update',
            'pages',
            5,
            $modifiedFields
        );

        $this->assertEquals(
            $expectedResult,
            $modifiedFields,
            'Processing "pages" table modified the fields.'
        );
    }

    /**
     * @test
     */
    public function dontProcessFurtherActions()
    {
        $expectedResult = ['title' => 'test'];
        $modifiedFields = $expectedResult;

        $this->subject->processDatamap_postProcessFieldArray(
            'new',
            'fe_users',
            5,
            $modifiedFields
        );

        $this->assertEquals(
            $expectedResult,
            $modifiedFields,
            'Processing "edit" action modified the fields.'
        );
    }

    /**
     * @test
     */
    public function dontProcessOnUnimportantInformation()
    {
        $expectedResult = ['title' => 'test'];
        $modifiedFields = $expectedResult;

        $this->subject->processDatamap_postProcessFieldArray(
            'update',
            'fe_users',
            5,
            $modifiedFields
        );

        $this->assertEquals(
            $expectedResult,
            $modifiedFields,
            'Processing unimportant fields modified the fields.'
        );
    }
}
