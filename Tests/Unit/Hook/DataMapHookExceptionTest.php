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
 * Test exceptions within hook.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class DataMapHookExceptionTest extends AbstractDataMapHook
{
    public function setUp()
    {
        parent::setUp();
        $this->subject->expects($this->once())
            ->method('getGoogleGeocode')
            ->with('An der Eickesmühle 38 41238 Mönchengladbach Germany')
            ->will(static::returnValue(
                json_encode([
                    'status' => 'Failure',
                ])
            ));
    }

    /**
     * @test
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessageRegExp #Could not geocode address.* "Failure".#
     * @expectedExceptionCode 1450279414
     */
    public function throwExceptionOnNonSuccessfullReturn()
    {
        $modifiedFields = ['address' => 'An der Eickesmühle 38'];

        $this->subject->processDatamap_postProcessFieldArray(
            'update',
            'fe_users',
            5,
            $modifiedFields
        );
    }
}
