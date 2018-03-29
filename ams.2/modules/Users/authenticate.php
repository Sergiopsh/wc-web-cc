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

if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');

$current_user_name=$_SESSION['user_name'];
$user = new User(null,$current_user_name);
if(!$user->authenticate($user_password)) {
    unset($_POST);
    header('location: index.php');
    exit();
}

$user->Initialize();
$home = new Home();
$home->getMenu();

$menu_module=$module=$default_module;
$action=$default_action;
$theme=$_SESSION['theme'];
$lang=$_SESSION['lang'];
include("lang/$lang.lang.php");
include("include/modules.php");

?>
