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

moduleHeader(($action == "AddOperator") ? $strAddOperator : $strEditOperator);

$op = new Operator($o_id);

if ($o_save && $o_fname && $o_name) {

    $op->set_fields($o_fname,$o_name,$o_tel1,$o_tel2,$o_fax,$o_email, $o_cperson,$o_address,$o_comment,$o_name_prev);   
    if($op->is_exists()) echo showMsg($strOperatorExists,'module-warning',0,'note.gif');
    else {
	if($action == "EditOperator") { 
	    $op->update();
	    showMsg($strOperatorUpdated,'module-warning',0,'note.gif');
        }else {
	    $op->insert();
	    loadModule(0,'','','OperatorsList');
	    exit();	
	}
    }
}
if(($action == 'EditOperator') && !$o_save) {
    list($o_id,$o_fname,$o_name,$o_tel1,$o_tel2,$o_fax,$o_email,$o_cperson,$o_address,$o_comment)=$op->get_data(); 
    $o_name_prev=$o_name;
}

moduleForm(array("o_id","o_name_prev"));

$tbl = new DataTbl("90%");

$p=$tbl->addElement("o_fname","text",$strFullName,1,$o_fname); $p->setOptions(45,true);
$p=$tbl->addElement("o_cperson","text",$strContactPerson,1,$o_cperson); $p->setOptions(32);
$p=$tbl->addElement("","button","",1,($action == 'AddOperator') ? $strSave : $strUpdate);
$p->action="loadModule(1,'','','$action',\$H({o_save: 1}));";
$p=$tbl->addElement("o_name","text",$strShortName,2,$o_name); $p->setOptions(25,1);
$p=$tbl->addElement("o_address","textarea",$strAddress,2,$o_address); 
$p->rowspan=2;  $p->setOptions(31,4); $p->valign1="top";
$p=$tbl->addElement("o_tel1","text","$strTel 1",3,$o_tel1); $p->setOptions(25);
$p=$tbl->addElement("o_tel2","text","$strTel 2",4,$o_tel2); $p->setOptions(25);
$p=$tbl->addElement("o_comment","textarea",$strComment,4,$o_comment); 
$p->rowspan=2;  $p->setOptions(31,4); $p->valign1="top";
$p=$tbl->addElement("o_fax","text","$strFax",5,$o_fax); $p->setOptions(25);
$p=$tbl->addElement("o_email","text","E-mail",6,$o_email); $p->setOptions(25,0,"","email");

//$tbl->cols=array("","","");
$tbl->show();

?>
</form>
