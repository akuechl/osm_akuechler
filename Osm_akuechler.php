<?php
/**
 * @package      Joomla
 * @copyright    Copyright (C) 2011-2017 Ariel Küchler. All rights reserved.
 * @license      MIT License (http://opensource.org/licenses/mit-license copyright information see above) OR GPL-3.0 (http://opensource.org/licenses/gpl-3.0)
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JHtml::_ ( 'jquery.framework' );

jimport ( 'joomla.plugin.plugin' );
class plgContentOsm_akuechler extends JPlugin {
    var $copyright = '&copy; <a href="http://www.openstreetmap.org" target="_blank">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a>';
    var $tileServer = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var $maxZoom = 18;
    var $cdnUsage = 0;
    var $detectRetina = true;
    var $dragging = true;
    var $touchZoom = true;
    var $scrollWheelZoom = true;
    var $doubleClickZoom = true;
    var $boxZoom = true;
    var $tap = true;
    var $tapTolerance = 15;
    var $trackResize = true;
    var $worldCopyJump = false;
    var $closePopupOnClick = true;
    var $bounceAtZoomLimits = true;
    var $version = '@leaflet-version@';
       
    function __construct(&$subject, $config) {
        parent::__construct ( $subject, $config );
    }

    function onContentPrepare($context, &$row, &$params, $limitstart = 0) {
        // fast fail
        $app = JFactory::getApplication ();
        if ($app->isAdmin () || (JString::strpos ( $row->text, '{osm' ) === false && JString::strpos ( $row->text, 'osm-map' ) === false)) {
            return true;
        }
        
        // old
        $regexOld = '/\{osm\s+(.*?)\}/i';
        // find all instances of plugin and put in $matches
        preg_match_all ( $regexOld, $row->text, $matches, PREG_PATTERN_ORDER );
        // Number of plugins
        $countOld = count ( $matches [1] );
        
        // new
        $regexNew = "/class\s*=\s*[\"']?[^\"']*osm-map[\"'\s>]/i";
        $resultNew = preg_match_all ( $regexNew, $row->text );
        
        // plugin only processes if there are any instances of the plugin in the text
        if ($countOld || $resultNew) {
            $this->_addConfig ();
            $this->_addLeafletScripts ();
            
            // old
            for($i = 0; $i < $countOld; $i ++) {
                $row->text = str_replace ( $matches [0] [$i], $this->_getReplacmentOld ( $matches [1] [$i] ), $row->text );
            }
            // new
            if ($resultNew) {
                $document = JFactory::getDocument ();
                $document->addScript ( 'plugins/content/Osm_akuechler/js/Osm_akuechler.min.js' );
                $document->addStyleSheet ( 'plugins/content/Osm_akuechler/css/Osm_akuechler.min.css' );
            }
        }
        return true;
    }

    function _addLeafletScripts() {
        $document = JFactory::getDocument ();
        
        if ($this->params->get ( 'cdn-usage', $this->cdnUsage ) == 1) {
            $document->addStyleSheet ( '//cdnjs.cloudflare.com/ajax/libs/leaflet/' . $this->version . '/leaflet.css' );
            $document->addScript ( '//cdnjs.cloudflare.com/ajax/libs/leaflet/' . $this->version . '/leaflet.js' );
        } else if ($this->params->get ( 'cdn-usage', $this->cdnUsage ) == 2) {
            $document->addStyleSheet ( '//cdn.jsdelivr.net/npm/leaflet@' . $this->version . '/dist/leaflet.css' );
            $document->addScript ( '//cdn.jsdelivr.net/npm/leaflet@' . $this->version . '/dist/leaflet.js' );
        } else if ($this->params->get ( 'cdn-usage', $this->cdnUsage ) == 3) {
            $document->addStyleSheet ( 'https://unpkg.com/leaflet@' . $this->version . '/dist/leaflet.css' );
            $document->addScript ( 'https://unpkg.com/leaflet@' . $this->version . '/dist/leaflet.js' );
        } else {
            $document->addStyleSheet ( 'plugins/content/Osm_akuechler/leaflet/leaflet.css' );
            $document->addScript ( 'plugins/content/Osm_akuechler/leaflet/leaflet.js' );
        }
    }

    function _addConfig() {
        $document = JFactory::getDocument ();
        
        $copyright = $this->params->get ( 'copyright', $this->copyright );
        $server = $this->params->get ( 'url', $this->tileServer );
        $maxZoom = $this->params->get ( 'maxZoom', $this->maxZoom );
        $detectRetina = $this->params->get ( 'detect-retina', $this->detectRetina );
        //
        $dragging = $this->params->get ( 'dragging', $this->dragging );
        $touchZoom = $this->params->get ( 'touchZoom', $this->touchZoom );
        $scrollWheelZoom = $this->params->get ( 'scrollWheelZoom', $this->scrollWheelZoom );
        $doubleClickZoom = $this->params->get ( 'doubleClickZoom', $this->doubleClickZoom );
        $boxZoom = $this->params->get ( 'boxZoom', $this->boxZoom );
        $tap = $this->params->get ( 'tap', $this->tap );
        $tapTolerance = $this->params->get ( 'tapTolerance', $this->tapTolerance );
        $trackResize = $this->params->get ( 'trackResize', $this->trackResize );
        $worldCopyJump = $this->params->get ( 'worldCopyJump', $this->worldCopyJump );
        $closePopupOnClick = $this->params->get ( 'closePopupOnClick', $this->closePopupOnClick );
        $bounceAtZoomLimits = $this->params->get ( 'bounceAtZoomLimits', $this->bounceAtZoomLimits );
        
        $document->addScriptDeclaration ( "\n\n// start map config\nvar osmAKuechlerConfig = {\"server\": \"$server\", \"mapConfig\": {\"dragging\": $dragging, \"touchZoom\": $touchZoom, \"scrollWheelZoom\": $scrollWheelZoom, \"doubleClickZoom\": $doubleClickZoom, \"boxZoom\": $boxZoom, \"tap\": $tap, \"tapTolerance\": $tapTolerance,\" trackResize\": $trackResize, \"worldCopyJump\": $worldCopyJump, \"closePopupOnClick\": $closePopupOnClick, \"bounceAtZoomLimits\": $bounceAtZoomLimits}, \"tileConfig\": {\"maxZoom\": $maxZoom, \"attribution\": " . json_encode ( $copyright, JSON_HEX_QUOT ) . ", \"detectRetina\": $detectRetina}};\n// end map config\n\n" );
    }

    function _getReplacmentOld(&$match) {
        $document = JFactory::getDocument ();
        
        $zoom = $this->_getVariableOld ( 'zoom', $match );
        $height = $this->_getVariableOld ( 'height', $match );
        $lat = $this->_getVariableOld ( 'lat', $match );
        $lon = $this->_getVariableOld ( 'lon', $match );
        $popup = $this->_getVariableOld ( 'popup', $match );
        
        $uuid = uniqid ( '', false );
        
        $j = "\n";
        $r = "\n";
        $r .= "<div id='map$uuid' style='height:${height}px'></div>\n";
        $j .= "// start map $uuid\n";
        $j .= "jQuery(document).ready(function() {\n";
        $j .= "var map$uuid = new L.Map('map$uuid', osmAKuechlerConfig.mapConfig);\n";
        $j .= "map$uuid.attributionControl.setPrefix('');\n";
        $j .= "var baselayer$uuid = new L.TileLayer(osmAKuechlerConfig.server, osmAKuechlerConfig.tileConfig);\n";
        $j .= "var koord$uuid     = new L.LatLng($lat, $lon);\n";
        $j .= "var marker$uuid    = new L.Marker(koord$uuid);\n";
        $j .= "map$uuid.addLayer(marker$uuid);\n";
        $j .= "map$uuid.setView(koord$uuid, $zoom).addLayer(baselayer$uuid);\n";
        $j .= "marker$uuid.bindPopup('$popup');\n";
        $j .= "});\n";
        $j .= "// end map $uuid\n";
        $document->addScriptDeclaration ( $j );
        return $r;
    }

    function _getVariableOld($name, &$match) {
        $regex = '/' . $name . '=(?:\'(.+?)\'|([^\s]+))/i';
        preg_match ( $regex, $match, $matches );
        return (isset ( $matches [1] ) ? $matches [1] : '') . (isset ( $matches [2] ) ? $matches [2] : '');
    }
}
?>
