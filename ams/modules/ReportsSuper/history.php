<?php
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");
cleardir("modules/ReportsSuper/images");
$CsvFileName = microtime(true).".csv";
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: История работы операторов
</div>
<?
$iformat=dt_format();
if(!isset($dateperiod)) $dateperiod=0;
//if(!isset($val)) $val=$_SESSION['currency'];
?>
<br>
<script>
rep = new ObjectD();

rep.setPeriod=function(z) {
    //if(z == '0') return;
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
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate"."$current_time");

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");                     						 //запросная форма
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);                                      		 //новый кусок
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="loadModule(1,'','ReportsSuper','HistoryReport',\$H({search: 1}));"; $p->align2="right";
$p=$tbl->addElement("iOperName","select","Оператор",2,$iOperName);
$p->options[0]="---";
if(!$db) $db=DbConnect();
$QUERY="select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%'";
$oper = $db->get_list($QUERY); 
foreach ($oper as $data){
     $p->options[$data[0]]=$data[1];
};
$tbl->show();
if (!$search) exit;
?>

</form>

 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
     <td align=center width="40%" class="module-note">
     </td>
     <td align=center width="40%" class="module-note">
     </td>
     <td></td>
      <td align=right nowrap>
        <a href="modules/ReportsSuper/out_csv.php?file=<?=$CsvFileName?>"><img src="images/xls_export.gif"></img>Экспорт в CSV</a>
    </td></tr>
</table>


<?



$CsvDelim = ";";
$CsvData = $CsvData."Оператор".$CsvDelim."Отработано часов".$CsvDelim."Входящих звонков".$CsvDelim."Исходящих звонков".$CsvDelim;
$CsvData = $CsvData."Всего звонков".$CsvDelim."Среднее время разговора входящие".$CsvDelim."Общее время разговора входящие".$CsvDelim."Среднее время разговора исходящие".$CsvDelim;
$CsvData = $CsvData."Общее время разговора исходящие".$CsvDelim."Общее время разговора".$CsvDelim."\n";

