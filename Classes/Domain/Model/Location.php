<?php
namespace WebVision\WvFeuserLocations\Domain\Model;

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

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extend default FrontendUser to have a unique naming and provide a way to
 * have multiple phone numbers and email addresses per location.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class Location extends FrontendUser
{
    /**
     * Get all phone numbers.
     *
     * @return array
     */
    public function getPhoneNumbers()
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->telephone);
    }

    /**
     * Get all email addresses.
     *
     * @return array
     */
    public function getEmails()
    {
        return GeneralUtility::trimExplode(PHP_EOL, $this->email);
    }
}
