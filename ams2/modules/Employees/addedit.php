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
include_once("lib/Class.datatbl.php");
?>
<div id="frame-module-header" nowrap>
<?=($action == "EditEmployee") ? $strEditEmployee : $strAddEmployee?>
</div>
<br>
<div id="module-warning" class="module-warning" style="display: none;">
</div>
<?

$emp = new Employee($e_id);

if ($e_save && $e_first_name) {

    $emp->set_fields($e_first_name,$e_last_name,$e_title,$department,
    $e_email,$e_address,$e_phone_work,$e_phone_office,$e_phone_mobile,
    $e_phone_home,$e_messenger,$e_fax,$e_comment,$e_channel,$e_context);
    if($action == "AddEmployee") $e_id=$emp->insert();
    else $emp->update();    
    loadModule(0,"","Employees","ViewEmployee","{e_id: '$e_id'}");
}
if($action == "EditEmployee" && !$e_save) {
    list($is_user,$e_first_name,$e_last_name,$e_comment,$e_address,
         $e_phone_work,$e_phone_mobile,$e_phone_home,$e_phone_office,
	 $department,$e_fax,$e_email,$e_messenger,$e_title) = $emp->get_data();
}

moduleForm(array("e_id"));

$tbl = new DataTbl();
$p=$tbl->addElement("e_first_name","text",$strFirstName,1,$e_first_name); $p->setOptions(30,1);
$p=$tbl->addElement("e_title","text",$strTitle,1,$e_title); $p->setOptions(30);
$p=$tbl->addElement("","button","",1,($action == "EditEmployee") ? $strUpdate : $strSave); 
$p->action="loadModule(1,'','Employees','$action',\$H({e_save: 1}));"; $p->align2="right";
$p=$tbl->addElement("e_last_name","text",$strLastName,2,$e_last_name); $p->setOptions(30);
$p=$tbl->addElement("department","text",$strDepartment,2,$department);
$p->setOptions(30,0,"","","modules/Departments/filldeps.php");
if(empty($is_user) && $action =="EditEmployee") {
    $p=$tbl->addElement("","button","",2,$strMakeUser); 
    $p->action="loadModule(1,'Users','Users','EditUser',\$H({u_id: '$e_id'}));"; 
    $p->align2="right";
}
array_push($tbl->filters,array("id"=>"e_email","type"=>"email","msg"=>$strFieldMustBeValidEmail));
$tbl->cols=array("14%","25%","12%");
$tbl->show();
echo "<br>";

$tbl = new DataTbl();
$p=$tbl->addElement("e_phone_office","text",$strPhoneOffice,1,$e_phone_office); $p->setOptions(30);
$p=$tbl->addElement("e_phone_work","text",$strPhoneWork,1,$e_phone_work); $p->setOptions(30);
$p=$tbl->addElement("e_messenger","text",$strMessenger,2,$e_messenger); $p->setOptions(30);
$p=$tbl->addElement("e_phone_mobile","text",$strPhoneMobile,2,$e_phone_mobile); $p->setOptions(30);
$p=$tbl->addElement("e_email","text",$strEmail,3,$e_email); $p->setOptions(30);
$p=$tbl->addElement("e_phone_home","text",$strPhoneHome,3,$e_phone_home); $p->setOptions(30);
$p=$tbl->addElement("e_address","textarea",$strAddress,4,$e_address); $p->setOptions(30,4); $p->valign1="top";
$p=$tbl->addElement("e_comment","textarea",$strComment,4,$e_comment);
$p->setOptions(30,4);$p->colspan=2; $p->valign1="top";
$tbl->cols=array("14%","25%","12%");
$tbl->show();
?>
</form>
