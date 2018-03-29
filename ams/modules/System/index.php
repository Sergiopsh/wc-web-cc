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

switch ($action) {
    case "System": include("modules/System/system.php"); break;
    case "AstCli": include("modules/System/astsystem.php"); break;
    case "ConfigFiles": if($_SESSION['acl']['ConfigFiles']['Access']) include("modules/ConfigFiles/index.php"); else include("include/notvalid.php"); break;
    case "LogFiles": include("modules/System/logfiles.php"); break;
    case "FileManager": if($_SESSION['acl']['FileManager']['Access']) include("modules/System/filemanager.php"); else include("include/notvalid.php"); break;
    case "AstDB": include("modules/System/astdb.php"); break;
    case "AstMonitor": include("modules/AstMonitor/index.php"); break;
    default: include("include/notvalid.php"); break;

}
?>