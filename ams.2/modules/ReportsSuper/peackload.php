<?php

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
cleardir("modules/ReportsSuper/images");

?>
<div id="frame-module-header" nowrap="nowrap">
<?=$strReports?>: Пиковые нагрузки
</div>
<br/>
<?

$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate");
$iformat=dt_format();

?>

<form name="module_form" id="module_form" method="post">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>"/>
<input type="hidden" name="test111" id="test111" value=""/>
<?
if(!isset($days_compare)) $days_compare=2;
$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat","","function (cal) { $('fixperiod').value='0'; }");
//$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="$('module_form').current_page.value=0;loadModule(1,'','ReportsSuper','PeackReport',\$H({search: 1}));";
$p->align2="right";
$p=$tbl->addElement("between","select",$strBetween,2,$between);
$p->options=array(0=>"один день",1=>"два дня",2=>"три дня",3=>"четыре дня",4=>"пять дней",5=>"шесть дней");
$tbl->show();

if ($search) {
	if(!$db) $db=DbConnect();
	$legend = array();
	$timefrom = strtotime($periodfrom) - $between*86400;
	$timeto = $timefrom;
	$count = $between;
	$count+=1;
	
	if (($count < 8)){
		$QUERY = "SELECT HOUR(FROM_UNIXTIME(time_id)) AS HR, ";
		for($i=0;$i<$count;$i++){
			$timeto = $timefrom + 86400;
			$QUERY.="COUNT(IF(verb = 'ENTERQUEUE' AND time_id BETWEEN $timefrom AND $timeto,1,NULL)) AS day";
			$QUERY.=$i;
				if((($count-$i)>1) && ($count>1))$QUERY.=", ";
				else $QUERY.=" ";
			array_push($legend, strftime('%d.%m.%y',$timefrom));
			$timefrom = $timeto;
		}
		$QUERY.="FROM queuemetrics.queue_log WHERE queue = '580' GROUP BY HR;";
	}
/*    elseif($between==1){
    	$count = round($count/7);
       	if($count < 5){
       		$QUERY = "SELECT HOUR(FROM_UNIXTIME(time_id)) AS HR, ";
    			for($i=0;$i<$count;$i++){
					$timeto = $timefrom + 604800;
					$QUERY.="ROUND(COUNT(IF(verb = 'ENTERQUEUE' AND time_id BETWEEN $timefrom AND $timeto,1,NULL))/7) AS week";
					$QUERY.=$i;
						if((($count-$i)>1) && ($count>1))$QUERY.=", ";
						else $QUERY.=" ";
					array_push($legend, strftime('%d.%m.%y',$timefrom));
					$timefrom = $timeto;
    			}
    		$QUERY.="FROM queuemetrics.queue_log WHERE queue = '580' GROUP BY HR;"; 	
    	}
    }
    elseif($between==2){
    	$count = round($count/30);
    	if($count < 7){
       		$QUERY = "SELECT HOUR(FROM_UNIXTIME(time_id)) AS HR, ";
 				for($i=0;$i<$count;$i++){
					$date_time_array = getdate($timefrom);
					$hours = $date_time_array['hours'];
    				$minutes = $date_time_array['minutes'];
    				$seconds = $date_time_array['seconds'];
    				$month = $date_time_array['mon'];
    				$day = $date_time_array['mday'];
    				$year = $date_time_array['year'];
					$month+=1;
					$timeto = mktime($hours,$minutes,$seconds,$month,$day,$year);    				
					$QUERY.="ROUND(COUNT(IF(verb = 'ENTERQUEUE' AND time_id BETWEEN $timefrom AND $timeto,1,NULL))/30) AS month";
					$QUERY.=$i;
						if((($count-$i)>1) && ($count>1))$QUERY.=", ";
						else $QUERY.=" ";
					array_push($legend, strftime('%m.%y',$timefrom));
					$timefrom = $timeto;
 				}
 			$QUERY.="FROM queuemetrics.queue_log WHERE queue = '580' GROUP BY HR;"; 
    	}
    }*/
    if($count){
    	$list = $db->get_list($QUERY);
    	$ind_end = $count-1;
    	$table_subtitle = "$strCallsPeriod: $legend[0] - $legend[$ind_end]";
    }
    if(isset($list[0][1])) include("modules/ReportsSuper/graph_peak.php");
    else{ 
?>
    <center><div class="module-note"><?=$strNoCalls?></div></center>
<?
    }
    
?>
    <center>
	<IMG SRC="modules/ReportsSuper/images/<?=$filenam_res?>">
	</center>
<?
}
?>


