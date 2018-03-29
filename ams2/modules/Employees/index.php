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
include_once("modules/Employees/lang/".$lang.".lang.php");
include_once("modules/Departments/Department.php");
include_once("modules/Employees/Employee.php");

switch ($action) {
    case "EmployeesList": include("modules/Employees/list.php");break;
    case "DepartmentsList": include("modules/Departments/index.php");break;
    case "AddEmployee": 
    case "EditEmployee": if($_SESSION['acl']['Employees']['Access'] ==2) include("modules/Employees/addedit.php"); else include("include/norights.php"); break;
    case "EditDepartment":
    case "AddDepartment": if($_SESSION['acl']['Employees']['Access'] ==2) include("modules/Departments/index.php"); else include("include/norights.php"); break;
    case "ViewEmployee": include("modules/Employees/view.php");break;
    default: include("include/notvalid.php"); break;

}
?>