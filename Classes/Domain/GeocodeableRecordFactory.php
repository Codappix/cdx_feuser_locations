<?php

namespace Codappix\CdxFeuserLocations\Domain;


use Codappix\CdxFeuserLocations\Service\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class GeocodeableRecordFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = GeneralUtility::makeInstance(ObjectManager::class)->get(Configuration::class);
    }

    public function getInstanceForTable(string $table, array $record): GeocodeableRecord
    {
        $configuration = $this->configuration->getRecordMapping();
        if (!isset($configuration[$table]['userFunc'])) {
            throw new \InvalidArgumentException('No "userFunc" configured for table "' . $table . '".', 1558957488 );
        }

        $params = [
            'table' => $table,
            'record' => $record,
            'configuration' => $configuration[$table],
        ];
        return GeneralUtility::callUserFunction($configuration[$table]['userFunc'], $params, $this);
    }
}
