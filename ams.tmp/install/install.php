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

if(!$_SESSION['ams_install']) die('Not A Valid Entry');


?>
<table border=0 cellspacing=0 cellpadding=0 width="100%" style="margin: 0px; background-color: #e1ecf1;">
<tr>
<td width="2%">&nbsp;</td>
<td>
<div style="font-size: 19px; color: #345f79; font-style: italic; font-weight: 700;font-family: sans,tahoma;">
<img align="absmiddle" src="images/ast_logo2.gif">

&nbsp;&nbsp;
<?=$label?>
</div>
</td>
</tr>
<tr><td colspan=2 style="background-color: #82aec9;height: 12px;">
</td>
</tr>
</table>
<center>
<div style="margin-top: 15px;margin-bottom: 5px; font-style: italic; font-weight: 500;font-size: 13px; color: #34548f;">
<?=$strInstallation?>
</div>
<div id="install-body" align="center" style="margin-top: 0px; border: 1px solid #cccccc; width: 70%; background-color: #ffffff;">
<div align="left" style="width: 90%; margin-bottom: 20px; font-family: verdana,sans; font-size: 11px;font-style: italic;">
<br>
<?
$_SESSION['db_host']=$db_host;
$_SESSION['db_name']=$db_name;
$_SESSION['db_user']=$db_user;
$_SESSION['db_pass']=$db_pass;
$_SESSION['createdb_flag']=$createdb_flag;
$_SESSION['createuser_flag']=$createuser_flag;
$_SESSION['db_admin_user']=$db_admin_user;
$_SESSION['db_admin_pass']=$db_admin_pass;
$_SESSION['ami_ip']=$ami_ip;
$_SESSION['ami_port']=$ami_port;
$_SESSION['ami_login']=$ami_login;
$_SESSION['ami_psw']=$ami_pass;
$_SESSION['config_dir']=$config_dir;
$_SESSION['tmp_dir']=$tmp_dir;
$_SESSION['admin_user']=$admin_user;
$_SESSION['admin_pass']=$admin_pass;

error_reporting(E_ERROR);
ini_set('track_errors',true);
if($createdb_flag || $createuser_flag) $db=@mysql_connect($db_host,$db_admin_user,$db_admin_pass);    
else $db=@mysql_connect($db_host,$db_user,$db_pass);    

function printError($continue,$errmsg,$phperr="") {
    echo "<font color=red>$errmsg...</font><br>";
    if($phperr) echo "Error: <font color=red>$phperr</font>";
    if($continue) return;
    @unlink("config.php"); 
    echo "<br><br><a href='".$_SERVER['PHP_SELF']."'><-- Back to start page</a>";
    exit();
}

function echoOk(){
    echo "<font color='green'>OK</font><br>";
}

function echoFailed(){
    echo "<font color='red'>FAILED</font><br>";
}


if(!$db) printError(0,$strCantConnectDb,mysql_error());

echo "Creating config file...";

if(!is_file($config_dir."/asterisk.conf")) 
    printError(0,"<br>Not found file asterisk.conf","");

$d=@parse_ini_file($config_dir."/asterisk.conf");
if(empty($d)) {
    printError(1,$strCantOpenFile." $config_dir/asterisk.conf",$php_errormsg);
    $lib_dir="/var/lib/asterisk";
    $log_dir="/var/log/asterisk";
    $spool_dir="/var/spool/asterisk";

}else {
    $lib_dir=trim($d['astvarlibdir'],"> ");
    $log_dir=trim($d['astlogdir'],"> ");
    $spool_dir=trim($d['astspooldir'],"> ");
}

$sound_dir=$lib_dir."/sounds";
$voicemail_dir=$spool_dir."/voicemail";
$monitor_dir=$spool_dir."/monitor";
$faxes_dir=$spool_dir."/faxes";
$www_dir=substr($_SERVER['PHP_SELF'],0,-10);
    
$vars = Array('$db_host','$db_port','$db_user','$db_pass','$db_name','$www_dir',
      '$tmp_dir','$config_dir','$spool_dir','$lib_dir','$monitor_dir','$faxes_dir','$voicemail_dir','$sound_dir',
      '$log_dir','$ami_ip','$ami_port','$ami_login','$ami_psw');

$conffile="config.php";
if(!($cf=@fopen("install/_config.php","r"))) printError(0,$strCantOpenFile." install/_config.php",$php_errormsg);
$i=0;

do { $line[$i++]=fgets($cf);} while(!feof($cf));

@fclose($cf);
if(!($f=@fopen($conffile,"w"))) printError(0,$strCantCreateConfigFile." $conffile",$php_errormsg);
$j=0;

