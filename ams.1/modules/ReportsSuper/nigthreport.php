<?php
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");
cleardir("modules/ReportsSuper/images");
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: Плохие звонки
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
$p->action="loadModule(1,'','ReportsSuper','NReport',\$H({search: 1}));"; $p->align2="right";
$tbl->show();
if (!$search) exit;

?>

</form>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl">
    <tr>
    <td>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	    <td align="center" >Дата</td>
	    <td align="center" >Источник</td>
	    <td align="center" >Время простоя</td>
	    <td align="center" >Оператор</td>
	</tr>
    </td>


<?
if ($search){
if(!$db) $db=DbConnect();
$start_time=strtotime($periodfrom);
$end_time=strtotime($periodto);
$date_clause=" AND b1.time_id >= $start_time AND b2.time_id < $end_time";
//для каждого оператора будем делать выборки

   $QUERY = "select 
		b1.time_id as data,
		b1.data2 as src,
		b2.data3 as time,
		b2.agent as dst 
	    from queuemetrics.bad_log b1 
	    inner join queuemetrics.bad_log b2 
	    on b1.call_id=b2.call_id 
	    where b1.verb='ENTERQUEUE' and b2.verb='ABANDON'
            ";
    $QUERY.=$date_clause;
    $arr = $db->get_list($QUERY);
    foreach ($arr as $data){
	print"<tr>";
	print "<td align=\"center\" >$OperName</td>";
    };
    print"<tr>";
	print "<td align=\"center\" >$OperName</td>";
	$CsvData = $CsvData.$OperName.$CsvDelim;
	
	print "<td align=\"center\" >";$st = display_hours($WorkTime);print"</td>";
	$t = to_hours($WorkTime);
	$CsvData = $CsvData.$t.$CsvDelim;	
	
	print "<td align=\"center\" >$InCall</td>";
	$CsvData = $CsvData.$InCall.$CsvDelim;
	
	print "<td align=\"center\" >$OutCall</td>";
	$CsvData = $CsvData.$OutCall.$CsvDelim;	
	
	print "<td align=\"center\" >$InOutCall</td>";
	$CsvData = $CsvData.$InOutCall.$CsvDelim;	
	
	print "<td align=\"center\" >";$t = display_minute($AvgInCall);print "</td>";
	$t = to_hours($AvgInCall);
	$CsvData = $CsvData.$t.$CsvDelim;	
	
	print "<td align=\"center\" >";$t = display_minute($SumInCall);print "</td>";
	$t = to_hours($SumInCall);
	$CsvData = $CsvData.$t.$CsvDelim;	
	
	print "<td align=\"center\" >";$t = display_minute($AvgOutCall);print"</td>";
	$t = to_hours($AvgOutCall);
	$CsvData = $CsvData.$t.$CsvDelim;	
	
	print "<td align=\"center\" >";$t = display_minute($SumOutCall); print"</td>";	
	$t = to_hours($SumOutCall);
	$CsvData = $CsvData.$t.$CsvDelim;
	
	print "<td align=\"center\" >";$t = display_minute($SumInOutCall); print"</td>";	
	$t = to_hours($SumInOutCall);
	$CsvData = $CsvData.$t.$CsvDelim."\n";	
    print"</tr>";    
print"        </table>";
print"        </td>";
print"        </tr>";
print" </table>";

$fp = fopen("/var/www/html/ams/modules/ReportsSuper/csv/".$CsvFileName, 'w');
fwrite($fp,$CsvData);
fclose($fp);

exit;

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
		    SUM(IF(q.verb = 'AGENTCALLBACKLOGOFF' AND data1 != '',data2,0)) AS all_time,
		    COUNT(IF(q.verb = 'CONNECT',1,NULL)) AS count_answ,
		    AVG(IF(q.queue = '580' AND q.verb LIKE 'COMPLETE%',data2,NULL)) AS asr_talk,
		    SUM(IF(q.queue = '580' AND q.verb LIKE 'COMPLETE%',data2,0)) AS all_talk,
		    q.agent
		FROM queuemetrics.queue_log q
		LEFT JOIN
		    ( select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%') as users1
			
		ON substr(q.agent,7,3)=users1.agent 
		WHERE q.agent LIKE '%5%' 
		    AND time_id >= 1317488400 
		    AND time_id < 1317524640 
              ";
              
    $QUERY.=$date_clause;
    $QUERY.=" GROUP BY users1.last_name HAVING NOT (last_name is null);";
    $list_agents = $db->get_list($QUERY);
    $num = $db->count;
    $data_agents = array();
    $name_agents = array();
       for($i=0; $i<$num; $i++){
       	array_push($data_agents, $list_agents[$i][2]);
       	array_push($name_agents, $list_agents[$i][0]);
        $aname = $list_agents[$i][5];
       	$QUERY = "SELECT queuemetrics.w_time($start_time,$end_time,'$aname');";
       	print "aname = $aname";
       	$aname = $db->get_list($QUERY);
       	$list_agents[$i][1] = $aname[0][0];
       }
    $agents_compare = $num;
    $list_agents[$i][1] = $aname[0][0];

    $QUERY = "SELECT q.time_id, users1.last_name, SUBSTRING(q.data1,1,4)
   				FROM queuemetrics.queue_log as q
				LEFT JOIN
				    ( select substr(login,7,9) as Agent,real_name as last_name from queuemetrics.arch_users where login like '%5%') as users1
				ON substr(q.agent,7,3)=users1.agent    				
   				WHERE verb = 'AGENTCALLBACKLOGIN' AND time_id <> data2 AND time_id > $start_time AND time_id < $end_time ORDER BY time_id DESC;";
   $work_list = $db->get_list($QUERY);
   $count_work=$db->count;

   $query = "SELECT calldate, src, dst, billsec, UNIX_TIMESTAMP(calldate) AS time_id
   				FROM cdr
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
  // print_r($call_list);
   

?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl">
    <tr>
    <td>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="center" >Оператор</td>
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
      	$sum = 0;
      	for($j=0;$j<$count_calls;$j++) if($call_list[$j][1] == $list_agents[$i][0]) $sum+=$call_list[$j][3];
      ?>
      <tr >
		<td align="center" nowrap height=30><?=$list_agents[$i][0]?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][1])?></td>
		<td align="center" nowrap><?=$list_agents[$i][2];?></td>
		<td align="center" nowrap><?    reset($result);
										for($k=0;$k<count($result);$k++){
			                           		if($list_agents[$i][0] == key($result)){
			                    	 		   $str = current($result);
			                    	 		   echo  $str;
			                    	 		   break;
			                    			}
			                    			next($result);
			                    	    }?></td>
      									
		<td align="center" nowrap ><?=$list_agents[$i][2] + $str?></td>
		<td align="center" nowrap><?=round($list_agents[$i][3])?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][4])?></td>
		<td align="center" nowrap><?=round($sum/$str)?></td>
		<td align="center" nowrap><?=display_hours($sum)?></td>
		<td align="center" nowrap><?=display_hours($list_agents[$i][4]+$sum)?></td>
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
