<?
function workTime($period_from,$period_to,$agent)
{
    if(!$db) $db=DbConnect();
    $date_clause=" AND time_id >= $period_from AND time_id < $period_to";
    $QUERY="
	SELECT time_id,verb FROM queuemetrics.queue_log
        WHERE queue='580' and agent='$agent'
	AND verb in ('ADDMEMBER','REMOVEMEMBER','PAUSE','UNPAUSE')
	$date_clause
	order by time_id asc
	";    
    $list_member=$db->get_list($QUERY);
    $list_member_count = $db->count-1;//отнимаем 1 чтобы считать с 0, для убодства работы с массивом

    $work_time = 0;
    $pause_time = 0;
    $prev_event = "start";
    $prev_event_time = $period_from;
    
    foreach ($list_member as $rec){
	if (strstr($rec[1],"ADDMEMBER")){
	    $prev_event = 'ADDMEMBER';
	    $prev_event_time = $rec[0];
	};
	if (strstr($rec[1],"REMOVEMEMBER")){
	    if (strstr($prev_event,"start")){
		$work_time = $rec[0] - $period_from;
		$prev_event = 'REMOVEMEMBER';
		$prev_event_time = $rec[0];
		continue;
	    };
	    if (strstr($prev_event,"ADDMEMBER")){
		//считаем время от начала $period_from
		$work_time += $rec[0] - $prev_event_time;
		$prev_event = 'REMOVEMEMBER';
		$prev_event_time = $rec[0];
		continue;
	    };
	    if (strstr($prev_event,"UNPAUSE")){
		//считаем время от начала $period_from
		$work_time += $rec[0] - $prev_event_time;
		$prev_event = 'REMOVEMEMBER';
		$prev_event_time = $rec[0];
		continue;
	    };
	    if (strstr($prev_event,"PAUSE")){
		//считаем время от начала $period_from
		$pause_time += $rec[0] - $prev_event_time;
		$prev_event = 'REMOVEMEMBER';
		$prev_event_time = $rec[0];
		continue;
	    };
	    
	};
	if (strstr($rec[1],"PAUSE") && !strstr("$rec[1]","UNPAUSE")){
	    if (strstr($prev_event,"start")){
		//считаем время от начала $period_from
		$work_time = $rec[0] - $period_from;
		$prev_event_time = $period_from;
	    };
	    if (strstr($prev_event,"ADDMEMBER")){
		//считаем время от начала $period_from
		$work_time += $rec[0] - $prev_event_time;
		$prev_event_time = $rec[0];
	    };
	    if (strstr($prev_event,"UNPAUSE")){
		//считаем время от начала $period_from
		$work_time += $rec[0] - $prev_event_time;
		$prev_event_time = $rec[0];
	    };

	    $prev_event = 'PAUSE';

	};
	if (strstr($rec[1],"UNPAUSE")){
	    if (strstr($prev_event,"start")){
		//считаем время от начала $period_from
		$pause_time = $rec[0] - $period_from;
		$prev_event_time = $period_from;
	    };
	    if (strstr($prev_event,"PAUSE") && !strstr($prev_event,"UNPAUSE")){
		//считаем время от начала $period_from
		$pause_time += $rec[0] - $prev_event_time;
		$prev_event_time = $rec[0];
	    };
	    $prev_event = 'UNPAUSE';

	};
	
    };

    if (strstr($prev_event,"ADDMEMBER")||strstr($prev_event,"UNPAUSE")){
	//считаем время от начала $period_from
	$work_time += $period_to - $prev_event_time;
    }else
    if (strstr($prev_event,"PAUSE")){
	$pause_time += $period_to - $prev_event_time;
    };    

//print_r($list_member);
//print "<br>";
//print "work_time = $work_time";
//print "<br>";
//print "$QUERY";
//print "<br>";
//exit;
$out['work_time'] = $work_time;
$out['pause_time'] = $pause_time;
    return $out;
}

?>
