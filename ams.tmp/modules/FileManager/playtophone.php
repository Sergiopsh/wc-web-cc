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
include("../../modules/System/AmsAMI.php");

$action=$_POST['action'];

$file=stripslashes_r($_POST['file']);
$channel=$_POST['channel'];
$exten=stripslashes_r($_POST['exten']);

$ast = new AmsAMI();
if(!$ast->login($ami_ip,$ami_port,$ami_login,$ami_psw)) {
    $ast->close();
    echo $ast->error;
    return;
}

$channel=$channel."/".$exten;
if($i=strrpos($file,".")) $file=substr($file,0,$i);

$res=$ast->Dial($channel,"__ams__play__file__",null,null,null,null,"play_file=".addslashes($file),null,null,null);
$ast->close();
if($ast->error) echo $ast->error;
exit();
?>


