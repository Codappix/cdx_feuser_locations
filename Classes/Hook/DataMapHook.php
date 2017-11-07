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

use Codappix\CdxFeuserLocations\Service\Geocode;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
     * Fieldnames that trigger geo decode.
     *
     * @var array
     */
    protected $fieldsTriggerUpdate = ['address', 'city', 'country', 'zip'];

    /**
     * Table to work on. Only this table will be processed.
     *
     * @var string
     */
    protected $tableToProcess = 'fe_users';

    /**
     * @var Geocode
     */
    protected $geocode = null;

    /**
     * @var Connection
     */
    protected $dbConnection = null;

    /**
     * @var FlashMessageQueue
     */
    protected $flashMessageQueue = null;

    public function __construct(
        Geocode $geocode = null,
        ConnectionPool $connectionPool = null,
        FlashMessageService $flashMessageService = null
    ) {
        if ($geocode === null) {
            $geocode = GeneralUtility::makeInstance(Geocode::class);
        }
        if ($connectionPool === null) {
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        }
        if ($flashMessageService === null) {
            $flashMessageService = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(FlashMessageService::class);
        }

        $this->geocode = $geocode;
        $this->dbConnection = $connectionPool->getConnectionForTable($this->tableToProcess);
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
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
        $action, $table, $uid, array &$modifiedFields
    ) {
        if (!$this->processGeocoding($table, $action, $modifiedFields)) {
            return;
        }

        try {
            $geoInformation = $this->geocode->getGeoinformationForUser($this->getFullUser($modifiedFields, $uid));
            $modifiedFields['lat'] = $geoInformation['geometry']['location']['lat'];
            $modifiedFields['lng'] = $geoInformation['geometry']['location']['lng'];
            $this->flashMessageQueue->addMessage(GeneralUtility::makeInstance(
                FlashMessage::class,
                '',
                'Updated latitude and longitude of record.',
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

    protected function processGeocoding(string $table, string $action, array $modifiedFields) : bool
    {
        // Do not process if foreign table, unintended action,
        // or fields were changed explicitly.
        if ($table !== $this->tableToProcess || $action !== 'update') {
            return false;
        }

        // If fields were cleared we force geocode
        if (isset($modifiedFields['lat']) && $modifiedFields['lat'] === ''
            && isset($modifiedFields['lng']) && $modifiedFields['lng'] === ''
        ) {
            return true;
        }

        // Only process if one of the fields was updated, containing new information.
        foreach (array_keys($modifiedFields) as $modifiedFieldName) {
            if (in_array($modifiedFieldName, $this->fieldsTriggerUpdate)) {
                return true;
            }
        }

        return false;
    }

    protected function getFullUser(array $modifiedFields, int $uid) : array
    {
        $fullUser = $this->dbConnection->select(
            $this->fieldsTriggerUpdate,
            $this->tableToProcess,
            ['uid' => (int) $uid]
        )->fetch();

        ArrayUtility::mergeRecursiveWithOverrule($fullUser, $modifiedFields);

        return $fullUser;
    }
}
