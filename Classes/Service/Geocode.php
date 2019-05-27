<?php

namespace Codappix\CdxFeuserLocations\Service;

use Codappix\CdxFeuserLocations\Domain\GeocodeableRecord;
use Codappix\CdxFeuserLocations\Domain\GeoInformation;

interface Geocode
{
    public function getGeoinformationForRecord(GeocodeableRecord $record): GeoInformation;

    public function getAddress(GeocodeableRecord $record): string;
}
