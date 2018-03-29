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
include("lang/".$_SESSION['lang'].".lang.php");
include("../../config.php");
include("../../lib/func.php");
include("AmsAMI.php");
$action=stripslashes_r($_POST['Action']);

if(empty($action)) exit();

$ast = new AmsAMI();
if(!$ast->login($ami_ip,$ami_port,$ami_login,$ami_psw)) {
    $ast->close();
    echo $ast->error;
    return;
}
echo $ast->getFormattedResponse($ast->cmd($action));
if($ast->error) echo $ast->error;
echo "<br><br>";
$ast->close();
?>


