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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Codappix.' . $_EXTKEY,
    'FeuserLocations',
    [
        'Location' => 'index'
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY]
    = 'Codappix\CdxFeuserLocations\Hook\DataMapHook';

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TYPO3_CONF_VARS'], [
    'SC_OPTIONS' => [
        'extbase' => [
            'commandControllers' => [
                $_EXTKEY . '-geocode' => \Codappix\CdxFeuserLocations\Command\GeocodeCommandController::class,
            ],
        ],
    ],
]);
