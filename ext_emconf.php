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

$EM_CONF[$_EXTKEY] = array (
    'title' => 'Frontenduser locations',
    'description' => 'Extend fe_users with locations.',
    'category' => 'frontend',
    'version' => '1.0.0',
    'state' => 'alpha',
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
    'constraints' => array (
        'depends' => array (
            'typo3' => '7.6',
        ),
    ),
);
