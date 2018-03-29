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

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
if(!isset($_SESSION['user_name'])) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
moduleHeader($strEditProfile);

?>
<script>

ms = new Object();

ms.ChangePswd=function() {
     $('change_pswd').value = '1';
}
ms.ChangeTheme=function() {
     $('change_theme').value = $F('u_theme');
     ams.changetheme=1;
     loadModule(1,menu_module,'MySettings','Profile');
}
ms.ChangeLang=function() {
     $('change_lang').value = $F('u_lang');
     ams.changelang=1;
     loadModule(1,menu_module,'MySettings','Profile');
}

</script>
<?
$u_name=$_SESSION['user_name'];
$user = new User($u_id,$u_name);
$currency = new Currency();
if($change_theme || $change_lang) {
    if($change_theme) $_SESSION['theme']=$change_theme;
    if($change_lang)  $_SESSION['lang']=$change_lang;
    $user->update_preferences($change_theme,$u_lang,$u_dateformat,$u_currency);

    ?>
    <form name="ms_reload" id="ms_reload" method="post" action="index.php">
        <input type="hidden" name="action" id="action" value="Profile">
        <input type="hidden" name="module" id="module" value="MySettings">
	<input type="hidden" name="menu_module" id="menu_module" value="">
    </form>
    
    <script>
//        $('ms_reload').menu_module.value=menu_module;
        $('ms_reload').menu_module.value='<?=$default_module?>';
        $('ms_reload').submit();
    </script>
    <?
    exit();
}

if ($u_update) {
    //$user->db->Debug=1;
 if(empty($u_name) OR empty($u_pswd) OR empty($u_conf_pswd)) 
    showMsg("$strMustFillFields <font color=red>*</font>","module-warning",0,"warning.gif");
 else {
  if($u_pswd <> $u_conf_pswd)
    showMsg($strPswdNotEqual,"module-warning",0,"warning.gif");
  else { 
    $user->pswd=$u_pswd;
    $user->first_name=$u_first_name;
    $user->last_name=$u_last_name;
    $user->title=$u_title;
    $user->department=$department;
    $user->email=$u_email;
    $user->address=$u_address;
    $user->phone_work=$u_phone_work;
    $user->phone_office=$u_phone_office;
    $user->phone_home=$u_phone_home;
    $user->phone_mobile=$u_phone_mobile;
    $user->messenger=$u_messenger;
    $user->fax=$u_fax;
    $user->comment=$u_comment;
    $user->theme=$u_theme;
    $user->currency=$u_currency;
    $user->lang=$u_lang;
    $user->dateformat=$u_dateformat;
    $user->setPreferences($u_theme,$u_lang,$u_dateformat,$u_currency);
    $user->name_prev=$u_name_prev;
    $user->change_pswd=$change_pswd;
    unset($change_pswd);
    $user->update_profile();
    showMsg($strUpdateProfileSuccess,'module-warning');
    unset($u_update);
    $_SESSION['theme']=$u_theme;
    $_SESSION['lang']=$u_lang;
    if(!empty($u_currency)) $_SESSION['currency']=$u_currency;
    $_SESSION['dateformat']=$u_dateformat;

  }
 }    
} else {
    $user->get_data();
    $u_id=$user->id;
    $u_first_name=$user->first_name;
    $u_last_name=$user->last_name;
    $u_comment=$user->comment;
    $u_address=$user->address;
    $u_phone_work=$user->phone_work;
    $u_phone_mobile=$user->phone_mobile;
    $u_phone_home=$user->phone_home;
    $u_phone_office=$user->phone_office;
    $department=$user->department;
    if($_SESSION['theme']) $u_theme=$_SESSION['theme'];
    else $u_theme=$default_theme;
    if($_SESSION['currency']) $u_currency=$_SESSION['currency'];
    else $u_currency=$default_currency;
    if($_SESSION['lang']) $u_lang=$_SESSION['lang'];
    else $u_lang=$default_lang;
    if($_SESSION['dateformat']) $u_dateformat=$_SESSION['dateformat'];
    else $u_dateformat=$default_dateformat;
    $u_fax=$user->fax;
    $u_email=$user->email;
    $u_messenger=$user->messenger;
    $u_title=$user->title;
    $u_pswd=$u_conf_pswd="********";
    
}

