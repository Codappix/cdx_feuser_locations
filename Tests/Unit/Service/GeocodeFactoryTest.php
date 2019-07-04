<?php

namespace Codappix\CdxFeuserLocations\Tests\Unit\Service;

use Codappix\CdxFeuserLocations\Service\Configuration;
use Codappix\CdxFeuserLocations\Service\GeocodeFactory;
use Codappix\CdxFeuserLocations\Service\Google;
use Codappix\CdxFeuserLocations\Service\OpenStreetMap;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class GeocodeFactoryTest extends TestCase
{
    /**
     * @var MockObject|Configuration
     */
    private $configurationMock;

    public function setUp()
    {
        parent::setUp();

        $this->configurationMock = $this->getMockBuilder(Configuration::class)->getMock();
        $this->objectManagerMock = $this->getMockBuilder(ObjectManager::class)->getMock();

        GeneralUtility::setSingletonInstance(ObjectManager::class, $this->objectManagerMock);
    }

    public function tearDown()
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider getPossibleServiceCombinations
     */
    public function googleServiceIsReturnsForConfiguredTable(string $table, string $serviceClassName)
    {
        $this->objectManagerMock->method('get')
            ->withConsecutive(
                [Configuration::class],
                [$serviceClassName]
            )
            ->will($this->onConsecutiveCalls($this->configurationMock, new $serviceClassName()));
        $this->configurationMock->method('getServiceMapping')->willReturn([
            'fe_users' => Google::class,
            'tt_address' => OpenStreetMap::class,
        ]);

        $subject = new GeocodeFactory();
        $returnedInstance = $subject->getInstanceForTable($table);
        $this->assertInstanceOf(
            $serviceClassName,
            $returnedInstance,
            'Did not receive ' . $serviceClassName . ' instance as configured.'
        );
    }

    public function getPossibleServiceCombinations(): array
    {
        return [
            'Google Service' => [
                'table' => 'fe_users',
                'serviceClassName' => Google::class,
            ],
            'OpenStreetMapService' => [
                'table' => 'tt_address',
                'serviceClassName' => OpenStreetMap::class,
            ],
        ];
    }

    /**
     * @test
     */
    public function exceptionIsThrownForUnconfiguredTable()
    {
        $this->objectManagerMock->method('get')
            ->with(Configuration::class)
            ->willReturn($this->configurationMock);
        $this->configurationMock->method('getServiceMapping')->willReturn([]);

        $subject = new GeocodeFactory();

        $this->expectException(\InvalidArgumentException::class);
        $subject->getInstanceForTable('unkown');
    }
}
