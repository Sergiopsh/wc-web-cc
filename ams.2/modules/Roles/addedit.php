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
moduleHeader(($action == "EditRole") ? $strEditRole : $strAddRole);

$mod = new Module(null);
$acl_list=$mod->getACL();

$role= new Role($role_id);
if($module_action == "save" && $role_name) {

	$role->set_fields($role_name,$role_desc,$acl,$name_prev);
	if($role->is_exists()) 
	    showMsg($strRoleExists,'module-warning');
 	else {
	    if($action == "AddRole") $role->insert();
	    else $role->update();
	    loadModule(0,'Roles','Roles','RolesList');
	}
	     
}

if($action == "EditRole") {
    list($role_name,$role_desc,$acl)=$role->get_data();
    $name_prev=$role_name;
}

?>

<script>

role = new ObjectD();

role.changeACL=function(id,rm,acl_name,acl1,acl2,acl3,acl1_str,acl2_str,acl3_str) {
 var html="<select name='acl["+rm+"]["+acl_name+"]' id='acl["+rm+"]["+acl_name+"]' >";
 if(acl3) html+="<option value='2:"+acl3+"'>"+acl3_str+"</option>";
 if(acl2) html+="<option value='1:"+acl2+"'>"+acl2_str+"</option>";
 if(acl1) html+="<option value='0:"+acl1+"'>"+acl1_str+"</option>";
 html+="</select>";
 $(id).innerHTML=html;

}
role.save = function() {
	$('module_form').module_action.value='save';
	loadModule(1,'Roles','Roles','<?=$action?>');
}
</script>


<form name="module_form" id="module_form" method="post">
<input type="hidden" name="module_action" id="module_action" value="">
<input type="hidden" name="role_id" id="role_id" value="<?=$role_id?>">
<input type="hidden" name="name_prev" id="name_prev" value="<?=hc($name_prev)?>">
<?
$tbl = new DataTbl("70%");
$p=$tbl->addElement("role_name","text",$strNameRole,1,$role_name);
$p->setOptions(35,1);
$p=$tbl->addElement("","button","",1,($action == "EditRole") ? $strUpdate : $strSave);
$p->action="role.save()"; $p->align2="right";
$p=$tbl->addElement("role_desc","textarea",$strDescription,2,$role_desc);
$p->setOptions(40,5); $p->valign1="top";
$tbl->show();
echo "<br>";
$disable=($action == "EditRole") ? true : false;

$role->showACLTable($acl_list,$acl,"role.changeACL",$disable);
?>


</form>
