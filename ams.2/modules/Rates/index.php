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
include_once("modules/TariffPlans/TariffPlan.php");
include_once("modules/CodesDirectory/CodesDirectory.php");
include_once("modules/Rates/Rate.php");


switch ($action) {
    case "EditRate": 
    case "AddRate": if($_SESSION['acl']['Rates']>1) include("modules/Rates/addedit.php"); else include("include/norights.php");break;
    case "ImportRates": if($_SESSION['acl']['Rates']>1) include("modules/Rates/import_rates.php"); else include("include/norights.php"); break;
    case "RatesList": include("modules/Rates/rateslist.php"); break;
    default: include("include/notvalid.php"); 

}
?>