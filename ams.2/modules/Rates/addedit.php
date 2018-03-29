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
if(!isset($_SESSION['current_t_id'])) die('Not a Valid Entry');
if($action == "EditRate" && !isset($r_name))  die('Not a Valid Entry'); 
include_once("lib/Class.datatbl.php");

$t_id=$_SESSION['current_t_id'];

$tplan = new TariffPlan($t_id);
list($t_name,$accountcode,$t_step,$t_val,$o_name)=$tplan->get_data();;

//echo "t_id=$t_id accountcode=$accountcode <br>";
moduleHeader(($action == "AddRate") ? $strAddRate : $strEditRate);

showMsg("$strTariffPlan: <b>".hc($t_name)."&nbsp;($t_step)</b>","","","","margin-top: 5px;margin-bottom: 5px;");

$rate = new Rate($r_name,$accountcode);

if ($r_save && $r_name) {
    $rate->set_fields($r_cfr,$r_cto,$r_min_len,$r_max_len,$r_rate,$num_codes,$r_name_prev);
    if($rate->is_exists()) showMsg($strRateExists,"module-warning");
    else {
	if($action == "EditRate") {
	   $rate->update();
	    showMsg($strUpdateRateSuccess,"module-warning");
	}else {
	    $rate->insert($add_to_cd);
	    if(!$act) loadModule(0,'Rates','Rates','RatesList',"{fr_name: '".addslashes($r_name)."'}");
	    showMsg($strSaveRateSuccess,"module-warning");
	    unset($r_name,$r_rate,$r_cfr,$r_cto,$r_min_len,$r_max_len,$num_codes);	
	}

    }
}

if($action == "EditRate" && !$r_save) {
    list($r_name,$r_cfr,$r_cto,$r_min_len,$r_max_len,$r_rate)=$rate->get_data();
    $num_codes=count($r_cfr);
    $r_name_prev=$r_name;
}
if(!isset($num_codes)) $num_codes=1;
if(!isset($r_min_len) || !is_numeric($r_min_len)) $r_min_len=1;
if(!isset($r_max_len) || !is_numeric($r_max_len)) $r_max_len=20;
if(!isset($r_rate) || !is_numeric($r_rate)) $r_rate=0;
?>
<div id="module_rates">
<script>
rate = new ObjectD();

rate.saveRate = function() {

    var e = false;
    var fr,to;
    var rate = $F('r_rate');
    var min_len = parseInt($F('r_min_len'));
    var max_len = parseInt($F('r_max_len'));
    var num = $F('num_codes');
    for (var i = 0 ; i < num ; i++) {
	fr = $F('r_cfr['+i+']').strip();
	to = $F('r_cto['+i+']').strip();
	if(!e && fr.length) e = true;
	if(fr && !ams.checkInput('r_cfr['+i+']','integer','<?=$strMustBeInteger?>')) return; 
	if(fr && !ams.checkInput('r_cfr['+i+']','integer','<?=$strMustBeInteger?>')) return; 
    }
    if(!e) { ams.toolTip('r_cfr[0]',0,'<?=$strMustFillCodeTable?>',{img: 'warning2.gif',hlight: 1}); return; }
    if(min_len > max_len)  { 
	  ams.toolTip('r_min_len',0,'<?=$strWrongLength?>',{img: 'warning2.gif', hlight: 1}); 
	  return; 
    }
    loadModule(1,'','Rates','<?=$action?>',$H({r_save: 1}));
}



rate.addCodes = function() {

   $('module_form').num_codes.value++;
   var n=$('module_form').num_codes.value;
   var s = '<table border=0 cellpadding=0 cellspacing=1>';
   var v1,v2;
   for (var i=0; i < n; i++) {
    v1=''; v2='';    
    if($('r_cfr['+i+']')) v1=$('r_cfr['+i+']').value;
    if($('r_cto['+i+']')) v2=$('r_cto['+i+']').value;
    s+="<tr><td align='right'>" +
       "<INPUT TYPE='text' NAME='r_cfr["+i+"]' id='r_cfr["+i+"]' size='15' value='"+v1+"'>" +
       "</td><td align='left'>" +
       "<INPUT TYPE='text' NAME='r_cto["+i+"]' id='r_cto["+i+"]' size='15' value='"+v2+"'>" +
       "</td></tr>";
 
   }
   s+="</table>";
   $('tbl-dst-codes').innerHTML=s;

}

</script>
<?
moduleForm(array("num_codes","r_name_prev","t_id","accountcode","current_page"));
?>
<table border="0" width="100%" cellpadding="2" cellspacing="4">
<tr>
<td valign="top" width="50%">
<?

