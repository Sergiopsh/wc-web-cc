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

if(!$_SESSION['ams_entry']) die('Not a Valid entry');
include_once("modules/Departments/lang/".$lang.".lang.php");
include_once("modules/Employees/lang/".$lang.".lang.php");

$module_menu = array(array($strEmployees,"images/employees.gif"),
		   array(array($strEmployeesList, "javascript:loadModule('','Employees','Employees','EmployeesList');")));
		   
if($_SESSION['acl']['Employees']['Access']==2) array_push($module_menu[1],array($strAddEmployee, "javascript:loadModule('','Employees','Employees','AddEmployee');"));
array_push($module_menu[1],array($strDepartmentsList, "javascript:loadModule('','Employees','Departments','DepartmentsList');"));
if($_SESSION['acl']['Employees']['Access']==2) array_push($module_menu[1],array($strAddDepartment, "javascript:loadModule('','Employees','Departments','AddDepartment');"));

?>