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
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../config.php");
include_once("../../lib/func.php");
include_once("lang/".$_SESSION['lang'].".lang.php");
include_once("Rate.php");

extract(stripslashes_r($_POST));
$newrate=$rate;

$rate = new Rate($name,$accountcode);
$rate->update_rate($name,$newrate);
echo "success";
exit();
?>