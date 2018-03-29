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
?>
<div id="frame-module-header" nowrap>
<?=$strOperatorsList?>
</div>
<?
$op= new Operator($o_id); 

if ($o_del) $op->delete_operator();

$list_op=$op->get_list(array($fo_name,$fo_tel));

?>
<script>
op = new ObjectD();

op.delOperator=function (id, o_fname) {

    if (!confirm("<?=$strWinConfirmDeleteOperator?>\n" + o_fname + " ?")) return;
    loadModule(1,'','TariffPlans','OperatorsList',$H({o_del: 1, o_id: id}));
 
}
op.showTariffPlans=function (id) {

    loadModule(1,'','TariffPlans','TariffPlansList',$H({o_id: id}));

}
op.editOperator=function (id) {

    loadModule(1,'','TariffPlans','EditOperator',$H({o_id: id}));
}
</script>

<br>
<form name="module_form" id="module_form" method="post">
<?
$tbl = new DataTbl();
$p=$tbl->addElement("fo_name","text",$strNameOperator,1,$fo_name); $p->setOptions(20);
$p=$tbl->addElement("fo_tel","text",$strTel,1,$fo_tel); $p->setOptions(20);
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$p->action="loadModule(1,'','Operators','OperatorsList');";
$tbl->show();
echo "<br>";

$tbl = new ListTbl();
if(!$list_op) { echo "</form>"; $tbl->emptyTbl($strNoOperators); }
if($_SESSION['acl']['TariffPlans']['Access']>1) $cn=2;
$tbl->tblHead(array($cn,"30","","25","25"),array($strNameOperator,$strTel,"E-mail",$strContactPerson));

	$i=0;
    foreach ($list_op as $l) {
	  $tbl->tblTr($i++);
	  if($_SESSION['acl']['TariffPlans']['Access']>1) {
	    $tbl->td("",10,"","op.delOperator",array($l[0],$l[1]),"drop.gif",$strDeleteOperator);
	    $tbl->td("",10,"","op.editOperator",$l[0],"edit.gif",$strEditOperator);
	    $tbl->td($l[1],"","left","op.showTariffPlans",$l[0],"",$strShowTariffPlans);
	}else $tbl->td($l[1],"","left","op.showTariffPlans",$l[0],"",$strShowTariffPlans);
	?>
	<td nowrap>
	<?=hc($l[3])?>
	</td>
	<td align="center"  nowrap><a href="mailto: <?=hc($l[6])?>"><?=hc($l[6])?></td>
	<td align="center"  nowrap><?=hc($l[7])?></td>
	</tr>
        <?	
    }
	 
$tbl->tblEnd();
?>

</form>

