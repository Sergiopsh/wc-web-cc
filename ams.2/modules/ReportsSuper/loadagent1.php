<?
function workTime($period_from,$period_to,$agent='')
{
    if(!$db) $db=DbConnect();
    $date_clause=" AND time_id >= $period_from AND time_id < $period_to";
        
    $QUERY="SELECT time_id, verb FROM queuemetrics.queue_log";
    $QUERY.=" WHERE queue='580' and agent='".$agent."'";
    $QUERY.=" AND verb IN ('ADDMEMBER','REMOVEMEMBER')";
    $QUERY.=$date_clause;
                        
    $list_member=$db->get_list($QUERY);
    $num_member=$db->count;
    
    $work_time=0;
    if ($num_member==0){
	return 0;
    }
    
    if ($list_member[0][1]=='ADDMEMBER'){
	$i=0; //если список начинается с ADDMEMBER, то все хорошо,
	//массив просматриваем с 0
    }
    else {
    $i=1;
    $work_time+=$list_member[0][0]-$period_from;
//	$work_time+=TIMESTAMPDIFF($list_member[0][0],$period_from);
    }
            
    while ($i<($num_member-1)){
    	$work_time+=$list_member[i+1][0]-$list_member[$i][0];
//	$work_time+=TIMESTAMPDIFF($list_member[i+1][0],$list_member[$i][0]);
	$i=$i+2;
    }
    if ($i==($num_member-1)) { //т.е. последняя запись типа ADDMEMBER
	$work_time+=$period_to-$list_member[$i][0];
//	$work_time+=TIMESTAMPDIFF($period_to,$list_member[$i][0]);
    }
/*    foreach ($list_member as $key => $value){
	if (is_array($value)) {
	echo "<br> [0]: ".$value[0]." [1]: ".$value[1];
	}
    }*/

//    print_r($list_member)
    return $work_time;
}