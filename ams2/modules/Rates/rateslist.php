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
//echo "quotes=".get_magic_quotes_gpc()."<br>";
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
if($t_id == "") unset($t_id);

if (!isset($t_id) && !isset($_SESSION['current_t_id'])) {
    showMsg($strChooseTariffPlanFirst);
    echo "<script>setTimeout(function() { loadModule(0,'','TariffPlans','TariffPlansList');},3000);</script>";
    exit();

} elseif (isset($t_id)) $_SESSION['current_t_id']=$t_id;
else $t_id=$_SESSION['current_t_id'];


$tplan = new TariffPlan($t_id);
list($t_name,$accountcode,$t_step,$t_val,$o_name)=$tplan->get_data();
if (empty($current_page)) $current_page=0;

$rate= new Rate($r_name,$accountcode);
//$rate->db->Debug=1;

if ($r_del) $rate->delete_rate($r_name);

if ($r_del_m) { $rate->delete_selected($list_mark); unset($list_mark); }

$rate->get_list(array($fr_name,$fr_code,$fr_rfr,$fr_rto),$current_page,$limit_display);

$list=$rate->list_rates;
$num=$rate->num_rows;
$num_disp=count($list);

$n_pages=num_pages($num,$limit_display,$current_page);

if(!is_numeric($fr_rfr)) unset($fr_rfr);
if(!is_numeric($fr_rto)) unset($fr_rto);

moduleHeader($strRatesList);
showMsg("$strTariffPlan: <b>".hc($t_name)."&nbsp;($t_step)</b>","","","","margin-top: 10px; margin-bottom: 10px;");

?>
<div id="module_rates">
<script>

rate = new ObjectD();

rate.rates = new Array();

rate.saveRate=function (evt,p) {
    var newrate=p.value;
    var id=p.parentId;
    var name=p.ratename;
    if(!ams.checkInput(p,'float','<?=$strFieldMustBeFloat?>',{offsetLeft: -150})) return; 
	var url='modules/Rates/saverate.php';
	var pb="name="+encodeURIComponent(name)+"&rate="+newrate+"&accountcode="+encodeURIComponent('<?=addslashes($accountcode)?>');

	new Ajax.Request(url,{postBody: pb,
		onComplete: function(t) {
		    //alert(t.responseText);
		    if(t.responseText.indexOf("success") != -1) {
			id.title='<?=$strDblClickToEdit?>';
			id.innerHTML=formatFloat(newrate,5); id.rateId=0; 
		    }else ams.showMessage('<?=$strErrorEditPrice?>','module-warning');
		
		}});
	return;

}
rate.saveRates=function () {
    var names = new Array();
    var rates = new Array();
    var newrate;
    for(var i=0,j=0; i < rate.rates.length; i++) { if($(rate.rates[i][0]).rateId && !ams.checkInput($(rate.rates[i][0]).rateId,'float','<?=$strFieldMustBeFloat?>',{offsetLeft: -150})) return;  }
    for(var i=0,j=0; i < rate.rates.length; i++) {
	if($(rate.rates[i][0]).rateId) {
	    newrate=$(rate.rates[i][0]).rateId.value;
	    rates[j]=$(rate.rates[i][0]).old=formatFloat(newrate,5);
	    names[j++]=rate.rates[i][2];

	}
    }
    if(!names.length) { $('tr_edit_selected').hide(); return; }
    
	var url='modules/Rates/saverates.php';
	var pb="accountcode="+encodeURIComponent('<?=addslashes($accountcode)?>')+getQueryString(names,'names')+getQueryString(rates,'rates');

	new Ajax.Request(url,{postBody: pb,
		onComplete: function(t) {
		    if(t.responseText.indexOf("success") != -1) rate.resetRates();
		    else ams.showMessage('<?=$strErrorEditPrice?>','module-warning');
		
		}});


}

rate.setRates = function() {
    for(var i=0; i < rate.rates.length; i++) {
	if($(rate.rates[i][0]).rateId) $(rate.rates[i][0]).rateId.value=$('common_price').value;
    
    }

}
rate.resetRates = function() {
    for(var i=0,old; i < rate.rates.length; i++) {
	if(!$(rate.rates[i][0]).rateId) continue;
	    $(rate.rates[i][0]).innerHTML=$(rate.rates[i][0]).old;
	    $(rate.rates[i][0]).rateId=0;
	    $(rate.rates[i][0]).title='<?=$strDblClickToEdit?>';
    }
    $('tr_edit_selected').hide();
}

rate.makeEditable = function(id,evt,name,sel) {

	var rt = id.rateId ? id.rateId.value : id.innerHTML;
	var el = document.createElement('input'); el.size=8;
	el.type='text'; el.value=rt;
	el.style.backgroundColor='#baeea5'; id.innerHTML='';
	el.ondblclick = rate.saveRate.bindAsEventListener(this,el);
	el.parentId = id; id.rateId=el;	id.old=rt;
	el.ratename=name;
	el.setAttribute("autocomplete","off");
	id.appendChild(el); 
	id.title='<?=$strDblClickToAccept?>';
	el.focus();
}
rate.editSelected=function () {
    var arr=getChecked();
    if(!arr.length) return;
    $('common_price').value='0.00000';
    $('tr_edit_selected').show();
    for (var i=0; i < limit_display; i++) {
	if($('list_mark['+i+']') && $('list_mark['+i+']').checked) 
			    rate.makeEditable($(rate.rates[i][0]),0,rate.rates[i][2],1);
    }
    $('common_price').focus();
}

rate.deleteSelected=function () {
  var arr=getChecked();
  if(!arr.length) return;
  if (!confirm("<?=$strWinConfirmDeleteSelectedRates?>")) return;
	loadModule(1,'Rates','Rates','RatesList',$H({r_del_m: 1}));
}

