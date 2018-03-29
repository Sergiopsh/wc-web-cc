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
include("../../modules/System/lang/".$_SESSION['lang'].".lang.php");

$file=stripslashes_r($_POST['file']);
$channel=$_POST['channel'];
$exten=$_POST['exten'];
$file_ext=$_POST['ext'];

$ast = new AmsAMI();
if(!$ast->login($ami_ip,$ami_port,$ami_login,$ami_psw)) {
    $ast->close();
    echo $ast->error;
    return;
}

$channel=$channel."/".$exten;
if($i=strrpos($file,".")) {
    $file=substr($file,0,$i);
}
$res=$ast->Dial($channel,"__ams__write__file__",null,null,null,null,"write_file=".$file."|file_ext=".$file_ext,null,null,null);
$ast->close();
if($ast->error) echo $ast->error;
else echo "success";
?>