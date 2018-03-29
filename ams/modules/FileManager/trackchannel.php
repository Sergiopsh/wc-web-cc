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
include("../../config.php");
include("../../modules/System/AmsAMI.php");

$channel=$_POST['channel'];

$ast = new AmsAMI();
if(!$ast->login($ami_ip,$ami_port,$ami_login,$ami_psw)) {
    $ast->close();
    echo "error";
    return;
}
$id=$ast->cmd("core show channels");
$ast->close();

if($ast->error || !$id) {
    echo "error";
    exit();
}
$list=$ast->response[$id];
foreach($list as $l) {
    if(stripos($l,$channel) !== false) {
	if(stripos($l,"ringing")) echo "Ringing...";
	elseif(stripos($l,"record")) echo "<font color='red'>Recording...</font>";
	elseif(stripos($l,"playback")) echo "<font color='green'>Playing...</font>";
	else if(stripos($l,"up")) echo "<font color='green'>Connected...</font>";
	else echo "...";
	exit();
    }

}
echo "completed";
exit();
?>


