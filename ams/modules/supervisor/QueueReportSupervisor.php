<?php


//получение информации по очередям
$queue_info_arr = explode("\n",shell_exec("tail -n 7000  /var/log/asterisk/queue_log"));

$queue_info_arr_size = count($queue_info_arr);
$agents = array();
foreach ($queue_info_arr as $key => $value){
    //Ищем агента, вощедшего в очередь
    if (strstr($value,"ADDMEMBER")){
	$val_arr = explode("|",$value);
	$status = "add";
	$call = array();
	$call['status'] = $status;
	$call['time_in_queue'] = $val_arr[0];
	preg_match("/\/(.*)@/",$val_arr[3],$agent);
	$call['agent'] = $agent[1];	
	$call['queue_number'] = $val_arr[2];
	
	for ($i=$key+1;$i<$queue_info_arr_size;$i++){
	    //Ищем для текущего агента и очереди в которую он вошел : 
	    //REMOVEMEMBER Вышел из очереди
	    //PAUSEALL поставился на паузу 
	    //CONNECT агент начал разговор
	    //UNPAUSE снялся с паузы
	    if (strstr($queue_info_arr[$i],$call['agent']."@") && strstr($queue_info_arr[$i],"|".$call['queue_number']."|")){
		//попадаем сюда, если нашлось какое-то событие для агента
		$agent_arr = explode("|",$queue_info_arr[$i]);
		if (strstr($queue_info_arr[$i],"REMOVEMEMBER")){
		    $status = "remove";
		    //дальше искать смысла не имеет, так как агент вышел из очереди
		    break;
		};
		if (strstr($queue_info_arr[$i],"|PAUSE|")){
		    $call['time_in_pause'] = $agent_arr[0];
		    //Агент встал на паузу. Необходимо попробовать найти конец этой паузы
		    $call['status'] = "pause";
		    continue;
		};
		
		if (strstr($queue_info_arr[$i],"CONNECT")){
		    $call['time_in_call'] = $agent_arr[0];
		    //Агент начал разговор
		    $call['status'] = "connect";
		    continue;
		};

		if (strstr($queue_info_arr[$i],"COMPLETE") || strstr($queue_info_arr[$i],"ABANDON") || strstr($queue_info_arr[$i],"TRANSFER")){
		    $call['time_end_call'] = $agent_arr[0];
		    //Агент закончил разговор
		    $call['status'] = "add";
		    continue;
		};

		if (strstr($queue_info_arr[$i],"UNPAUSE|")){
		    $call['time_end_pause'] = $agent_arr[0];
		    //Агент снялся с паузы
		    $call['status'] = "add";
		    continue;
		};
	    };
	};
	if (strstr($status,"add") || strstr($status,"pause") || strstr($status,"connect")){
	    array_push($agents,$call);
	};
    };
}


$calls = array();
foreach ($queue_info_arr as $key => $value){
    //Ищем звонок, попавший в очередь
    if (strstr($value,"ENTERQUEUE")){
	$val_arr = explode("|",$value);
	$status = "queue";
	$call = array();
	$call['status'] = $status;
	$call['time_in_queue'] = $val_arr[0];
	$call['caller'] = $val_arr[6];
	$call['queue_number'] = $val_arr[2];
	for ($i=$key+1;$i<$queue_info_arr_size;$i++){
	    //Ищем для него CONNECT тоесть ответил оператор
	    //или ищем для него ABONDAN
	    if (strstr($queue_info_arr[$i],$val_arr[1])){
		if (strstr($queue_info_arr[$i],"CONNECT")){
		    //Ответил оператор.
		    $connstrarr = explode("|",$queue_info_arr[$i]);
	    	    $status = "connect";
	    	    $call['status'] = $status;
	    	    $call['time_in_connect'] = $connstrarr[0];
	    	    $call['agent'] = $connstrarr[3];
	    	    //попробуем найти завершение вызова или перевод звонка
	    	    for ($k=$i+1;$k<$queue_info_arr_size;$k++){
	    		if (strstr($queue_info_arr[$k],$val_arr[1])){
	    		    if (strstr($queue_info_arr[$k],"COMPLETE")||strstr($queue_info_arr[$k],"TRANSFER")){
	    			$status = "complete";
	    			break;
	    		    }else{
	    			print "Logic Error";
	    			exit;
	    		    }
	    		};
	    	    };
	    	    break;
		};
		if (strstr($queue_info_arr[$i],"ABANDON")){
		    //Абонент сам повешал трубку
	    	    $status = "abandon";
	    	    break;
		};

	    };
	};
	if (strstr($status,"queue") || strstr($status,"connect")){
	    array_push($calls,$call);
	};
    };	
}
$queues = array();
foreach ($agents as $agent){
    $exist = 0;
    foreach ($queues as $queue){
	if(strstr($agent['queue_number'],$queue)){
	    $exist = 1;
	    break;
	};
    };
    if ($exist==0){
	array_push($queues,$agent['queue_number']);
    };
};


