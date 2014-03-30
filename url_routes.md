Custom URL Routes
=================

Requirements
------------

This feature requires more strict requirements than the rest of RepresentMap.

- PHP 5.3 or higher
- [PHP Breeze](https://github.com/whatthejeff/breeze)


Setup
-----

1. Your webserver will need [URL Rewriting
   configured](https://github.com/whatthejeff/breeze/blob/master/INSTALL.md#server-configurations)
1. In ``represent-map/include``, rename ``url_routes_example.php`` to
   ``url_routes.php``
1. Add custom URLs to ``url_routes.php``


Syntax
------

Here is a sample ``url_routes.php`` file:

    <?php
    $url_routes = array(
        'url' => 'latitude,longitude,zoom_level',
        'url2' => 'latitude,longitude'
    );
    ?>

Above, 'url' and 'url2' are the URLs that will be available
(representmap.com/url and representmap.com/url2). RepresentMap will
automatically remove spaces, underscores, dashes, and %20s in the url in order
to figure out which url_route to use. (representmap.com/url2,
representmap.com/url-2, and representmap.com/url_2 are all identical, and will
use 'url2' above. 'Latitude' and 'longitude' should be self-explanatory.
'Zoom_level' is optional, but recommended. Out of the box, the zoom level on
RepresentMap is 11. Setting a custom URL with a different latitude and longitude
but no custom zoom level would result in the map just being shifted. Adding the
zoom level will also show a smaller (or larger) portion of the map.


Warning
-------

Trailing slashes on the custom URL result in strange behavior. Not sure if this
is because of the web server, Breeze, or RepresentMap itself. Use
representmap.com/url, **not** representmap.com/url/.
