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

function list_dbs($link) {
    $list = @mysql_list_dbs($link);
    while($row = @mysql_fetch_row($list)) {
	$dbs[] = $row[0];
    }
    return $dbs;
}


extract($_POST);

$msg="&nbsp;";
if($create_user != 'true') {
    $res=@mysql_connect($host,$user,$pass);
    if(!$res) {
	$msg="&nbsp;&nbsp;<font color=red>$strConnectionFailed</font>";

    } else {
	$dbs=list_dbs($res);
	$msg="&nbsp;&nbsp;<font color=green>$strConnectionSuccess</font><br>";
	$info=@mysql_get_server_info($res);
        $msg.="&nbsp;&nbsp;MySQL Server: ".$info;
	if($info[0] < 4) {
	    $msg.="&nbsp;<font color=red>($strNotSupportedVersion)</font>";
	    echo $msg;
	    @mysql_close($res);
	    exit();
	}
    }

    if(empty($admin_user) && $res) {
	if(!in_array($name,$dbs))
	    $msg.="<br><font color=red>$strDbNotExists or Access denied for user $user@$host</font>";
	echo $msg;
	@mysql_close($res);
	exit();
    }

    @mysql_close($res);

}
if(empty($admin_user)) { echo $msg; exit(); }

$res_admin=@mysql_connect($host,$admin_user,$admin_pass);
if(!$res_admin) {
    $msg.=",&nbsp;&nbsp;<font color=red>$strConnectionAdminFailed</font>";
    echo $msg;
    @mysql_close($res_admin);
    exit();
}
$dbs=list_dbs($res_admin);

if($create_user == 'true') {
    $result=@mysql_query("SELECT * FROM mysql.user WHERE User='".mysql_real_escape_string($user)."'",$res_admin);
    if(@mysql_fetch_row($result)) $msg="<font color=red>User $user is exists</font>";

}
$msg.=",&nbsp;&nbsp;<font color=green>$strConnectionAdminSuccess</font>";
if(!$res) {
    $info=@mysql_get_server_info($res_admin);
    $msg.="<br>&nbsp;&nbsp;MySQL Server: ".$info;
    if($info[0] < 4) {
	$msg.="&nbsp;<font color=red>($strNotSupportedVersion)</font>";
    }
}

$sel=in_array($name,$dbs);
if(!$sel && $create_db != 'true') {
	$msg.="&nbsp;<br><font color=red>$strDbNotExists or Access denied for user $user@$host</font>";
    
}elseif($sel && $create_db == 'true') {
	$msg.="&nbsp;<br><font color=red>Database $name is EXISTS</font>";
}

echo $msg;
@mysql_close($res);

exit();

?>

