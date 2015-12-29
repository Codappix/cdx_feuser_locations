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
 * Fields lice username and password will be removed.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
call_user_func(
    function ($extKey) {
        $coreLanguagePath = 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:';
        $languagePath = 'LLL:EXT:' . $extKey . '/Resources/Private/Language/Backend.xlf:';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
            $extKey,
            'fe_users',
            'categories',
            [
                'label' => $languagePath . 'model.location.businessSegment',
                'fieldConfiguration' => array(
                    'renderType' => 'selectSingle',
                    // Construction as it's the most used business segment
                    'default' => 8,
                    'size' => 1,
                    'foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0)' .
                        ' AND sys_category.parent IN (1,2)' .
                        ' ORDER BY sys_category.title ASC',
                ),

            ]
        );

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TCA']['fe_users'],
            [
                'ctrl' => [
                    'label_alt' => 'company',
                ],
                'types' => [
                    'Tx_WvFeuserLocations_Domain_Model_Location' => [
                        'showitem' => '--palette--;;wv_address,--palette--;;wv_contact,' .
                            ',--palette--;;wv_geoinformation' .
                            ',--div--;' . $coreLanguagePath . 'fe_users.tabs.extended,image,tx_extbase_type' .
                            ',--div--;' . $coreLanguagePath . 'fe_users.tabs.access,disable,starttime,endtime',
                    ],
                ],
                'palettes' => [
                    'wv_address' => [
                        'showitem' => 'company,categories,--linebreak--,address,--linebreak--,zip,city,--linebreak--,country',
                    ],
                    'wv_contact' => [
                        'showitem' => 'telephone,fax,--linebreak--,email,www',
                    ],
                    'wv_geoinformation' => [
                        'showitem' => 'lat,lng,--linebreak--,map',
                    ],
                ],
                'columns' => [
                    'tx_extbase_type' => [
                        'config' => [
                            'items' => [
                                99 => [
                                    0 => $languagePath . 'model.location.type',
                                    1 => 'Tx_WvFeuserLocations_Domain_Model_Location',
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
                            'type' => 'user',
                            'userFunc' => 'WebVision\WvFeuserLocations\Tca\FieldRendering\MapFieldRendering->render',
                        ],
                    ],
                    'telephone' => [
                        'config' => [
                            'type' => 'text',
                        ],
                    ],
                    'email' => [
                        'config' => [
                            'type' => 'text',
                        ],
                    ],
                ],
            ]
        );
        $GLOBALS['TCA']['fe_users']['interface']['showRecordFieldList'] .= ',lat,lng';
    },
    'wv_feuser_locations'
);
