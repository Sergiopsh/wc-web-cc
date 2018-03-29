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
if(!$_SESSION['ams_install']) die('Not a Valid Entry');
include_once("lang/".$_SESSION['install_lang'].".lang.php");
extract($_POST);

$msg="";
if(!is_dir($config_dir)) $msg="<font color=red>$strIsNotDir</font>";
else  {
    if(!is_writable($config_dir)) $msg="<font color=red>$strIsNotWritable</font>";
    else {
	$files=@glob($config_dir.'/*');
	foreach($files as $f) {
	  if(!is_writable($f)) {
	    $fl_w=1;
	    break;
	  }
	}
	if($fl_w) $msg="<font color=red>$strIsNotWritable</font>";	    
	else $msg="<font color=green>$strWritable</font>";	    
	
    }
}
if(!is_dir($temp_dir)) $msg.=",<font color=red>$strIsNotDir</font>";
else  {
    if(!is_writable($temp_dir)) $msg.=",<font color=red>$strIsNotWritable</font>";
    else $msg.=",<font color=green>$strWritable</font>";
    
}
if(!is_writable($www_root)) $msg.=",<font color=red>$strIsNotWritable</font>";
else $msg.=",<font color=green>$strWritable</font>";
    

echo $msg;
exit();
?>

