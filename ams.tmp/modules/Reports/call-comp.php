<?php
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
cleardir("modules/Reports/images");

?>
<div id="frame-module-header" nowrap="nowrap">
<?=$strReports?>: <?=$strCompareCalls?>
</div>
<br/>
<?


$date_clause='';

$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt($currentdate);


if (!isset($days_compare)){
	$days_compare=2;
}
$iformat=dt_format();

?>

<form name="module_form" id="module_form" method="post">
<input type="hidden" name="test111" id="test111" value=""/>
<?
if(!isset($days_compare)) $days_compare=2;
//echo $iformat."<br/>";
$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat","","",false); $p->width1="15%"; $p->colspan=5;
$p=$tbl->addElement("days_compare","select",$strLapsDays,1,$days_compare);  $p->width1="15%";
$p->options=array(4=>"- 4 $str2Days",3=>"- 3 $str2Days",2=>"- 2 $str2Days",1=>"- 1 $str2Days");
$p=$tbl->addElement("","button","",1,$strSearch); $p->width2="15%";
$p->action="loadModule(1,'','Reports','CompareCalls',\$H({search: 1}));"; $p->align2="right";
$p=$tbl->addElement("dst","text",$strDialedDigits,2,$dst); $p->setOptions(21);$p->colspan=5;
$p=$tbl->addElement("userfield","text",$strUserField,2,$userfield);  $p->setOptions(21);
$p=$tbl->addElement("name","text",$strDest,3,$name); $p->setOptions(21);$p->colspan=5;
$p=$tbl->addElement("accountcode","text",$strAccountCode,3,$accountcode); $p->setOptions(21);
$p=$tbl->addElement("src","text",$strSource,4,$src); $p->setOptions(21);$p->colspan=5;
$p=$tbl->addElement("statustype","select",$strDisposition,4,$statustype); 
$p->options=array(1=>$strAll,2=>$strAnswered,3=>$strNoAnswer,4=>$strBusy,6=>$strFailed,8=>$strChanUnavail);
/*
$p=$tbl->addElement("department","text",$strDepartment,5,$department);
$p->setOptions(40,0,"","","modules/Departments/filldeps.php");
*/
$p=$tbl->addElement("","simple","",5,$strDuration);
$p=$tbl->addElement("duration1","text",$strFrom,5,$duration1);
$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
$p=$tbl->addElement("duration2","text",$strTo,5,$duration2);
$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
$p=$tbl->addElement("","simple","",5,$strSec);

$tbl->show();

?>



</FORM>
</center>
<br>


