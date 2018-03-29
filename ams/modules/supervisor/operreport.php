<?php

function GetOperNameByID($id){
    $id2 = $id + 10;
    if(!$db) $db=DbConnect();
    $query = "select first_name FROM users where user_name like '%$id%' or user_name like '%$id2%'";
    $list_two = $db->get_list($query);
    if (strstr($list_two[0][0],"admin")){
	$list_two[0][0] = "-";
    };
    if (strlen($list_two[0][0])<2){
	    return $id;
    }else{
	return $list_two[0][0];
    }
};



//получение информации по очередям
$queue_info_arr = explode("\n",shell_exec("tail -n 7000  /var/log/asterisk/queue_log"));

$queue_info_arr_size = count($queue_info_arr);
$calls = array();
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
	    array_push($calls,$call);
	};
    };
}
//print_r($calls);
?>
<div nowrap id="frame-module-header">
<?=$strOperReport?>: 
</div>
<br>


<table border="1" cellspacing="0" cellpadding="0" width="100%" class="report-tbl" style="font-size:70px;"   >
 <tr><td>
 	<table border="1" cellspacing="0" cellpadding="0" width="100%" style="font-size:16px;" >

	<tr id="head">
	<td align="center">Агент</td>
	<td align="center">Время регистрации</td>
	<td align="center">Номер очереди</td>
	<td align="center">На паузе с(время)</td>
	<td align="center">Последний звонок (время подключения)</td>
	<td align="center">Длительность последнего звонка</td>
	</tr>
<?
$c=0;
foreach ($calls as $cal){
if (!strstr($cal['queue_number'],"580")){continue;};
	$color='lightgreen';
	if (strstr($cal['status'],"connect")){
	    $color='red';
	};
	if (strstr($cal['status'],"pause")){
	    $color='yellow';
	};
	echo "<tr>";
	$agent_name = GetOperNameByID($cal['agent'])."(".$cal['agent'].")";
	echo "<td bgcolor=\"$color\" align=\"center\">".$agent_name."</td>";
	$time = getdate($cal['time_in_queue']);
	$time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
	echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	echo "<td bgcolor=\"$color\" align=\"center\">".$cal['queue_number']."</td>";
	if (strstr($cal['status'],"pause")){
	    $time = getdate($cal['time_in_pause']);
	    $time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
	    echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	}else{
	    echo "<td bgcolor=\"$color\" align=\"center\">"."-"."</td>";
	};
	if (isset($cal['time_in_call'])){
	    $time = getdate($cal['time_in_call']);
	    $time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
	    echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	}else{
	    echo "<td bgcolor=\"$color\" align=\"center\">"."-"."</td>";
	};
	if (strstr($cal['status'],"connect")){
	    echo "<td bgcolor=\"$color\" align=\"center\">"."Идёт разговор"."</td>";
	}else 
	    if (isset($cal['time_end_call'])){
		$time = $cal['time_end_call'] - $cal['time_in_call'];
		echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	    }else{
		echo "<td bgcolor=\"$color\" align=\"center\">"."-"."</td>";
	    };	
	echo "</tr>";
};


?>

	</table>
</tr>
</table>

<script type="text/javascript">
oper_timer_id = 0;
function oper_timer(){
 var obj=document.getElementById('oper_timer_inp');
  obj.innerHTML--;
   if(obj.innerHTML==0){clearTimeout(oper_timer_id);setTimeout(function(){},1000);loadModule('','supervisor','supervisor','OperReport');}
    else{clearTimeout(oper_timer_id);oper_timer_id = setTimeout(oper_timer,1000);}
    }
    oper_timer_id = setTimeout(oper_timer,1000);
    </script>
<br>
<br>
<div nowrap id="frame-module-header">
Автоматическое обновление через:
</div>  

  
    <div style="font-size:70px; color:red;" id="oper_timer_inp">10</div>    
    <?
    	    echo "<input class=\"sbutton\" type=\"button\" value=\"Принудительно\" onclick=\"loadModule('','supervisor','supervisor','OperReport');\">";	    
    ?>