About
=====

The extension extends existing `fe_users` records. Another type *location* is
added with different fields.

The set of fields is reduced to a minimum.

Installation
============

Install as usual via composer or extension manager.
In addition to extension, you also need a Google Map API Key. And need to
include the provided static TypoScript template.
For further information about the key, see *Configuration*.

Configuration
=============

Configuration is done via Constants Editor where each field provides further
information.

You need to activate *Google Maps JavaScript API* for displaying google maps,
and *Google Maps Geocoding API* for geocoding of addresses.

Usage
=====

TYPO3 Backend
-------------

Insert a new frontend user, select type *Location* and fill out the fields.
As soon as an address is provided, latitude and longitude will be geocoded via
Google Maps API.
As soon as latitude and longitude are provided, a Google Map is shown in the
edit form.

Command controller
------------------

The extension provides a command controller to update all existing frontend
users with latitude and longitude:

.. code-block:: bash

   typo3cms geocode:feuser

The command controller makes use of logging, so adding a logger to see what's going on is possible.

Form finisher
-------------

To enable geocoding for data submitted by form, enable the provided finisher:

.. code-block:: yaml

   TYPO3:
       CMS:
           Form:
               prototypes:
                   standard:
                       finishersDefinition:
                           GeocodeFrontendUser:
                               implementationClassName: Codappix\CdxFeuserLocations\Domain\Finishers\GeocodeFrontendUserFinisher

Afterwards use the provider in your form and make use of the provided latitude and longitude. If
they could not be fetched, both will be zero.

Due to https://forge.typo3.org/issues/82833 the values are always strings.

.. code-block:: yaml

   finishers:
       -
           identifier: GeocodeFrontendUser
       -
           identifier: SaveToDatabase
           options:
               1:
                   table: 'fe_users'
                   mode: insert
                   databaseColumnMappings:
                       lat:
                           value: '{Geocode.lat}'
                       lng:
                           value: '{Geocode.lng}'

Currently the encoding just works if the submitted form provides the following fields: address, zip,
city and country.

If this does not work for you, please provide a Pull Request.