?>
<form name="module_form" id="module_form" method="post">
<input type="hidden" name="u_id" id="u_id" value="<?=$u_id?>">
<input type="hidden" name="change_pswd" id="change_pswd" value="<?=$change_pswd?>">
<input type="hidden" name="change_theme" id="change_theme" value="">
<input type="hidden" name="change_lang" id="change_lang" value="">
<input type="hidden" name="u_update" id="u_update" value="">
<?
$tbl = new DataTbl();
$p=$tbl->addElement("u_name","text",$strNameUser,1,$u_name,"","i_readonly");
$p->setOptions(25,1);$p->readonly=true;
$p=$tbl->addElement("u_first_name","text",$strFirstName,1,$u_first_name);
$p->setOptions(25);
$p=$tbl->addElement("","button","",1,$strUpdate);
$p->align2="right"; $p->action="$('module_form').u_update.value=1;loadModule(1,menu_module,'MySettings','Profile');";
$p=$tbl->addElement("u_pswd","text",$strPassword,2,$u_pswd);
$p->setOptions(25,1); $p->type="password"; $p->action=" onfocus=\"$('u_pswd').value='';\" onchange=\"$('change_pswd').value = '1';\" "; 
$p=$tbl->addElement("u_last_name","text",$strLastName,2,$u_last_name);
$p->setOptions(25);
$p=$tbl->addElement("u_conf_pswd","text",$strConfirmPassword,3,$u_conf_pswd);
$p->setOptions(25,1); $p->type="password"; $p->action=" onfocus=\"$('u_conf_pswd').value='';\""; 
$tbl->cols=array("14%","25%","12%");
array_push($tbl->filters,array("id" => "u_email","type"=> "email","msg" => $strFieldMustBeValidEmail));
$tbl->show(); 

echo "<br>";

$tbl = new DataTbl();
$p=$tbl->addElement("u_title","text",$strTitle,1,$u_title);
$p->setOptions(25);
$p=$tbl->addElement("u_phone_work","text",$strPhoneWork,1,$u_phone_work);
$p->setOptions(25);
$p=$tbl->addElement("u_phone_mobile","text",$strPhoneMobile,1,$u_phone_mobile);
$p->setOptions(25);
$p=$tbl->addElement("department","text",$strDepartment,2,$department);
$p->setOptions(25,0,"","","modules/Departments/filldeps.php");
$p=$tbl->addElement("u_phone_office","text",$strPhoneOffice,2,$u_phone_office);
$p->setOptions(25);
$p=$tbl->addElement("u_phone_home","text",$strPhoneHome,2,$u_phone_home);
$p->setOptions(25);
$p=$tbl->addElement("u_email","text",$strEmail,3,$u_email);
$p->setOptions(25);
$p=$tbl->addElement("u_messenger","text",$strMessenger,3,$u_messenger);
$p->setOptions(25);
$p=$tbl->addElement("u_fax","text",$strFax,3,$u_fax);
$p->setOptions(25);
$p=$tbl->addElement("u_address","textarea",$strAddress,4,$u_address);
$p->setOptions(30,3); $p->valign1="top";
$p=$tbl->addElement("u_comment","textarea",$strComment,4,$u_comment);
$p->setOptions(30,3);$p->colspan=2; $p->valign1="top";
$tbl->cols=array("14%","25%","12%");
$tbl->show();

echo "<br>";

$tbl = new DataTbl();
$p=$tbl->addElement("u_theme","select",$strTheme,1,$u_theme);
$p->type="theme"; $p->action="ms.ChangeTheme()";

$p=$tbl->addElement("u_currency","select",$strCurrency,1,$u_currency);
$p->type="currency"; $p->cur=$currency;
$p=$tbl->addElement("u_lang","select",$strLang,1,$u_lang);
$p->type="lang"; $p->action="ms.ChangeLang()";
$p=$tbl->addElement("u_dateformat","select",$strDateFormat,1,$u_dateformat);
$p->type="dateformat";

$tbl->show();
?>
</form>
<br>

<script>
ms.tuneHM = function(id) {

 if($('ms-tune-home').style.display=='none') {
    $('ms-tune-home').style.display='';
    $('test').src="images/minus2.gif";

 }else {    
    $('ms-tune-home').hide();
    $('test').src="images/plus2.gif";
 }
}
</script>




<div id="frame-module-header2" nowrap>
<?=$strTuneHomeMenu?>&nbsp;
<a href="javascript:ms.tuneHM(this);">
<img id="test" align="absmiddle" src="images/plus2.gif" onclick="">
</a>
</div>

<div id="ms-tune-home" style="display: none;">
<?
include_once("modules/Home/tunehome_inc.php");
?>
</div>