<?php
/*
 * Asterisk Management System - An open source toolkit for Asterisk PBX.
 * See http://www.asterisk.org for more information about
 * the Asterisk project.
 *
 * Copyright (C) 2006 - 2007, West-Web Limited.
 *
 * Nickolay Shestakov <ns@ampex.ru>
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 */
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/Rates/lang/".$lang.".lang.php");
$module_menu = array(array($strRates,"images/rates.gif"),
	   array(array($strRatesList, "javascript:loadModule('','','Rates','RatesList');")));
if($_SESSION['acl']['Rates']['Access']==2) {
		 array_push($module_menu[1],array($strImportRates, "javascript:loadModule('','','Rates','ImportRates');"));
		 array_push($module_menu[1],array($strAddRate, "javascript:loadModule('','','Rates','AddRate');"));
	}
if($_SESSION['acl']['TariffPlans']['Access']) {
	array_push($module_menu[1],array($strTariffPlansList, "javascript:loadModule('','Rates','TariffPlans','TariffPlansList');"));
	array_push($module_menu[1],array($strOperatorsList, "javascript:loadModule('','Rates','Operators','OperatorsList');")); 
}


?>