<?
if ($search) {

  if(!$db) $db=DbConnect();
  $sql="";
  switch($statustype) {
		case 2: $status_sql = " AND disposition = 'ANSWERED'"; break;
		case 3: $status_sql = " AND disposition = 'NO ANSWER'"; break;
		case 4: $status_sql = " AND disposition = 'BUSY'"; break;
		case 6: $status_sql = " AND disposition = 'FAILED'"; break;
		case 8: $status_sql = " AND disposition = 'CHANANVAIL'"; break;
		default: $status_sql = " "; break;
  }
  $sql.=$status_sql;

  if($duration1) $sql.=" AND (ratesec >= ".quote($duration1).")";
  if($duration2) $sql.=" AND (ratesec <= ".quote($duration2).")";

  $db->set_sql_interval("ratesec",array($duration1,$duration2));
  $sql.=$db->sql_interval;

  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;
  
  $periodfrom=dt_to_dbformat($periodfrom."00:00","00");
  
  $fromstatsday_sday=substr($periodfrom,8,2);
  $fromstatsmonth_sday=substr($periodfrom,0,7);
  $fromstatsdate=$fromstatsmonth_sday."-".$fromstatsday_sday;

  $sql.=" AND calldate < ADDDATE('$fromstatsdate',INTERVAL 1 DAY)
	 AND calldate >= SUBDATE('$fromstatsdate',INTERVAL $days_compare DAY)";  



	$QUERY = "SELECT calldate,ratesec,duration, IF(disposition = 'ANSWERED', (duration - billsec), NULL)";
	$QUERY.=" FROM $table WHERE 1".$sql;  

	$list = $db->get_list($QUERY);
	$num = $db->count;

if (is_array($list) && count($list)>0){

$table_graph=array();
$table_graph_hours=array();
$numm=0;
foreach ($list as $recordset){

		$numm++;
		$mydate= substr($recordset[0],0,10);
		$mydate_hours= substr($recordset[0],0,13);

		if (is_array($table_graph_hours[$mydate_hours])){
			$table_graph_hours[$mydate_hours][0]++;
			$table_graph_hours[$mydate_hours][1]=$table_graph_hours[$mydate_hours][1]+$recordset[1];
		}else{
			$table_graph_hours[$mydate_hours][0]=1;
			$table_graph_hours[$mydate_hours][1]=$recordset[1];
		}
		
		
		if (is_array($table_graph[$mydate])){
			$table_graph[$mydate][0]++;
			$table_graph[$mydate][1]=$table_graph[$mydate][1]+$recordset[1];
		}else{
			$table_graph[$mydate][0]=1;
			$table_graph[$mydate][1]=$recordset[1];
		}
		if ($recordset[1]) 
{
		    $table_graph[$mydate][2]++;
		    $table_graph[$mydate][3]+=$recordset[2]-$recordset[1];
		}
}

$mmax=0;
$totalcall==0;
$totalminutes=0;
$totalcallsanswered=0;
$totalpdd=0;
foreach ($table_graph as $tkey => $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	
	$totalcall+=$data[0];
	$totalminutes+=$data[1];
	$totalcallsanswered+=$data[2];
	$totalpdd+=$data[3];
}

?>




<center>
<div class="report-head">	
<? echo $strTrafficForPeriod;
	    if($days_compare > 1) $d="day"; else $d="days";
	
	   echo dbformat_to_dt(date("Y-m-d",strtotime($periodfrom." -$days_compare $d")));
	   echo " - ".substr(dbformat_to_dt($periodfrom),0,10);
?>
</div>
				
<table border="0" cellspacing="0" cellpadding="0" width="85%" class="report-tbl">
<tr><td>			
	<table cellspacing="1" cellpadding="1" width="100%">

	<tr id="head">
	<td align="left" >&nbsp;<?=$strDate?></td>
        <td align="center"><?=$strShortDuration?></td>
		<td align="center"><?=$strGraph?></td>
		<td align="center"><?=$strCalls?></td>
		<td align="center"><acronym title="<?=$strNoteACD?>">ACD <?=$strMin?></acronym></td>
		<td align="center"><acronym title="<?=$strNoteASR?>">ASR %</acronym></td>
		<td align="center"><acronym title="<?=$strNotePDD?>, <?=$strSec?>">PDD <?=$strSec?></acronym></td>
                			
		<!-- LOOP -->
	<? 		
		$i=0;

		foreach ($table_graph as $tkey => $data){	
		$i=($i+1)%2;		
		if ($data[2]) $tmc = $data[1]/$data[2];
		else $tmc=0;
		if ($data[0]) $asr = $data[2]/$data[0]*100;
		else $asr=0;
		if ($data[2]) $pdd = $data[3]/$data[2];
		else $pdd=0;
		$tmc_60 = sprintf("%02d",intval($tmc/60)).":".sprintf("%02d",intval($tmc%60));		
		$minutes_60 = sprintf("%02d",intval($data[1]/60)).":".sprintf("%02d",intval($data[1]%60));
		if ($mmax) $widthbar= intval(($data[1]/$mmax)*130);
		else $widthbar=0;
		

	?>
	</tr><tr>
	<td align="left" id="head3" nowrap><?=$tkey?></td>
	<td align="center" nowrap><?=$minutes_60?></td>
        <td align="left" nowrap width="<?=$widthbar+60?>">
        <table cellspacing="0" cellpadding="0" width="<?=$widthbar?>">
	<tr id="bar">
        <td></td>
        </tr></table></td>
        <td align="center" nowrap><?=$data[0]?></td>
        <td align="center" nowrap><?=$tmc_60?></td>
        <td align="center" nowrap><?=number_format($asr,1)?></td>
        <td align="center" nowrap><?=number_format($pdd,1)?></td>
     <?	 }	 
	 	if ($totalcallsanswered) $total_tmc_60 = sprintf("%02d",intval(($totalminutes/$totalcallsanswered)/60)).":".sprintf("%02d",intval(($totalminutes/$totalcallsanswered)%60));
		else $total_tmc_60 = 0;
		$total_minutes_60 = sprintf("%02d",intval($totalminutes/60)).":".sprintf("%02d",intval($totalminutes%60));
	 
	 ?>                   	
	</tr>

	<tr id="end">
		<th align="left" nowrap="nowrap">&nbsp;<?=$strTotal?></th>
		<th align="center" nowrap="nowrap"><?=$total_minutes_60?> </th>
		<th align="center" nowrap="nowrap"></td>
		<th align="center" nowrap="nowrap"><?=$totalcall?></th>
		<th align="center" nowrap="nowrap"><?=$total_tmc_60?></th>                        
		<th align="center" nowrap="nowrap"><? if ($totalcall) echo number_format(($totalcallsanswered/$totalcall*100),1);
												      else echo "0.0";?></th>                        
		<th align="center" nowrap="nowrap"><? if ($totalcallsanswered) echo number_format($totalpdd/$totalcallsanswered,1); 
												      else echo "0.0";?></th>                        
		
	</tr>
   </table>

</td></tr>
</table>

<?

include("modules/Reports/graph_stat.php");
?>
	<IMG SRC="modules/Reports/images/<?=$filenam_res?>">

<? }else{ ?>
	<center><div class="module-note"><?=$strNoCalls?></div></center>
<? } ?>

</center>

<?
}
?>


