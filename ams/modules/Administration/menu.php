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
include_once("modules/Administration/lang/".$lang.".lang.php");

$module_menu = array(array($strAdministrationHeader,"images/admin.gif"),
		     array());

if($_SESSION['acl']['Users']['Access']) array_push($module_menu[1],array($strUsers,"javascript:loadModule('','Users','Users','UsersList');","images/2user.gif"));
if($_SESSION['acl']['Roles']['Access']) array_push($module_menu[1],array($strRoles, "javascript:loadModule('','Roles','Roles','RolesList');","images/userrights.png"));
if($_SESSION['acl']['Currencies']['Access']) array_push($module_menu[1],array($strCurrencies, "javascript:loadModule('','Administration','Currencies','CurrenciesList');","images/currency.gif"));
if($_SESSION['acl']['CodesDirectory']['Access']) array_push($module_menu[1],array($strCodesDirectory, "javascript:loadModule('','CodesDirectory','CodesDirectory','CodesDirectoryList');","images/codes.gif"));
if($_SESSION['acl']['System']['Access']) array_push($module_menu[1],array($strSystem, "javascript:loadModule('','System','System','System');","images/sys.gif"));
if($_SESSION['root']) array_push($module_menu[1],array($strModules, "javascript:loadModule('','Modules','Modules','ModulesList');","images/modules.png"));
	             //array(array("")));
?>
