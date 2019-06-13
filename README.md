osm_akuechler [![Build Status](https://travis-ci.org/admiralsmaster/osm_akuechler.svg?branch=master)](https://travis-ci.org/admiralsmaster/osm_akuechler) [![Codacy Badge](https://api.codacy.com/project/badge/grade/ca55f3e5de3548ffbfb64a63cc1adf93)](https://www.codacy.com/app/github-ariel/osm_akuechler)
=================================================================

A Joomla Plugin for Joomla 2.5 and 3.x to show an Openstreetmap map.
The plugin use the Leaflet javascript library.

1. [Joomla](http://www.joomla.org/)
2. [Open Street Map](http://www.openstreetmap.org/)
3. [Leaflet](http://leafletjs.com/)

The advantage of OpenStreetMap is that no license fees such as Google maps (see [here](https://developers.google.com/maps/faq#usage_calculated)) are necessary. OpenStreetMap (OSM) is a collaborative project to create a free editable map of the world. (see [Wikipedia](https://en.wikipedia.org/wiki/OpenStreetMap))

Example
----------------

An example can be found [here](https://www.kuechler.info/osm).

Usage
---------------

Write in one line, i.e.

```xml
<div class="osm-map" data-lat="51.047382" data-lon="13.734087" data-zoom="17">
  <div class="osm-point" data-lat="51.047382" data-lon="13.734087">
    <h3>
    	Spielplatz in der Innenstadt von Dresden.
  	</h3>
  </div>
  <div class="osm-point" data-lat="51.0466" data-lon="13.733087">
    <h3>
    	Parkplatz.
  	</h3>
    <p>
      Hier könnt ihr parken.
    </p>
  </div>
</div>
```

Name	| Description
------- | -------------
osm-map 	| DIV tag with the card data.
osm-point	| DIV tag with a points, you can add lots of points. Included HTML text is for marker popup.
data-lat 	| Coordinate, Latitude
data-lon 	| Coordinate, Longitude
data-zoom 	| Zoom factor, depends on tiles server, between 1 and 18.
height 	| The height of DIV is the height of the card. Set the height via CSS, default is 400px.


CHANGELOG
--------------
 see [CHANGELOG.md](CHANGELOG.md)

License
------------------

You can choose: [MIT License](http://opensource.org/licenses/mit-license) or [GPL-3.0](http://opensource.org/licenses/gpl-3.0)

Leaflet is provided with the [BSD 2-Clause License](https://github.com/Leaflet/Leaflet/blob/master/LICENSE).


Configuration
-------------------

No idea? Use the default value.

You can configurate the tile server URL. A tile server provides up to date map images. The default value is http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png. Another tile server is i.e. the Wikipedia tile server in Germany: http://{s}.www.toolserver.org/tiles/germany/{z}/{x}/{y}.png. Or you can choose you own tile server. See OpenStreetMap Wiki [here](http://wiki.openstreetmap.org/wiki/TMS) and [here](http://wiki.openstreetmap.org/wiki/Tileserver). The describtion of the placeholders {s}, {x}, {y}, {z} can you find in the Leaflet [documentation](http://leafletjs.com/reference.html#url-template).

You can choose that the leaflet library is loaded not from your server but from Cloudflares [CdnJS](http://cdnjs.com/). This CDN (see [Wikipedia](https://en.wikipedia.org/wiki/Content_delivery_network)) can rise the perfomance of your page and can reduce your webservers load.

