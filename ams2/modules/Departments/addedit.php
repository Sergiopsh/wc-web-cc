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
moduleHeader(($action == "EditDepartment") ? $strEditDepartment : $strAddDepartment);

$dep=new Department($d_id);

if ($d_save && $d_name) {
    //$dep->db->Debug=1;
    $dep->name=$d_name;
    if($dep->is_exists()) showMsg($strDepartmentExists,"module-warning");
    else {
	$dep->set_fields($d_name,$d_chief,$d_phone_work,$d_phone_office,$d_fax,$d_desc,$name_prev);  
	if($action == "AddDepartment") {
	    $dep->insert();
	    unset($d_name,$d_chief,$d_phone_work,$d_phone_office,$d_fax,$d_desc);
	} else $dep->update();    
	showMsg("$strDepartmentSaveSuccess","module-warning");
	unset($d_save);
    }
}

if($action == "EditDepartment" && !$d_save) {
    list($d_name,$d_phone_work,$d_phone_office,$d_desc,$d_chief,$d_fax)=$dep->get_data();
    $name_prev=$d_name;
}

moduleForm(array("d_id","name_prev"));
$tbl = new DataTbl();
$p=$tbl->addElement("d_name","text",$strNameDep,1,$d_name); $p->setOptions(50,1);
$p=$tbl->addElement("d_chief","text",$strChief,1,$d_chief); $p->setOptions(30);
$p=$tbl->addElement("","button","",1,($action == "EditDepartment") ? $strUpdate : $strSave); 
$p->action="loadModule(1,'Employees','Departments','$action',\$H({d_save: 1}))"; $p->align2="right";
$p=$tbl->addElement("d_phone_office","text",$strPhoneOffice,2,$d_phone_office); $p->setOptions(31);
$p=$tbl->addElement("d_phone_work","text",$strPhoneWork,2,$d_phone_work); $p->setOptions(30);
$p=$tbl->addElement("d_desc","textarea",$strDesc,3,$d_desc); $p->setOptions(30,4); $p->valign1="top";
$tbl->show();
?>

</form>
