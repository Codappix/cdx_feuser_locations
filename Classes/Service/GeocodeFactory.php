<?php
namespace Codappix\CdxFeuserLocations\Service;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class GeocodeFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configuration = $this->objectManager->get(Configuration::class);
    }

    public function getInstanceForTable(string $table): Geocode
    {
        $configuration = $this->configuration->getServiceMapping();
        if (!isset($configuration[$table])) {
            throw new \InvalidArgumentException('No service configured for table "' . $table . '".', 1558957488 );
        }

        return $this->objectManager->get($configuration[$table]);
    }
}
