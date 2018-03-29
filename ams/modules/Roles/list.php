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
?>
<div id="frame-module-header" nowrap>
<?=$strRolesList?>
</div>

<?
$role = new Role($role_id);
if ($role_del) {
    $role->delete_role();
    unset($role_id);
}
$list=$role->get_list(array($f_name));
$num=$role->num_rows;

?>
<script>

role = new ObjectD();

role.deleteRole=function (id) {
 if (confirm("<?=$strWinConfirmDeleteRole?>")) {
    $('module_form').role_del.value=1;
    $('module_form').role_id.value=id;
    loadModule(1);
 }
}

role.editRole=function (id) {
    $('module_form').role_id.value=id;
    loadModule(1,'Roles','Roles','EditRole');
}
role.viewRole=function (id) {
    $('module_form').role_id.value=id;
    loadModule(1);
}
</script>

<br>
<FORM NAME="module_form" id="module_form" METHOD=POST>
<INPUT TYPE="hidden" NAME="role_id" id="role_id" value="">
<INPUT TYPE="hidden" NAME="role_del" id="role_del" value="">

<table class="input-data-tbl" width="100%" cellspacing=5 cellpadding=0>
    <tr><td width="10%">&nbsp;<?=$strNameRole?></td>
    <td><input type="text" name="f_name" size=15 value="<?=hc($f_name)?>"> </td>
    <td width="50%" align=right>
    <input type="button" onclick="loadModule(1);" value="<?=$strSearch?>" class="sbutton">
    </td></tr>

</table>
<br>
<? 
$tbl = new ListTbl();
if(!$num) {echo "</form>"; $tbl->emptyTbl($strNoRoles); }
if($_SESSION['acl']['Roles']['Access']>1) $cn=2;
else $cn="";

$tbl->tblHead(array($cn,"12","30","","","",""),array($strNameRole,$strDescription,$strCreatedBy,$strDateEntered,$strModifiedBy,$strDateModified));

	for($i=0; $i < $num; $i++) {
	    $tbl->tblTr($i);
	    if($_SESSION['acl']['Roles']['Access']>1) {
	    $tbl->td("",20,"","role.deleteRole",$list[$i][0],"drop.gif",$strDeleteRole);
	    $tbl->td("",20,"","role.editRole",$list[$i][0],"edit.gif",$strEditRole);
	    }
	    $tbl->td($list[$i][5],"","left","role.viewRole",$list[$i][0],"",$strViewRole);

	    if($list[$i][2][0]) $m_by=dbformat_to_dt($list[$i][2]);
	    $tbl->tblTds(array($list[$i][6],$list[$i][4],dbformat_to_dt($list[$i][1]),$list[$i][3],$m_by));
	    echo "</tr>";
	}
$tbl->tblEnd();
?>

<br>

<?
if (isset($role_id) && $role_id != "")  { 
    

$role->id=$role_id;
$role->get_data();
$acl=$role->acl;

?>
<div id="frame-module-header2" nowrap>
<?=$strRole?>: <?=hc($role->name)?>
</div>
<br>

<?


list($acl_names,$arr,$width_tbl,$w1,$w_td,$num_td)=$role->prepareACLTable($acl);
?>

<table border=0 width="<?=$width_tbl?>%">
<tr><td>
  <table width="100%" border=0  class="data-tbl2" cellpadding="2" cellspacing="0">
    <tr><th id="th_1" nowrap width="<?=$w1?>%" rowspan=2 align=center><?=$strModule?></th>
    <th colspan=20 id="th_2" align=center><?=$strACL?></th></tr>
    <?
	echo "<tr>";
        foreach($acl_names as $a) {
	    echo "<th width=\"$w_td%\" align=center>".getStr($a)."</th>";
	}
	echo "</tr>";
    foreach ($arr as $rm) {
	if(!$acl[$rm]) $acl[$rm]['Access']="0:Disable";
	
		?>
		<tr>
		<td id="td_1" align="center" <?if(in_array($rm,$admin_only_modules)) echo "style='color: red';";?>>
		<?=getStr($rm)?></td>
		<?
		foreach($acl_names as $a) {
		    echo "<td align=center>";
		    if(!$acl[$rm][$a]) {echo "&nbsp;</td>"; continue;}
		    
		$al=explode(":",$acl[$rm][$a]);
		   if($a=="Access") {
		     switch ($al[0]) {
			case "0"; echo "<font color=red>$strDisable</font>";break;
		    	case "1"; echo "<font color=blue>$strView</font>";break;
			case "2"; echo "<font color=green>$strFull</font>";break;
		     } 
		   }else echo "<font color=blue>".getStr($al[1])."</font>";
                 	
		echo "</td>";
		}
		echo "</tr>";
        
	
   }
  ?>
    
  </table>
 </td></tr>
 </table>
<?}?>
</form>