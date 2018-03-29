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

if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
$_SESSION['ams_entry']=false;
$theme="Original";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>		
		<title><?=$label?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta http-equiv="Content-Script-Type" content="text/javascript"/>
		<meta name="MSSmartTagsPreventParsing" content="TRUE"/>
		<script type="text/javascript" src="js/prototype.js"></script>
		<script type="text/javascript" src="js/webtoolkit.md5.js"></script>

		<style type="text/css" media="screen">
			@import url("themes/<?=$theme?>/style.css");
			@import url("themes/<?=$theme?>/header.css");
		</style>

	</head>
<body id="login-body" onload="setTimeout('init_login()',50);">
<?
include("themes/$theme/header.php");
$user_name= $_SESSION['user_name'] ? $_SESSION['user_name'] : $_COOKIE['user_name'];


?>
<script type="text/javascript">

function init_login() {
    $('password').value='';
    $('user_name').value='<?=$user_name?>';
}

function check_login() {
    var psw=$('password').value;
    if($('user_name').value && psw) {
	$('user_password').value=MD5(MD5(psw) + '<?=md5(md5(session_id()))?>');
	$('password').value="";
	return true;		
    }
    $('login-box-note').innerHTML='<img align="middle" src="images/note.gif" alt="note" /> <?=htmlspecialchars($strLoginErrorEmpty)?>';
    return false;
};

</script>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style=" margin-top: -4px;">
<tr>
 <td width="100%" style="background-image : url(themes/<?=$theme?>/images/grbg.gif);
    background-repeat: repeat-x; background-position: 100% 0%;
 ">&nbsp;
 </td>
 </tr>
</table>


<div id="login-box">
<div style="width: 100%; height: 18px;background-image : url(themes/<?=$theme?>/images/grbg_r.gif);
    background-repeat: repeat-x; background-position: 0% 100%;">&nbsp;
</div>

<center>
    <div id="login-box-header">
    <img src="images/sec.gif" align="middle" alt="sec" />
    <?=$strWelcomeToAMS?>
    </div>
    <div id="login-box-note">
    
    <?
    if  (isset($_SESSION['login_error'])) {
	//switch ($_SESSION['login_error']) {
	//    case $strLoginErrorName: unset($_SESSION['user_name']); break;
	//    case $strLoginErrorPassword: unset($_SESSION['user_password']); break;
	//}
	$strLoginError=$_SESSION['login_error'];
	echo "<img align='absmiddle' src='images/warning.gif'<font color=red> $strLoginError </font>";
    }
    else echo htmlspecialchars($strEnterName);
    ?>
    
   </div>
</center>
<form id="login_form" onsubmit="return check_login()" name="login_form" method="post" action="index.php">	
<input type="hidden" name="user_password" id="user_password" value=""/>
<input type="hidden" name="module" id="module" value="Users"/>
<input type="hidden" name="action" id="action" value="Authenticate"/>
<table  border="0" width="100%">
   <tr>
    <td width="30%" align="right">
    <?=$strLogin?>&nbsp;</td>
    <td width="35%" align="center">
    <input type="text" style="width: 120px;" size="20" id="user_name" name="user_name" value=""/>
    </td><td width="30%"></td>
    </tr>

    <tr><td align="right">
    <?=$strPassword?>&nbsp;</td>
    <td align="center" width="35%">
    <input type="password"  style="width: 120px;" size="20" id="password" name="password" value=""/>
    </td></tr>
    <tr>
    <td width="30%"></td>
    <td align="center" style="padding-top: 5px;" width="35%"><input type="submit" name="login" value="<?=$strLogin?>" class="sbutton"/>
    </td>
    </tr>
</table>
</form>

</div>
</body>
</html>

