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

/**
 * Service for configurations.
 *
 * Provides single point interactions to Configurations.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class Configuration
{
    /**
     * Get Google API Key.
     *
     * @return string
     */
    public static function getGoogleApiKey()
    {
        return 'AIzaSyCkXIWKSAtB932vyNc4W_pdO9LEXMjbzbo';
    }
}
