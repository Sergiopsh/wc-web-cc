<?
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
?>
<div nowrap id="frame-module-header">
<?=$strReports?>: Детальный отчёт входящие
</div>
<br>
<?

$iformat=dt_format();
if(!isset($val)) $val=$_SESSION['currency'];
//if(empty($current_page)) $current_page=0;
if(!isset($sort_date)) $sort_date=0;
if(!isset($fixperiod)) $fixperiod=1;
$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");

$rec = new Recording();
$rec->setFilters($periodfrom,$periodto,$src,$dest,$dirtype,$sort_date,$e_department);
$rec->getAllList();
$list_rec=$rec->list_recs;
$count_rec = count($list_rec);
?>

<script>
var sort_date=<?=$sort_date?>;

rep = new ObjectD();

rep.setFixPeriod=function(p) {
    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");

}
rep.printReport=function() {

}
rep.sortByDate=function() {
    if(sort_date) sort_date=0; else sort_date=1;
    loadModule(1,'ReportsSuper','ReportsSuper','DetailReport',$H({sort_date: sort_date}));
}
rep.exportToCSV=function() {
  $('export-form').action='include/export_csv.php?pref_export=Export_cdr_';
  $('export-form').submit();
}
rep.exportToPDF=function() {
  $('export-form').action='include/export_pdf.php';
  $('export-form').submit();
}
rep.downloadFile=function (file) {
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=file;
    $('download-form').submit();
}
rep.listenRecord=function (file) {
    $('listen-record-div').show();
    var s="<embed src='lib/loadfile.php?file="+file+"&type=wav"+
    "' autostart='true' loop='false' width='300' height='40'></embed>";
    s+="&nbsp;<a href='javascript:void(0)' onclick='$(\"listen-record-div\").innerHTML=\"\"'>"+
	"<img src='images/hide.gif' align='top'></a>";
    $('listen-record-div').innerHTML=s;
}
</script>

<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>"/>
<INPUT TYPE="hidden" NAME="limit_display" id="limit_display" value="<?=$limit_display?>"/>
<INPUT TYPE="hidden" NAME="sort_date" id="sort_date" value="<?=$sort_date?>"/>
<?

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="$('module_form').current_page.value=0;loadModule(1,'','ReportsSuper','DetailReport',\$H({search: 1}));";
$p->align2="right";
$p=$tbl->addElement("fixperiod","select",$strFixPeriod,2,$fixperiod); $p->colspan=5;
$p->options=array("0" => "&nbsp;---&nbsp;","1" => $strHour3, "2" => $strHour12, "3" => $strToday,
  "4" => $strYesterday,"5" => $strDay3, "6"=> $strThisWeek, "7"=> $strThisMonth, "8" => $strPrevMonth);
$p->action="rep.setFixPeriod(this.value)";
$p=$tbl->addElement("src","text",$strSource,3,$src);
$p->setOptions(18);
$tbl->show();

