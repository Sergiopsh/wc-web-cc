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

$names=stripslashes_r($_POST['names']);
$newrates=stripslashes_r($_POST['rates']);
$accountcode=stripslashes_r($_POST['accountcode']);

$rate = new Rate("",$accountcode);
$i=0;
foreach($names as $name) {
    $rate->update_rate($name,$newrates[$i++]);
}
echo "success";
exit();
?>