<?
function get_agent_queue($queue_info_arr,$agent){

//Ищем в queue_info_arr все строки удовлетворяющую маске Local/номер_агента. Как только нашли, идём обратно до строки содержащей слово members. 
//Cтрока выше members будет содержать имя очереди
$agent_queue = array();
foreach ($queue_info_arr as $key => $value){
    if (strstr($value,"Local/".$agent)){
	//Идём назад, ищем слово members
	$members_position = $key-1;
	while (!strstr($queue_info_arr[$members_position],"Members")){
	    $members_position --;
	};
	//Из строки с номером учереди убираем мусор
	$has_position = strpos($queue_info_arr[$members_position-1],"has");
	$queue_name = substr($queue_info_arr[$members_position-1],0,$has_position-2);
	//В 0 строке берётся какойто мусор в начале. Выкосим его
	if ($members_position==1){
	     $queue_name = substr($queue_name,strpos( $queue_name,"m")+1,strlen($queue_name));
	};
	$queue_name = preg_replace("/\s+/", "", $queue_name);
	array_push($agent_queue,$queue_name);
    };
}

return $agent_queue;
}

function get_queue_list($queue_info_arr){

//Ищем в queue_info_arr строкe содержащую слово members. 
//Cтрока выше members будет содержать имя очереди
$queues = array();
foreach ($queue_info_arr as $key => $value){
    if (strstr($value,"Members")){
	//Идём назад, ищем слово members
	$members_position = $key;
	//Из строки с номером учереди убираем мусор
	$has_position = strpos($queue_info_arr[$members_position-1],"has");
	$queue_name = substr($queue_info_arr[$members_position-1],0,$has_position-2);
	//В 0 строке берётся какойто мусор в начале. Выкосим его
	if ($members_position==1){
	     $queue_name = substr($queue_name,strpos( $queue_name,"m")+1,strlen($queue_name));
	};
	$queue_name = preg_replace("/\s+/", "", $queue_name);
	array_push($queues,$queue_name);
    };
}

return $queues;
}

function agent_in_queue($agent_queues,$queue){
    foreach ($agent_queues as $val){
	if (strstr($val,$queue)) {
	    return true;
	};
    };
    return false;
};
?>
