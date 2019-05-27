<?php
namespace Codappix\CdxFeuserLocations\Tests\Unit\Domain\Finishers;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\CdxFeuserLocations\Domain\Finishers\GeocodeFrontendUserFinisher;
use Codappix\CdxFeuserLocations\Service\Geocode;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;
use TYPO3\CMS\Form\Domain\Finishers\FinisherContext;
use TYPO3\CMS\Form\Domain\Finishers\FinisherVariableProvider;

class GeocodeFrontendUserFinisherTest extends TestCase
{
    /**
     * @var GeocodeFrontendUserFinisher
     */
    protected $subject;

    /**
     * @var Geocode
     */
    protected $geocodeMock;

    /**
     * @var FinisherVariableProvider
     */
    protected $finisherVariableProvider;

    /**
     * @var FinisherContext
     */
    protected $contextMock;

    public function setUp()
    {
        parent::setUp();

        $this->geocodeMock = $this->getMockBuilder(Geocode::class)->getMock();
        $this->finisherVariableProvider = new FinisherVariableProvider();
        $this->contextMock = $this->getMockBuilder(FinisherContext::class)->disableOriginalConstructor()->getMock();
        $this->contextMock
            ->method('getFinisherVariableProvider')
            ->willReturn($this->finisherVariableProvider);
        $this->subject = new GeocodeFrontendUserFinisher();
        $this->inject($this->subject, 'geocode', $this->geocodeMock);
    }

    /**
     * @test
     */
    public function dataIsAddedToFinisher()
    {
        $this->geocodeMock
            ->method('getGeoinformationForUser')
            ->willReturn([
                'geometry' => [
                    'location' => [
                        'lat' => '23.2323',
                        'lng' => '32.2323',
                    ],
                ],
            ]);
        $this->subject->execute($this->contextMock);

        $this->assertSame(
            '23.2323',
            $this->finisherVariableProvider->get('Geocode', 'lat'),
            'Latitude was not added to variable provider.'
        );
        $this->assertSame(
            '32.2323',
            $this->finisherVariableProvider->get('Geocode', 'lng'),
            'Longitude was not added to variable provider.'
        );
    }

    /**
     * @test
     */
    public function zeroIsAddedIfExceptionIsThrown()
    {
        $this->geocodeMock
            ->method('getGeoinformationForUser')
            ->will($this->throwException(new \UnexpectedValueException));

        $this->subject->execute($this->contextMock);
        $this->assertSame(
            '0',
            $this->finisherVariableProvider->get('Geocode', 'lat'),
            'Latitude was not set to 0 on variable provider.'
        );
        $this->assertSame(
            '0',
            $this->finisherVariableProvider->get('Geocode', 'lng'),
            'Longitude was not set to 0 on variable provider.'
        );
    }
}
