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
include_once("modules/Modules/lang/".$lang.".lang.php");


$module_menu = array(array($strModules,"images/modules.png"),
	       array(array("")));
if($_SESSION['is_admin']) array_push($module_menu[1],array($strModulesList, "javascript:loadModule('','Modules','Modules','ModulesList');"));

?>