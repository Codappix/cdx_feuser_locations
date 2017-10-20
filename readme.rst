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
