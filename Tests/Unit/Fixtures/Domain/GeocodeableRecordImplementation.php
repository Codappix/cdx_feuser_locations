<?php

namespace Codappix\CdxFeuserLocations\Tests\Unit\Fixtures\Domain;

use Codappix\CdxFeuserLocations\Domain\GenericRecord;
use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use PHPUnit\Framework\Assert;

class GeocodeableRecordImplementation
{
    public function convertArrayToInstance(array $parameters): GeocodeableRecord
    {
        Assert::assertSame(
            'fe_users',
            $parameters['table'],
            'Callback did not receive table.'
        );

        Assert::assertSame(
            [
                'houseNumber' => '123',
                'street' => 'Grand Central',
            ],
            $parameters['record'],
            'Callback did not receive record.'
        );

        Assert::assertSame(
            [
                'userFunc' => GeocodeableRecordImplementation::class . '->convertArrayToInstance',
                'configuration' => [
                    'test' => 1,
                ]
            ],
            $parameters['configuration'],
            'Callback did not receive configuration.'
        );

        return new GenericRecord('', '', '', '', '');
    }
}