foreach($line as $l) {
	$j++;
	$n=stripos($l,"=");
	if($n===false) $n=1;
	$v=substr($l,0,$n);
	if(!in_array($v,$vars)) {
	    if(!@fwrite($f,$l)) {
		if(!strlen($l) && $i==$j) break;
		printError(0,$strCantWriteFile." $conffile",$php_errormsg);
		break;
	    }		
	    continue;
	}
	$newline=$v."="."\"".${substr($v,1)}."\";\n";
	//echo $newline;
	if(!@fwrite($f,$newline)) printError(0,$strCantWriteFile." $conffile",$php_errormsg);
	    
}
@fclose($f);
echoOk();
echo "Copying file ams_ext.conf to $config_dir...";
if(!(@copy("install/ams_ext.conf","$config_dir/ams_ext.conf"))) printError(1,$strCantCopyFile." ams_ext.conf",$php_errormsg);
else echoOk();


echo "Including ams_ext.conf to extensions.conf...";
$extensions_conf=$config_dir."/extensions.conf";
if(!$lines=@file($extensions_conf)) echoFailed();
elseif(!($extf=@fopen($extensions_conf,"w"))) echoFailed();
else {	$num=count($lines);
    for($i=0; $i < $num ;$i++) {
	if(stripos($lines[$i],"[globals]")!== false) {
	    @fwrite($extf,$lines[$i]); 
	    while($i < $num ) { 
		$i++; $l=trim($lines[$i]);
		if(stripos($lines[$i],"#include \"ams_ext.conf\"")!== false) {
		    $fl_ok = true;
		    @fwrite($extf,$lines[$i]); 
		    break;
		}
		if($l[0]=="[") {
		    $fl_ok = true;
		    @fwrite($extf,"#include \"ams_ext.conf\"\n".$lines[$i]);
		    break;
		
		}else @fwrite($extf,$lines[$i]); 

	    }
	    if(!$fl_ok) @fwrite($extf,"\n#include \"ams_ext.conf\"\n");

	}
	else @fwrite($extf,$lines[$i]);
	
    }
    if($i >= $num) echoOk();
}

@fclose($extf);


$info=@mysql_get_server_info($db);
if($info[0] < 4) printError(0,"MySQL: ".$info." $strNotSupportedVersion","");
if($createuser_flag) {
    echo "Creating DB User $db_user...";
    $q="GRANT USAGE ON *.* TO '$db_user'@'$db_host' IDENTIFIED BY '$db_pass' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0";
    if(!@mysql_query($q,$db)) printError(0,$strDbQueryError,mysql_error());
    else echoOk();

}

if($createdb_flag) {
    echo "Creating DB $db_name...";
    $q="CREATE DATABASE $db_name";
    if(!@mysql_query($q,$db)) printError(0,$strCantCreateDb,mysql_error());
    else echoOk();
}else{
    echo "Checking Database $db_name...";
    if(!@mysql_select_db($db_name,$db)) printError(0,$strDbNotExists,mysql_error());
    else echoOk();
}


if($createdb_flag || $createuser_flag) {
    echo "Grant privileges on database $db_name to user $db_user...";
    $q="GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'$db_host'";
    if(!@mysql_query($q,$db)) printError(0,$strDbQueryError,mysql_error());
    else echoOk();
}


echo "Creating tables in DB $db_name...";
if(stripos($info,"4.0")!==false) $sql="install/mysql40.sql";
else $sql="install/mysql.sql";
$res=execute(which("mysql")." --user=$db_user --password=$db_pass --host=$db_host $db_name < $sql 2>&1",$res);
if(!empty($res)) printError(0,"FAILED",$res);
else echoOk();

echo "Inserting admin user...";
$psw=md5($admin_pass);
$q="TRUNCATE table $db_name.users";
if(!@mysql_query($q,$db)) printError(0,$strDbQueryError,mysql_error($db));
$q="INSERT INTO $db_name.users (user_name, user_password,first_name,is_admin, date_entered,status) 
	VALUES (".quote($admin_user).",".quote($psw).",".quote($admin_user).",1,NOW(),1)";
if(!@mysql_query($q,$db)) printError(0,$strDbQueryError,mysql_error($db));
$q="UPDATE $db_name.users SET id=0 WHERE user_name=".quote($admin_user);
if(!@mysql_query($q,$db)) printError(0,$strDbQueryError,mysql_error($db));
echoOk();

echo "<font color=green>AMS successfully installed !!!</font><br>If nesessary you can edit file config.php to change settings manually.";
session_destroy();
?>
<br><br>
<a href="<?=$_SERVER['PHP_SELF']?>">Start AMS --></a>
</div>
</div>
</center>
</body>
</html>



	

