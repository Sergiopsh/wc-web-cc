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
moduleHeader($strFaxesList);

$fax = new VirtualFax();

if((!isset($periodfrom)) || (!isset($periodto))) {
     $periodfrom=dbformat_to_dt(date("Y-m-d H:00",strtotime("-2 hours")));
     $periodto=dbformat_to_dt(date("Y-m-d H:59"));
}
if($_SESSION['acl']['VirtualFax']['Visibility']<2) $e_department=$_SESSION['department'];
if(!isset($sort_date)) $sort_date=0;
if(!isset($fixperiod)) $fixperiod=1;

$fax->setFilters($periodfrom,$periodto,$src,$dest,$sort_date,$e_department);

$fax->getList($current_page,$limit_display);
$list_faxes=$fax->list_faxes;
$num=$fax->num;
$num_disp=count($list_faxes);

$iformat=dt_format();
$n_pages=num_pages($num,$limit_display,$current_page);

unset($list_mark);
?>
<script>

vf = new ObjectD();

var sort_date=<?=$sort_date?>;

vf.lookPDF=function(file) {
    var url="modules/VirtualFax/tifftopdf.php";
    var pb="file="+encodeURIComponent(file);
    new Ajax.Request(url, {postBody: pb,
	    onComplete: function(t) {
		if(t.responseText.indexOf("success") == -1) 
		    ams.showMessage('<?=$strPDFCreateError?>','fax-warning',0,'warning.gif');
		else {
		    var wt="top=" + Math.ceil(screen.availHeight/93);
		    var wl=", left=" + Math.ceil(screen.availWidth/9);
		    url="modules/VirtualFax/lookpdf.php?file="+file;
		    var h = Math.ceil(screen.availHeight * 0.85);
		    var w = Math.ceil(screen.availWidth * 0.8);
		    var pdf = open(url, "pdffile", "height="+h+", width="+w+",toolbar=0,menubar=0,status=1,resizable=1,scrollbars=0," + wt + wl);
		}
	    }});
}

vf.setFixPeriod=function(p) {
    $('module-warning').style.display="none";
    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
    
}
vf.searchFaxes=function(){
    var from = Date.parseDate($('periodfrom').value,"<?=$iformat?> %H:%M").getTime();
    var to = Date.parseDate($('periodto').value,"<?=$iformat?> %H:%M").getTime();
    if(from > to) {
	ams.showMessage('<?=$strErrorPeriodSelection?>','module-warning',0,'note.gif');
	return;
    }
    if(to > (from + 2678400000)) {
	ams.showMessage('<?=$strTooBigPeriodSelection?>','module-warning',0,'note.gif');
	return;
    }
    loadModule(1,'','VirtualFax','FaxesList');

}
vf.sortListByDate=function() {
    loadModule(1,'','VirtualFax','FaxesList',$H({sort_date: sort_date ? 0:1}));
}

vf.downloadFile=function (file) {
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=file;
    $('download-form').submit(); 
}
vf.downloadSelected=function () {
    var files = getChecked();
    if(!files.length) return;
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=files;
    $('download-form').submit(); 
}
vf.deleteFax=function (file) {

  if (!confirm("<?=$strWinConfirmDelete?>")) return;
    var url='lib/deletefiles.php';
    var pb ="files=" + encodeURIComponent(file);
    new Ajax.Request(  url,
		      { postBody: pb, 
			onComplete: function(t){
			    if(t.responseText.indexOf("success") == -1) {
				ams.showMessage('<?=$strErrorDeleteFile?><br>'+t.responseText,'fax-warning',1,'warning.gif');
			    } else {
			    	$('module_form').current_page.value=0;
				loadModule(1,'','VirtualFax','FaxesList');
			    }
			 }
		      });
}
vf.deleteSelected=function () {
  var files = getChecked();
  if(!files.length) return;
   if (!confirm("<?=$strWinConfirmDelete?>")) return;
  var url='lib/deletefiles.php';
  var pb ="delete=1&"+getQueryString(files,'files');
    new Ajax.Request(  url,
		      { postBody: pb, 
			onComplete: function(t){
			    if(t.responseText.indexOf("success") == -1) {
				ams.showMessage('<?=$strErrorDeleteFile?><br>'+t.responseText,'fax-warning',1,'warning.gif');
			    } else {
			    	$('module_form').current_page.value=0;
				loadModule(1,'','VirtualFax','FaxesList');
			    }
			 }
		      });
}

