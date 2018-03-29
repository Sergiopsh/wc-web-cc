<?php

	$ex = exec("/bin/ps ax |/bin/grep php|/usr/bin/wc -l");
	if ($ex>20) exit;
	
	include '/var/www/html/ams/modules/autocall/call_with_summ.php';
	
	$h=@mysql_pconnect("localhost", "root", "");
	$res=mysql_query("select id,calls_in_min,try_delay,answer_wait,try_count,end_date from autocall.tasks_with_summ where task_status='work' and end_date>NOW()",$h); 
	while ($row = mysql_fetch_array($res)) {
	    //проверка на выполненность всех обзвонов
	    $QUERY="select count(*) as cnt from autocall.calls_with_summ where task_id=".$row["id"]." and call_status in (0,1,3)";
	    $c_res = mysql_query($QUERY);
	    $c = mysql_fetch_array($c_res);
	    if ($c["cnt"]==0){
		$QUERY="update autocall.tasks_with_summ set task_status='end' where id=".$row["id"];
		mysql_query($QUERY);
		continue;
	    };
	        for ($proc_num = 0; $proc_num < $row["calls_in_min"]; $proc_num++) {
	            $pid2 = pcntl_fork();
	            if ($pid2) {
	        	// we are the parent
	        	//pcntl_wait($status); //Protect against Zombie children
	        	continue;
		    }else {
			posix_setsid();
	        	$mypid = getmypid();
	        	sleep(5);
	        	sleep(5*$proc_num);
	        	$mypid = getmypid();
	        	echo "Pid after".$mypid." try_count=".$row["try_count"]."\n";
	            	$h=@mysql_pconnect("localhost", "root", "");
	        	$mypid = getmypid();
	        	$QUERY="UPDATE autocall.calls_with_summ set Processed_by = ".$mypid.", processed_status = '1' WHERE Processed_by=0 and processed_status=0 and task_id=".$row["id"]." LIMIT 1";
	        	mysql_query($QUERY);
	        	$up_res = explode(' ',mysql_info());
	        	if ($up_res[2]!=1) exit;
	        	$QUERY = "select phone_number,path,id,summ from autocall.calls_with_summ where Processed_by=".$mypid;
	        	$ph_res=mysql_query($QUERY);
	        	$ph = mysql_fetch_array($ph_res);
	        	$QUERY = "update autocall.calls_with_summ set call_status=1,date_first_try=NOW() where Processed_by=".$mypid;
	        	mysql_query($QUERY);
	        	//начинаем попытки дозвона для конкретной записи
	        	for($try_count = 0; $try_count < $row["try_count"]; $try_count++){
	        	    $QUERY = "update autocall.calls_with_summ set call_status=1,try_number=try_number+1 where Processed_by=".$mypid;
	        	    mysql_query($QUERY);
	        	    //echo "start call";
	        	    //123
	        	    $rub = floor($ph["summ"]);
	        	    $kop = (($ph["summ"] - $rub)*100)%100;
	        	    if (call_with_summ($rub,$kop,$ph["phone_number"],$ph["path"],$row["answer_wait"],$ph["id"])==1){
	        		$QUERY = "update autocall.calls_with_summ set call_status=2,date_end=NOW(),Processed_by=1 where Processed_by=".$mypid;
	        		mysql_query($QUERY);
	        		echo "end call";	        	    
	        		exit;
	        	    };
	        	    //echo "end call";
	        	    $QUERY = "update autocall.calls_with_summ set call_status=3 where Processed_by=".$mypid;
	        	    mysql_query($QUERY);
	        	    sleep($row["try_delay"]);
	        	};
	        	$QUERY = "update autocall.calls_with_summ set call_status=4,date_end=NOW(),Processed_by=1 where Processed_by=".$mypid;
	        	mysql_query($QUERY);	        	    
	        	
	        	exit;
	            }
	        }
	}

?>
