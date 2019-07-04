<?php

namespace Codappix\CdxFeuserLocations\Domain;

class GoogleGeoInformation implements GeoInformation
{
    private $lat;
    private $lng;

    public function __construct(array $response)
    {
        $this->lat = $response['results'][0]['geometry']['location']['lat'];
        $this->lng = $response['results'][0]['geometry']['location']['lng'];
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
