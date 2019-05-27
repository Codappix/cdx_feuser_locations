<?php
namespace Codappix\CdxFeuserLocations\Domain;

/*
 * Copyright (C) 2019  Daniel Siepmann <coding@daniel-siepmann.de>
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

class GenericRecord implements GeocodeableRecord
{
    private $street = '';
    private $houseNumber = '';
    private $zip = '';
    private $city = '';
    private $country = '';

    public function __construct(string $street, string $houseNumber, string $zip, string $city, string $country)
    {
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
