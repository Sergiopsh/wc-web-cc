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
if ($o_id) {
    $op= new Operator($o_id);
    list($o_id,$o_fname,$o_name)=$op->get_data();
}

moduleHeader($strTariffPlansList);

if ($o_id) showMsg("$strOperator: <b>$o_fname</b><br><br>");

$tplan = new TariffPlan ($t_id);

if ($t_del) $tplan->delete_tplan();

$tplan->provider=$o_name;
$list_t=$tplan->get_list(array($ft_name,$ft_acccode));

?>
<script>

tp = new ObjectD();

tp.deleteTariffPlan=function (id, name, name_id) {
 if (!confirm("<?=$strWinConfirmDeleteTariffPlan?>" + name)) return;

    loadModule(1,'','TariffPlans','TariffPlansList',$H({t_id: id,t_del: 1}));
}
tp.editTariffPlan=function(id) {
    loadModule(0,'','TariffPlans','EditTariffPlan',$H({t_id: id}));
}
tp.showRatesList=function(id) {
    loadModule(0,'Rates','Rates','RatesList',$H({t_id: id}));
}

</script>


<form name="module_form" id="module_form" method="post">
<input type="hidden" name="o_id" id="o_id" value="<?=$o_id?>">
<?
$tbl = new DataTbl();
$p=$tbl->addElement("ft_name","text",$strNameTariffPlan,1,$ft_name); $p->setOptions(20);
$p=$tbl->addElement("ft_acccode","text",$strAccCode,1,$ft_acccode); $p->setOptions(20);
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$p->action="loadModule(1,'','TariffPlans','TariffPlansList');";
$tbl->show();
echo "<br>";

$tbl = new ListTbl();
if (!$list_t) { echo "</form>"; $tbl->emptyTbl($strNoTariffPlans); }
if($_SESSION['acl']['TariffPlans']['Access']>1) $cn=2;
$tbl->tblHead(array($cn,"25","","15","15",""),array($strNameTariffPlan,$strAccCode,$strBillStep,$strCurrency,$strOperator));

	$i=0;
    foreach ($list_t as $l) {
	    $tbl->tblTr($i++);
	  if($_SESSION['acl']['TariffPlans']['Access']>1) {
	      $tbl->td("",15,"","tp.deleteTariffPlan",array($l[0],$l[1],$l[2]),"drop.gif",$strDeleteTariffPlan);
	      $tbl->td("",15,"","tp.editTariffPlan",$l[0],"edit.gif",$strEditTariffPlan);
	  }
	  $tbl->td($l[1],"","left","tp.showRatesList",$l[0],"",$strShowDestRates);
	  $tbl->tblTds(array($l[2],$l[3],$l[4],$l[5]));
	  echo "</tr>";
    }
$tbl->tblEnd();
?>
</form>


