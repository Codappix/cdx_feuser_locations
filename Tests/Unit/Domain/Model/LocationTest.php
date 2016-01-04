<?php
namespace WebVision\WvFeuserLocations\Tests\Unit\Domain\Model;

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

use WebVision\WvFeuserLocations\Tests\Unit\TestCase;

/**
 * Test model location.
 *
 * @author Daniel Siepmann <d.siepmann@web-vision.de>
 */
class LocationTest extends TestCase
{
    /**
     * @test
     * @dataProvider jsonFile
     */
    public function modelReturnsMultipleEmails($input, $expectedResult)
    {
        $model = new \WebVision\WvFeuserLocations\Domain\Model\Location;
        $model->setEmail($input);

        $this->assertEquals(
            $input,
            $model->getEmail(),
            'Original input was changed.'
        );

        $this->assertEquals(
            $expectedResult,
            $model->getEmails(),
            'Did not return the provided emails as an array.'
        );
    }

    /**
     * @test
     * @dataProvider jsonFile
     */
    public function modelReturnsMultiplePhoneNumbers($input, $expectedResult)
    {
        $model = new \WebVision\WvFeuserLocations\Domain\Model\Location;
        $model->setTelephone($input);

        $this->assertEquals(
            $input,
            $model->getTelephone(),
            'Original input was changed.'
        );

        $this->assertEquals(
            $expectedResult,
            $model->getPhoneNumbers(),
            'Did not return the provided phone numbers as an array.'
        );
    }
}
