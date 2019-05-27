<?php

namespace Codappix\CdxFeuserLocations\Tests\Unit\Domain;

use Codappix\CdxFeuserLocations\Domain\GenericRecordFactory;
use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use Codappix\CdxFeuserLocations\Tests\Unit\TestCase;

class GenericRecordFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider getPossibleCreationCombinations
     */
    public function googleServiceIsReturnsForConfiguredTable(array $parameters, callable $callback)
    {
        $subject = new GenericRecordFactory();
        $returnedInstance = $subject->getInstance($parameters);
        $this->assertTrue(
            $callback($returnedInstance),
            'Generated GenericRecord did not match expectation.'
        );
    }

    public function getPossibleCreationCombinations(): array
    {
        return [
            'All fields cleanly available' => [
                'parameters' => [
                    'table' => 'fe_users',
                    'record' => [
                        'address' => 'Central Street',
                        'number' => '123 A',
                        'zip' => 'AC341',
                        'city' => 'New York',
                        'country' => 'USA',
                    ],
                    'configuration' => [
                        'userFunc' => 'ClassName->methodName',
                        'mapping' => [
                            'street' => 'address',
                            'houseNumber' => 'number',
                            'zip' => 'zip',
                            'city' => 'city',
                            'country' => 'country',
                        ],
                    ],
                ],
                'callback' => function(GeocodeableRecord $record) {
                    return $record->getStreet() === 'Central Street'
                        && $record->getHouseNumber() === '123 A'
                        && $record->getZip() === 'AC341'
                        && $record->getCity() === 'New York'
                        && $record->getCountry() === 'USA';
                }
            ],
            'Some fields missing in configuration' => [
                'parameters' => [
                    'table' => 'fe_users',
                    'record' => [
                        'address' => 'Central Street',
                        'zip' => 'AC341',
                        'number' => '123 A',
                        'city' => 'New York',
                        'country' => 'USA',
                    ],
                    'configuration' => [
                        'userFunc' => 'ClassName->methodName',
                        'mapping' => [
                            'street' => 'address',
                            'zip' => 'zip',
                            'city' => 'city',
                            'country' => 'country',
                        ],
                    ],
                ],
                'callback' => function(GeocodeableRecord $record) {
                    return $record->getStreet() === 'Central Street'
                        && $record->getHouseNumber() === ''
                        && $record->getZip() === 'AC341'
                        && $record->getCity() === 'New York'
                        && $record->getCountry() === 'USA';
                },
            ],
            'Missing configuration' => [
                'parameters' => [
                    'table' => 'fe_users',
                    'record' => [
                        'address' => 'Central Street',
                        'zip' => 'AC341',
                        'number' => '123 A',
                        'city' => 'New York',
                        'country' => 'USA',
                    ],
                    'configuration' => [
                        'userFunc' => 'ClassName->methodName',
                    ],
                ],
                'callback' => function(GeocodeableRecord $record) {
                    return $record->getStreet() === ''
                        && $record->getHouseNumber() === ''
                        && $record->getZip() === 'AC341'
                        && $record->getCity() === 'New York'
                        && $record->getCountry() === 'USA';
                },
            ],
        ];
    }
}
