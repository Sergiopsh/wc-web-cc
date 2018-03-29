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
include_once("modules/System/lang/".$lang.".lang.php");

$module_menu = array(array($strSystemTools,"images/sys.gif"),
		     array(array($strSystem,"javascript:loadModule('','System','System','System');"),
		           array($strLogFiles, "javascript:loadModule('','System','System','LogFiles');"),
		           array($strAsteriskDB, "javascript:loadModule('','System','AstDB','AstDB');"),
		           array($strAsteriskCLI, "javascript:loadModule('','System','System','AstCli');"),
		           array($strAstMonitor, "javascript:loadModule('','System','AstMonitor','AstMonitor');")));

if($_SESSION['acl']['FileManager']['Access']) array_push($module_menu[1],array($strFileManager, "javascript:loadModule('','System','FileManager','FileManager');"));
if($_SESSION['acl']['ConfigFiles']['Access']) array_push($module_menu[1],array($strConfigFiles, "javascript:loadModule('','System','System','ConfigFiles');"));
?>
