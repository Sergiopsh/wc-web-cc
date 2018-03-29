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

$emp = new Employee($e_id);
//$emp->db->Debug=1;
if (empty($current_page)) $current_page=0;

if ($e_del) $emp->deleteEmployee();

if ($e_del_m) {
    if ($list_mark) $emp->deleteSelected($list_mark);
    unset($list_mark);

}

$list=$emp->getList(array($department,$f_first_name,$f_last_name,$f_phone_work,
    $f_phone_office,$f_phone_mobile,$f_email,$f_messenger,$f_title),$current_page,$limit_display);

$num=$emp->num_rows;

$num_disp=count($list);
$n_pages=num_pages($num,$limit_display,$current_page);

?>

<div id="frame-module-header" nowrap>
<?=$strEmployeesList?>
</div>

<script language=javascript>

emp = new ObjectD();

emp.deleteSelected = function () {
 if (!confirm("<?=$strWinConfirmDeleteSelectedEmployees?>")) return;
    loadModule(1,'','Employees','EmployeesList',$H({e_del_m: 1}));
}
emp.deleteEmployee = function (id,name) {
 if (!confirm("<?=$strWinConfirmDeleteEmployee?>\n" + name + "?")) return;
    loadModule(1,'','Employees','EmployeesList',$H({e_del: 1,e_id: id}));
}
emp.editEmployee = function (id) {
    loadModule(1,'','Employees','EditEmployee',$H({e_id: id}));
}
emp.viewEmployee = function(id) {
    loadModule(1,'','Employees','ViewEmployee',$H({e_id: id}));
}
</script>
<br>
<?
moduleForm(array("current_page","limit_display"));
$tbl = new DataTbl();
$p=$tbl->addElement("f_first_name","text",$strFirstName,1,$f_first_name); $p->setOptions(30);
$p=$tbl->addElement("f_phone_office","text",$strPhoneOffice,1,$f_phone_office); $p->setOptions(20);
$p=$tbl->addElement("f_phone_mobile","text",$strPhoneMobile,1,$f_phone_mobile); $p->setOptions(20);
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$p->action="$('module_form').current_page.value=0;loadModule(1,'','Employees','EmployeesList');";
$p=$tbl->addElement("f_last_name","text",$strLastName,2,$f_last_name); $p->setOptions(30);
$p=$tbl->addElement("f_phone_work","text",$strPhoneWork,2,$f_phone_work); $p->setOptions(20);
$p=$tbl->addElement("f_messenger","text",$strMessenger,2,$f_messenger); $p->setOptions(20);
$p=$tbl->addElement("department","text",$strDepartment,3,$department); 
$p->setOptions(30,0,"","","modules/Departments/filldeps.php");
$p=$tbl->addElement("f_title","text",$strTitle,3,$f_title); $p->setOptions(20);
$p=$tbl->addElement("f_email","text",$strEmail,3,$f_email); $p->setOptions(20);
$tbl->show();
echo "<br>";

$tbl = new ListTbl();
if(!$num) {echo "</form>"; $tbl->emptyTbl($strNoEmployees); }
$tbl->exportLink($strExportEmployees,"$('export-form').submit()");
if($_SESSION['acl']['Employees']['Access']>1) $cn=3;
$tbl->tblHead(array($cn,"25","25","9","","10","10"),array($strNameEmployee,$strDepartment,$strPhoneOffice,$strPhoneWork,$strPhoneMobile,$strEmail));


    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  $full_name=$list[$j][4]." ".$list[$j][5];
	  if($_SESSION['acl']['Employees']['Access']>1) {
	    $tbl->checkbox($j,$list[$j][0]);
	    $tbl->td("",15,"","emp.deleteEmployee",array($list[$j][0],$full_name),"drop.gif",$strDeleteEmployee);
	    $tbl->td("",15,"","emp.editEmployee",$list[$j][0],"edit.gif",$strEditEmployee);
	    $tbl->td($full_name,"","left","emp.viewEmployee",$list[$j][0],"",$strViewEmployee);

	  }else $tbl->td($full_name,"","left","emp.viewEmployee",$list[$j][0],"",$strViewEmployee);
	$tbl->td($list[$j][13],"","left");  
	$tbl->tblTds(array($list[$j][17],$list[$j][16],$list[$j][15],$list[$j][19]));
	echo "</tr>";

    }
$tbl->tblEnd($current_page,$n_pages);

if($_SESSION['acl']['Employees']['Access']>1) {
    $tbl->addSelOper($strDeleteSelected,"emp.deleteSelected()","drop.gif");
    $tbl->selOper();
}
?>
<INPUT TYPE="hidden" NAME="is_user" id="is_user"  value="">
</form>

<form name="export-form" id="export-form" method="post" action="include/export_csv.php?pref_export=Export_employees_" target="export-frame">
</form>
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>