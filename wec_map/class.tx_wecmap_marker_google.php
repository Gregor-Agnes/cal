<?php
/***************************************************************
* Copyright notice
*
* (c) 2005 Foundation for Evangelism
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://webempoweredchurch.org) ministry of the Foundation for Evangelism
* (http://evangelize.org). The WEC is developing TYPO3-based
* (http://typo3.org) free software for churches around the world. Our desire
* is to use the Internet to help offer new life through Jesus Christ. Please
* see http://WebEmpoweredChurch.org/Jesus.
*
* You can redistribute this file and/or modify it under the terms of the
* GNU General Public License as published by the Free Software Foundation;
* either version 2 of the License, or (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This file is distributed in the hope that it will be useful for ministry,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the file!
***************************************************************/
/**
 * Plugin 'Map' for the 'wec_map' extension.
 *
 * @author	Web Empowered Church Team <map@webempoweredchurch.org>
 */

define('PATH_tslib', t3lib_extMgm::extPath('cms').'tslib/');
require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Main class for the wec_map extension.  This class sits between the various 
 * frontend plugins and address lookup service to render map data.
 * 
 * @author Web Empowered Church Team <map@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecmap
 */
class tx_wecmap_marker_google extends tx_wecmap_marker {
	var $index;

	var $latitude;
	var $longitude;
	
	var $title;
	var $description;
	var $color;
	var $strokeColor;
	
	function tx_wecmap_marker_google($index, $latitude, $longitude, $title, $description, $color='0xFF0000', $strokeColor='0xFFFFFF') {
		$this->index = $index;
		
		$this->title = $title;
		$this->description = addslashes($description);
		$this->color = $color;
		$this->strokeColor = $strokeColor;
		
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}
	
	function writeJS() {
		return 'map.addOverlay(createMarker(new GLatLng('.$this->latitude.','.$this->longitude.'), icon, "<h1>'.$this->title.'</h1>'.$this->description.'"));';			
		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/wec_map/class.tx_wecmap_marker_google.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/wec_map/class.tx_wecmap_marker_google.php']);
}


?>