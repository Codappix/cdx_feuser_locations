<?php

namespace Codappix\CdxFeuserLocations\Domain;

class OpenStreetMapGeoInformation implements GeoInformation
{
    private $lat;
    private $lng;

    public function __construct(array $response)
    {
        $this->lat = $response[0]['lat'];
        $this->lng = $response[0]['lon'];
    }

    public function lat(): float
    {
        return $this->lat;
    }

    public function lng(): float
    {
        return $this->lng;
    }
}
