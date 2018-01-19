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

use Codappix\CdxFeuserLocations\Hook\DataMapHook;
use Codappix\CdxFeuserLocations\Service\Geocode;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

/**
 * Test exceptions within hook.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
abstract class AbstractDataMapHook extends TestCase
{
    /**
     * @var DataMapHook
     */
    protected $subject;

    /**
     * @var Geocode
     */
    protected $geocodeMock;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilderMock;

    /**
     * @var FlashMessageQueue
     */
    protected $flashMessageQueueMock;

    public function setUp()
    {
        parent::setUp();

        $this->geocodeMock = $this->getMockBuilder(Geocode::class)
            ->setMethods(['getGoogleGeocode'])
            ->getMock();
        $this->flashMessageQueueMock = $this->getMockBuilder(FlashMessageQueue::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryRestrictionMock = $this->getMockBuilder(QueryRestrictionContainerInterface::class)->getMock();
        $this->queryBuilderMock = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryBuilderMock->expects($this->any())
            ->method('getRestrictions')
            ->willReturn($queryRestrictionMock);
        $flashMessageServiceMock = $this->getMockBuilder(FlashMessageService::class)->getMock();
        $flashMessageServiceMock->expects($this->any())
            ->method('getMessageQueueByIdentifier')
            ->willReturn($this->flashMessageQueueMock);
        $connectionPoolMock = $this->getMockBuilder(ConnectionPool::class)->getMock();
        $connectionPoolMock->expects($this->any())
            ->method('getQueryBuilderForTable')
            ->with('fe_users')
            ->willReturn($this->queryBuilderMock);

        $statementMock = $this->getMockBuilder(\Doctrine\DBAL\Driver\Statement::class)
            ->getMock();
        $statementMock->expects($this->any())
            ->method('fetch')
            ->willReturn([
                'address' => 'An der Eickesmühle 38',
                'zip' => '41238',
                'city' => 'Mönchengladbach',
                'country' => 'Germany',
                'uid' => 5,
            ]);

        $methodsToReturnMock = ['select' , 'from', 'where' , 'setParameter'];
        foreach ($methodsToReturnMock as $methodToReturnMock) {
            $this->queryBuilderMock->expects($this->any())
                ->method($methodToReturnMock)
                ->willReturn($this->queryBuilderMock);
        }
        $this->queryBuilderMock->expects($this->any())
            ->method('execute')
            ->willReturn($statementMock);

        $this->subject = new DataMapHook($this->geocodeMock, $connectionPoolMock, $flashMessageServiceMock);
    }
}
