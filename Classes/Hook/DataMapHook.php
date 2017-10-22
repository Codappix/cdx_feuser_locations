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
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

        if ($this->geocode === null) {
            $this->geocode = GeneralUtility::makeInstance(Geocode::class);
        }
        $geoInformation = $this->geocode
            ->getGeoinformationForUser($this->getFullUser($modifiedFields, $uid));
        $modifiedFields['lat'] = $geoInformation['geometry']['location']['lat'];
        $modifiedFields['lng'] = $geoInformation['geometry']['location']['lng'];
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
        $fullUser = $this->getDatabaseConnection()
            ->exec_SELECTgetSingleRow(
                implode(',', $this->fieldsTriggerUpdate),
                $this->tableToProcess,
                'uid = ' . (int) $uid
            );

        ArrayUtility::mergeRecursiveWithOverrule(
            $fullUser,
            $modifiedFields
        );

        return $fullUser;
    }
}
