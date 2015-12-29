<?php
namespace WebVision\WvFeuserLocations\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Service for configurations.
 *
 * Provides single point interactions to Configurations.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class Configuration implements SingletonInterface
{
    /**
     * The configuration for extension.
     *
     * Null if not fetched.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Inject configuration via ConfigurationManager.
     *
     * @param ConfigurationManagerInterface $configurationManager
     *
     * @return Configuration
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configuration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'WvFeuserLocations',
            'WvFeuserLocations'
        );

        return $this;
    }

    /**
     * Get configuration. Everything, or a specific part, depending on parameter.
     *
     * Provide dot notation as in fluid.
     *
     * @param string $path Empty to get all, path to get one option.
     *
     * @return string
     */
    public function getConfiguration($path = '')
    {
        if ($path === '') {
            return $this->configuration;
        }

        return ObjectAccess::getPropertyPath($this->configuration, $path);
    }

    /**
     * Get Google API Key.
     *
     * @return string
     */
    public function getGoogleApiKey()
    {
        return $this->getConfiguration('googleApiKey');
    }
}
