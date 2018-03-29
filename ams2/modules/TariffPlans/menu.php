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
include_once("modules/TariffPlans/lang/".$lang.".lang.php");
include_once("modules/Operators/lang/".$lang.".lang.php");

$module_menu = array(array($strTariffPlans,"images/tplans.gif"),
		   array(array($strTariffPlansList, "javascript:loadModule('','TariffPlans','TariffPlans','TariffPlansList');")));
if($_SESSION['acl']['TariffPlans']['Access']==2) array_push($module_menu[1],array($strAddTariffPlan, "javascript:loadModule('','TariffPlans','TariffPlans','AddTariffPlan');"));
	       array_push($module_menu[1], array($strOperatorsList, "javascript:loadModule('','TariffPlans','TariffPlans','OperatorsList');"));
if($_SESSION['acl']['TariffPlans']['Access']==2) array_push($module_menu[1], array($strAddOperator, "javascript:loadModule('','TariffPlans','TariffPlans','AddOperator');"));
?>




