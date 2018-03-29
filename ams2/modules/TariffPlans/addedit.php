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

moduleHeader(($action == "EditTariffPlan") ? "$strEditTariffPlan $t_name" : $strAddTariffPlan);

if(!isset($t_st1) || !is_numeric($t_st1)) $t_st1=0;
if(!isset($t_st2) || !is_numeric($t_st2)) $t_st2=1;
if(!isset($t_st3) || !is_numeric($t_st3)) $t_st3=1;

$t_step="$t_st1"."/"."$t_st2"."/"."$t_st3";

$op = new Operator();


$tplan = new TariffPlan($t_id,$accountcode);

if ($t_save && $t_name && $accountcode) {

	$tplan->set_fields($t_name,$t_step,$t_val,$t_op,$t_pr_orig,$t_pr_repl,$accode_prev);
	if($tplan->is_exists()) showMsg($strTariffPlanExists,"module-warning");
	else { 
	    if($action == "EditTariffPlan") {
		$tplan->update();
		showMsg($strUpdateTariffPlanSuccess,"module-warning",true);

	    }else {
		$tplan->insert();
		loadModule(0,"","TariffPlans","TariffPlansList");

	    }
	}
}

$cur= new Currency();
$cur->get_list();
$list_cur=$cur->list_currencies;

if($action == "EditTariffPlan" && !$t_save) {

    list($t_name,$accountcode,$t_step,$t_val,$t_op,$t_pr_orig,$t_pr_repl)=$tplan->get_data();
    list($t_st1,$t_st2,$t_st3)=explode("/",$t_step);
    $accode_prev=$accountcode;
}

$list_op = $op->get_list();

moduleForm(array("t_id","accode_prev"));

$tbl = new DataTbl("50%");
$p=$tbl->addElement("t_name","text",$strNameTariffPlan,1,$t_name); $p->setOptions(40,1); $p->colspan=6;
$p=$tbl->addElement("accountcode","text",$strAccCode,2,$accountcode);
$p->setOptions(30,1,"","symbols"); $p->colspan=6;
$p=$tbl->addElement("t_st1","text",$strBillStep,3,$t_st1); $p->setOptions(2,0,2,"integer");
$p=$tbl->addElement("t_st2","text","/",3,$t_st2); $p->setOptions(2,0,2,"integer");
$p=$tbl->addElement("t_st3","text","/",3,$t_st3); $p->setOptions(2,0,2,"integer");
$p=$tbl->addElement("","simple","",3,$strSec);
$p=$tbl->addElement("t_val","select",$strCurrency,4,$t_val,"width: 170px;");
$p->type="currency"; $p->colspan=6; $p->cur=$cur;
if(!empty($list_op)) {

    $arr = array();
    foreach($list_op as $l) {
	$arr[$l[2]]=$l[2];
    }
    $p=$tbl->addElement("t_op","select",$strOperator,5,$t_op,"width: 170px;");
    $p->options=$arr; $p->colspan=6;   
}

$tbl->cols=array("25%","3%","3%","3%","3%","3%");
$tbl->show();
?>

<br>
<table border=0 class="input-data-tbl" width="50%" cellspacing=5 cellpadding=0>
 <tr><td colspan=2 align=center><?=$strTransRules?></td></tr>

    <tr><td align=right><?=$strOrigPrefix?>
    </td><td align=left><?=$strRepPrefix?>
    </td></tr>
<?
for ($i=0; $i < 5; $i++) {
?>
    <tr><td align=right>
    <INPUT TYPE="text" NAME="t_pr_orig[<?=$i?>]" id="t_pr_orig[<?=$i?>]" size="15" value="<?=hc($t_pr_orig[$i])?>">
    </td><td align="left">
    <INPUT TYPE="text" NAME="t_pr_repl[<?=$i?>]" id="t_pr_repl[<?=$i?>]" size="15" value="<?=hc($t_pr_repl[$i])?>">
    </td></tr>
<?
}
?>

</table>

<br>
<?
$tbl2 = new DataTbl("50%");
$p=$tbl2->addElement("","button","",1,($action == "EditTariffPlan") ? $strUpdate : $strSave);
$p->action="loadModule(1,'TariffPlans','TariffPlans','$action',\$H({t_save: 1}));";
$p->align2="center"; $tbl2->required=$tbl->required; $tbl2->filters=$tbl->filters;
$tbl2->show();
?>
</form>
