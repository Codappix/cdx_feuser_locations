<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY]
    = Codappix\CdxFeuserLocations\Hook\DataMapHook::class;

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TYPO3_CONF_VARS'], [
    'SC_OPTIONS' => [
        'extbase' => [
            'commandControllers' => [
                $_EXTKEY . '-geocode' => \Codappix\CdxFeuserLocations\Command\GeocodeCommandController::class,
            ],
        ],
    ],
]);
