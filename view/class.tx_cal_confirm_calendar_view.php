<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2004 
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once (PATH_t3lib.'class.t3lib_svbase.php');
require_once (PATH_tslib."class.tslib_pibase.php");
require_once (t3lib_extMgm :: extPath('cal').'controller/class.tx_cal_calendar.php');
require_once (t3lib_extMgm :: extPath('cal').'controller/class.tx_cal_shared.php');

/**
 * A service which renders a form to create / edit a phpicalendar event.
 *
 * @author Mario Matzulla <mario(at)matzullas.de>
 */
class tx_cal_confirm_calendar_view extends t3lib_svbase {

	var $cObj;
	var $rightsObj;
	var $controller;
	var $shared;
	var $prefixId = 'tx_cal_controller';

	function setCObj(&$cObj){
		$this->cObj = &$cObj;
		$this->controller = &$cObj->conf[$this->prefixId];
		$this->rightsObj = &$this->controller->rightsObj;
		$tx_cal_shared = t3lib_div::makeInstanceClassName('tx_cal_shared');
		$this->shared = new $tx_cal_shared($this->cObj);
	}
	
	/**
	 *  Draws a create calendar form.
	 *  @param		string		Comma separated list of pids.
	 *  @param		object		A location or organizer object to be updated
	 *	@return		string		The HTML output.
	 */
	function drawConfirmCalendar(){	

		
		$page = $this->cObj->fileResource($this->cObj->conf["view."]["calendar."]["confirmCalendarTemplate"]);
		if ($page=="") {
			return "<h3>calendar: no create calendar template file found:</h3>".$this->cObj->conf["view."]["calendar."]["confirmCalendarTemplate"];
		}
		
		$languageArray = array();
		$form 	= $this->cObj->getSubpart($page, "###FORM_START###");
		$languageArray ['l_confirm_calendar'] = $this->shared->lang('l_confirm_calendar');
		if($this->rightsObj->isAllowedToEditCalendarHidden() || $this->rightsObj->isAllowedToCreateCalendarHidden()){
			$form 	.= $this->cObj->getSubpart($page, "###FORM_HIDDEN###");
			if ($this->controller->piVars["hidden"] == "on") {
				$hidden = "true";
			} else {
				$hidden = "false";
			}
			$languageArray['l_hidden'] = $this->shared->lang('l_hidden');
			$languageArray['hidden'] = $this->shared->lang('l_'.$hidden);
		}
		
		if($this->rightsObj->isAllowedToEditCalendarTitle() || $this->rightsObj->isAllowedToCreateCalendarTitle()){
			$form 	.= $this->cObj->getSubpart($page, "###FORM_TITLE###");
			$languageArray['l_title'] = $this->shared->lang('l_event_title');
			$languageArray['title'] = $this->controller->piVars["title"];
		}
		
		if($this->rightsObj->isAllowedToEditCalendarFeUser() || $this->rightsObj->isAllowedToCreateCalendarFeUser()){
			if ($this->controller->piVars["fe_user_id"]) {
				$form 	.= $this->cObj->getSubpart($page, "###FORM_FEUSER###");
				$languageArray['l_fe_user'] = $this->shared->lang('l_fe_user');
				$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery("uid,username","fe_users","uid = ".$this->controller->piVars["fe_user_id"]."");
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
					$languageArray['fe_user_id'] = $row['uid'];
					$languageArray['fe_user'] = $row['username'];
				}
			}
		}
		
		$form 	.= $this->cObj->getSubpart($page, "###FORM_END###");
		$languageArray['uid'] = $this->controller->piVars["uid"];
		$languageArray['type'] = $this->controller->piVars["type"];
		$languageArray['l_submit'] = $this->shared->lang('l_submit');
		$languageArray['l_cancel'] = $this->shared->lang('l_cancel');
		$languageArray['action_url'] = $this->controller->pi_linkTP_keepPIvars_url(array("view"=>"save_calendar"));
		
		$page = $this->shared->replace_tags($languageArray,$form);		
		return $page;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/view/class.tx_cal_confirm_calendar_view.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cal/view/class.tx_cal_confirm_calendar_view.php']);
}
?>