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
include_once("modules/Modules/lang/".$lang.".lang.php");
include_once("lang/".$lang.".lang.php");
include_once("modules/Modules/Module.php");

switch ($action) {
    case "ModulesList": if($_SESSION['root']) include("modules/Modules/moduleslist.php"); else include("include/norights.php"); break;
    default: include("include/notvalid.php"); break;

}
?>