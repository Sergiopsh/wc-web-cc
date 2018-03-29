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
moduleHeader($strAstDB);

if(empty($current_page)) $current_page=0;

$astdb = new AstDB();

$login=$astdb->login($ami_ip,$ami_port,$ami_login,$ami_psw);

if($del && $login) {

    $astdb->db_fam=addslashes($del);
    $astdb->db_key=addslashes($key);
    $astdb->deleteRecord();
    $current_page=0;
}

if($save && $login) {
    
    
    if($db_update) {
        $astdb->db_fam=addslashes($db_fam_prev);
	$astdb->db_key=addslashes($db_key_prev);
	$astdb->deleteRecord();
    }
    if(!$astdb->error) {
	$astdb->db_fam=addslashes($db_fam);
	$astdb->db_key=addslashes($db_key);
	$astdb->db_val=addslashes($db_val);
	$astdb->insertRecord();
    }

}

if($login) {
    $astdb->setFilters($f_fam,$f_key,$f_val);
    $astdb->getList($current_page,$limit_display);
    $astdb->close();
    $num=$astdb->num;
    $list_db=$astdb->list_db;
    $num_disp=count($list_db);
}
$n_pages=num_pages($num,$limit_display,$current_page);

unset($db_fam,$db_key,$db_val,$db_fam_prev,$db_key_prev);


?>

<script language=javascript>
astDb = new ObjectD();

astDb.deleteFamily=function (id) {
    if (!confirm("<?=$strWinConfirmDeleteFamily?>")) return;
    loadModule(1,'','AstDB','AstDB');
}

astDb.deleteValue=function (id) {
    if (!confirm("<?=$strWinConfirmDeleteValue?>")) return;
    var f=$F('list_fam['+id+']');
    var k=$F('list_key['+id+']');
    loadModule(1,'','AstDB','AstDB',$H({del: f, key: k}));
}

astDb.editValue=function (id) {
    $('db_fam').value=$('db_fam_prev').value=$F('list_fam['+id+']');
    $('db_key').value=$('db_key_prev').value=$F('list_key['+id+']');
    $('db_val').value=$F('list_val['+id+']');
    $('button_save').value='<?=$strUpdate?>';
    $('dbrec_header').innerHTML='<?=$strEditRecord?>';
    $('db_update').value=1;
}

astDb.clearFields=function () {
    $('db_fam').value=$('db_key').value=$('db_val').value='';
    $('button_save').value='<?=$strAddNew?>';
    $('dbrec_header').innerHTML='<?=$strAddRecord?>';
    $('db_update').value=0;
    
}
astDb.save=function () {

    loadModule(1,'','AstDB','AstDB',$H({save: 1}));
}

<?

 if($astdb->error) {?>
 ams.showMessage("<?=$astdb->error?>","module-warning",1,"warning.gif")
 <?} else if($astdb->res){?>
  ams.showMessage("<?=$astdb->res?>","module-warning")
 <?}?>
</script>
<?
moduleForm(array("current_page","limit_display"));

$tbl = new DataTbl();
$p=$tbl->addElement("f_fam","text",$strDBFamily,1,$f_fam);
$p->setOptions(30);
$p=$tbl->addElement("f_key","text",$strDBKey,1,$f_key);
$p->setOptions(20);
$p=$tbl->addElement("f_val","text",$strDBValue,1,$f_val);
$p->setOptions(20);
$p=$tbl->addElement("","button","",1,$strSearch);
$p->action="searchModule()"; $p->align2="right";
$tbl->show();

echo "<br>";

$tbl = new ListTbl();
 if (!$num) $tbl->emptyTbl($strNoDBRecords,"100%",0);
 else {
  if($_SESSION['acl']['System']['Access']>1) $cn=2;
  $tbl->tblHead(array($cn,"35","","35"),array($strDBFamily,$strDBKey,$strDBValue));


    for($j=0; $j<$num_disp; $j++) {

	$tbl->tblTr($j);
	if($_SESSION['acl']['System']['Access']>1) {
	    $tbl->td("",20,"","astDb.deleteValue",$j,"drop.gif",$strDeleteRecord);
	    $tbl->td("",20,"","astDb.editValue",$j,"edit.gif",$strEditRecord);
	    $tbl->td($list_db[$j][0],20,"left","astDb.editValue",$j,"",$strEditRecord,"list_fam[$j]",$list_db[$j][0]);

	}else $tbl->td($list_db[$j][0],"","left");
	$tbl->td($list_db[$j][1],"","","","","","","list_key[$j]",$list_db[$j][1]);	
	$tbl->td($list_db[$j][2],"","","","","","","list_val[$j]",$list_db[$j][2],"",0);	
	echo "</tr>";

    }

$tbl->tblEnd($current_page,$n_pages);
}

if($_SESSION['acl']['System']['Access']>1) {?>

<br>
<div id="frame-module-header2" style="margin-bottom: 10px;">
    <div id="dbrec_header">  
    <?=$strAddRecord?>
  </div>
</div>

<?
$tbl = new DataTbl("70%");
$p=$tbl->addElement("db_fam","text",$strDBFamily,1,$db_fam);
$p->symbols=array(" ",":"); $p->setOptions(25,1,"","symbols");
$p=$tbl->addElement("db_key","text",$strDBKey,1,$db_key);
$p->symbols=array(" ",":"); $p->setOptions(25,1,"","symbols");
$p=$tbl->addElement("button_save","button","",1,($db_fam) ? $strUpdate : $strAddNew);
$p->action="astDb.save()"; $p->align2="right";
$p=$tbl->addElement("db_val","text",$strDBValue,2,$db_val);
$p->symbols=array(" ",":"); //$p->setOptions(25,1);
$p->setOptions(25,1,"","symbols");
$p=$tbl->addElement("","button","",2,$strClear);
$p->setOptions("astDb.clearFields()",false); $p->colspan=3; $p->align2="right";
$tbl->show();
?>
<INPUT TYPE="hidden" NAME="db_fam_prev" id="db_fam_prev" value="<?=hc($db_fam)?>" >
<INPUT TYPE="hidden" NAME="db_key_prev" id="db_key_prev" value="<?=hc($db_key)?>" >
<INPUT TYPE="hidden" NAME="db_update" id="db_update" value="0">
<?}?>
</form>

