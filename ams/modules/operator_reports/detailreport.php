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
<?=$strReports?>: Детальный отчёт входящие
</div>
<br>
<?

$CsvFileName = microtime(true).".csv";

$iformat=dt_format();
if(!isset($val)) $val=$_SESSION['currency'];
//if(empty($current_page)) $current_page=0;
if(!isset($sort_date)) $sort_date=1;
if(!isset($fixperiod)) $fixperiod=1;
$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");

$rec = new Recording();
$rec->setFilters($periodfrom,$periodto,$src,$dest,$dirtype,$sort_date,$e_department);
$rec->getAllList();
$list_rec=$rec->list_recs;
$count_rec = count($list_rec);
function numb($var){
   	return(!empty($var[7]));
}
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
    loadModule(1,'operator_reports','operator_reports','DetailReport',$H({sort_date: sort_date}));
}
rep.exportToCSV=function() {
  $('export-form').action='modules/operator_reports/out_csv.php?file=<?=$CsvFileName?>';
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
$p->action="$('module_form').current_page.value=0;loadModule(1,'','operator_reports','DetailReport',\$H({search: 1}));";
$p->align2="right";

/*
$p=$tbl->addElement("iOperName","select","Оператор",2,$iOperName);
$p->options[0]="---";
if(!$db) $db=DbConnect();
$QUERY="select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%'";
$oper = $db->get_list($QUERY); 
foreach ($oper as $data){
     $p->options[$data[0]]=$data[1];
};
*/
$tbl->show();

echo "</form>";


   if(!$db) $db=DbConnect();
   if($sort_date == 0) $sc = "ASC ;";
   else $sc = "DESC ;";
   $start_time=strtotime($periodfrom);
   $end_time=strtotime($periodto);
   $date_clause="call_id >= $start_time AND call_id < $end_time GROUP BY call_id ";
   //
   $date_clause.=$sc;
    if ($iOperName==0){
	$query = "SELECT FROM_UNIXTIME(time_id-data1-data2) as time,call_id,agent,verb,data1,data2,data3,data4
			FROM queuemetrics.queue_log
			WHERE (verb LIKE 'COMPLETE%' OR verb='TRANSFER' OR verb='ABANDON')
			AND agent like '%$agent%' AND ";
    }else{
	$query = "SELECT FROM_UNIXTIME(time_id-data1-data2) as time,call_id,agent,verb,data1,data2,data3,data4
			FROM queuemetrics.queue_log
			WHERE (verb LIKE 'COMPLETE%' OR verb='TRANSFER' OR verb='ABANDON')
			AND agent like '%$agent%' AND
			";    
    };
   $query.=$date_clause;
   
//    print $query;
//    exit;

$_SESSION['sql_export']=$query;
$list_one = $db->get_list($query);

//$total_calls=$db->count;
$total_calls=count($list_one);
$n_pages = num_pages($total_calls,$limit_display,$current_page);

$query = "SELECT CONCAT('Agent/',user_name),first_name,last_name FROM users;";
$list_two = $db->get_list($query);
$n_oper = $db->count;

$query = "SELECT call_id,data2 FROM queuemetrics.queue_log
			WHERE verb = 'ENTERQUEUE' AND queue = '580' AND ";
if(!empty($src))$query.= "data2 = $src AND ";
$query.=$date_clause;
$list_th = $db->get_list($query);
$n_calls = $db->count;

for ($pos=0;$pos<$total_calls;$pos++){	 	for ($tw=0;$tw<$n_oper;$tw++){	        if ($list_two[$tw][0] == $list_one[$pos][2]){	        	$list_one[$pos][2] = $list_two[$tw][1]." ".$list_two[$tw][2];
	        	break;
	        }
	 	}
	 	for ($tw=0;$tw<$n_calls;$tw++){			if ($list_th[$tw][0] == $list_one[$pos][1]){ 			  $list_one[$pos][7] = $list_th[$tw][1];
 			  break;
 			}
		}
}


if(!empty($src)){	$list_one = array_filter($list_one, numb);
    $total_calls=count($list_one);
    $list_temp[] = current($list_one);
    for ($tp=1;$tp<$total_calls;$tp++){
    	array_push($list_temp, next($list_one));
    }
    $list_one = $list_temp;
}


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

$CsvDelim = ";";
$CsvData = "Дата".$CsvDelim."№ телефона".$CsvDelim."Время ожидания".$CsvDelim."Продолжительность".$CsvDelim."Оператор".$CsvDelim."Статус".$CsvDelim."Положил трубку".$CsvDelim."\n";

for ($i=0;$i<count($list_one);$i++){
	$CsvData = $CsvData.$list_one[$i][0].$CsvDelim;
	$CsvData = $CsvData.$list_one[$i][7].$CsvDelim;
	switch ($list_one[$i][3]){
		case "ABANDON":
			$data1 = $list_one[$i][6];
			$data2 = "-";
			$data3 = "-";
			$data4 = "Не дождался";
			$data5 = "-";
			break;
		case "COMPLETECALLER":
			$data1 = $list_one[$i][4];
			$data2 = $list_one[$i][5];
			$data3 = $list_one[$i][2];
			$data4 = "Обработан";
			$data5 = "Абонент";
			break;
		case "COMPLETEAGENT":
			$data1 = $list_one[$i][4];
			$data2 = $list_one[$i][5];
			$data3 = $list_one[$i][2];
			$data4 = "Обработан";
			$data5 = "Оператор";
			break;
		case "TRANSFER":
			$data1 = $list_one[$i][6];
			$data2 = "-";
			$data3 = $data3 = $list_one[$i][2];
			$data4 = "Переведён";
			$data5 = "-";
			break;
		 };
	$CsvData = $CsvData.to_hours($data1).$CsvDelim;
	$CsvData = $CsvData.to_hours($data2).$CsvDelim;	
	$CsvData = $CsvData.substr($data3,6,3).$CsvDelim;
	$CsvData = $CsvData.$data4.$CsvDelim;	
	$CsvData = $CsvData.$data5.$CsvDelim."\n";	
};

    $fp = fopen("/var/www/html/ams/modules/operator_reports/csv/".$CsvFileName, 'w');
    fwrite($fp,$CsvData);
    fclose($fp);



	$a=$current_page*$limit_display;
	if(($current_page+1)*$limit_display > $total_calls) $b=$total_calls;
	else $b=($current_page+1)*$limit_display;

	for($cn=$a; $cn<$b; $cn++){
		$tbl->tblTr($cn,"font-size: 11px;font-family: arial,sans;");
			$cheked = false;
			for($co=0;$co<$count_rec;$co++){
		 		if($list_rec[$co][1] == $list_one[$cn][1]){
					$f_name = $list_rec[$co][0];
					$cheked = true;
					break;
				}
			}

	if (!$cheked) $f_name = "";
	?>
	<td nowrap><a ></a></td>
	<td width=6><a title="<?=$strDownloadFile?>"  href="http://10.10.11.34/records/search.php?&find=<?=$list_one[$cn][1];?>"><img src="images/download.gif" ></a></td>
	<td align="center" nowrap><?=$list_one[$cn][0];?></td>
	<td align="center" nowrap><?=$list_one[$cn][7];?></td>
	<?switch ($list_one[$cn][3]){		case "ABANDON":
			$data1 = $list_one[$cn][6];
			$data2 = "-";
			$data3 = "-";
			$data4 = "Не дождался";
			$data5 = "-";
			break;
		case "COMPLETECALLER":
			$data1 = $list_one[$cn][4];
			$data2 = $list_one[$cn][5];
			$data3 = $list_one[$cn][2];
			$data4 = "Обработан";
			$data5 = "Абонент";
			break;
		case "COMPLETEAGENT":
			$data1 = $list_one[$cn][4];
			$data2 = $list_one[$cn][5];
			$data3 = $list_one[$cn][2];
			$data4 = "Обработан";
			$data5 = "Оператор";
			break;
		case "TRANSFER":
			$data1 = $list_one[$cn][6];
			$data2 = "-";
			$data3 = $data3 = $list_one[$cn][2];
			$data4 = "Переведён";
			$data5 = "-";
			break;	 }
	?>
	<td align="center" nowrap><?=display_minute($data1);?></td>
	<td align="center" nowrap><?=display_minute($data2);?></td>
	<td align="center" nowrap><?=substr($data3,6,3);?></td>
	<td align="center" nowrap><?=$data4;?></td>
	<td align="center" nowrap><?=$data5;?></td>
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
