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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Codappix.CdxFeuserLocations',
    'FeuserLocations',
    'Frontend User Locations'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['cdxfeuserlocations_feuserlocations'] = 'select_key';
