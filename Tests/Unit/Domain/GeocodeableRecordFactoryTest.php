<?php

namespace Codappix\CdxFeuserLocations\Tests\Unit\Service;

use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use Codappix\CdxFeuserLocations\Domain\GeocodeableRecordFactory;
use Codappix\CdxFeuserLocations\Service\Configuration;
use Codappix\CdxFeuserLocations\Tests\Unit\Fixtures\Domain\GeocodeableRecordImplementation;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class GeocodeableRecordFactoryTest extends TestCase
{
    /**
     * @var MockObject|Configuration
     */
    private $configurationMock;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock = $this->getMockBuilder(Configuration::class)->getMock();
        $objectManagerMock = $this->getMockBuilder(ObjectManager::class)->getMock();
        $objectManagerMock->method('get')->with(Configuration::class)->willReturn($this->configurationMock);

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManagerMock);
    }

    public function tearDown()
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function configuredCodeIsCalledToCreateInstance()
    {
        $this->configurationMock->method('getRecordMapping')->willReturn([
            'fe_users' => [
                'userFunc' => GeocodeableRecordImplementation::class . '->convertArrayToInstance',
                'configuration' => [
                    'test' => 1,
                ]
            ]
        ]);

        $subject = new GeocodeableRecordFactory();
        $returnedInstance = $subject->getInstanceForTable('fe_users', [
            'houseNumber' => '123',
            'street' => 'Grand Central',
        ]);
        $this->assertInstanceOf(
            GeocodeableRecord::class,
            $returnedInstance,
            'Did not receive an geocodable record.'
        );
    }

    /**
     * @test
     */
    public function exceptionIsThrownForUnconfiguredTable()
    {
        $this->configurationMock->method('getRecordMapping')->willReturn([]);

        $subject = new GeocodeableRecordFactory();

        $this->expectException(\InvalidArgumentException::class);
        $subject->getInstanceForTable('unkown', []);
    }
}
