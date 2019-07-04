<?php
namespace Codappix\CdxFeuserLocations\Hook;

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

use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use Codappix\CdxFeuserLocations\Domain\GeocodeableRecordFactory;
use Codappix\CdxFeuserLocations\Domain\GeoInformation;
use Codappix\CdxFeuserLocations\Service\Configuration;
use Codappix\CdxFeuserLocations\Service\GeocodeFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Hook to process updated records.
 *
 * Will geocode addresses for fe_users.
 */
class DataMapHook
{
    /**
     * @var string
     */
    private $tableToProcess = '';

    /**
     * Table + Fieldnames combination, that trigger geo decode.
     *
     * @var array
     */
    private $allowedTables = [];

    /**
     * @var GeocodeFactory
     */
    private $geocodeFactory;

    /**
     * @var GeocodeableRecordFactory
     */
    private $geocodeableRecordFactory;

    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var FlashMessageQueue
     */
    private $flashMessageQueue;

    public function __construct(
        ConnectionPool $connectionPool = null,
        GeocodeFactory $geocodeFactory = null,
        GeocodeableRecordFactory $geocodeableRecordFactory = null,
        FlashMessageService $flashMessageService = null
    ) {
        if ($connectionPool === null) {
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        }
        if ($geocodeFactory === null) {
            $geocodeFactory = GeneralUtility::makeInstance(GeocodeFactory::class);
        }
        if ($geocodeableRecordFactory === null) {
            $geocodeableRecordFactory = GeneralUtility::makeInstance(GeocodeableRecordFactory::class);
        }
        if ($flashMessageService === null) {
            $flashMessageService = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(FlashMessageService::class);
        }

        $this->geocodeFactory = $geocodeFactory;
        $this->geocodeableRecordFactory = $geocodeableRecordFactory;
        $this->connectionPool = $connectionPool ;
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();

        $this->allowedTables = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(Configuration::class)->getAllowedTables();
    }

    /**
     * Hook to add latitude and longitude to locations.
     *
     * @param string $action The action to perform, e.g. 'update'.
     * @param string $table The table affected by action, e.g. 'fe_users'.
     * @param int $uid The uid of the record affected by action.
     * @param array $modifiedFields The modified fields of the record.
     *
     * @return void
     */
    public function processDatamap_postProcessFieldArray( // @codingStandardsIgnoreLine
        $action,
        $table,
        $uid,
        array &$modifiedFields
    ) {
        $this->tableToProcess = $table;
        if (!$this->processGeocoding($action, $modifiedFields)) {
            return;
        }

        try {
            $geocoding = $this->geocodeFactory->getInstanceForTable($this->tableToProcess);
            $geoInformation = $geocoding->getGeoinformationForRecord($this->getGeocodableRecord($modifiedFields, $uid));
            $modifiedFields = $this->updateRecord($modifiedFields, $geoInformation);

            $this->flashMessageQueue->addMessage(GeneralUtility::makeInstance(
                FlashMessage::class,
                '',
                sprintf('Updated latitude and longitude of record (%u).', $uid),
                FlashMessage::OK,
                true
            ));
        } catch (\UnexpectedValueException $e) {
            $this->flashMessageQueue->addMessage(GeneralUtility::makeInstance(
                FlashMessage::class,
                $e->getMessage(),
                'Could not geocode record',
                FlashMessage::ERROR,
                true
            ));
        }
    }

    private function updateRecord(array $record, GeoInformation $geoInformation): array
    {
        $latField = $this->allowedTables[$this->tableToProcess]['geoFields']['lat'];
        $lngField = $this->allowedTables[$this->tableToProcess]['geoFields']['lng'];

        $record[$latField] = $geoInformation->lat();
        $record[$lngField] = $geoInformation->lng();

        return $record;
    }

    private function processGeocoding(string $action, array $modifiedFields): bool
    {
        // Do not process if foreign table, unintended action,
        // or fields were changed explicitly.
        if ($this->isTableAllowed($this->tableToProcess) === false || $action !== 'update') {
            return false;
        }

        $latField = $this->allowedTables[$this->tableToProcess]['geoFields']['lat'];
        $lngField = $this->allowedTables[$this->tableToProcess]['geoFields']['lng'];

        // If fields were cleared we force geocode
        if (isset($modifiedFields[$latField]) && $modifiedFields[$latField] === ''
            && isset($modifiedFields[$lngField]) && $modifiedFields[$lngField] === ''
        ) {
            return true;
        }

        // Only process if one of the fields was updated, containing new information.
        $addressFields = GeneralUtility::trimExplode(',', $this->allowedTables[$this->tableToProcess]['addressFields']);
        foreach (array_keys($modifiedFields) as $modifiedFieldName) {
            if (in_array($modifiedFieldName, $addressFields, true)) {
                return true;
            }
        }

        return false;
    }

    private function getGeocodableRecord(array $modifiedFields, int $uid): GeocodeableRecord
    {
        $fullRecord = $this->getQueryBuilder($this->tableToProcess)
            ->select(... GeneralUtility::trimExplode(',', $this->allowedTables[$this->tableToProcess]['addressFields']))
            ->from($this->tableToProcess)
            ->where('uid = :uid')
            ->setParameter('uid', (int) $uid)
            ->execute()
            ->fetch();

        ArrayUtility::mergeRecursiveWithOverrule($fullRecord, $modifiedFields);

        return $this->geocodeableRecordFactory->getInstanceForTable($this->tableToProcess, $fullRecord);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->tableToProcess);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder;
    }

    private function isTableAllowed(): bool
    {
        return in_array($this->tableToProcess, array_keys($this->allowedTables));
    }
}
