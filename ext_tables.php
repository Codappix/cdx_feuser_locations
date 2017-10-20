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

// Would not exist if called in Configuration folder.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'fe_users',
    'EXT:' . $_EXTKEY . '/Resources/Private/Language/Csh/FeUsers.xlf'
);

call_user_func(
    function ($extKey) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$extKey . '-googleMap'] = [
            'nodeName' => 'googleMap',
            'priority' => 10,
            'class' => \Codappix\CdxFeuserLocations\Form\Element\MapElement::class,
        ];

        foreach ($GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'] as $nodeRegistry) {
            if (isset($nodeRegistry['nodeName']) && strtolower($nodeRegistry['nodeName']) === 'singlefieldcontainer') {
                return;
            }
        }
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][$extKey . '-singleFieldContainer'] = [
            'nodeName' => 'singleFieldContainer',
            'priority' => 10,
            'class' => \Codappix\CdxFeuserLocations\Form\Container\SingleFieldContainer::class,
        ];
    },
    'cdx_feuser_locations'
);
