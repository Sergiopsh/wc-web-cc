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

$dep = new Department($d_id);

if ($d_del_m) {

    if($list_mark) $dep->deleteSelected($list_mark);
    unset($list_mark);
}


if ($d_del) $dep->delete_department();

//$limit_display=4;
if(empty($current_page)) $current_page=0;
$list=$dep->get_list(array($f_name,$f_tel,$f_chief),$current_page,$limit_display);
$num=$dep->num_rows;
$num_disp=count($list);

$n_pages=num_pages($num,$limit_display,$current_page);


?>
<div id="frame-module-header" nowrap>
<?=$strDepartmentsList?>
</div>

<script>
menu_module='Employees';
dep = new ObjectD();

dep.deleteSelected=function () {
 if (confirm("<?=$strWinConfirmDeleteSelected?> ?")) {
    loadModule(1,'Employees','Departments','DepartmentsList',$H({d_del_m: 1}));
 }
}

dep.deleteDepartment=function (id,name) {
 if (!confirm("<?=$strWinConfirmDeleteDep?>\n" + name + "?")) return;
    loadModule(1,'Employees','Departments','DepartmentsList',$H({d_del: 1,d_id: id}));
}
dep.editDepartment=function (id) {
    loadModule(1,'Employees','Departments','EditDepartment',$H({d_id: id}));
}
dep.viewEmployees=function (dep) {
    $('module_form').department.value=dep;
    loadModule(1,'Employees','Employees','EmployeesList');
}
</script>
<br>
<?
moduleForm(array("current_page","limit_display"));
$tbl = new DataTbl();
$p=$tbl->addElement("f_name","text",$strNameDep,1,$f_name); $p->setOptions(15);
$p=$tbl->addElement("f_chief","text",$strChief,1,$f_chief); $p->setOptions(15);
$p=$tbl->addElement("f_tel","text",$strTel,1,$f_tel); $p->setOptions(15);
$p=$tbl->addElement("","button","",1,$strSearch); $p->action="searchModule()"; $p->align2="right";
$tbl->show();
echo "<br>";

$tbl = new ListTbl();
if(!$num) { echo "</form>";$tbl->emptyTbl($strNoDeps); }
$tbl->exportLink($strExportCSV,"$('export-form').submit()");
if($_SESSION['acl']['Employees']['Access']==2) $cn=3;

$tbl->tblHead(array($cn,"40","","","","10"),array($strNameDep,$strChief,$strPhoneOffice,$strPhoneWork,$strFax));


    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	if($_SESSION['acl']['Employees']['Access']==2) {
	    $tbl->checkbox($j,$list[$j][0],10);
	    $tbl->td("",10,"","dep.deleteDepartment",array($list[$j][0],$list[$j][1]),"drop.gif",$strDeleteDepartment);
	    $tbl->td("",10,"","dep.editDepartment",$list[$j][0],"edit.gif",$strEditDepartment);
	    $tbl->td($list[$j][1],"","left","dep.viewEmployees",$list[$j][1],"",$strViewEmployees);

	} else $tbl->td($list[$j][1],"","left","dep.viewEmployees",$list[$j][1],"",$strViewEmployees);
	$tbl->tblTds(array($list[$j][7],$list[$j][4],$list[$j][3],$list[$j][5]));
	echo "</tr>";

    }
$tbl->tblEnd($current_page,$n_pages);

if($_SESSION['acl']['Employees']['Access']>1) {
    $tbl->addSelOper($strDeleteSelected,"dep.deleteSelected()","drop.gif");
    $tbl->selOper();

}

?>
<INPUT TYPE="hidden" name="department" id="department" value="">
</form>
<form name="export-form" id="export-form" method="post" action="include/export_csv.php?pref_export=Export_departments_" target="export-frame">
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
