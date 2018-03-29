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
include_once("modules/TariffPlans/TariffPlan.php");
include_once("modules/Operators/Operator.php");
include_once("modules/Currencies/Currency.php");

switch ($action) {
    case "AddTariffPlan": 
    case "EditTariffPlan": if($_SESSION['acl']['TariffPlans']['Access']>1) include("modules/TariffPlans/addedit.php"); else include("include/norights.php"); break;
    case "TariffPlansList": include("modules/TariffPlans/list.php"); break;
    case "EditOperator": 
    case "AddOperator": if($_SESSION['acl']['TariffPlans']['Access']>1) include("modules/Operators/index.php"); else include("include/norights.php"); break;
    case "OperatorsList": include("modules/Operators/index.php"); break;
    default: include("include/notvalid.php"); break;

}
?>

