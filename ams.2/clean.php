#!/usr/bin/php
<?
    $link = mysql_connect("localhost", "root", "")
            or die("Could not connect: " . mysql_error());
    mysql_query("insert into queuemetrics.bad_log select q.* from queuemetrics.queue_log as q inner join (select call_id from queuemetrics.queue_log where verb='ABANDON' and data3<6) as id on q.call_id=id.call_id;", $link) OR die(mysql_error());
    $query = mysql_query("select distinct call_id from queuemetrics.queue_log where verb='ABANDON' and data3<6", $link) OR die(mysql_error());
    $arr = array();
    while ($row = mysql_fetch_array($query, MYSQL_NUM)) {
	array_push($arr,$row[0]);
    }; 
    foreach($arr as $data){
	mysql_query("delete from queuemetrics.queue_log where call_id='$data'", $link) OR die(mysql_error());
	//mysql_query("insert into queuemetrics.queue_log select * from queuemetrics.queue_logt where call_id='$data'", $link) OR die(mysql_error());
	print $data."\n";
    };
    mysql_close($link);
?>

