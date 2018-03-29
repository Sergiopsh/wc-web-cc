<?php
/*include ("modules/ReportsSuper/loadagent1.php");
print workTime(1317488423,1317574823,'Local/502@from-internal');
exit;

include ("modules/ReportsSuper/loadagent.php");
workTime(1317488423,1317574823,'Local/502@from-internal');
exit;
*/

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");

cleardir("modules/ReportsSuper/images");
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: Общая статистика Колл Центра
</div>
<?
$iformat=dt_format();
if(!isset($dateperiod)) $dateperiod=0;
?>
<br>
<script>
rep = new ObjectD();

rep.setPeriod=function(z) {
    if(z == '0') {
	var a = new Date();
	var m = a.getMinutes();
	if (m<10) m = "0" + m;
	var h = a.getHours();	
	if (h<10) h = "0" + h;	
	var dt = setPeriod(z);
	$('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
	$('periodto').value=dt[1].print("<?=$iformat?> "+h+":"+m);
	return;
    };
    var dt = setPeriod(z);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
}

rep.printReport=function() {

}

rep.exportPDF=function() {

}
</script>

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="pos" id="pos" value=<?=$dateperiod?>/>

<?

$currentdate=date("Y-m-d");
$current_time=date(" H:i");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
//if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate"."$current_time");

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");                     						 //запросная форма
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);                                      		 //новый кусок
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="loadModule(1,'','ReportsSuper','CommonReport',\$H({search: 1}));"; $p->align2="right";
$p=$tbl->addElement("dateperiod","select",$strPeriod,2,$dateperiod);
$p->options=array(0 => "Сегодня", 1 => $strYesterday, 2 => $strJan, 3 => $strFeb, 4 => $strMar, 5 => $strApr, 6 => $strMay,
		  7 => $strJun, 8 => $strJul, 9 => $strAug, 10 => $strSep, 11 => $strOct, 12 => $strNov, 13 => $strDec );
$p->action="rep.setPeriod(this.value)";

//$p=$tbl->addElement("","simple","",2,$strDuration);
//$p=$tbl->addElement("duration1","text",$strFrom,2,$duration1);
//$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
//$p=$tbl->addElement("duration2","text",$strTo,2,$duration2);
//$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
$tbl->show();

?>


</FORM>

<br>

