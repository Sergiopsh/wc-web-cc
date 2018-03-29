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
moduleHeader($strCodesDirectory);

$code = new CodesDirectory();

if ($c_del) $code->deleteCodes($c_name);
if ($c_del_m) $code->deleteCodes($list_mark);

if(empty($current_page)) $current_page=0;

$start_time=getmicrotime();
$list=$code->getList(array($fc_name,$fc_code),$current_page,$limit_display);
$num_disp=count($list);
$num = $code->num_rows;

$n_pages = num_pages($num,$limit_display,$current_page);

?>
<div id="module_codesdirectory">
<script>

cd = new ObjectD();

cd.deleteSelected = function() {
 if (confirm("<?=$strWinConfirmDeleteSelectedDest?> ?\n")) {
    loadModule(1,'','','',$H({c_del_m:1}));
 }

}
cd.showCodes = function (el,name) {
    var id=el.id="__sc__"+Math.round(Math.random()*1000000000);
    var url='modules/CodesDirectory/showcodes.php';
    var pb="name="+encodeURIComponent(name);
    new Ajax.Request(url,{postBody: pb,
		onComplete: function(t) {
		    if(t.responseText) {
			var style='overflow: auto;';
			if(t.responseText.length > 4000) style+='height: 350px;'
			ams.toolTip(id,0,t.responseText,{close: 1, style: style, bgcolor: '#ffffff',container: 'module_codesdirectory'});    
			//ams.toolTip(id,0,t.responseText,{close: 1, style: style, bgcolor: '#ffffff'});    
		    }
		}});
}
cd.deleteCode = function(name) {
 if (confirm("<?=$strWinConfirmDeleteDest?>\n" + name + "?")) {
    loadModule(1,'','','',$H({c_del:1,c_name: name}));
 }
}
cd.editCode = function(name) {
    loadModule(1,'','','EditDest',$H({c_name: name}));
}
cd.onMouseOver = function(el,name) {
    cd.tsc=setTimeout(function() { cd.showCodes(el,name) },600);
}
</script>
<br>
<? 
moduleForm(array("current_page","limit_display")); 

$tbl = new DataTbl("90%");
$p=$tbl->addElement("fc_name","text",$strNameDest,1,$fc_name); $p->setOptions(20);
$p=$tbl->addElement("fc_code","text",$strCode,1,$fc_code); $p->setOptions(20);
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$tbl->cols=array("10%","20%","5%","20%","30%");
$tbl->show();
echo "<br>";

$tbl = new ListTbl();
if (!$num) { echo "</form>"; $tbl->emptyTbl($strNoCodes,"90%"); }
$tbl->exportLink($strExportCodes,"$('export-form').submit()","90%");

  if($_SESSION['acl']['CodesDirectory']['Access']==2) $cn=3;
  $tbl->tblHead(array($cn,"45","","15","15"),array($strNameDest,$strCodes,$strMinLen,$strMaxLen),"90%");
	 
	for($j=0; $j < $num_disp; $j++) {
	  $tbl->tblTr($j);
	  if($_SESSION['acl']['CodesDirectory']['Access']==2) {
	
		$tbl->checkbox($j,$list[$j][1]);
		$tbl->td("",15,"","cd.deleteCode",$list[$j][1],"drop.gif",$strDeleteDest);    
		$tbl->td("",15,"","cd.editCode",$list[$j][1],"edit.gif",$strEditDest);    

	  }
	  $tbl->td($list[$j][1],"","left");
	  //$tbl->td($list[$j][2]."...","","left","cd.showCodes",array("this",$list[$j][1]),"",$strShowCodes,"","","onclick");
	  ?>
	  <td align="left"><a href="javascript:void(0)" 
	    <?/*onclick="cd.showCodes(this,'<?=ah($list[$j][1])?>')" */?>
	    onmouseover="cd.onMouseOver(this,'<?=ah($list[$j][1])?>')" 
	    onmouseout="clearTimeout(cd.tsc);">
	    <?=$list[$j][2]."..."?></a></td>
	    
	  <td><?=$list[$j][4]?></td>
	  <td><?=$list[$j][5]?></td>
	  </tr>

        <?	

	}

$tbl->tblEnd($current_page,$n_pages);

if($_SESSION['acl']['CodesDirectory']['Access']==2) {
 $tbl->addSelOper($strDeleteSelectedDest,"cd.deleteSelected()","drop.gif");
 $tbl->selOper();
}

?>
</form>
<form name="export-form" id="export-form" method="post" action="include/export_csv.php?pref_export=Export_codes_" target="export-frame">
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
</div>