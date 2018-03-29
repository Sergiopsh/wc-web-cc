<?
function workTime($period_from,$period_to,$agent)
{
    if(!$db) $db=DbConnect();
    $date_clause=" AND time_id >= $period_from AND time_id < $period_to";
    $QUERY="
	SELECT time_id,verb FROM queuemetrics.queue_log
        WHERE queue='580' and agent='$agent'
	AND verb in ('ADDMEMBER','REMOVEMEMBER')
	$date_clause
	order by time_id desc
	";    
    $list_member=$db->get_list($QUERY);
    $list_member_count = $db->count-1;//отнимаем 1 чтобы считать с 0, для убодства работы с массивом
    if ($list_member_count < 0) return 0;//если в выборке было менее двух записей
    
    //предполагается что будем рассматривать день в целом, тоесть рассматриваемый интервал будет начинаться с add_member
    //Поэтому если последний элемент=REMOVEMEMBER,то перед нима поставим addmember со временем period_from
    if ($list_member[$list_member_count][1] == 'REMOVEMEMBER'){
	//$list_member[$list_member_count+1][1] = 'ADDMEMBER';
	//$list_member[$list_member_count+1][0] = $period_from;	
	//$list_member_count++;
	unset($list_member[$list_member_count]);	
	$list_member_count--;
    };
    //если первый элемент = ADDMEMBER, то после него поставим REMOVEMEMBER с period_to
    if ($list_member[0][1] == 'ADDMEMBER'){
	$list_member = array_pad($list_member,-($list_member_count+2),array($period_to,'REMOVEMEMBER'));
	$list_member_count++;
    };
    //получен массив с чётным количеством элементов, начинающихся с REMOVEMEMBER и заканчивающихся ADDMEMBER
    $w_time = 0;
    for ($i=0;$i<$list_member_count;$i+=2)
    {
	$w_time += $list_member[$i][0] - $list_member[$i+1][0];
    };
    return $w_time;
}

?>
