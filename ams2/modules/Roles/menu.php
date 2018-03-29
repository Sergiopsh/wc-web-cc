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
include_once("modules/Roles/lang/".$lang.".lang.php");

$module_menu = array(array($strRoles,"images/userrights.png"),
		   array(array($strRolesList,"javascript:loadModule('','','Roles','RolesList');")));
if($_SESSION['acl']['Roles']['Access']==2) array_push($module_menu[1],array($strAddRole, "javascript:loadModule('','','Roles','AddRole');"));

?>