echo "</form>";

   if(!$db) $db=DbConnect();
   if($sort_date == 0) $sc = "ASC)";
   else $sc = "DESC)";
   $start_time=strtotime($periodfrom);
   $end_time=strtotime($periodto);

   $date_clause="t.x >= $start_time AND t.x < $end_time";
   if(!empty($src)) $date_clause.= " AND t.data2in = '$src';";
   else $date_clause.=";";

   $query = "SELECT t.x, FROM_UNIXTIME(time_conn), users1.last_name, agentin, data2in, verbcon, data3con, verbend, data1end, data2end
		  FROM(SELECT ttt.x, MAX(ttt.time_conn) AS time_conn, MAX(ttt.agentin) AS agentin, MAX(ttt.data2in) AS data2in,  MAX(ttt.verbcon) AS verbcon,
		  	MAX(ttt.data2con) AS data2con, MAX(ttt.data3con) AS data3con, MAX(ttt.verbend) AS verbend,
		  	MAX(ttt.data1end) AS data1end, MAX(ttt.data2end) AS data2end
		  FROM
		  (SELECT call_id AS x, space(10) AS time_conn, space(20) AS agentin, data2 AS data2in,  space(20) AS verbcon, space(20) AS data2con,
		  	space(5) AS data3con, space(20) AS verbend, space(5) AS data1end, space(5) AS data2end
		  	FROM queue_log WHERE verb='ENTERQUEUE' AND queue = 'callcenterqueue'
		  UNION
		  SELECT call_id AS x, time_id AS time_conn, agent AS agentin, space(20) AS data2in, verb AS verbcon, data2 AS data2con,
		  	data3 AS data3con,  space(20) AS verbend, space(5) AS data1end, space(5) AS data2end
		  	FROM queue_log WHERE (verb='CONNECT' OR verb = 'TRANSFER' OR verb = 'ABANDON') AND queue = 'callcenterqueue'
		  UNION
		  SELECT call_id AS x, space(10) AS time_conn, space(20) AS agentin, space(20) AS data2in, space(20) AS verbcon, space(20) AS data2con,
		  	space(5) AS data3con,  verb AS verbend, data1 AS data1end, data2 AS data2end
		  	FROM queue_log WHERE verb LIKE 'COMPLETE%' AND queue = 'callcenterqueue')
		  AS ttt GROUP BY ttt.x ";
   $query.=$sc;
   $query.= " AS t LEFT JOIN
		  	(SELECT CONCAT('Agent/',TRIM(User_name)) AS agent, last_name
		  	FROM users GROUP BY user_name) AS users1 ON t.agentin=users1.agent
		    WHERE ";
$query.=$date_clause;
$_SESSION['sql_export']=$query;
$list = $db->get_list($query);
$total_calls=$db->count;
$n_pages = num_pages($total_calls,$limit_display,$current_page);
?>
<br>
<?
$tbl = new ListTbl();
if(!$total_calls) $tbl->emptyTbl($strNoCalls);
?>
 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
     <td align=center width="40%" class="module-note">
	<?=$strNumCalls?> : <?=$total_calls?>
      </td>
      <td></td>
      <td align=right nowrap>
      <?$tbl->exportLink("Экспорт в CSV","rep.exportToCSV()");?>
    </td></tr>
</table>
<?
$img = $sort_date ? "up.gif" : "down.gif";
$dh="<a href=\"javascript:rep.sortByDate();\">Поступил&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src='images/$img' style=\"margin: 0;border:0;\"></a>";
$tbl->tblHead(array("2","","","","","","",""),array("$dh","№ телефона","Время ожидания","Продолжительность","Оператор","Статус","Положил трубку"),"100%");

	$a=$current_page*$limit_display;
	if(($current_page+1)*$limit_display > $total_calls) $b=$total_calls;
	else $b=($current_page+1)*$limit_display;
	
	for($cn=$a; $cn<$b; $cn++){
		$tbl->tblTr($cn,"font-size: 11px;font-family: arial,sans;");
			$cheked = false;
			for($co=0;$co<$count_rec;$co++){
		 		if($list_rec[$co][1] == $list[$cn][0]){
					$f_name = $list_rec[$co][0];
					$cheked = true;
					break;
				}
			}

	if (!$cheked) $f_name = "";
	?>
	<td nowrap><a href="javascript:rep.listenRecord('<?=$f_name;?>')" title="Прослушать"><img src="images/sound.png" ></a></td>
	<td width=6><a title="<?=$strDownloadFile?>"  href="javascript:rep.downloadFile('<?=$f_name?>')"><img src="images/download.gif" ></a></td>
	<td align="center" nowrap><?=$list[$cn][1];?></td>
	<td align="center" nowrap><?=$list[$cn][4];?></td>
	<td align="center" nowrap><?if($list[$cn][5] == "ABANDON") $str = $list[$cn][6];
								else $str = $list[$cn][8];
								echo $str;?></td>
	<td align="center" nowrap><?=$list[$cn][9];?></td>
	<td align="center" nowrap><?=$list[$cn][2];?></td>
	<td align="center" nowrap><?if($list[$cn][5] == "CONNECT") $str = "Обработан";
								else if($list[$cn][5] == "ABANDON") $str = "Не дождался";
								else if($list[$cn][5] == "TRANSFER") $str = "Переведён";
								echo $str;?></td>
	<td align="center" nowrap><?if($list[$cn][7] == "COMPLETECALLER") $str = "Абонент";
								else if($list[$cn][7] == "COMPLETEAGENT") $str = "Оператор";
								echo $str;?></td>
	</tr>
	<?
	}
	$tbl->tblEnd($current_page,$n_pages);
	?>
<center>
<div id="listen-record-div" style="width: 400px;display: none;">
</div>
</center>

<form name="export-form" id="export-form" method="post" target="export-frame">
</form>

<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>

<form name="download-form" id="download-form" method="post" target="download-frame">
<input type="hidden" name="files" id="files" value="">
<input type="hidden" name="msg" id="msg" value="rec-warning">
</form>
<iframe name="download-frame" id="download-frame" frameborder=0 style="width: 0px; height: 0px;">
</iframe>
