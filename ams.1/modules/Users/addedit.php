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
if($u_id==0 && !$_SESSION['root']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php"); 

$user = new User($u_id);

$currency=new Currency();
$role=new Role();

if ($u_save) {
    if(empty($u_first_name)) $u_first_name=$u_name;
    $user->set_fields($u_name,$u_pswd,$u_first_name,$u_last_name,$is_admin,
	$is_employee,$u_title,$department,$u_email,$u_address,$u_status,$u_phone_work,$u_phone_office,
	$u_phone_mobile,$u_phone_home,$u_messenger,$u_fax,$u_comment,$u_role);  
    $user->setPreferences($u_theme,$u_lang,$u_dateformat,$u_currency);

    $user->setACL($acl);  
    $user->change_pswd=$change_pswd;
    if($user->is_exists($action)) showMsg($strUserExists,"module-warning",0,"warning.gif");
    else {    
	if($action == "EditUser")  $user->update();
	else  $user->insert();
	loadModule(0,"","Users","ViewUser","{u_name: '".addslashes($u_name)."'}");
    }
}
    

if ($action == "EditUser" && !$u_save && !$change_role) {

    $user->get_data();
    $u_name=$user->user_name;
    $u_first_name=$user->first_name;
    $u_last_name=$user->last_name;
    $is_employee=$user->is_employee;
    $is_admin=$user->is_admin;
    $u_comment=$user->comment;
    $u_address=$user->address;
    $u_status=$user->status;
    $u_phone_work=$user->phone_work;
    $u_phone_mobile=$user->phone_mobile;
    $u_phone_home=$user->phone_home;
    $u_phone_office=$user->phone_office;
    $department=$user->department;
    $acl=$user->acl;
    $u_role=$user->role_name;
    $u_theme=$user->theme;
    $u_currency=$user->currency;
    $u_lang=$user->lang;
    $u_dateformat=$user->dateformat;
    $u_fax=$user->fax;
    $u_email=$user->email;
    $u_messenger=$user->messenger;
    $u_title=$user->title;
    $u_name_prev=$u_name;
    if(empty($u_name)) $u_pswd=$u_conf_pswd="";
    else $u_pswd=$u_conf_pswd="********";
}
if(!isset($u_theme)) $u_lang=$default_theme;
if(!isset($u_lang)) $u_lang=$default_lang;
if(!isset($u_dateformat)) $u_lang=$default_dateformat;
if(!isset($u_currency)) $u_lang=$default_currency;

moduleHeader(($action == "EditUser") ? "$strEditUser: $u_name" : $strAddUser);
?>

<script>
user = new ObjectD();

user.saveUser = function() {
    if($F('u_pswd') != $F('u_conf_pswd')) {
	ams.toolTip('u_pswd',0,'<?=$strPswdNotEqual?>',{img: 'warning.gif',hlight: 1}); 
	return;   
    }
    loadModule(1,'','Users','<?=$action?>',$H({u_save: 1}));
}
user.changeACL=function(id,rm,acl_name,acl1,acl2,acl3,acl1_str,acl2_str,acl3_str) {

 $('u_role').value='';    
 var html="<select name='acl["+rm+"]["+acl_name+"]' id='acl["+rm+"]["+acl_name+"]' >";
 if(acl1) html+="<option selected value='0:"+acl1+"'>"+acl1_str;
 if(acl2) html+="<option value='1:"+acl2+"'>"+acl2_str;
 if(acl3) html+="<option value='2:"+acl3+"'>"+acl3_str;
 html+="</select>";
 $(id).innerHTML=html;

}

user.changeRole=function() {

    loadModule(1,'','Users','<?=$action?>',$H({change_role: 1}));
}
user.changePswd=function() {
     $('change_pswd').value = '1';
}
</script>
<?
moduleForm(array("u_id","u_theme","change_pswd"));

$tbl = new DataTbl();
$p=$tbl->addElement("u_name","text",$strNameUser,1,$u_name); $p->setOptions(25,1);
$p=$tbl->addElement("u_first_name","text",$strFirstName,1,$u_first_name); $p->setOptions(25);
$p=$tbl->addElement("is_admin","checkbox",$strAdmin,1,$is_admin); 
$p=$tbl->addElement("","button","",1,$action=="EditUser"?$strUpdate:$strSave); $p->align2="right"; $p->action="user.saveUser()";
$p=$tbl->addElement("u_pswd","text",$strPassword,2,$u_pswd);
$p->setOptions(25,1); $p->type="password"; $p->action=" onfocus=\"$('u_pswd').value='';\" onchange=\"$('change_pswd').value = '1';\" "; 
$p=$tbl->addElement("u_last_name","text",$strLastName,2,$u_last_name); $p->setOptions(25);
$p=$tbl->addElement("is_employee","checkbox",$strEmployee,2,$is_employee);
$p=$tbl->addElement("u_conf_pswd","text",$strConfirmPassword,3,$u_conf_pswd);
$p->setOptions(25,1); $p->type="password"; $p->action=" onfocus=\"$('u_conf_pswd').value='';\""; 
$p=$tbl->addElement("u_status","select",$strStatus,3,$u_status);
$p->options=array(1 => $strActive,0 => $strDisable);
$tbl->cols=array("14%","25%","12%");
array_push($tbl->filters,array("id" => "u_email","type"=> "email","msg" => $strFieldMustBeValidEmail));
$tbl->show(); 
echo "<br>";

$tbl = new DataTbl();
$p=$tbl->addElement("u_title","text",$strTitle,1,$u_title); $p->setOptions(25);
$p=$tbl->addElement("u_phone_work","text",$strPhoneWork,1,$u_phone_work); $p->setOptions(25);
$p=$tbl->addElement("u_phone_mobile","text",$strPhoneMobile,1,$u_phone_mobile); $p->setOptions(25);
$p=$tbl->addElement("department","text",$strDepartment,2,$department);
$p->setOptions(25,0,"","","modules/Departments/filldeps.php");
$p=$tbl->addElement("u_phone_office","text",$strPhoneOffice,2,$u_phone_office); $p->setOptions(25);
$p=$tbl->addElement("u_phone_home","text",$strPhoneHome,2,$u_phone_home); $p->setOptions(25);
$p=$tbl->addElement("u_email","text",$strEmail,3,$u_email); $p->setOptions(25);
$p=$tbl->addElement("u_messenger","text",$strMessenger,3,$u_messenger); $p->setOptions(25);
$p=$tbl->addElement("u_fax","text",$strFax,3,$u_fax); $p->setOptions(25);
$p=$tbl->addElement("u_address","textarea",$strAddress,4,$u_address); $p->setOptions(30,3); $p->valign1="top";
$p=$tbl->addElement("u_comment","textarea",$strComment,4,$u_comment);
$p->setOptions(30,3);$p->colspan=2; $p->valign1="top";
$tbl->cols=array("14%","25%","12%");
$tbl->show();

echo "<br>";
$tbl = new DataTbl();
$p=$tbl->addElement("u_role","select",$strRole,1,$u_role); $p->action="user.changeRole()";
$role->get_list();
$list_roles=$role->list_roles;
$arr=array("" => "&nbsp;-----");
if($list_roles) {
    foreach($list_roles as $r) { $arr[$r[5]]=$r[5]; }
} 
$p->options=$arr;
$p=$tbl->addElement("u_currency","select",$strCurrency,1,$u_currency);
$p->type="currency"; $p->cur=$currency;
$p=$tbl->addElement("u_lang","select",$strLang,1,$u_lang);
$p->type="lang"; $p->action="ms.ChangeLang()";
$p=$tbl->addElement("u_dateformat","select",$strDateFormat,1,$u_dateformat);
$p->type="dateformat";

$tbl->show();
echo "<br>";

if($u_id == 0 && $action=="EditUser") exit();

$mod = new Module(null);
$acl_list=$mod->getACL();

if((empty($acl) && $action=="AddUser") OR ($change_role && empty($u_role))) {
	$role->showACLTable($acl_list,array(),"user.changeACL",false);
	exit();
}
	    
if($change_role && !empty($u_role))
	$acl=$role->get_acl_by_name($u_role);
elseif($action == "EditUser") $acl=$user->getACL();
$role->showACLTable($acl_list,$acl,"user.changeACL");



?>

</form>
