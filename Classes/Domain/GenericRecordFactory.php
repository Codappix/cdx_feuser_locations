<?php

namespace Codappix\CdxFeuserLocations\Domain;

class GenericRecordFactory
{
    public function getInstance(array $parameters): GeocodeableRecord
    {
        $record = $parameters['record'];
        $record = $this->makeSureNecessaryFieldsExist($record);
        $record = $this->mapByConfiguration($record, $parameters['configuration']['mapping'] ?? []);

        return new GenericRecord(
            $record['street'],
            $record['houseNumber'],
            $record['zip'],
            $record['city'],
            $record['country']
        );
    }

    private function makeSureNecessaryFieldsExist(array $record): array
    {
        $expectedFields = ['street', 'houseNumber', 'zip', 'city', 'country'];

        foreach ($expectedFields as $field) {
            if (!isset($record[$field])) {
                $record[$field] = '';
            }
        }

        return $record;
    }

    private function mapByConfiguration(array $record, array $mapping): array
    {
        foreach ($mapping as $property => $field) {
            if (isset($record[$field])) {
                $record[$property] = $record[$field];
            }
        }

        return $record;
    }
}
