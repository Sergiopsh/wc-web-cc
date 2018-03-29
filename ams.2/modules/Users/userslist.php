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
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
$user = new User($u_id);

if (empty($current_page)) $current_page=0;

if ($u_del) $user->deleteUser();

if ($u_del_m) {
    if ($list_mark) $user->deleteMarked($list_mark);
    unset($list_mark);

}

if(!isset($f_status)) $f_status=2; 

$list_users=$user->getList(array($f_name,$f_first_name,$f_last_name,$f_phone_work,$f_phone_office,$f_email,$f_messenger,$department),$f_admin,$f_status,$current_page,$limit_display);
$num=$user->num_rows;
$num_disp=count($list_users);
$n_pages = num_pages($num,$limit_display,$current_page);


?>

<div id="frame-module-header" nowrap>
<?=$strUsersList?>
</div>

<script>

user = new ObjectD();

user.deleteUsers=function () {
 if (!confirm("<?=$strWinConfirmDeleteSelectedUsers?>")) return;
    $('module_form').current_page.value=0;
    loadModule(1,'','Users','UsersList',$H({u_del_m: 1}));
}
user.deleteUser=function (id,name) {
 if (!confirm("<?=$strWinConfirmDeleteUser?>\n" + name + "?")) return;
    $('module_form').current_page.value=0;
    loadModule(1,'','Users','UsersList',$H({u_id: id, u_del: 1}));
}

user.editUser=function (id) {
    loadModule(1,'','Users','EditUser',$H({u_id: id}));
}
user.viewUser=function (id) {
    loadModule(1,'','Users','ViewUser',$H({u_id: id}));
}
</script>
<br>
<?
moduleForm(array("current_page","limit_display"));
$tbl = new DataTbl();
$p=$tbl->addElement("f_name","text",$strNameUser,1,$f_name); $p->setOptions(15);
$p=$tbl->addElement("f_phone_work","text",$strPhoneWork,1,$f_phone_work); $p->setOptions(15);
$p=$tbl->addElement("department","text",$strDepartment,1,$department);
$p->setOptions(25,0,"","","modules/Departments/filldeps.php"); 
$p=$tbl->addElement("f_admin","checkbox",$strAdmin,1,$f_admin); 
$p=$tbl->addElement("","button","",1,$strSearch); $p->action="searchModule()";
$p->align2="right";
$p=$tbl->addElement("f_first_name","text",$strFirstName,2,$f_first_name); $p->setOptions(15);
$p=$tbl->addElement("f_phone_office","text",$strPhoneOffice,2,$f_phone_office); $p->setOptions(15);
$p=$tbl->addElement("f_messenger","text",$strMessenger,2,$f_messenger); $p->setOptions(25);
$p=$tbl->addElement("f_last_name","text",$strLastName,3,$f_last_name); $p->setOptions(15);
$p=$tbl->addElement("f_email","text",$strEmail,3,$f_email); $p->setOptions(15);
$p=$tbl->addElement("f_status","select",$strStatus,3,$f_status); 
$p->options=array(2=>$strAll,1=>$strActive,0=>$strDisable);

$tbl->show();
echo "<br>";

$tbl = new ListTbl(); 
if (!$num) { echo "</form>"; $tbl->emptyTbl($strNoUsers); }
$tbl->exportLink($strExportUsers,"$('export-form').submit()");

if($_SESSION['acl']['Users']['Access']>1) $cn=3;
$tbl->tblHead(array($cn,"10","17","17","","","","",""),array($strNameUser,$strFirstLastName,$strDepartment,$strPhoneWork,$strPhoneOffice,$strEmail,$strStatus,$strAdm));

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  if($_SESSION['acl']['Users']['Access']>1) {
	  $tbl->checkbox($j,$list_users[$j][0]);
	  $tbl->td("",15,"","user.deleteUser",array($list_users[$j][0],$list_users[$j][1]),"drop.gif",$strDeleteUser);
	  $tbl->td("",15,"","user.editUser",$list_users[$j][0],"edit.gif",$strEditUser);
	  $tbl->td($list_users[$j][1],15,"left","user.viewUser",$list_users[$j][0],"",$strViewUser);
	
	}else $tbl->td($list_users[$j][1],15,"left","user.viewUser",$list_users[$j][0],"edit.gif",$strViewUser);
	?>
	<td nowrap align="left">&nbsp;<?=hc($list_users[$j][4]."  ".$list_users[$j][5])?></td>
	<td nowrap align="left"><?=hc($list_users[$j][13])?></td>
	<td nowrap><?=hc($list_users[$j][16])?></td>
	<td nowrap><?=hc($list_users[$j][17])?></td>
	<td nowrap><?=hc($list_users[$j][19])?></td>
	<td nowrap>
	<?if($list_users[$j][21]) echo "<font color=green>$strActive</font>"; 
	  else echo "<font color=red>$strDisabled</font>"; 
	?>
	</td>
	<td align="center" nowrap="nowrap">
	<?if($list_users[$j][6]) echo "<img src=\"images/check.gif\">";?>
	</td>
	</tr>

<?	

    }
$tbl->tblEnd($current_page,$n_pages);
if($_SESSION['acl']['Users']['Access']>1) {
    $tbl->addSelOper($strDeleteSelected,"user.deleteUsers()","drop.gif");
    $tbl->selOper(); 

}
?>

</form>
<form name="export-form" id="export-form" method="post" action="include/export_csv.php?pref_export=Export_users_" target="export-frame">
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