vf.sendEmail=function () {
  var faxes = getChecked();
  if(!faxes.length) return;
  var url='modules/VirtualFax/sendemail.php';
  var pb ="faxes=" + faxes;
  new Ajax.Request( url,
		      { postBody: pb, onFailure: function() {},
			onComplete:  function(t) {
			    if(t.responseText) {
			    	Element.show('service-div');
				Element.update('service-div',t.responseText);
			    }
			}
		      });
 
}
    
</script>

<form name="service-form" id="service-form" method="post" target="service-frame">
</form>

<?
moduleForm(array("current_page","start_date"));
$tbl = new DataTbl("",0,0,3,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strSearch);
$p->action="vf.searchFaxes()"; $p->align2="right";
$p=$tbl->addElement("dest","text",$strAbonent,2,$dest);
$p->setOptions(21);

$p=$tbl->addElement("fixperiod","select",$strFixPeriod,2,$fixperiod);
$p->options=array("0" => "&nbsp;---&nbsp;","1" => $strHour3, "2" => $strHour12, "3" => $strToday,
  "4" => $strYesterday,"5" => $strDay3, "6"=> $strThisWeek, "7"=> $strThisMonth, "8" => $strPrevMonth);
$p->action="vf.setFixPeriod(this.value)";

$p=$tbl->addElement("src","text",$strSource,3,$src);
$p->setOptions(21);
$tbl->cols=array("10%","20%","7%","20%");
$tbl->show();

?>
<div id="fax-warning" class="module-warning" style="display: none;">
</div>
<div id="service-div" style="width: 100%; display: none;margin-bottom: 10px;">
</div>

<?
$tbl = new ListTbl();
if (!$num) { echo "</form>"; $tbl->emptyTbl($strNoFaxes); }

?>

<table border=0 cellpadding=0 cellspacing=0 width="70%">
<tr><td align="right" width="50%" class="module-note"><?echo "$strNumFiles: $num";?>&nbsp;
</td>
<td align="left" class="module-note">&nbsp;
</td>
</tr>
</table>

<?

if($_SESSION['acl']['VirtualFax']['Access']>1) $cn=3;
else $cn=2;
$img = $sort_date ? "up.gif": "down.gif";
$dh="<a href=\"javascript:vf.sortListByDate();\">$strDate&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src=\"images/$img\" style=\"margin: 0;border:0;\"></a>";
$tbl->tblHead(array($cn,"19","21","7","","12","2"),array($dh,$strFaxFileName,"&nbsp;$strKByte&nbsp;",$strSource,$strAbonent,"&nbsp;"));

    for($j=0; $j < $num_disp; $j++) {
           $tbl->tblTr($j);
	   $f=basename($list_faxes[$j][1]);
	   $f=substr($f,0,10)."...".substr($f,-8,3);
	$tbl->checkbox($j,$list_faxes[$j][1],6);	
	if($_SESSION['acl']['VirtualFax']['Access']>1) {
	    $tbl->td("",15,"","vf.deleteFax",$list_faxes[$j][1],"drop.gif",$strDeleteFax);

	}
	$tbl->td("",15,"","vf.downloadFile",$list_faxes[$j][1],"download.gif",$strDownloadFile);
	$tbl->td($list_faxes[$j][0]);
	$tbl->td($f.".tif","","","vf.lookPDF",$list_faxes[$j][1],"",$strLookPDF);
	$tbl->td(number_format($list_faxes[$j][2]/1024,1));
	$tbl->td($list_faxes[$j][4]);
	$tbl->td($list_faxes[$j][5]);
	$tbl->td("",15,"","vf.lookPDF",$list_faxes[$j][1],"pdf_icon.gif",$strLookPDF);
	echo "</tr>";

    }
$tbl->tblEnd($current_page,$n_pages);

$tbl->addSelOper($strDeleteSelected,"vf.deleteSelected()","drop.gif");
$tbl->addSelOper($strDownloadSelected,"vf.downloadSelected()","download.gif");
$tbl->addSelOper($strEmailSelected,"vf.sendEmail()","email.gif");
$tbl->selOper();
?>
<br>
</form>
<form name="download-form" id="download-form" method="post" target="download-frame">
<input type="hidden" name="files" id="files" value="">
</form>
<iframe name="download-frame" id="download-frame" frameborder=0 style="width: 0px; height: 0px; ">
</iframe>
