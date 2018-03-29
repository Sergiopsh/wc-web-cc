<?
echo "Hello";
exit;
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
?>
<div nowrap id="frame-module-header">
<?=$strReports?>: Ночные звонки
</div>
<br>
<?
$iformat=dt_format();
if(!isset($sort_date)) $sort_date=0;
if(!isset($fixperiod)) $fixperiod=1;
$currentdate=date("Y-m-d");
if (!isset($periodfrom)){
	$timestamp = time();
	$date_time_array = getdate($timestamp);
	$timestamp = mktime(
    	00,
    	00,
    	00,
    	$date_time_array['mon'],
    	$date_time_array['mday'],
    	$date_time_array['year']
    );
    $timestamp-=7200;
    $periodfrom=dbformat_to_dt(strftime('%Y-%m-%d %H:%M',$timestamp));
}
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 07:00");
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
rep.sortByDate=function() {
    if(sort_date) sort_date=0; else sort_date=1;
    loadModule(1,'Reports','Reports','NReport',$H({sort_date: sort_date}));
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
$p->action="$('module_form').current_page.value=0;loadModule(1,'','ReportsSuper','NReport',\$H({search: 1}));";
$p->align2="right";
//$p=$tbl->addElement("fixperiod","select",$strFixPeriod,2,$fixperiod); $p->colspan=5;
//$p->options=array("0" => "&nbsp;---&nbsp;","1" => $strHour3, "2" => $strHour12, "3" => $strToday,
//  "4" => $strYesterday,"5" => $strDay3, "6"=> $strThisWeek, "7"=> $strThisMonth, "8" => $strPrevMonth);
//$p->action="rep.setFixPeriod(this.value)";
//$p=$tbl->addElement("dst","text",$strDest,3,$dst);
//$p->setOptions(18);
$tbl->show();

echo "</form>";

   if(!$db) $db=DbConnect();
   $start_time=strtotime($periodfrom);
   $end_time=strtotime($periodto);
   $query = "SELECT HOUR(calldate) AS HR, 
   			 COUNT(IF (billsec > 0,1,NULL)) AS answer, 
   			 COUNT(IF (billsec = 0,1,NULL)) AS noanswer 
   			 FROM cdr WHERE accountcode = 'nigth' 
   			 AND calldate > FROM_UNIXTIME($start_time) AND calldate < FROM_UNIXTIME($end_time) 
   			 GROUP BY HR;";
   $nigth_list = $db->get_list($query);
   $count_nigth=$db->count;
  if($count_nigth>0){
   $dataanswered = array();
   $datanoanswer = array();
   $count = 0;
   for($i=0; $i<24; $i++) {
     	$dataanswered[] = 0;
      	$datanoanswer[] = 0;
        if($nigth_list[$count][0] == $i){
            $dataanswered[$i] = $nigth_list[$count][1];
            $datanoanswer[$i] = $nigth_list[$count][2];
            if ($dataanswered[$i] < 0) {
        		$datanoanswer[$i-1]+=$datanoanswer[$i];
        		$datanoanswer[$i] = 0;
        		$dataanswered[$i] = 0;
            }
            $count++;
        }
    }
    $dataanswered = array_merge(array_slice($dataanswered,22),array_slice($dataanswered,0,7));
    $datanoanswer = array_merge(array_slice($datanoanswer,22),array_slice($datanoanswer,0,7));
    $timeset = array("22","23","00","01","02","03","04","05","06");
    include("modules/Reports/graph_nigth.php");
    
?>
<br><br>
<IMG SRC="modules/Reports/images/<?=$fname_res?>" width="68%" align=right>
<table border="0" cellspacing="0" cellpadding="0" width="30%" class="report-tbl">
 <tr><td>
	<table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="center">Время</td>
	<td align="center">Принято</td>
	<td align="center">Пропущено</td>
 </tr>
<? 
	for($k=0;$k<count($timeset);$k++){
?>	
	<tr >
		<td align="center" height=30 nowrap><?=$timeset[$k]?></td>
		<td align="center" nowrap><?=$dataanswered[$k]?></td>
		<td align="center" nowrap><?=$datanoanswer[$k]?></td>
	</tr>
<?
	}
?>
	<tr id="end">
	<td align="center">Всего</td>
	<td align="center"><?=array_sum($dataanswered)?></td>
	<td align="center"><?=array_sum($datanoanswer)?></td>
 	</tr>
	</table>
 </td></tr>
</table>
<br><br><br><br><br>
<br><br><br><br><br>
<?
  }
 else echo "<div class='module-note' align='center'>$strNoCalls</div>";
?>

<center>
<div id="listen-record-div" style="width: 400px;display: none;">
</div>
</center>
<form name="export-form" id="export-form" method="post" target="export-frame">
</form>
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
