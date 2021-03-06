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
moduleHeader($strRecording);

$rec = new Recording();

if(!isset($sort_date)) $sort_date=0;
if(!isset($fixperiod)) $fixperiod=1;

$iformat=dt_format();

if(!(isset($periodfrom)) || (!isset($periodto))) {
    $periodto=dbformat_to_dt(date("Y-m-d H:59"));
    $periodfrom=dbformat_to_dt(date("Y-m-d H:00",strtotime("-2 hours")));

}

$rec->setFilters($periodfrom,$periodto,$src,$dest,$dirtype,$sort_date,$e_department);


$rec->getList($current_page,$limit_display);
$list_records=$rec->list_recs;
$num=$rec->num;
$total_dur=$rec->total_dur;
$num_disp = count($list_records);

$n_pages=num_pages($num,$limit_display,$current_page);

unset($list_mark);
?>
<script>
rec = new ObjectD();

var sort_date=<?=$sort_date?>;

rec.setFixPeriod=function(p) {
    $('module-warning').style.display="none";
    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");

}
rec.searchRecords=function(){
    var from = Date.parseDate($('periodfrom').value,"<?=$iformat?> %H:%M").getTime();
    var to = Date.parseDate($('periodto').value,"<?=$iformat?> %H:%M").getTime();
    if(from > to) {
	ams.showMessage('<?=$strErrorPeriodSelection?>','module-warning');
	return;
    }
    if(to > (from + 2678400000)) {
	ams.showMessage('<?=$strTooBigPeriodSelection?>','module-warning');
	return;
    }
    searchModule();

}
rec.sortListByDate=function() {
    loadModule(1,'Recording','Recording','RecordingList',$H({sort_date: sort_date ?0:1}));
}

rec.downloadFile=function (file) {
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=file;
    $('download-form').submit();
}
rec.downloadSelected=function () {
    var files = getChecked();
    if(!files.length) return;
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=files;
    $('download-form').submit();
}
rec.deleteRecords = function (files) {
  if (!confirm("<?=$strWinConfirmDelete?>")) return;
    var url='lib/deletefiles.php';
    var pb ="files=" + files;
    new Ajax.Request(  url, { postBody: pb,
			onComplete: function(t){
			    if(t.responseText.indexOf("success") == -1) {
				ams.showMessage('<?=$strErrorDeleteFile?><br>'+t.responseText,'rec-warning',1,'warning.gif');
			    } else {
			    	$('module_form').current_page.value=0;
				loadModule(1,'Recording','Recording','RecordingList');
			    }
			 }
		      });
}
rec.deleteSelected=function () {
  var files = getChecked();
  if(files.length == 0) return;
  if (!confirm("<?=$strWinConfirmDelete?>")) return;
  var url='lib/deletefiles.php';
  var pb ="delete=1&"+getQueryString(files,'files');
  new Ajax.Request(  url, { postBody: pb,
			onComplete: function(t){
			    if(t.responseText.indexOf("success") == -1) {
				ams.showMessage('<?=$strErrorDeleteFile?><br>'+t.responseText,'rec-warning',1,'warning.gif');
			    } else {
			    	$('module_form').current_page.value=0;
				loadModule(1,'Recording','Recording','RecordingList');
			    }
			 }
		      });
}

rec.sendEmail=function () {
  var files = getChecked();
  if(files.length == 0) return;
  var url='modules/Recording/sendemail.php';
  var pb ="files=" + files;
  new Ajax.Request( url, { postBody: pb,
			  onComplete:  function(t) {
			    if(t.responseText) {
			    	Element.show('service-div');
				Element.update('service-div',t.responseText);
			    }
			}
		      });

}

rec.listenRecord=function (file) {
    $('listen-record-div').show();
    var s="<embed src='lib/loadfile.php?file="+file+"&type=wav"+
    "' autostart='true' loop='false' width='300' height='40'></embed>";
    s+="&nbsp;<a href='javascript:void(0)' title='<?=$strSwitchOff?>' onclick='$(\"listen-record-div\").innerHTML=\"\"'>"+
	"<img src='images/hide.gif' align='top'></a>";
    $('listen-record-div').innerHTML=s;

}

</script>
<form name="service-form" id="service-form" method="post" target="service-frame">
</form>


<?
moduleForm(array("current_page","sort_date"));
$tbl = new DataTbl("",0,0,3,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strSearch);
$p->action="rec.searchRecords()"; $p->align2="right";
$p=$tbl->addElement("dest","text",$strAbonent,2,$dest);
$p->setOptions(21);
$p=$tbl->addElement("dirtype","select",$strDirection,2,$dirtype);
$p->options=array("a" => $strAll,"i" => $strIncoming, "o" => $strOutgoing);
$p=$tbl->addElement("src","text",$strSource,3,$src);
$p->setOptions(21);
$p=$tbl->addElement("fixperiod","select",$strFixPeriod,3,$fixperiod);
//$p->options=array("0" => "&nbsp;---&nbsp;","1" => $strHour3, "2" => $strHour12, "3" => $strToday,
//  "4" => $strYesterday,"5" => $strDay3, "6"=> $strThisWeek, "7"=> $strThisMonth, "8" => $strPrevMonth);
$p->options=array(0 => "&nbsp;---&nbsp;",1 => $strHour3,2 => $strHour12,3 => $strToday,
 4 => $strYesterday,5 => $strDay3,6 => $strThisWeek,7 => $strThisMonth,8  => $strPrevMonth);