rate.showCodes=function (el,name) {

    var id=el.id="__sc__"+Math.round(Math.random()*1000000000);
    var url='modules/Rates/showcodes.php';
    var pb="accountcode="+encodeURIComponent('<?=addslashes($accountcode)?>')+"&name="+encodeURIComponent(name);
    new Ajax.Request(url,{postBody: pb,
		onComplete: function(t) {
		    if(t.responseText) {
			var style='overflow: auto;';
			if(t.responseText.length > 4000) style+='height: 350px;'
			ams.toolTip(id,0,t.responseText,{close: 1, style: style, bgcolor: '#ffffff',container: 'module_rates'});    
		    } }});
}

rate.deleteRate=function (name) {
    if (!confirm("<?=$strWinConfirmDeleteRate?>\n" + name + "?")) return;
    loadModule(1,'Rates','Rates','RatesList',$H({r_del: 1,r_name: name}));
}

rate.editRate=function (name) {

    loadModule(1,'Rates','Rates','EditRate',$H({r_name: name}));
}
rate.onMouseOver = function (el,name) {
    rate.tsc = setTimeout(function() { rate.showCodes(el,name) },600);
}
</script>

<?
moduleForm(array("accountcode","t_id","current_page","limit_display"));
$tbl = new DataTbl();
$p=$tbl->addElement("fr_name","text",$strNameRate,1,$fr_name); $p->setOptions(35);
$p=$tbl->addElement("fr_code","text",$strCode,1,$fr_code); $p->setOptions(10);
$p=$tbl->addElement("fr_rfr","text","$strRate $strFrom",1,$fr_rfr); $p->setOptions(7,0,"","float");
$p=$tbl->addElement("fr_rto","text",$strTo,1,$fr_rto); $p->setOptions(7,0,"","float");
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$p->action="$('module_form').current_page.value=0;loadModule(1,'','Rates','RatesList');";
$tbl->cols=array("","","","","","","","30%");
$tbl->show();
echo "<br>";

$tbl = new ListTbl();

if(!$num) { echo "</form>"; $tbl->emptyTbl($strNoRates); }
$tbl->exportLink($strExportRates,"$('export-form').submit()");

if($_SESSION['acl']['Rates']['Access']==2) $cn=3;
$tbl->tblHead(array($cn,"40","20","","","10"),array($strNameRate,$strCodes,$strMinLen,$strMaxLen,"$strRate, $t_val"));
?>
    <tr id="tr_edit_selected" class="trcls1" style="display: none;">
    <td colspan=7 align="right" class="module-note" style="border-bottom: 1px solid #cccccc;">
    
    <?=$strPriceAll?>&nbsp;
    <a title="<?=$strSave?>" href="javascript:rate.saveRates()"><img align="absmiddle" src="images/save2.gif"></a>
    <a title="<?=$strResetAndClose?>" href="javascript:rate.resetRates()"><img align="absmiddle" src="images/close3.gif"></a>
    </td><td align="center" style="border-bottom: 1px solid #cccccc;">
    <input type="text" autocomplete="off" style="background-color: #b0c2e7;" size=8 name="common_price" id="common_price" value="0.00000" onkeyup="rate.setRates();">
    
    </td></tr>
<?
    for($j=0; $j < $num_disp; $j++) {
	$tbl->tblTr($j);
	if($_SESSION['acl']['Rates']['Access']==2) {
		$tbl->checkbox($j,$list[$j][1]);
		$tbl->td("",15,"","rate.deleteRate",$list[$j][1],"drop.gif",$strDeleteDest);
		$tbl->td("",15,"","rate.editRate",$list[$j][1],"edit.gif",$strEditDest);

	}
	$tbl->td($list[$j][1],"","left");
	//$tbl->td($list[$j][2]."...","","left","rate.showCodes",array("this",$list[$j][1]),"",$strShowCodes,"","","onclick");	
	?>
	 <td align="left"><a href="javascript:void(0)" 
	   <?/*onclick="rate.showCodes(this,'<?=ah($list[$j][1])?>')" */?>
	    onmouseover="rate.onMouseOver(this,'<?=ah($list[$j][1])?>')" onmouseout="clearTimeout(rate.tsc);">
	    <?=$list[$j][2]."..."?></a></td>
	<td nowrap><?=$list[$j][4]?></td>
	<td nowrap><?=$list[$j][5]?></td>
	<?  if($_SESSION['acl']['Rates']['Access']==2) {
	    $id=uniqid(true);
	?>
	<td nowrap id="<?=$id?>" ondblclick="rate.makeEditable(this,event,'<?=ah($list[$j][1])?>',0)" title="<?=$strDblClickToEdit?>" onmouseover="this.style.color='red'" onmouseout="this.style.color='black'"><?=$list[$j][7]?></td>
	<script>rate.rates[<?=$j?>]=Array('<?=$id?>','<?=$list[$j][7]?>','<?=addslashes($list[$j][1])?>');</script>
	<?  }else echo "<td nowrap>".$list[$j][7]."</td>";
	
	echo "</tr>";

    }
$tbl->tblEnd($current_page,$n_pages);

if($_SESSION['acl']['Rates']['Access']==2) {
    $tbl->addSelOper($strDeleteSelected,"rate.deleteSelected()","drop.gif");
    $tbl->addSelOper($strEditSelected,"rate.editSelected()","edit.gif");
    $tbl->selOper();
}
?>
</form>
<form name="export-form" id="export-form" method="post" action="include/export_csv.php?pref_export=Export_rates_" target="export-frame">
</form>
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
</div>	