?>
<div nowrap id="frame-module-header">
<?=$strQueueReportSupervisor?>: 
</div>
<br>


<table border="1" cellspacing="0" cellpadding="0" width="100%" class="report-tbl">
 <tr><td>
 	<table border="1" cellspacing="0" cellpadding="0" width="100%" style="font-size:16px;">

	<tr id="head">
	<td align="center">Номер очереди</td>
	<td align="center">Кол-во активных агентов</td>
	<td align="center">Кол-во свободных агентов</td>
	<td align="center">Кол-во агентов на паузе</td>
	<td align="center">Кол-во занятых агентов</td>
	<td align="center">Кол-во ожидающих звонков</td>	
	</tr>
<?
foreach ($queues as $queue){
if (!strstr($queue,"580")) {continue;};
    	echo "<tr>";
	echo "<td align=\"center\">".$queue."</td>";
	$agent_active_count = 0;
	$agent_free_count = 0;	
	$agent_pause_count = 0;	
	$agent_connect_count = 0;	
	foreach ($agents as $agent){
	    if (strstr($agent['queue_number'],$queue)){
		$agent_active_count++;
		if (strstr($agent['status'],"add")){
		    $agent_free_count++;	    
		    continue;
		};
		if (strstr($agent['status'],"pause")){
		    $agent_pause_count++;	    
		    continue;
		};		
		if (strstr($agent['status'],"connect")){
		    $agent_connect_count++;	    
		    continue;
		};		
	    };
	};
	$wait_calls = 0;
	foreach ($calls as $cal){
	    if (strstr($call['queue_number'],$queue)){
		 if (strstr($call['status'],"queue")){
		    $wait_calls++;
		 }
	    };
	};
	echo "<td align=\"center\">".$agent_active_count."</td>";	
	echo "<td align=\"center\">".$agent_free_count."</td>";	
	echo "<td align=\"center\">".$agent_pause_count."</td>";	
	echo "<td align=\"center\">".$agent_connect_count."</td>";	
	echo "<td align=\"center\">".$wait_calls."</td>";		
    	echo "</tr>";
};
?>

	</table>
</tr>
</table>

<script type="text/javascript">
oper_timer_queue_report_id = 0;
function oper_timer_queue_report(){
 var obj=document.getElementById('oper_timer_queue_report_inp');
  obj.innerHTML--;
   if(obj.innerHTML==0){clearTimeout(oper_timer_queue_report_id);setTimeout(function(){},1000);loadModule('','supervisor','supervisor','QueueReportSupervisor');}
    else{clearTimeout(oper_timer_queue_report_id);oper_timer_queue_report_id = setTimeout(oper_timer_queue_report,1000);}
    }
    oper_timer_queue_report_id = setTimeout(oper_timer_queue_report,1000);
    </script>
<br>
<br>
<div nowrap id="frame-module-header">
Автоматическое обновление через:
</div>  

  
    <div style="font-size:70px; color:red;" id="oper_timer_queue_report_inp">10</div>    
    <?
    	    echo "<input class=\"sbutton\" type=\"button\" value=\"Принудительно\" onclick=\"loadModule('','supervisor','supervisor','QueueReportSupervisor');\">";	    
    ?>