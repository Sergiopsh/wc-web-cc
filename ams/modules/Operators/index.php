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
include_once("modules/Operators/lang/".$lang.".lang.php");
include_once("modules/Operators/Operator.php");

switch ($action) {
    case "EditOperator": 
    case "AddOperator": if($_SESSION['acl']['Operators']['Access']>1) include("modules/Operators/addedit.php"); else include("include/norights.php"); break;
    case "OperatorsList": include("modules/Operators/list.php"); break;
    default: include("include/notvalid.php"); break;

}
?>