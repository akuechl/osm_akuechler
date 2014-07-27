<?php
/**
 * @version      $Id$
 * @package      Joomla
 * @copyright    Copyright (C) 2011-2014 Ariel Küchler. All rights reserved.
 * @license      MIT License (http://opensource.org/licenses/mit-license copyright information see above) OR GPL-3.0 (http://opensource.org/licenses/gpl-3.0)
 *
 * Example: {osm height='400' lat='50.9918' lon='13.7815' zoom='14' popup='<h3>Kinder- und Jugendbauernhof Nickern e.V.</h3><p><a href="http://www.kinderundjugendbauernhof.de/">Homepage</a></p>'}
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

//if (!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

class plgContentOsm_akuechler extends JPlugin
{
	var $copyright = '&copy; <a href="http://www.openstreetmap.org" target="_blank">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a>';

	var $tileServer = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

	var $maxZoom = 18;
	
	var $cdnCloudflare = 0;
	
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
	
	function plgContentOsm_akuechler( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	function useCdn() {
		return $this->params->get('cdn-cloudflare', $this->cdnCloudflare) != 0;
	}
	
	function useHttpsCdn() {
		return $this->params->get('cdn-cloudflare', $this->cdnCloudflare) == 2;
	}
	
	function onContentPrepare( $context, &$row, &$params, $limitstart=0 ) {
		// fast fail
		$app = JFactory::getApplication();
		if ($app->isAdmin() || JString::strpos( $row->text, '{osm' ) === false ) {
			return true;
		}

		$regex = '/\{osm\s+(.*?)\}/i';

		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $row->text, $matches, PREG_PATTERN_ORDER );

		// Number of plugins
		$count = count( $matches[1] );

		// plugin only processes if there are any instances of the plugin in the text
		if ( $count ) {
			$document = JFactory::getDocument();
			
			if ($this->useCdn()) {
				if ($this->useHttpsCdn()) {
					$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css');
					$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js');
				}else {
					$document->addStyleSheet('http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css');
					$document->addScript('http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js');
				}
			} else {
				$document->addStyleSheet('plugins/content/Osm_akuechler/leaflet/leaflet.css');
				$document->addScript('plugins/content/Osm_akuechler/leaflet/leaflet.js');
			}
			for ($i=0; $i < $count; $i++) {
				$row->text = str_replace($matches[0][$i], $this->_getReplacment($matches[1][$i]), $row->text);
			}
		}
		return true;
	}

	function _getReplacment(&$match) {
		$zoom = $this->_getVariable('zoom', $match);
		$height = $this->_getVariable('height', $match);
		$lat = $this->_getVariable('lat', $match);
		$lon = $this->_getVariable('lon', $match);
		$popup = $this->_getVariable('popup', $match);

		$uuid = uniqid('', false);

		$copyright =  $this->params->get('copyright', $this->copyright);
		$server = $this->params->get('url', $this->tileServer);
		$maxZoom = $this->params->get('maxZoom', $this->maxZoom);
		$baseurl = JURI::base( true );
		$detectRetina = $this->params->get('detect-retina', $this->detectRetina);
		// 
	    $dragging = $this->params->get('dragging', $this->dragging);
		$touchZoom = $this->params->get('touchZoom', $this->touchZoom);
		$scrollWheelZoom = $this->params->get('scrollWheelZoom', $this->scrollWheelZoom);
		$doubleClickZoom = $this->params->get('doubleClickZoom', $this->doubleClickZoom);
		$boxZoom = $this->params->get('boxZoom', $this->boxZoom);
		$tap = $this->params->get('tap', $this->tap);
		$tapTolerance = $this->params->get('tapTolerance', $this->tapTolerance);
		$trackResize = $this->params->get('trackResize', $this->trackResize);
		$worldCopyJump = $this->params->get('worldCopyJump', $this->worldCopyJump);
		$closePopupOnClick = $this->params->get('closePopupOnClick', $this->closePopupOnClick);
		$bounceAtZoomLimits = $this->params->get('bounceAtZoomLimits', $this->bounceAtZoomLimits);
	
		$r = "\n";
		$r .= "<div id='map$uuid' style='height:${height}px'></div>\n";
		$r .= "<script type=\"text/javascript\">var map$uuid = new L.Map('map$uuid', {dragging: $dragging, touchZoom: $touchZoom, scrollWheelZoom: $scrollWheelZoom, doubleClickZoom: $doubleClickZoom, boxZoom: $boxZoom, tap: $tap, tapTolerance: $tapTolerance, trackResize: $trackResize, worldCopyJump: $worldCopyJump, closePopupOnClick: $closePopupOnClick, bounceAtZoomLimits: $bounceAtZoomLimits});";
		$r .= "map$uuid.attributionControl.setPrefix('');";
		$r .= "var baselayer$uuid = new L.TileLayer('$server', {maxZoom: $maxZoom, attribution: '$copyright', detectRetina: $detectRetina});";
		$r .= "var koord$uuid     = new L.LatLng($lat, $lon);";
		$r .= "var marker$uuid    = new L.Marker(koord$uuid);";
		$r .= "map$uuid.addLayer(marker$uuid);";
		$r .= "map$uuid.setView(koord$uuid, $zoom).addLayer(baselayer$uuid);";
		$r .= "marker$uuid.bindPopup('$popup');";
		$r .= "</script>\n";
		return $r;
}

function _getVariable($name, &$match) {
	$regex = '/'.$name.'=(?:\'(.+?)\'|([^\s]+))/i';
	preg_match($regex, $match, $matches);
	return (isset($matches[1])?$matches[1]:'') . (isset($matches[2])?$matches[2]:'');
}
}
?>
