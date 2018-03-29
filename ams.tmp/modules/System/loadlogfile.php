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

include_once("../../lib/func.php");
include_once("../../config.php");
include_once("lang/".$_SESSION['lang'].".lang.php");
$logfile=stripslashes_r($_POST['logfile']);
$logdate=stripslashes_r($_POST['logdate']);
if($logdate[4]=="0") $logdate[4]=" ";
if(empty($logfile)) exit();

if(!$file=@fopen($logfile,"rb")) {
    echo "<script>ams.showMessage('$strCantOpenFile&nbsp;$logfile','module-note',1);</script>";
    exit();
}

$i=0;
echo "<br>";
$logdate2=date("Y-m-d",strtotime($logdate));

while(!feof($file)) {
    $s=fgets($file); 
    $ds=substr($s,0,12);
    if(stristr($ds,$logdate) || stristr($ds,$logdate2)) {
	$s=str_replace("ERROR","<font color=red>ERROR</font>",$s);
	$s=str_replace("WARNING","<font color=green>WARNING</font>",$s);
	echo "&nbsp;".$s."<br>";
	$i++;
    }
}
echo "<br><br>";
fclose($file);

if(!$i) echo "<script>ams.showMessage('$strNoLogs&nbsp;$logdate','module-note',1); $('logfile-body').hide();</script>";
else echo "<script>$('logfile-body').show();</script>";
exit();
?>

