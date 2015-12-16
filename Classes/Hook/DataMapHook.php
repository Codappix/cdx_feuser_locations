<?php
namespace WebVision\WvFeuserLocations\Hook;

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
 * Hook to process updated records.
 *
 * Will geocode adresses for fe_users.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
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
     * Hook to add latitude and longitude to locations.
     *
     * @param string $action The action to perform, e.g. 'update'.
     * @param string $table The table affected by action, e.g. 'fe_users'.
     * @param int $uid The uid of the record affected by action.
     * @param array $modifiedFields The modified fields of the record.
     *
     * @return void
     */
    public function processDatamap_postProcessFieldArray($action, $table, $uid, array &$modifiedFields)
    {
        if(!$this->processGeocoding($table, $action, $modifiedFields)) {
            return;
        }

        $geoInformation = $this->getGeoinformation(
            $this->getAddress($modifiedFields, $uid)
        );
        $modifiedFields['lat'] = $geoInformation['geometry']['location']['lat'];
        $modifiedFields['lng'] = $geoInformation['geometry']['location']['lng'];
    }

    /**
     * Check whether to fetch geo information or not.
     *
     * NOTE: Currently allwayd for fe_users, doesn't check the type at the moment.
     *
     * @param string $table
     * @param string $action
     * @param array $modifiedFields
     *
     * @return bool
     */
    protected function processGeocoding($table, $action, array $modifiedFields)
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

    /**
     * Get address of the given record.
     *
     * Merges information from database with modified ones.
     *
     * @param array $modifiedFields Modified fields for overwrite.
     * @param int $uid Uid to fetch record from db.
     *
     * @return string
     */
    protected function getAddress(array $modifiedFields, $uid)
    {
        $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            implode(',', $this->fieldsTriggerUpdate),
            $this->tableToProcess,
            'uid = ' . (int) $uid
        );

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $record,
            $modifiedFields
        );

        return $record['address'] . ' ' . $record['zip'] . ' ' . $record['city'] . ' ' . $record['country'];
    }

    /**
     * Get api key for requests.
     *
     * @return string
     */
    protected function getApiKey()
    {
        return 'AIzaSyCkXIWKSAtB932vyNc4W_pdO9LEXMjbzbo';
    }

    /**
     * Get geo information from Google for given address.
     *
     * @param string $address
     *
     * @return array
     */
    protected function getGeoinformation($address)
    {
        $response = json_decode(
            \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl(
                'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $this->getApiKey()
            ),
            true
        );

        if ($response['status'] === 'OK') {
            return $response['results'][0];
        }

        throw new \Exception(
            'Could not geocode address: "' . $address . '". Return status was: "' . $response['status'] . '".',
            1450279414
        );
    }
}
