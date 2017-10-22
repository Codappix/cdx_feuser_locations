<?php

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
 * Extend fe_users with another type to use for locations only.
 * Fields like username and password will be removed.
 */
call_user_func(
    function ($extKey) {
        $table = 'fe_users';
        $coreLanguagePath = 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:';
        $languagePath = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend.xlf:';

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TCA'][$table],
            [
                'ctrl' => [
                    'label_alt' => 'company',
                ],
                'types' => [
                    'Tx_CdxFeuserLocations_Domain_Model_Location' => [
                        'showitem' => '--palette--;;cdx_address,--palette--;;cdx_contact,' .
                            ',--palette--;;cdx_geoinformation' .
                            ',--div--;' . $coreLanguagePath . $table . '.tabs.extended,image,tx_extbase_type' .
                            ',--div--;' . $coreLanguagePath . $table . '.tabs.access,disable,starttime,endtime',
                    ],
                ],
                'palettes' => [
                    'cdx_address' => [
                        'showitem' => 'company' .
                            ',--linebreak--,address' .
                            ',--linebreak--,zip,city,country',
                    ],
                    'cdx_contact' => [
                        'showitem' => 'telephone,fax,--linebreak--,email,www',
                    ],
                    'cdx_geoinformation' => [
                        'showitem' => 'lat,lng,--linebreak--,map',
                    ],
                ],
                'columns' => [
                    'tx_extbase_type' => [
                        'config' => [
                            'items' => [
                                99 => [
                                    0 => $languagePath . 'model.location.type',
                                    1 => 'Tx_CdxFeuserLocations_Domain_Model_Location',
                                ],
                            ],
                        ],
                    ],
                    'lat' => [
                        'label' => $languagePath . 'model.location.lat',
                        'config' => [
                            'type' => 'input',
                            'eval' => 'nospace,trim',
                        ],
                    ],
                    'lng' => [
                        'label' => $languagePath . 'model.location.lng',
                        'config' => [
                            'type' => 'input',
                            'eval' => 'nospace,trim',
                        ],
                    ],
                    'map' => [
                        'label' => $languagePath . 'model.location.map',
                        'config' => [
                            'type' => 'passthrough',
                            'renderType' => 'googleMap',
                            'fields' => [
                                'lat' => 'lat',
                                'lng' => 'lng',
                            ],
                        ],
                    ],
                    'telephone' => [
                        'config' => [
                            'type' => 'text',
                            'wrap' => 'off',
                            'max' => '__UNSET',
                            'size' => '__UNSET',
                            'eval' => '__UNSET',
                        ],
                    ],
                    'email' => [
                        'config' => [
                            'type' => 'text',
                            'wrap' => 'off',
                            'max' => '__UNSET',
                            'size' => '__UNSET',
                            'eval' => '__UNSET',
                        ],
                    ],
                ],
            ]
        );
        $GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] .= ',lat,lng';
    },
    'cdx_feuser_locations'
);
