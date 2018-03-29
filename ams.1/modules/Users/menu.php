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
include_once("modules/Users/lang/".$lang.".lang.php");

$module_menu = array(array($strUsers,"images/2user.gif"),
		   array(array($strUsersList,"javascript:loadModule('','Users','Users','UsersList');")));

if($_SESSION['acl']['Users']['Access'] ==2) array_push($module_menu[1],array($strAddUser, "javascript:loadModule('','Users','Users','AddUser',0,1);"));
if($_SESSION['acl']['Roles']['Access']) array_push($module_menu[1],array($strRoles, "javascript:loadModule('','Users','Roles','RolesList');","images/userrights.png"));


?>
