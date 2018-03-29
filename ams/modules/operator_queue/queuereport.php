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

/*
Переведённые звонки
*/

//получение информации по текущим звонкам
$queue_call_arr = explode("\n",shell_exec("asterisk -rx \"show channels concise\"|grep -v \"None\" |egrep \"(trWM\\(auto-blkvm\\))|(trW!)\""));
foreach($queue_call_arr as $q_call_arr){
    if (strlen($q_call_arr)<10){
	continue;
    };
    $cal = array();
    $arr = explode("!",$q_call_arr);
    $cal['status'] = "connect_transfer";
    $cal['queue_number'] = "580";
    $ag_arr = explode("||",$arr[6]);
    $ag_arr = explode("/",$ag_arr[0]);
    $cal['agent'] = "Local/".$ag_arr[1]."@from-internal";
    $cal['time_in_queue'] = time();
    $cal['time_in_connect'] = time();
    $cal['caller'] = $arr[7];
    array_push($calls,$cal);
};


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<div nowrap id="frame-module-header">
<?=$strQueueReport?>: Очереди
</div>
<br>
<?

//Получим номер агента из $_SESSION['user_name']
$agent = explode("-",$_SESSION['user_name']);
$agent = $agent[1];
if (!is_numeric($agent)) exit;
?>
<table border="1" cellspacing="0" cellpadding="0" width="100%" class="report-tbl">
 <tr><td>
 	<table border="1" cellspacing="0" cellpadding="0" width="100%" style="font-size:16px;">

	<tr id="head">
	<td align="center">Наименование Очереди</td>
	<td align="center">Статус звонка</td>
	<td align="center">Время входа в очередь</td>
	<td align="center">Время ожидания ответа специалиста</td>
	<td align="center">Номер звонящего</td>
	<td align="center">Номер агента</td>
	</tr>
<?
//Вывод сначала тех, которые в ожидании
foreach ($calls as $cal){
    if (strstr($cal['status'],'queue')){
	echo "<tr>";
	echo "<td align=\"center\">".$cal['queue_number']."</td>";
	echo "<td align=\"center\">В очереди</td>";
	$time = getdate($cal['time_in_queue']);
	$time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
	echo "<td align=\"center\">".$time."</td>";
	$time = time() - $cal['time_in_queue'];
	echo "<td align=\"center\">".$time."</td>";
	echo "<td align=\"center\">".$cal['caller']."</td>";
	echo "<td align=\"center\">-</td>";
	echo "</tr>";
    };
};

//теперь тех кто разговаривает
$c=0;
foreach ($calls as $cal){

	$c++;
	if ($c%2==1){
	    $color="lightgreen";
	}else{
	    $color="green";
	};

    if (strstr($cal['status'],'connect')){
	echo "<tr bgcolor=\"$color\">";
	echo "<td bgcolor=\"$color\" align=\"center\">".$cal['queue_number']."</td>";
	if (strstr($cal['status'],'transfer')){
	    echo "<td bgcolor=\"$color\" align=\"center\">Соединён с агентом(Переведённый звонок)</td>";
	}else{
	    echo "<td bgcolor=\"$color\" align=\"center\">Соединён с агентом</td>";
	};
	$time = getdate($cal['time_in_queue']);
	$time = $time['year']."-".$time['month']."-".$time['mday']." ".$time['hours'].":".$time['minutes'].":".$time['seconds'];
	echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	$time = $cal['time_in_connect'] - $cal['time_in_queue'];
	echo "<td bgcolor=\"$color\" align=\"center\">".$time."</td>";
	echo "<td bgcolor=\"$color\" align=\"center\">".$cal['caller']."</td>";
	preg_match("/\/(.*)@/",$cal['agent'],$agent);
	$agent = $agent[1];
	$agent_name = GetOperNameByID($agent)."(".$agent.")";

	echo "<td bgcolor=\"$color\" align=\"center\">".$agent_name."</td>";

	echo "</tr>";
    };
};

?>

	</table>
</tr>
</table>

<script type="text/javascript">
timer_id = 0;
function timer(){
 var obj=document.getElementById('timer_inp');
  obj.innerHTML--;
   if(obj.innerHTML==0){clearTimeout(timer_id);setTimeout(function(){},1000);loadModule('','operator_queue','operator_queue','QueueReport');}
    else{clearTimeout(timer_id);timer_id = setTimeout(timer,1000);}
    }
    timer_id = setTimeout(timer,1000);
    </script>
<br>
<br>
<div nowrap id="frame-module-header">
Автоматическое обновление через:
</div>  

  
    <div style="font-size:70px; color:red;" id="timer_inp">10</div>    
    <?
    	    echo "<input class=\"sbutton\" type=\"button\" value=\"Принудительно\" onclick=\"loadModule('','operator_queue','operator_queue','QueueReport');\">";	    
    ?>