$tbl = new DataTbl("100%",0,0,9,"margin-top: 12px;");
$p=$tbl->addElement("r_name","text",$strNameRate,1,$r_name); $p->setOptions(45,1); $p->colspan=3;
$p=$tbl->addElement("r_min_len","text",$strMinLen,2,$r_min_len); 
$p->setOptions(4,1,2,"integer"); $p->width2="10%";
$p=$tbl->addElement("r_max_len","text",$strMaxLen,2,$r_max_len); 
$p->setOptions(4,1,2,"integer"); $p->width1="10%";
$p=$tbl->addElement("r_rate","text",$strRate,3,number_format($r_rate,5)); $p->setOptions(10,0,8,"float"); 
$p=$tbl->addElement("","simple","",3,$t_val);$p->colspan=3; 
$tbl->show();
?>
<br>
<table  width="100%" class="input-data-tbl">
    <tr ><td align=right width="50%"><font color=red>*</font>&nbsp;&nbsp;<?=$strCodeFrom?>: &nbsp;&nbsp;</td><td><?=$strCodeTo?>:</td><tr>
    <tr><td colspan=2 align=center id="tbl-dst-codes">

    <table border="0" cellpadding="0" cellspasing="0">
    
    <?
	for($i=0; $i<$num_codes;$i++) { 	    
		if (!isset($r_cfr[$i])) $r_cfr[$i]="";
		if (!isset($r_cto[$i])) $r_cto[$i]=$r_cfr[$i];
    ?>
      <tr><td align="right">
      <INPUT TYPE="text" NAME="r_cfr[<?=$i?>]" id="r_cfr[<?=$i?>]" size="15" value="<?=$r_cfr[$i]?>" >
      </td><td align="left">
      <INPUT TYPE="text" NAME="r_cto[<?=$i?>]" id="r_cto[<?=$i?>]" size="15" value="<?=$r_cto[$i]?>" >
      </td></tr>
    <? } ?>


      </table>	
     
     </td></tr>
     <tr height="2"><td></td></tr>
 </table>
 <table border="0" width="100%">
    <tr><td width="48%">&nbsp;</td>
    <td width="10%" align=left>
    <img src="images/arrow_ltr.png">
    </td><td><a href="javascript:rate.addCodes()"><?=$strAddCodes?></a>
     </td></tr>
 </table>

<?
$tbl2 = new DataTbl("100%",0,5);
if($action == "AddRate") {
    $p=$tbl2->addElement("act","select",$strSaveAndThen,1,$act);
    $p->options=array(0=>$strReturn,1=>$strAddNewRate);
    $p=$tbl2->addElement("","button","",1,$strSave); 
    $p->action="rate.saveRate()"; $p->align2="right";
}else {
    $p=$tbl2->addElement("","button","",1,$strUpdate); 
    $p->action="rate.saveRate()"; $p->align2="center";

}
$tbl2->required=$tbl->required; $tbl2->filters=$tbl->filters;
$tbl2->show();

if($action == "AddRate") {
?>

  <table border="0">
  <tr><td>
  <input type="checkbox" name="add_to_cd" id="add_to_cd" value="1" <?if ($add_to_cd) echo "checked";?> class="checkbox">
  </td><td class="module-note">
  <font size=1> - <?=$strAddToCD?></font>
  </td></tr></table>
<?}
?>

</td><td valign="top">

<?
/*----- codes directory ----*/

if(!isset($cd_dest)) $cd_dest="";
if(!isset($cd_code)) $cd_code="";

?>
<script>
rate.onMouseOver=function (el,name) {
    rate.tsc=setTimeout(function() {rate.showCodes(el,name) },600);
}
rate.showCodes=function (el,name) {

    var id=el.id="__sc__"+Math.round(Math.random()*1000000000);
    var url='<?=$www_dir?>/modules/CodesDirectory/showcodes.php';
    var pb="name="+encodeURIComponent(name);
    new Ajax.Request(url,{postBody: pb,
		onComplete: function(t) {
		    if(t.responseText) {
			var style='overflow: auto;';
			if(t.responseText.length > 4000) style+='height: 350px;'
			ams.toolTip(id,0,t.responseText,{close: 1, style: style, bgcolor: '#ffffff',container: 'module_rates',offsetLeft: -150});    
		    } }});
}

rate.setPage = function(n) {
    $('current_page').value=n-1;
    rate.refreshCD();
}

rate.refreshCD = function() {
    var url="modules/Rates/embedcd.php";
    var pb="cd_dest="+encodeURIComponent($('cd_dest').value)+"&cd_code="+encodeURIComponent($('cd_code').value)+"&current_page="+$('current_page').value;
    new Ajax.Request(url,{postBody: pb,
	    onComplete: function(t) {
		if(t.responseText) Element.update('embedcd',t.responseText);	    
	    
	    }});
}
rate.insertDest = function(name) {
    var url="modules/Rates/insertdest.php";
    var pb="name="+encodeURIComponent(name);
    new Ajax.Request(url,{postBody: pb,
	    onComplete: function(t) {
		if(t.responseText) eval(t.responseText);	    
	    
	    }});
}
</script>
<center><div class="module-note"><font size=1><b><?=$strCodesDirectory?></b></font></div></center>
<?
$tbl = new DataTbl();
$p=$tbl->addElement("cd_dest","text",$strNameDest,1,$cd_dest); $p->setOptions(20);
$p->action="onkeyup=\"$('current_page').value=0;rate.refreshCD();\"";
$p=$tbl->addElement("cd_code","text",$strCode,1,$cd_code); $p->setOptions(15);
$p->action="onkeyup=\"$('current_page').value=0; rate.refreshCD();\"";
$tbl->show();
?>  
<div id="embedcd">
</div>

<script>   
 rate.refreshCD(); 
</script>

<?/*--- end codes directory ---*/?>

</td>
</tr>
</table>
</form>
</div>


