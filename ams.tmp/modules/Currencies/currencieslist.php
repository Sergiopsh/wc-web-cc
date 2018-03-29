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
<?=$strCurrencies?>
</div>

<?

$cur = new Currency($cur_id);

if ($cur_del) {

    $cur->delete_currency();
    unset($cur_id,$cur_name,$cur_rate,$cur_symbol,$cur_code,$module_action);
}
if ($cur_save) {
    $cur->set_fields($cur_name,$cur_rate,$cur_symbol,$cur_code);
    if($cur->is_exists()) $cur_is_exist=1;
    else {
	if($module_action == "Edit") $cur->update();
        else $cur->insert();
        unset($cur_id,$cur_name,$cur_rate,$cur_symbol,$cur_code,$module_action);
    }
}
	
$list=$cur->get_list();
$num=$cur->num_rows;

if(!isset($module_action)) $module_action = "Add";
if($module_action=="Edit") {
    //$cur->id=$cur_id;
    $cur->get_data();
    list($cur_name,$cur_rate,$cur_symbol,$cur_code)=$cur->get_data();
}


?>

<script>

cur = new ObjectD();

cur.deleteCurrency=function (id) {
 if (!confirm("<?=$strWinConfirmDeleteCurrency?>")) return;
    $('module_form').cur_del.value=1;
    $('module_form').cur_id.value=id;
    loadModule(1,'','Currencies','CurrenciesList');
}
cur.editCurrency=function (id,j) {

    $('cur_id').value=id;
    $('cur_name').value=$('list_name' + j).value;
    $('cur_rate').value=$('list_rate' + j).value;
    $('cur_code').value=$('list_code' + j).value;
    $('cur_symbol').value=$('list_symbol' + j).value;
    $('module_action').value='Edit';
    $('cur_header').innerHTML='<?=$strEditCurrency?>';
    $('button_save').value='<?=$strUpdate?>';
    $('validate-fields').innerHTML='&nbsp;';
}

cur.clearFields=function () {
    $('cur_id').value=$('cur_name').value=$('cur_rate').value=$('cur_code').value=$('cur_symbol').value='';
    $('module_action').value='Add';
    $('cur_header').innerHTML='<?=$strAddCurrency?>';
    $('button_save').value='<?=$strAddNew?>';
    $('validate-fields').innerHTML='&nbsp;';
    
}
cur.save = function () {
    $('cur_save').value=1;    
    loadModule(1,'','Currencies','CurrenciesList');
}

</script>

<br>
<FORM NAME="module_form" id="module_form" METHOD=POST>
<INPUT TYPE="hidden" NAME="cur_id" id="cur_id" value="<?=$cur_id?>">
<INPUT TYPE="hidden" NAME="cur_del" id="cur_del" value="">
<INPUT TYPE="hidden" NAME="cur_save" id="cur_save" value="">
<INPUT TYPE="hidden" NAME="module_action" id="module_action" value="<?=$module_action?>">


<? 
$tbl = new ListTbl();
 if (!$num) $tbl->emptyTbl($strNoCurrencies,"100%",0); 
 else {
 if($_SESSION['acl']['Currencies']['Access']>1) $cn=2;
 $tbl->tblHead(array($cn,"15","","","","","","",""),array($strNameCurrency,$strCurrencyCode,$strCurrencySymbol,
   $strConversationRate,$strCreatedBy,$strDateEntered,$strModifiedBy,$strDateModified));


    for($j=0; $j < $num; $j++) {

	 $tbl->tblTr($j);
	 if($_SESSION['acl']['Currencies']['Access']>1) {
	    $tbl->td("",20,"","cur.deleteCurrency",$list[$j][0],"drop.gif",$strDeleteCurrency);
	    $tbl->td("",20,"","cur.editCurrency",array($list[$j][0],$j),"edit.gif",$strEditCurrency);
	    $tbl->td($list[$j][1],"","left","cur.editCurrency",array($list[$j][0],$j),"",$strEditCurrency);

	}else $tbl->td($list[$j][1],"","left");
	if($list[$j][3][0]) $m_by = dbformat_to_dt($list[$j][3]);
	$tbl->tblTds(array($list[$j][8],$list[$j][7],$list[$j][6],$list[$j][4],dbformat_to_dt($list[$j][2]),$list[$j][5],$m_by));
        ?>
	</tr>
	<input type="hidden" id="list_name<?=$j?>" name="list_name<?=$j?>" value="<?=hc($list[$j][1])?>">
	<input type="hidden" id="list_code<?=$j?>" name="list_code<?=$j?>" value="<?=hc($list[$j][8])?>">
	<input type="hidden" id="list_symbol<?=$j?>" name="list_symbol<?=$j?>" value="<?=hc($list[$j][7])?>">
	<input type="hidden" id="list_rate<?=$j?>" name="list_rate<?=$j?>" value="<?=hc($list[$j][6])?>">
	
<?    }
$tbl->tblEnd();
}
?>

<br>
<?
if($_SESSION['acl']['Currencies']['Access']>1) {?>
<div id="frame-module-header2" style="margin-bottom: 10px;">
    <div id="cur_header">  
    <?=isset($cur_id) ? $strEditCurrency : $strAddCurrency?>
  </div>
</div>

<div id="validate-fields" class="module-warning" style="display: none;"></div>
<?
if($cur_is_exist) showMsg($strCurrencyExists,"validate-fields",0,"note.gif");

$tbl = new DataTbl("60%",0,0,7);
$p=$tbl->addElement("cur_name","text",$strNameCurrency,1,$cur_name);
$p->setOptions(15,1);
$p=$tbl->addElement("cur_symbol","text",$strCurrencySymbol,1,$cur_symbol);
$p->setOptions(15,1);
$p=$tbl->addElement("button_save","button","",1,($module_action == "Edit") ? $strUpdate : $strAddNew);
$p->action="cur.save()"; $p->align2="right";
$p=$tbl->addElement("cur_rate","text",$strConversationRate,2,$cur_rate);
$p->setOptions(15,1,"","float");
$p=$tbl->addElement("cur_code","text",$strCurrencyCode,2,$cur_code);
$p->setOptions(15,1);
$p=$tbl->addElement("","button","",2,$strClear);
$p->action="cur.clearFields()"; $p->submit=false; $p->align2="right";
$tbl->show();
}
?>
</form>