if ($search){
if(!$db) $db=DbConnect();
$start_time=strtotime($periodfrom);
$end_time=strtotime($periodto);
$date_clause=" AND time_id >= $start_time AND time_id < $end_time";
include ("modules/ReportsSuper/loadagent.php");

print "<br>";
print "<br>";
print "<br>";
print "<br>";

if ($duration1&&$duration2) $date_clause.=" AND ratesec >= $duration1 AND ratesec < $duration2";

   if ($dateperiod<20){
/*    $QUERY = "SELECT users1.last_name,
              SUM(IF(verb = 'AGENTCALLBACKLOGOFF' AND data1 != '',data2,0)) AS all_time,
              COUNT(IF(verb = 'CONNECT',1,NULL)) AS count_answ,
              AVG(IF(queue = '508' AND verb LIKE 'COMPLETE%',data2,NULL)) AS asr_talk,
              SUM(IF(queue = '508' AND verb LIKE 'COMPLETE%',data2,0)) AS all_talk,
              queuemetrics.queue_log.agent
              FROM queuemetrics.queue_log
              LEFT JOIN (
              SELECT CONCAT('Agent/',TRIM(User_name)) AS Agent, last_name
              FROM users GROUP BY user_name
              ) AS users1
              ON queuemetrics.queue_log.agent=users1.agent
              WHERE queuemetrics.queue_log.agent LIKE '%5%' ";*/
    $QUERY = "
		SELECT 
		    users1.last_name,
		    COUNT(IF(q.verb = 'ENTERQUEUE',1,NULL)) AS all_time,
		    COUNT(IF(q.verb = 'CONNECT',1,NULL)) AS count_answ,
		    AVG(IF(q.queue = '580' AND q.verb LIKE 'COMPLETE%',data2,NULL)) AS asr_talk,
		    SUM(IF(q.queue = '580' AND q.verb LIKE 'COMPLETE%',data2,0)) AS all_talk,
		    q.agent
		FROM queuemetrics.queue_log q
		LEFT JOIN
		    ( select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%') as users1
			
		ON substr(q.agent,7,3)=users1.agent 
		WHERE q.agent LIKE '%50%' 
              ";
//"              
    $QUERY.=$date_clause;
    $QUERY.=" GROUP BY users1.last_name HAVING NOT (last_name is null);";
    $list_agents = $db->get_list($QUERY);
    $num = $db->count;
    $data_agents = array();
    $name_agents = array();
//       for($i=0; $i<$num; $i++){
//       	array_push($data_agents, $list_agents[$i][2]);
//       	array_push($name_agents, $list_agents[$i][0]);
//        $aname[0][0] = $list_agents[$i][5];
//       	$name = explode ("@",$aname);
//       	$name = explode ("/",$name[0]);
//       	$name = $name[1];
//	$WorkTime = workTime($start_time,$end_time,$name);//Отработано часов
//	$WorkTime = $WorkTime['work_time'];
//       	$list_agents[$i][1] = $WorkTime;

//        $aname = $list_agents[$i][5];
//       	$QUERY = "SELECT queuemetrics.w_time($start_time,$end_time,'$aname');";
//       	print "aname = $aname";
//       	$aname = $db->get_list($QUERY);
//       	$list_agents[$i][1] = $list_agents[$i][5];


//       }
//    $agents_compare = $num;
//    $list_agents[$i][1] = $aname[0][0];
//       	print "aname[0][0]". $aname[0][0];
/*
    $QUERY = "SELECT q.time_id, users1.last_name, SUBSTRING(q.data1,1,4)
   				FROM queuemetrics.queue_log as q
				LEFT JOIN
				    ( select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%') as users1
				ON substr(q.agent,7,3)=users1.agent    				
   				WHERE verb = 'AGENTCALLBACKLOGIN' AND time_id <> data2 AND time_id > $start_time AND time_id < $end_time ORDER BY time_id DESC;";
   $work_list = $db->get_list($QUERY);
   $count_work=$db->count;

   $query = "SELECT calldate, src-10, dst, billsec, UNIX_TIMESTAMP(calldate) AS time_id
   				FROM asteriskcdrdb.cdr
   				WHERE length(dst)>5 AND billsec > 0 
   				AND calldate > FROM_UNIXTIME($start_time) AND calldate < FROM_UNIXTIME($end_time) 
   				AND dst <> '580' ORDER BY calldate ASC";
   $query.=$sc;

   $call_list = $db->get_list($query);
   $count_calls=$db->count;


   for($i=0;$i<$count_calls;$i++){
   		for($j=0;$j<$count_work;$j++){
   			if(($call_list[$i][1] == $work_list[$j][2]) && ($call_list[$i][4] > $work_list[$j][0])) {
					$call_list[$i][1] = $work_list[$j][1];
					break;
   			}
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
*/   

?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl">
    <tr>
    <td>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="center" >Оператор</td>
	<td align="center" >Время первого входа</td>
	<td align="center" >Время последнего выхода</td>
	<td align="center" >Отработано часов</td>
	<td align="center" >Входящих звонков</td>
	<td align="center" >Исходящих звонков</td>
	<td align="center" >Всего звонков</td>
	<td align="center" >Среднее время разговора входящие</td>
	<td align="center" >Общее время разговора входящие</td>
	<td align="center" >Среднее время разговора исходящие</td>
	<td align="center" >Общее время разговора исходящие</td>
	<td align="center" >Общее время разговора</td>
    </tr>
    </td>

      <?
      for($i=0; $i<$num; $i++){
      	//$sum = 0;
      	//for($j=0;$j<$count_calls;$j++) if($call_list[$j][1] == $list_agents[$i][0]) $sum+=$call_list[$j][3];
      ?>
      <tr >
		<td align="center" nowrap height=30><?=$list_agents[$i][0]?></td>
		<td align="center" nowrap height=30><?

		    $query = "  select min(time_id) 
				from queuemetrics.queue_log 
				where agent = '".$list_agents[$i][5]."'
				and verb='ADDMEMBER' 
				".$date_clause;
				$t = $db->get_list($query);
				if ($t[0][0]> 1000){
				    $time = getdate($t[0][0]);
				    $time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
				    print $time;
				};
		?></td>
		<td align="center" nowrap height=30><?

		    $query = "  select max(time_id) 
				from queuemetrics.queue_log 
				where agent = '".$list_agents[$i][5]."'
				and verb='REMOVEMEMBER' 
				".$date_clause;
				$t = $db->get_list($query);
				if ($t[0][0]> 1000){
				    $time = getdate($t[0][0]);
				    $time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
				    print $time;
				};
		?></td>


		<td align="center" nowrap><?
					    $name = $list_agents[$i][5];
					    $name = explode ("@",$name);
					    $name = explode ("/",$name[0]);
					    $name = $name[1];
					    $outname = $name + 10;
					    $WorkTime = workTime($start_time,$end_time,$list_agents[$i][5]);//Отработано часов
					    display_hours( $WorkTime['work_time']);
					    ?></td>
		<td align="center" nowrap><?=$list_agents[$i][2];?></td>
		<td align="center" nowrap><?    $outstr = (string)$outname;
						$query = "  select count(src) count,sum(billsec) sum 
							    from asteriskcdrdb.cdr 
							    where src like '".$outstr[0]."_".$outstr[2]."' 
								and length(dst)>2
								and billsec>0  
								AND calldate > FROM_UNIXTIME($start_time) AND calldate < FROM_UNIXTIME($end_time) 
								group by src;";
								//echo $query;
						$out_call = $db->get_list($query);
						print $out_call[0][0];
			                    	    ?></td>
      									
		<td align="center" nowrap ><?=$list_agents[$i][2] + $out_call[0][0]?></td>
		<td align="center" nowrap><?=round($list_agents[$i][3])?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][4])?></td>
		<td align="center" nowrap><?=round($out_call[0][1]/$out_call[0][0])?></td>
		<td align="center" nowrap><?=display_hours($out_call[0][1])?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][4]+$out_call[0][1])?></td>
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
 } //else echo "<div class='module-note' align='center'>$strNoCalls</div>";
?>
