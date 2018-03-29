<?php
if(!$_SESSION['ams_entry']) die('Not a Valid ENtry');
include_once("lib/Class.datatbl.php");
cleardir("modules/Reports/images");

?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: <?=$strHourlyLoad?>
</div>
<br>

<?

$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt($currentdate);
$iformat=dt_format();

?>

<script>

</script>


<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->
	
<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="posted" id="posted" value=1>
<?
$tbl = new DataTbl("",0,0,5,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat","","",false); $p->width1="15%"; $p->colspan=5;

if(!isset($type_gr)) $type_gr=1;
$p=$tbl->addElement("type_gr","select",$strGraphType,1,$type_gr);  $p->width1="15%";
$p->options=array(1=>$strGraphCalls,2=>$strASRPerHour,3=>$strCallsAndASRPerHour);

$p=$tbl->addElement("","button","",1,$strSearch); 
$p->action="loadModule(1,'','Reports','HourlyLoad',\$H({search: 1}));"; $p->align2="right";
$p=$tbl->addElement("dst","text",$strDialedDigits,2,$dst); $p->setOptions(21); $p->colspan=5;
$p=$tbl->addElement("userfield","text",$strUserField,2,$userfield);  $p->setOptions(21);
$p=$tbl->addElement("name","text",$strDest,3,$name); $p->setOptions(21); $p->colspan=5;
$p=$tbl->addElement("accountcode","text",$strAccountCode,3,$accountcode); $p->setOptions(21);

$p=$tbl->addElement("src","text",$strSource,4,$src); $p->setOptions(21); $p->colspan=5;
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
</form>

<br><br>

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

  $db->set_sql_interval("ratesec",array($duration1,$duration2));
  $sql.=$db->sql_interval;

  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;
 
  $sql.=" AND calldate LIKE '".substr(dt_to_dbformat($periodfrom."00:00","00"),0,10)."%' ";
	/************************/
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

		if (isset($recordset[3])) {

		    $table_graph[$mydate][2]++;
		    $table_graph[$mydate][3]+=$recordset[2]-$recordset[1];
		    $table_graph_hours[$mydate_hours][2]++;
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

///##################################################################
	$QUERY1 = "SELECT calldate,ratesec,duration, IF(disposition = 'ANSWERED', (duration - billsec), NULL)";
	$QUERY1.=" FROM $table WHERE (disposition = 'NO ANSWER') ".$sql;  
//echo $QUERY1;
    $list1 = $db->get_list($QUERY1);
    $num = $db->count;

if (is_array($list1) && count($list1)>0){

$table_graph1=array();
$table_graph_hours1=array();
$numm1=0;
foreach ($list1 as $recordset1){
		$numm++;
		$mydate1= substr($recordset1[0],0,10);
		$mydate_hours1= substr($recordset1[0],0,13);

		if (is_array($table_graph_hours1[$mydate_hours1])){
			$table_graph_hours1[$mydate_hours1][0]++;
			$table_graph_hours1[$mydate_hours1][1]=$table_graph_hours1[$mydate_hours1][1]+$recordset1[1];
		}else{
			$table_graph_hours1[$mydate_hours1][0]=1;
			$table_graph_hours1[$mydate_hours1][1]=$recordset1[1];
		}
		
		
		if (is_array($table_graph1[$mydate1])){
			$table_graph1[$mydate1][0]++;
			$table_graph1[$mydate1][1]=$table_graph1[$mydate1][1]+$recordset1[1];
		}else{
			$table_graph1[$mydate1][0]=1;
			$table_graph1[$mydate1][1]=$recordset1[1];
		}

		if (isset($recordset1[3])) {

		    $table_graph1[$mydate1][2]++;
		    $table_graph1[$mydate1][3]+=$recordset1[2]-$recordset1[1];
		    $table_graph_hours1[$mydate_hours1][2]++;
		}    		
		
}

}
//$mmax=0;
//$totalcall==0;
//$totalminutes=0;
//$totalcallsanswered=0;
//$totalpdd=0;
//foreach ($table_graph1 as $tkey => $data){	
//	if ($mmax < $data[1]) $mmax=$data[1];
//	$totalcall+=$data[0];
//	$totalminutes+=$data[1];
//	$totalcallsanswered+=$data[2];
//	$totalpdd+=$data[3];
//	
//}

///##################################################################

?>


<center>
<div class="report-head">	
<?=$strTrafficForPeriod." ".$periodfrom?>
</div>	  

<table border="0" cellspacing="0" cellpadding="0" width="85%" class="report-tbl">
  <tr><td>			
	<table border="0" cellspacing="1" cellpadding="1" width="100%" >

	<tr id="head">
	<td align="left" >&nbsp;<?=$strDate?></td>
        <td align="center"><?=$strShortDuration?></td>
	<td align="center"><?=$strGraph?></td>
	<td align="center"><?=$strCalls?></td>
	<td align="center"><acronym title="<?=$strNoteACD?>, <?=$strMin?>">ACD</acronym></td>
	<td align="center"><acronym title="<?=$strNoteASR?>">ASR %</acronym></td>
	<td align="center"><acronym title="<?=$strNotePDD?>, <?=$strSec?>">PDD</acronym></td>
                			
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
		<td id="head3" align="left" nowrap><?=$tkey?></td>
		<td align="center" nowrap><?=$minutes_60?></td>
    		<td align="left" nowrap width="<?=$widthbar+60?>">
    		<table cellspacing="0" cellpadding="0" width="<?=$widthbar?>" border=0>
		<tr id="bar">
		  <td>
		  </td></tr>
		</table>
		</td>
    		<td align="center" nowrap><?=$data[0]?></td>
    		<td align="center" nowrap><?=$tmc_60?></td>
		<td align="center" nowrap><?=number_format($asr,1)?></td>
		<td align="center" nowrap><?=number_format($pdd,1)?></td>
     <?	 }	 
	 	if ($totalcallsanswered) $total_tmc_60 = sprintf("%02d",intval(($totalminutes/$totalcallsanswered)/60)).":".sprintf("%02d",intval(($totalminutes/$totalcallsanswered)%60));
		else $total_tmc_60=0;	
		$total_minutes_60 = sprintf("%02d",intval($totalminutes/60)).":".sprintf("%02d",intval($totalminutes%60));
	 
	 ?>                   
	</tr>

	<tr id="end">
		<th align="left" nowrap="nowrap"><?=$strTotal?></th>
		<th align="center" nowrap="nowrap"><?=$total_minutes_60?></th>
		<th align="center" nowrap="nowrap"></td>
		<th align="center" nowrap="nowrap"><?=$totalcall?></th>
		<th align="center" nowrap="nowrap"><?=$total_tmc_60?></th>                        
		<th align="center" nowrap="nowrap">
		<? if ($totalcall) echo number_format(($totalcallsanswered/$totalcall*100),1);
		   else echo "0.0";?></th>                       
		<th align="center" nowrap="nowrap">
		<? if ($totalcallsanswered) echo number_format($totalpdd/$totalcallsanswered,1);
		   else echo "0.0";?></th>
												      </b></td>       
	</tr>

	  </table>

</td></tr></table>
<br>
<?

include("modules/Reports/graph_statbar.php");

?>
<img src="modules/Reports/images/<?=$filename_res?>">

<!-- ** ** ** ** ** HOURLY LOAD ** ** ** ** ** -->
&nbsp;
<br>
<center>
<div id="frame-module-header2" style="margin-bottom: 5px;">
<?=$strChooseInterval?>
</div>
<script>
showSuperFrame=function() {
 $('superframe').style.display='inline';
 if($('hourlyload').typegraph.value=='fluctuation')
    $('superframe').style.height='500px';
 else $('superframe').style.height='800px';
}
</script>

<FORM name="hourlyload" id="hourlyload" METHOD=POST onsubmit="showSuperFrame();" 
 ACTION="modules/Reports/graph_hourdetail.php?lang=<?=$lang?>&min_call=<?=$min_call?>&periodfrom=<?=$periodfrom?>&fromstatsmonth_sday=<?=$fromstatsmonth_sday?>&dst=<?=$dst?>&src=<?=$src?>&clid=<?=$clid?>&userfield=<?=$userfield?>&accountcode=<?=$accountcode?>&name=<?=$name?>&duration1=<?=$duration1?>&duration2=<?=$duration2?>&department=<?=$department?>" target="superframe">


	<!-- ** ** ** ** ** HOURLY LOAD ** ** ** ** ** -->
		<table width="75%" class="input-data-tbl" cellspacing="1" cellpadding="2" align="center">
			<tr height=4><td></td></tr>
			<tr>
			<td align="right">&nbsp;&nbsp;<?=$strHourInterval?> :
			</td>				
			<td><select name="hourinterval" id="hourinterval">
			<? 
					for ($i=0;$i<=23;$i++){							
						echo '<option value="'.sprintf("%02d",$i)."\"> $strInterval [".sprintf("%02d",$i).'h to '.sprintf("%02d",$i+1).'h] </option>';
					}
			?>					
			</select>
			</td><td align="right">&nbsp;&nbsp;<?=$strGraphType?> :
			</td>				
			<td><select name="typegraph" id="typrgraph">
				<option value="fluctuation"> &nbsp;<?=$strGraphFluctuation?>&nbsp;&nbsp;</option> 
				<option value="watch-call" selected> &nbsp;<?=$strGraphCalls?></option>
			    </select>
			</td>				
			<td>
			<input type="submit"  name="SearchHI" value="<?=$strShow?>" class="sbutton">				
  			</td>
			<tr height=4><td></td></tr>
    		</tr>
		</table>
</FORM>
<br>
<iframe name="superframe" id="superframe" style="display: none;" src="" BGCOLOR=white width=770 height=800 marginWidth=0 marginHeight=0  frameBorder=0  scrolling="yes">
</iframe>

</center>

<? }else{ ?>
	<center><div class="module-warning"><?=$strNoCalls?></div></center>
<? } ?>

<?
 }

?>  
