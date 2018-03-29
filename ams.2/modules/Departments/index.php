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
include_once("modules/Departments/lang/".$lang.".lang.php");
include_once("modules/Departments/Department.php");
include_once("modules/Employees/Employee.php");

switch ($action) {
    case "DepartmentsList": include("modules/Departments/list.php"); break;
    case "AddDepartment": 
    case "EditDepartment": if($_SESSION['acl']['Employees']['Access']>1) include("modules/Departments/addedit.php"); else include("include/norights.php");break;
    default: include("include/notvalid.php"); break;

}
?>