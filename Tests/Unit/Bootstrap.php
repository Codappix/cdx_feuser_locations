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
 * Provide bootstraping for PHPUnit Tests.
 *
 * @author https://github.com/pagemachine/cors/blob/master/Tests/bootstrap.php
 */

if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    throw new \InvalidArgumentException('Could not find vendor/autoload.php, make sure you ran composer.');
}
define('PATH_thisScript', realpath(__DIR__ . '/../../vendor/typo3/cms/typo3/index.php')); // @codingStandardsIgnoreLine
define('TYPO3_MODE', 'BE');
putenv('TYPO3_CONTEXT=Testing');
call_user_func(
    function ($composerClassLoader, $bootstrap) {
        // Use old setup order for TYPO3 < 7.3
        if (method_exists($bootstrap, 'unregisterClassLoader')) {
            $bootstrap->baseSetup('typo3/');
            $bootstrap->initializeClassLoader();
        } else {
            $bootstrap->initializeClassLoader($composerClassLoader);
            $bootstrap->baseSetup('typo3/');
        }
        // Backwards compatibility with TYPO3 < 7.3
        if (method_exists($bootstrap, 'disableCoreAndClassesCache')) {
            $bootstrap->disableCoreAndClassesCache();
        } else {
            $bootstrap->disableCoreCache();
        }
    },
    require_once __DIR__ . '/../../vendor/autoload.php',
    \TYPO3\CMS\Core\Core\Bootstrap::getInstance()
);
