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

require_once(t3lib_extMgm::extPath('cal').'model/class.tx_cal_location.php');
require_once(t3lib_extMgm::extPath('cal').'service/class.tx_cal_base_service.php');

/**
 * Base model for the calendar organizer.  Provides basic model functionality that other
 * models can use or override by extending the class.  
 *
 * @author Mario Matzulla <mario@matzullas.de>
 * @package TYPO3
 * @subpackage cal
 */
class tx_cal_location_service extends tx_cal_base_service {
 	
	/**
	 * Looks for an organizer with a given uid on a certain pid-list
	 * @param	integer		$uid		The uid to search for
	 * @param	string		$pidList	The pid-list to search in
	 * @return	object	A tx_cal_organizer_partner object
	 */
	function find($uid, $pidList){
		if($pidList==""){
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_cal_location", " hidden = 0 AND deleted = 0 AND uid=".$uid);
		}else{
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_cal_location", " pid IN (".$pidList.") AND hidden = 0 AND deleted = 0 AND uid=".$uid);
		}
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
			$tx_cal_location = t3lib_div::makeInstanceClassName('tx_cal_location');
			return new $tx_cal_location($this->cObj, $this->rightsObj, $row, $pidList);
		}
	}
	
	/**
	 * Looks for an organizer with a given uid on a certain pid-list
	 * @param	string		$pidList	The pid-list to search in
	 * @return	array	A tx_cal_organizer_partner object array
	 */
	function findAll($pidList){
		$locations = array();
		if($pidList==""){
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_cal_location", " hidden = 0 AND deleted = 0 ");
		}else{
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_cal_location", " pid IN (".$pidList.") AND hidden = 0 AND deleted = 0 ");
		}
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
			$tx_cal_location = t3lib_div::makeInstanceClassName('tx_cal_location');
			$locations[] = new $tx_cal_location($this->cObj, $this->rightsObj, $row, $pidList);
		}
		return $locations;
	}
	
	/**
	 * Search for organizer
	 * @param	object	$cObj	The content object
	 * @param	string	$pidList	The pid-list to search in
	 */
	function search($pidList=''){
		$sw = $this->controller->piVars['query'];
		$organizerArray = array();
		$organizer = t3lib_div::makeInstanceClassName('tx_cal_location');
		if($sw!=""){
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_cal_location", " pid IN (".$pidList.") AND hidden = 0 AND deleted = 0 ".$this->searchWhere($sw));
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
				$organizerArray[] = new $organizer($this->cObj, $this->rightsObj, $row, $pidList);
			}
		}
		return $organizerArray;
	}
	
	/**
	 * Generates a search where clause.
	 *
	 * @param	string		$sw: searchword(s)
	 * @return	string		querypart
	 */
	function searchWhere($sw) {
		$where = $this->cObj->searchWhere($sw, $this->cObj->conf["view."]["search."]["searchLocationFieldList"], 'tx_cal_location');
		return $where;
	}
	
	function updateLocation($uid){

		$insertFields = array("tstamp" => time());
		//TODO: Check if all values are correct
		
		$this->retrievePostData($insertFields);
		
		// Creating DB records
		$table = "tx_cal_location";
		$where = "uid = ".$uid;			
		$result = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$insertFields);
	}
	
	function removeLocation($uid){
		
		if($this->rightsObj->isAllowedToDeleteLocations()){
			$updateFields = array("tstamp" => time(), "deleted" => 1);
			$table = "tx_cal_location";
			$where = "uid = ".$uid;	
			$result = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$updateFields);
		}
	}
	
	function retrievePostData(&$insertFields){
		$hidden = 0;
		if($this->controller->piVars['hidden']=="true" && 
				($this->rightsObj->isAllowedToEditLocationHidden() || $this->rightsObj->isAllowedToCreateLocationHidden()))
			$hidden = 1;
		$insertFields['hidden'] = $hidden;

		if($this->rightsObj->isAllowedToEditLocationName() || $this->rightsObj->isAllowedToCreateLocationName()){
			$insertFields['name'] = $this->controller->piVars['name'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationDescription() || $this->rightsObj->isAllowedToCreateLocationDescription()){
			$insertFields['title'] = $this->controller->piVars['description'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationStreet() || $this->rightsObj->isAllowedToCreateLocationStreet()){
			$insertFields['address'] = $this->controller->piVars['street'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationZip() || $this->rightsObj->isAllowedToCreateLocationZip()){
			$insertFields['zip'] = $this->controller->piVars['zip'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationCity() || $this->rightsObj->isAllowedToCreateLocationCity()){
			$insertFields['city'] = $this->controller->piVars['city'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationPhone() || $this->rightsObj->isAllowedToCreateLocationPhone()){
			$insertFields['phone'] = $this->controller->piVars['phone'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationEmail() || $this->rightsObj->isAllowedToCreateLocationEmail()){
			$insertFields['email'] = $this->controller->piVars['email'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationImage() || $this->rightsObj->isAllowedToCreateLocationImage()){
			$insertFields['image'] = $this->controller->piVars['image'];
		}
		
		if($this->rightsObj->isAllowedToEditLocationLink() || $this->rightsObj->isAllowedToCreateLocationLink()){
			$insertFields['www'] = $this->controller->piVars['link'];
		}

	}
	
	function saveLocation($pid){

		$crdate = time();
		$insertFields = array("pid" => $pid, "tstamp" => $crdate, "crdate" => $crdate);
		//TODO: Check if all values are correct
		
		$hidden = 0;
		if($this->controller->piVars['hidden']=="true")
			$hidden = 1;
		$insertFields['hidden'] = $hidden;
		if($this->controller->piVars['name']!=''){
			$insertFields['name'] = $this->controller->piVars['name'];
		}
		if($this->controller->piVars['description']!=''){
			$insertFields['title'] = $this->controller->piVars['description'];
		}
		if($this->controller->piVars['street']!=''){
			$insertFields['address'] = $this->controller->piVars['street'];
		}
		if($this->controller->piVars['zip']!=''){
			$insertFields['zip'] = $this->controller->piVars['zip'];
		}
		if($this->controller->piVars['city']!=''){
			$insertFields['city'] = $this->controller->piVars['city'];
		}
		if($this->controller->piVars['phone']!=''){
			$insertFields['phone'] = $this->controller->piVars['phone'];
		}
		if($this->controller->piVars['email']!=''){
			$insertFields['email'] = $this->controller->piVars['email'];
		}
		if($this->controller->piVars['image']!=''){
			$insertFields['image'] = $this->controller->piVars['image'];
		}
		if($this->controller->piVars['link']!=''){
			$insertFields['www'] = $this->controller->piVars['link'];
		}
		
		// Creating DB records
		$insertFields['cruser_id'] = $this->rightsObj->getUserId();
		$table = "tx_cal_location";
						
		$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery($table,$insertFields);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/service/class.tx_cal_location_address_service.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/service/class.tx_cal_location_address_service.php']);
}
?>