$p->action="rec.setFixPeriod(this.value)";

$tbl->cols=array("10%","20%","7%","20%");
$tbl->show();

?>


<div id="rec-warning" class="module-warning" style="display: none;">
</div>
<div id="service-div" style="width: 100%; display: none; margin-bottom: 10px;">
</div>
<div id="records_list_table">
<?
$tbl = new ListTbl();
if(!$num) { echo "</form>"; $tbl->emptyTbl($strNoRecords); }
?>
<table border=0 cellpadding=0 cellspacing=0 width="100%">
<tr><td align="right" width="50%"><div class="module-note"><?echo "$strNumCalls: $num";?>&nbsp;</div>
</td>
<td align="left"><div class="module-note">&nbsp;
<?echo "$strTotalDur: ";
if($total_dur >= 3600) printf("%02d $strHour %02d $strMin %02d $strSec",intval($total_dur/3600),intval(($total_dur%3600)/60),intval($total_dur%60));
else printf("%02d $strMin %02d $strSec",intval($total_dur/60),intval($total_dur%60));
?>
</div>
</td>
</tr></table>


<?
if($_SESSION['acl']['Recording']['Access']>1) $cn=3;
else $cn=2;
if($sort_date) $img="up.gif'"; else $img="down.gif'";
$dh="<a href=\"javascript:rec.sortListByDate();\">$strDate&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src='images/$img' style=\"margin: 0;border:0;\"></a>";
$tbl->tblHead(array($cn,"13","14","8","","","8","9"),array($dh,$strRecordFileName,$strKByte,$strSource,$strDestination,$strDur,$strDirection));

	for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	    $f=basename($list_records[$j][1]);
	    $f=substr($f,0,7)."...".substr($f,-8,4);

	?>
	<td width=6><input type="checkbox" class="checkbox" id="list_mark[<?=$j?>]" name="list_mark[<?=$j?>]" value="<?=$list_records[$j][1]?>" <?if ($list_mark[$j]) echo "checked";?>></td>
	<? if($_SESSION['acl']['Recording']['Access']>1) {?>
	<td width=6><a title="<?=$strDeleteRecord?>"  href="javascript:rec.deleteRecords('<?=$list_records[$j][1]?>')"><img src="images/drop.gif" ></a></td>
	<?}?>
	<td width=6><a title="<?=$strDownloadFile?>"  href="javascript:rec.downloadFile('<?=$list_records[$j][1]?>')"><img src="images/download.gif" ></a></td>
	<td nowrap><?=$list_records[$j][0]?></td>  <!--$list_records[1][0] - ���� ������ -->
	<td nowrap><a href="javascript:rec.listenRecord('<?=$list_records[$j][1]?>')" title="<?=$strListenRecord?>"><?=$f?>.wav</a></td>    <!--$f - ������ �� ����-->
	<td nowrap><?=number_format($list_records[$j][2]/1024,1);?></td>     <!--������ � �� -->
	<td nowrap><?=$list_records[$j][4]?></td>    <!--��������-->
	<td nowrap><?=$list_records[$j][5]?></td>    <!--�����������-->
	<td nowrap>
	<?
	if($list_records[$j][7]) echo display_minute($list_records[$j][7]);      //������������
	else echo "0:00";
	?></td>
	<td align="center" nowrap="nowrap"><?
	if($list_records[$j][6]=="o") echo $strOutgoing;                         // ���� - �����
	elseif($list_records[$j][6]=="i") echo $strIncoming;
	else echo "&nbsp;";

	?></td>
	</tr>

        <?

	}

$tbl->tblEnd($current_page,$n_pages);
//print_r($list_records[1]);
?>
</div>
<?
if($_SESSION['acl']['Recording']['Access']>1) $tbl->addSelOper($strDeleteSelected,"rec.deleteSelected()","drop.gif");
$tbl->addSelOper($strDownloadSelected,"rec.downloadSelected()","download.gif");
$tbl->addSelOper($strEmailSelected,"rec.sendEmail()","email.gif");
$tbl->selOper();
?>


</form>
<center>
<div id="listen-record-div" style="width: 400px;display: none;">
</div>
</center>
</div>
<form name="download-form" id="download-form" method="post" target="download-frame">
<input type="hidden" name="files" id="files" value="">
<input type="hidden" name="msg" id="msg" value="rec-warning">

</form>
<iframe name="download-frame" id="download-frame" frameborder=0 style="width: 0px; height: 0px;">
</iframe>