<? if ($search){
?>
<h1 align="center">Общая статистика за период <?=$periodfrom." - ".$periodto?></h1>
<?
  if(!$db) $db=DbConnect();

$start_time=strtotime($periodfrom);
$end_time=strtotime($periodto);
$date_clause=" AND time_id >= $start_time AND time_id < $end_time";
if ($duration1&&$duration2) $date_clause.=" AND ratesec >= $duration1 AND ratesec < $duration2";


				  // ((COUNT(IF(verb = 'ENTERQUEUE',1,NULL)))-(COUNT(IF(verb = 'ABANDON' AND data3 <= 5,1,NULL)))) AS all_calls,
				  // ((COUNT(IF(verb = 'ENTERQUEUE',1,NULL)))-((COUNT(IF(verb = 'ENTERQUEUE',1,NULL)))-(COUNT(IF(verb = 'ABANDON' AND data3 <= 5,1,NULL)))))
    $QUERY = "SELECT COUNT(IF(verb = 'ENTERQUEUE',1,NULL)) AS all_calls,
                     COUNT(IF(verb = 'ABANDON',1,NULL)) AS noanswer,
                     AVG(IF(verb LIKE 'COMPLETE%',data1,NULL)) AS wait,
                     AVG(IF(verb LIKE 'COMPLETE%',data2,NULL)) AS avg_calls,
                     COUNT(IF(verb LIKE 'COMPLETE%' AND data1 > 120,1,NULL)) AS over_two,
					 COUNT(IF(verb = 'TRANSFER',1,NULL)) AS trnsfered
					 FROM queuemetrics.queue_log WHERE queue = '580' "; 


    $QUERY.=$date_clause;
    $list_month = $db->get_list($QUERY); 
    $num = $db->count;			//скока строк в запросе...
if ($list_month[0][0]>0){

$totalcall=0;
$noanswer=0;
$totalwite=0;
$asrtalk=0;
$totalowertwo=0;

foreach ($list_month as $data){
		$totalcall=$data[0];
		$noanswer=$data[1];
		$totalwite=$data[2];
	    $asrtalk=$data[3];
	    $totalowertwo=$data[4];
}

$data_graph[0]=$totalcall-$noanswer;
$data_graph[1]=$noanswer;
$mylegend=Array("Принято", "Пропущено");
$graph_title="Принято/Пропущено";
//print_r($noanswer);

if ($totalcall)include("modules/ReportsSuper/graph_calls.php");

?>
<br>

<IMG SRC="modules/ReportsSuper/images/<?=$filename_res?>" WIDTH="48%" ALIGN=RIGHT>

<table border="0" cellspacing="0" cellpadding="0" width="50%" class="report-tbl">
 <tr><td>

	<table border="0" cellspacing="1" cellpadding="1" width="100%">

	<tr id="head">
	<td align="center">Наименование показателя</td>
	<td align="center">Норма</td>
	<td align="center">Значение</td>
	</tr>
	<tr >
		<td align="center" height=30 nowrap>Общее количество поступивших звонков</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=$totalcall?></td>
	</tr>
	<tr >
		<td align="center" height=30 nowrap>Процент пропущенных вызовов</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=round(100-(($totalcall-$noanswer)*100/$totalcall))."%"?></td>
	</tr>
	<tr >
		<td align="center" height=30 nowrap>Среднее время ожидания ответа специалиста</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=round($totalwite)."сек."?></td>
	</tr>
	<tr >
		<td align="center" height=30 nowrap>Среднее время разговора</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=round($asrtalk)."сек."?></td>
	</tr>
	<tr >
		<?
		    if (!$totalowertwo) $owertwo="-";
		    else $owertwo=$totalowertwo;
		?>
		<td align="center" height=30 nowrap>Звонки с простоем более 2 мин.</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=$owertwo?></td>
		<tr >
		<td align="center" height=30 nowrap>Переведено</td>
		<td align="center" nowrap>-</td>
		<td align="center" nowrap><?=$list_month[0][5];?></td>
	</tr>
		
	
  </table>


</td></tr></table>


<br><br><br>



<?

  if ($dateperiod<20){

    $QUERY = "SELECT HOUR(FROM_UNIXTIME(time_id)) AS HR,
	      (COUNT(IF(verb = 'ENTERQUEUE',1,NULL))-COUNT(IF(verb = 'ABANDON',1,NULL))) AS all_calls,
	      COUNT(IF(verb = 'ABANDON',1,NULL)) AS noanswer
	      FROM queuemetrics.queue_log
	      WHERE queue = '580' ";
    $QUERY.=$date_clause;
    $QUERY.=" GROUP BY HR;";

    $list_day = $db->get_list($QUERY);
    $num = $db->count;
    $dataanswered = array();
    $datanoanswer = array();
    $empty = 0;
    $count = 0;
    for($i=0; $i<24; $i++) {
      array_push($dataanswered, $empty);
      array_push($datanoanswer, $empty);
        if($list_day[$count][0] == $i){
            $dataanswered[$i] = $list_day[$count][1];// - $list_day[$count][2];
            $datanoanswer[$i] = $list_day[$count][2];
            if ($dataanswered[$i] < 0) {
        	$datanoanswer[$i-1]+=$datanoanswer[$i];
        	$datanoanswer[$i] = 0;
        	$dataanswered[$i] = 0;
            }
            $count++;
        }
    }
    include("modules/ReportsSuper/graph_day.php");
 ?>
  <br><br>

<h1 align="center">Принятые и не принятые звонки во временном разрезе</h1>

  <IMG SRC="modules/ReportsSuper/images/<?=$filenam_res?>" WIDTH="78%" ALIGN=RIGHT>

  <table border="0" cellspacing="0" cellpadding="0" width="20%" class="report-tbl">
  <tr>
  <td>
   <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="center" height=10>Час</td>
	<td align="center" height=10>Принято</td>
	<td align="center" height=10>Пропущенно</td>
	</tr>
        </td>

        <?
    	    for($j=0; $j<24; $j++){
        ?>
        <tr >
		<td align="center" height=21 nowrap><?=$j?></td>
		<td align="center" nowrap><?=$dataanswered[$j]?></td>
		<td align="center" nowrap><?=$datanoanswer[$j]?></td>
	</tr>
        <?

        }

        ?>
        </table>
        </td>
        </tr>
 </table>
<?
 }
?>
<?

    include ("modules/ReportsSuper/loadagent.php");
   if ($dateperiod<20){
    $QUERY = "SELECT users1.last_name,
              SUM(IF(verb = 'REMOVEMEMBER',data2,0)) AS all_time,
              COUNT(IF(verb = 'CONNECT',1,NULL)) AS count_answ,
              AVG(IF(queue = '580' AND verb LIKE 'COMPLETE%',data2,NULL)) AS asr_talk,
              SUM(IF(queue = '580' AND verb LIKE 'COMPLETE%',data2,NULL)) AS all_talk,
              q.agent
              FROM queuemetrics.queue_log as q
              LEFT JOIN
              ( select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%') as users1
              
              ON substr(q.agent,7,3)=users1.agent 
              WHERE q.agent LIKE '%50%' ";
    $QUERY.=$date_clause;
    $QUERY.=" GROUP BY users1.last_name HAVING NOT (last_name is null) order by 3 desc;";
    $list_agents = $db->get_list($QUERY);
    $num = $db->count;
    
    $data_agents = array();
    $name_agents = array();
       for($i=0; $i<$num; $i++){
       	array_push($data_agents, $list_agents[$i][2]);
       	array_push($name_agents, $list_agents[$i][0]);
        $aname = $list_agents[$i][5];
       	//$QUERY = "SELECT w_time($start_time,$end_time,'$aname');";
       	//$aname = $db->get_list($QUERY);
       	$list_agents[$i][1] = workTime($start_time,$end_time,$aname);
       	//print '<br><br>';
       	$aname = 0;
       }
    $agents_compare = $num;
    //$list_agents[$i][1] = $aname[0][0];
    $graph_title = "По операторам";
    include("modules/ReportsSuper/graph_agents.php");
?>
<br><br>
    <h1 align="center">Разбивка по операторам</h1>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl" style="background-color:white;">
    <tr><td width="50%">
    <!--IMG SRC="modules/ReportsSuper/images/<?=$agents_res?>" WIDTH="47%" ALIGN=RIGHT-->

    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl">
    <tr>
    <td>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="center" >Оператор</td>
	<td align="center" >Отработано часов</td>
	<td align="center" >Обработано звонков</td>
	<td align="center" >Звонков в час</td>
	<td align="center" >Среднее время разговора</td>
	<td align="center" >Общее время разговора</td>
    </tr>
    </td>

      <?
      for($i=0; $i<$num; $i++){
      ?>
      <tr >
		<td align="center" nowrap height=30><?=$list_agents[$i][0]?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][1])?></td>
		<td align="center" nowrap><?=$list_agents[$i][2];?></td>
		<td align="center" nowrap><?
									 if($list_agents[$i][1]){
									 	if($list_agents[$i][1]>3600) $cnt = round(3600*$list_agents[$i][2]/$list_agents[$i][1]);
									 	else $cnt = $list_agents[$i][2];
									 }
									 else $cnt = 0;
									 echo $cnt;?></td>
		<td align="center" nowrap><?=display_minute(round($list_agents[$i][3]))?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][4])?></td>
	</tr>
    <?
        }
    ?>
        </table>
        </td>
        </tr>
 </table>
 </td>
 <td width="50%">
 <table border="0" cellspacing="0" cellpadding="0" width="100%" style="background-color:white;">
 <tr><td width="2%" style="background-color:white;"></td><td width="48%">
 <IMG SRC="modules/ReportsSuper/images/<?=$agents_res?>" width="100%" ALIGN=RIGHT>
 </td></tr>
 </table>
 </td>
 </tr></table>

<?


  }
?>
  <br><br><br><br>    <br><br><br><br>
<?

 } else echo "<div class='module-note' align='center'>$strNoCalls</div>";

}
?>
