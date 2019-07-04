<?php
namespace Codappix\CdxFeuserLocations\Domain;


interface GeoInformation
{
    public function lat(): float;
    public function lng(): float;
}
