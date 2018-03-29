<?


//Получим номер агента из $_SESSION['user_name']
$agent = explode("-",$_SESSION['user_name']);
$agent = $agent[1];
if (!is_numeric($agent)) exit;



if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
?>
<div nowrap id="frame-module-header">
<?=$strReports?>: Детальный отчёт исходящие
</div>
<br>
<?

$CsvFileName = microtime(true).".csv";
$iformat=dt_format();
if(!isset($val)) $val=$_SESSION['currency'];
//if(empty($current_page)) $current_page=0;
if(!isset($sort_date)) $sort_date=1;
if(!isset($fixperiod)) $fixperiod=1;
//if(!isset($src)) $src=0;
$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");

if (strtotime(dbformat_to_dt("$currentdate 00:00")) > strtotime($periodfrom)) $tmp_time = strtotime($periodfrom);
else $tmp_time = strtotime(dbformat_to_dt("$currentdate 00:00"));

$rec = new Recording();
$rec->setFilters($periodfrom,$periodto,$src,$dest,$dirtype,$sort_date,$e_department);
$rec->getAllList();
$list_rec=$rec->list_recs;
$count_rec = count($list_rec);

?>
<script>
var sort_date=<?=$sort_date?>;

outrep = new ObjectD();

outrep.setFixPeriod=function(p) {
    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
}
outrep.sortByDate=function() {
    if(sort_date) sort_date=0; else sort_date=1;
    loadModule(1,'operator_reports','operator_reports','OutReport',$H({sort_date: sort_date}));
}
outrep.exportToCSV=function() {
  $('export-form').action='modules/operator_reports/out_csv.php?file=<?=$CsvFileName?>';
  $('export-form').submit();
}
outrep.downloadFile=function (file) {
    $('download-form').action="lib/downloadfiles.php"
    $('download-form').files.value=file;
    $('download-form').submit();
}
outrep.listenRecord=function (file) {
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
$p->action="$('module_form').current_page.value=0;loadModule(1,'','operator_reports','OutReport',\$H({search: 1}));";
$p->align2="right";

$tbl->show();


$CsvDelim = ";";
$CsvData = "Дата".$CsvDelim."Оператор".$CsvDelim."Номер".$CsvDelim."Продолжительность".$CsvDelim."\n";

echo "</form>";
   
   if(!$db) $db=DbConnect();
   if($sort_date == 0) $sc = "ASC";
   else $sc = "DESC";
   $start_time=strtotime($periodfrom);
   $end_time=strtotime($periodto);
   $agentp10 =$agent + 10;
   
   $query = "SELECT calldate, src, dst, billsec, UNIX_TIMESTAMP(calldate) AS time_id, uniqueid,u.first_name as name
   				FROM asteriskcdrdb.cdr as c 
   				left join ams.users as u on c.src=u.phone_work
   				WHERE length(dst)>2 AND billsec > 0 AND disposition = 'ANSWERED'
   				AND (((src='".$agent."' or src='".$agentp10."' or dst='".$agent."' or dst='".$agentp10."'  )))
   				AND dst not like '%*%'
   				and length(src) < 5
   				AND calldate > FROM_UNIXTIME($start_time) AND calldate < FROM_UNIXTIME($end_time) ";
   $query.="ORDER BY calldate ";
   $query.=$sc;
   //print $query;
   //exit;
   $call_list = $db->get_list($query);
   $count_calls=$db->count;
   $total_time = 0;

   
   for($i=0;$i<$count_calls;$i++){
    $CsvData = $CsvData.$call_list[$i][0].$CsvDelim;
    $CsvData = $CsvData.$call_list[$i][1].$CsvDelim;    
    $CsvData = $CsvData.$call_list[$i][2].$CsvDelim;    
    $CsvData = $CsvData.to_hours($call_list[$i][3]).$CsvDelim."\n";            
   };
    $fp = fopen("/var/www/html/ams/modules/operator_reports/csv/".$CsvFileName, 'w');
    fwrite($fp,$CsvData);
    fclose($fp);
   
   for($i=0;$i<$count_calls;$i++){
   		for($j=0;$j<$count_work;$j++){
   			if(($call_list[$i][1] == $work_list[$j][2]) && ($call_list[$i][4] > $work_list[$j][0])){
					$call_list[$i][1] = $work_list[$j][1];
					break;
   			}
   		}
    	$total_time+=$call_list[$i][3];
   }
	
   $arrone = array();
    if (!empty($fio_src)){
   		for($i=0;$i<$count_calls;$i++){
   			if($call_list[$i][1] == $fio_src) $arrone[] = $call_list[$i];
   		}
   }
   
   $fio_list=array();
   for($j=0;$j<$count_work;$j++) array_push($fio_list, $work_list[$j][1]);
   $fio_list = array_unique($fio_list);
   $fio_list=array_flip($fio_list);

   $count_list=array();
   for($j=0;$j<$count_calls;$j++) array_push($count_list, $call_list[$j][1]);
   $count_list = array_count_values($count_list);
   $result = array_intersect_key($count_list, $fio_list);
   
   if (!empty($fio_src)){
   		$call_list =$arrone;
   		$count_calls = count($call_list);
   }


$_SESSION['sql_export']=$query;
$n_pages = num_pages($count_calls,$limit_display,$current_page);
?>
<?

$tbl = new ListTbl();
if(!$count_calls) $tbl->emptyTbl($strNoCalls);
?>
    </tr>
    
</table>

 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
     <td align=center width="40%" class="module-note">
      </td>
      <td align=center width="40%" class="module-note">
      </td>
      <td></td>
      <td align=right nowrap>
      <?$tbl->exportLink("Экспорт в CSV","outrep.exportToCSV()");?>
    </td></tr>
</table>


<table width="50%" border="0" align="rigth" cellpadding="0" cellspacing="0">
<?
	for($j=0;$j<count($result);$j++){
?>
     <tr><td align=rigth width="50%" height=20>
		Оператор <?=key($result)?> исходящих звонков:
     </td>
     <td>
     	 <?=current($result)?>
     </td>
     </tr>
<?
	next($result);
	}
?>
</table>
<?
$img = $sort_date ? "up.gif" : "down.gif";
$dh="<a href=\"javascript:outrep.sortByDate();\">скачать&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src='images/$img' style=\"margin: 0;border:0;\"></a>";
$tbl->tblHead(array("","","","","",""),array("","Дата","Оператор","Номер","Продолжительность"),"100%");

//$dh="<a href=\"javascript:rep.sortByDate();\">Поступил&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src='images/$img' style=\"margin: 0;border:0;\"></a>";
//$tbl->tblHead(array("2","","","","","","",""),array("$dh","№ телефона","Время ожидания","Продолжительность","Оператор","Статус","Положил трубку"),"100%");


	$a=$current_page*$limit_display;
	if(($current_page+1)*$limit_display > $count_calls) $b=$count_calls;
	else $b=($current_page+1)*$limit_display;

	for($j=$a; $j<$b; $j++){
		$tbl->tblTr($j,"font-size: 11px;font-family: arial,sans;");
		$cheked = false;
		 for($cnt=0;$cnt<$count_rec;$cnt++){
		 		if($list_rec[$cnt][1] == $call_list[$j][5]){
					$f_name = $list_rec[$cnt][0];
					$cheked = true;
					break;
				}
		 }
		//echo "<br>";  
		if (!$cheked) $f_name = "";
		$link = $call_list[$j][0];
		$link[10]='_';
		$link[13]='-';
		$link = substr($link,0,16);
		$link = $link."-".$call_list[$j][2].".wav";
		print "<td width=6><a title=\"$strDownloadFile\"  href=\"http://10.10.11.34/records/$link\"><img src=\"images/download.gif\" ></a></td>";
	?>
	
	<td align="center" nowrap><?=$call_list[$j][0];?></td>
	<td align="center" nowrap><?=$call_list[$j][1];?>(<?=$call_list[$j][6];?>)</td>
	<td align="center" nowrap><?=$call_list[$j][2];?></td>
	<td align="center" nowrap><?=display_minute($call_list[$j][3]);?></td>
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