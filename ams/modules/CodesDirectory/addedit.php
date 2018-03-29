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
moduleHeader(($action == "EditDest") ? $strEditDestHeader : $strAddDestHeader);

$code = new CodesDirectory($c_name);

if ($c_save) {
	$code->set_fields($c_cfr,$c_cto,$c_min_len,$c_max_len,$num_codes,$c_name_prev);
	if($code->is_exists()) showMsg($strDestExists,"module-warning");        
	else {
	    if($action == "EditDest") $code->update();
	    else $code->insert();
	    if($action == "EditDest" || !$act) 
		    loadModule(0,'CodesDirectory','CodesDirectory','CodesDirectoryList',"{fc_name: '".ah($c_name)."'}");
	   showMsg($strSaveDestSuccess,"module-warning");
	   unset($c_save,$c_name,$c_name_prev,$c_cfr,$c_cto,$num_codes);
	}
}

if($action == "EditDest" && !$add_codes && !$c_save) {
    $code->getData();
    list($c_cfr,$c_cto,$c_min_len,$c_max_len,$num_codes)=$code->getData();
    $c_name_prev=$c_name;
}

if(!isset($num_codes)) $num_codes=1;
if(!isset($c_min_len) || !is_numeric($c_min_len)) $c_min_len=1;
if(!isset($c_max_len) || !is_numeric($c_max_len)) $c_max_len=20;
?>
<script>
cd = new ObjectD();

cd.addCode = function() {
  $('module_form').num_codes.value++;
  loadModule(1,'','CodesDirectory','<?=$action?>',$H({add_codes: 1}));

}
cd.saveCode = function() {

    var e = false;
    var fr,to;
    var min_len = parseInt($F('c_min_len').strip());
    var max_len = parseInt($F('c_max_len').strip());
    var num = $F('num_codes');
    for (var i = 0 ; i < num ; i++) {
	fr = $F('c_cfr['+i+']').strip();
	to = $F('c_cto['+i+']').strip();
	if(!e && fr.length) e = true;
	if(fr && !ams.checkInput('c_cfr['+i+']','integer','<?=$strFieldMustBeInteger?>')) return;
	if(to && !ams.checkInput('c_cto['+i+']','integer','<?=$strFieldMustBeInteger?>')) return;
    }
    if(!e){ ams.toolTip('c_cfr[0]',0,'<?=$strMustFillCodeTable?>',{img: 'warning2.gif',hlight: 1}); return; }
    if(min_len > max_len)  { 
	  ams.toolTip('c_min_len',0,'<?=$strWrongLength?>',{img: 'warning2.gif',hlight: 1}); 
	  return; 
    }
    loadModule(1,'','CodesDirectory','<?=$action?>',$H({c_save: 1}));
}

</script>

<?
moduleForm(array("num_codes","c_name_prev"));
$tbl = new DataTbl("50%");
$p=$tbl->addElement("c_name","text",$strNameDest,1,$c_name);
$p->setOptions(45,1); $p->colspan=3;
$p=$tbl->addElement("c_min_len","text",$strMinLen,2,$c_min_len);
$p->setOptions(6,1,2,"integer");
$p=$tbl->addElement("c_max_len","text",$strMaxLen,2,$c_max_len);
$p->setOptions(6,1,2,"integer");
$tbl->cols=array("25%","25%","23%");
$tbl->show();
?>

<br>
 <table  width="50%" class="input-data-tbl" border=0 cellpadding=0 cellspacing=1>
    <tr heigth=4><td align=right width="50%"><font color=red>*</font>&nbsp;&nbsp;<?=$strCodeFrom?>: &nbsp;&nbsp;</td><td><?=$strCodeTo?>:</td><tr>


    <?


	for($i=0;$i<$num_codes;$i++) { 	    

		//if(!isset($c_cfr[$i])) $c_cfr[$i]="";	
		if(!isset($c_cto[$i])) $c_cto[$i]=$c_cfr[$i];


    ?>
      <tr><td align=right>
      <INPUT TYPE="text" NAME="c_cfr[<?=$i?>]" id="c_cfr[<?=$i?>]" size="15" value="<?=$c_cfr[$i]?>" >
      </td><td align="left">
      <INPUT TYPE="text" NAME="c_cto[<?=$i?>]" id="c_cto[<?=$i?>]" size="15" value="<?=$c_cto[$i]?>" >
      </td></tr>
    <? }?>	

      
     <tr height="3"><td></td></tr>
 </table>

 <table border=0 width="50%">
    <tr><td width="48%">&nbsp;</td>
    <td align=left width="10%">
    <img align="absmiddle" src="images/arrow_ltr.png">
    </td><td class="module-note"><a href="javascript:cd.addCode()"><?=$strAddCodes?></a>
    </td></tr>
 </table>
<?
$tbl2 = new DataTbl("50%",0,0,7);
if($action == "EditDest") {
    $p=$tbl2->addElement("","button","",1,$strUpdate);
    $p->action="cd.saveCode()"; $p->align2="center";
}else {
    $p=$tbl2->addElement("act","select",$strSaveAndThen,1,$act);
    $p->options=array(0=>$strReturn,1=>$strAddNewDest);
    $p=$tbl2->addElement("","button","",1,$strAddNew);
    $p->action="cd.saveCode()"; $p->align2="right";
}
$tbl2->required=$tbl->required; $tbl2->filters=$tbl->filters; 
$tbl2->show();
?>

</form>

