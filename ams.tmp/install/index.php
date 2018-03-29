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

if(empty($_SESSION['ams_install'])) die('Not A Valid Entry');
$lang=$_POST['install_lang'];
if(!$lang) $lang="en_us";
$_SESSION['install_lang']=$lang;

$label="Installing Asterisk Management System";
$t_size=30;
include_once("install/lang/".$lang.".lang.php");
include_once("lib/func.php");
include_once("lib/sysfunc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>		
		<title><?=$label?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script language="javascript" src="js/prototype.js"></script>
		<style type="text/css" media="screen">
			@import url("install/style.css");
		</style>
		<meta name="MSSmartTagsPreventParsing" content="TRUE">
	</head>
<body style="background-color: #e0e0e0;">
<?
include("install/_config.php");
extract($_POST);
$www_root=$_SERVER['DOCUMENT_ROOT'].substr($_SERVER['PHP_SELF'],0,-10);
if($do_install) {
    include("install/install.php");
    exit();
}

?>

<script language="javascript">
var www_dir='<?=substr($_SERVER['PHP_SELF'],0,-10)?>';

doInstall = function () {
 if(!$F('db_host') || !$F('db_name') || !$F('db_user')) {
    alert('<?=$strDbSettingsMustNotBeEmpty?>');
    return;
 }
 if(($('createdb_flag').checked || $('createuser_flag').checked) && !$F('db_admin_user')) {
	alert('<?=$strDbSettingsMustNotBeEmpty?>');
	return;
 }
 if(!$F('admin_user') || !$F('admin_pass')) {
    alert('<?=$strAdminUserPassMustNotBeEmpty?>');
    return;
 }
 $('f_install').do_install.value=1;
 $('f_install').submit();
}

checkDirectories = function() {
 if(!$F('config_dir') || !$F('tmp_dir') ) {
    alert('<?=$strFieldsMustNotBeEmpty?>');
    return;
 }

 var url=www_dir+"/install/chkdirs.php";

 var pb="config_dir="+$F('config_dir')+"&temp_dir="+$F('tmp_dir')+"&www_root="+$F('www_root');
 new Ajax.Request(url,
	    {postBody: pb,
	     onComplete: function(t) {
	    	    //alert(t.responseText);
		    var a=t.responseText.split(',');
		    $('check_config_dir').innerHTML=a[0];
		    $('check_temp_dir').innerHTML=a[1];
		    $('check_www_dir').innerHTML=a[2];
		    }
	     });

}


checkDbServerConnection = function() {

 if(!$F('db_host') || !$F('db_name') || !$F('db_user')) {
    alert('<?=$strDbSettingsMustNotBeEmpty?>');
    return;
 }
 if(($('createdb_flag').checked || $('createuser_flag').checked) && !$F('db_admin_user')) {
	alert('<?=$strDbSettingsMustNotBeEmpty?>');
	return;
 }

 var url=www_dir+"/install/chkdb.php";
 var pb="host="+$F('db_host')+"&name="+$F('db_name')+"&user="+$F('db_user')+"&pass="+$F('db_pass');
 if($('createdb_flag').checked || $('createuser_flag').checked)
    pb+="&admin_user="+$F('db_admin_user')+"&admin_pass="+$F('db_admin_pass')+"&create_user="+$('createuser_flag').checked+"&create_db="+$('createdb_flag').checked;
 new Ajax.Request(url,
	    {postBody: pb,
	     onComplete: function(t) {

		    var a=t.responseText.split(',');
		    $('check_db_connection').innerHTML=a[0];
		    if($('createdb_flag').checked || $('createuser_flag').checked) {
		       if(a[1]) $('check_admin_db_connection').innerHTML=a[1];
		     }
	         }
	     });

}

checkAstConnection = function() {
 if(!$F('ami_ip') || !$F('ami_port') || !$F('ami_login')) {
    alert('<?=$strAMISettingsMustNotBeEmpty?>');
    return;
 }
 var url=www_dir+"/install/chkastcon.php";
 var pb="host="+$F('ami_ip')+"&port="+$F('ami_port')+"&user="+$F('ami_login')+"&pass="+$F('ami_psw');
 new Ajax.Request(url,
	    {postBody: pb,
	     onComplete: function(t) {
	    	    //alert(t.responseText);
		    $('check_ast_connection').innerHTML=t.responseText;
	         }
	     });


}

</script>
<form name="f_install" id="f_install" method="post">
<input type="hidden" name="do_install" id="do_install" value="">

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
<div align="left" style="width: 90%; margin-bottom: 20px; font-family: verdana,sans; font-size: 11px;">
<br>

<? 
/*
if(count($languages) > 1) {?>
<div width="100%" align="right">
   <?=$strInstallLang;?>
   &nbsp;
   <select id="install_lang">
 <?foreach($languages as $key=>$value) {
     
 ?>
    <option <?if($key==$lang) echo "selected";?> value="<?=$key?>"><?=$value?>

<?}?>
</select>
</div>
<?}
*/
?>

<div class="config-header">
<?=$strDbSettings?>
</div>
<br>

<table border=0 width="100%" class="input-data-tbl" cellspacing=3 cellpadding=0>
<tr><td width="25%" nowrap><font color=red>*</font>
<?=$strDbHost?>&nbsp;
</td><td>
<input type=text name="db_host" id="db_host" size="<?=$t_size?>" value="<?=$_SESSION['db_host'] ? $_SESSION['db_host'] : $db_host?>"
</td>
<td align="right" colspan=4 nowrap>
<?=$strChkDbServerConnection?>&nbsp;
<input type="button" onclick="checkDbServerConnection()" value="<?=$strCheck?>" class="sbutton">
&nbsp;
</td>
</tr>
<tr><td nowrap><font color=red>*</font>
<?=$strDbName?>&nbsp;
</td><td>
<input type=text name="db_name" id="db_name" size="<?=$t_size?>" value="<?=$_SESSION['db_name'] ? $_SESSION['db_name'] : $db_name?>"

</td>
<td colspan=4 rowspan=2 width="50%" valign="top">
<div id="check_db_connection" class="message" align="center">
</div>
</td>
</tr>
<tr><td><font color=red>*</font>
<?=$strDbUser?>&nbsp;
</td><td>
<input type=text name="db_user" id="db_user" size="<?=$t_size?>" value="<?=$_SESSION['db_user'] ? $_SESSION['db_user'] : $db_user?>"

</td></tr>
<tr><td>&nbsp;
<?=$strDbPass?>&nbsp;
</td>
<td>
<input type=text name="db_pass" id="db_pass" size="<?=$t_size?>" value="<?=$_SESSION['db_pass'] ? $_SESSION['db_pass'] : ''?>">
</td>
<td align="right">
&nbsp;<?=$strCreateDb?>&nbsp;
</td><td>
<input type="checkbox" name="createdb_flag" id="createdb_flag" 
  onclick="if(this.checked) $('create_dbuser').show(); else if(!$('createuser_flag').checked) $('create_dbuser').hide();" class="checkbox" <?if($_SESSION['createdb_flag']) echo "checked";?> >
</td>
<td align="right" nowrap>
&nbsp;<?=$strCreateUser?>&nbsp;
</td><td>
<input type="checkbox" name="createuser_flag" id="createuser_flag" 
  onclick="if(this.checked) $('create_dbuser').show(); else if(!$('createdb_flag').checked) $('create_dbuser').hide();" class="checkbox" <?if($_SESSION['createuser_flag']) echo "checked";?> >
</td>

</tr>
</table>

<div id="create_dbuser" style="display: <?if($_SESSION['createdb_flag'] || $_SESSION['createuser_flag']) echo 'inline;'; else echo 'none;';?>">
  <table border=0 width="100%" class="input-data-tbl" cellspacing=3 cellpadding=0 style="border-top: 0;">
  <tr><td width="25%" nowrap><font color=red>*</font>
  <?=$strAdminDbUser?>&nbsp;
  </td><td>
  <input type=text name="db_admin_user" id="db_admin_user" size="<?=$t_size?>" 
  value="<?if ($_SESSION['db_admin_user']) echo $_SESSION['db_admin_user']; else echo $db_user;?>">
  </td>
  <td colspan=2 rowspan=2 width="50%">
  <div id="check_admin_db_connection" class="message" align="center">
  </div>
  </td>
  </tr>
  <tr><td nowrap>&nbsp;
  <?=$strAdminDbPass?>&nbsp;
  </td><td>
  <input type=text name="db_admin_pass" id="db_admin_pass" size="<?=$t_size?>" value="<?=$_SESSION['db_admin_pass'] ? $_SESSION['db_admin_pass'] : ''?>">
  </td>
  </tr>
  </table>
</div>

<br>
<div class="config-header">
<?=$strDirectories?>
</div>
<br>

<table width="100%" class="input-data-tbl" cellspacing=3 cellpadding=0>
<tr><td width="25%"><font color=red>*</font>
<?=$strConfDir?>&nbsp;
</td><td>
<input type=text name="config_dir" id="config_dir" size="<?=$t_size?>" value="<?=$_SESSION['config_dir'] ? $_SESSION['config_dir'] : $config_dir?>">
</td>
<td width="15%">
<div id="check_config_dir" class="message">
</div>
</td>
<td align="right" colspan=2 nowrap>
<?=$strCheckDirectories?>&nbsp;
<input type="button" onclick="checkDirectories()" value="<?=$strCheck?>" class="sbutton">
&nbsp;
</td>

</tr>

<tr><td><font color=red>*</font>
<?=$strTempDir?>&nbsp;
</td><td>
<input type=text name="tmp_dir" id="tmp_dir" size="<?=$t_size?>" value="<?=$_SESSION['tmp_dir'] ? $_SESSION['tmp_dir'] : $tmp_dir;?>">
</td>
<td width="15%">
<div id="check_temp_dir" class="message">
</div>
</td>
</tr>
<tr><td><font color=red>*</font>
<?=$strWWWRoot?>&nbsp;
</td><td>
<input type=text name="www_root" id="www_root" size="<?=$t_size?>" 
 value="<?=$www_root?>" readonly=1 class="i_readonly">
</td>
<td width="15%">
<div id="check_www_dir" class="message">
</div>
</td>
</tr>
</table>
<br>

<div class="config-header">
<?=$strAMISettings?>
</div>
<br>
<table border=0 width="100%" class="input-data-tbl" cellspacing=3 cellpadding=0>
<tr><td width="25%">&nbsp;
<?=$strAMIAddress?>&nbsp;
</td><td>
<input type=text name="ami_ip" id="ami_ip" size="<?=$t_size?>" value="<?=$_SESSION['ami_ip'] ? $_SESSION['ami_ip'] : $ami_ip?>">
</td>
<td align="right" colspan=2>
<?=$strChkAstConnection?>&nbsp;
<input type="button" onclick="checkAstConnection()" value="<?=$strCheck?>" class="sbutton">
&nbsp;
</td>

</tr>
<tr><td>&nbsp;
<?=$strAMIPort?>&nbsp;
</td><td>
<input type=text name="ami_port" id="ami_port" size="<?=$t_size?>" value="<?=$_SESSION['ami_port'] ? $_SESSION['ami_port'] : $ami_port?>"

</td>
<td colspan=2 rowspan=3 width="50%"> 
<div id="check_ast_connection" class="message" align="center">
</div>
</td>

</tr>
<tr><td>&nbsp;
<?=$strAMIUser?>&nbsp;
</td><td>
<input type=text name="ami_login" id="ami_login" size="<?=$t_size?>" value="<?=$_SESSION['ami_login'] ? $_SESSION['ami_login'] : $ami_login?>">

</td></tr>
<tr><td>&nbsp;
<?=$strAMIPassword?>&nbsp;
</td><td>
<input type=text name="ami_psw" id="ami_psw" size="<?=$t_size?>" value="<?=$_SESSION['ami_psw'] ? $_SESSION['ami_psw'] : $ami_psw?>">

</td></tr>
</table>

<br>
<div class="config-header">
<?=$strSystemSettings?>
</div>
<br>

<table width="100%" class="input-data-tbl" cellspacing=3 cellpadding=0>
<tr><td width="25%"><font color=red>*</font>
<?=$strSystemAdminUser?>&nbsp;
</td><td>
<input type=text name="admin_user" id="admin_user" size="<?=$t_size?>" value="<?=$_SESSION['admin_user'] ? $_SESSION['admin_user'] : 'admin'?>">
</td></tr>
<tr><td><font color=red>*</font>
<?=$strSystemAdminPass?>&nbsp;
</td><td>
<input type=text name="admin_pass" id="admin_pass" size="<?=$t_size?>" value="<?=$_SESSION['admin_pass'] ? $_SESSION['admin_pass'] : ''?>">

</td>
</tr>
</table>
<br>
<div align="right" width="100%">
<input type="button" onclick="doInstall()" value="Install" class="sbutton">
&nbsp;&nbsp;
</div>



</div>
</div>
</form>
</center>
</body>
</html>

