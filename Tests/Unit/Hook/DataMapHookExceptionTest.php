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

use TYPO3\CMS\Core\Messaging\FlashMessage;

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

        $this->geocodeMock->expects($this->once())
            ->method('getGoogleGeocode')
            ->with('An der Eickesmühle 38 41238 Mönchengladbach Germany')
            ->willReturn(json_encode(['status' => 'Failure']));

        $this->flashMessageQueueMock->expects($this->once())
            ->method('addMessage')
            ->with($this->callback(function ($subject) {
                return $subject->getTitle() === 'Could not geocode record'
                    && $subject->getMessage() === 'Could not geocode address: "An der Eickesmühle 38 41238 Mönchengladbach Germany". Return status was: "Failure".'
                    && $subject->getSeverity() === FlashMessage::ERROR
                    ;
            }));
    }

    /**
     * @test
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
