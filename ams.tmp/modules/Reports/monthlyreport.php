<?php
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");

cleardir("modules/Reports/images");
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: <?=$strMonthlyReport?>
</div>
<?
$tplan= new TariffPlan(array(""));
$cur=new Currency();
$cur->get_list();
$list_cur=$cur->list_currencies;
if(!isset($val)) $val=$_SESSION['currency'];

?>
<br>
<script>
rep = new ObjectD();

rep.printReport=function() {

}
rep.exportPDF=function() {

}
</script>
	
<FORM name="module_form" id="module_form" METHOD="POST">
<?
if (!isset($months_compare)) $months_compare = 3;
if (!isset($fromstatsmonth_sday)) $fromstatsmonth_sday = date("Y-m");


$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");

$year_actual = date("Y");  	
$months = array($strJan,$strFeb,$strMar,$strApr,$strMay,$strJun,$strJul,$strAug,$strSep,$strOct,$strNov,$strDec);

$opt=array();
for ($i=$year_actual;$i >= $year_actual-1;$i--) {
    if ($year_actual==$i) $monthnumber = date("n")-1; 
    else $monthnumber=11;
    for ($j=$monthnumber; $j >=0; $j--){	
	$month_formated = sprintf("%02d",$j+1);
	$opt["$i-$month_formated"] = "$months[$j]-$i";
    }
}	
$p=$tbl->addElement("fromstatsmonth_sday","select",$strPeriod,1,$fromstatsmonth_sday);
$p->options=$opt;
if(!isset($months_compare)) $months_compare=2;
$p=$tbl->addElement("months_compare","select",$strLapsMonths,1,$months_compare); $p->colspan=5; $p->width2="15%";
$p->options=array(6=> "- 6 $strMonths",5=>"- 5 $strMonths",4=>"- 4 $strMonthTwo",3=>"- 3 $strMonthTwo",2=>"- 2 $strMonthTwo",1=>"- 1 $strMonthOne");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="loadModule(1,'','Reports','MonthlyReport',\$H({search: 1}));"; $p->align2="right";
$p=$tbl->addElement("dst","text",$strDialedDigits,2,$dst); $p->setOptions(21);
$p=$tbl->addElement("userfield","text",$strUserField,2,$userfield); $p->colspan=5; $p->setOptions(21);
$p=$tbl->addElement("name","text",$strDest,3,$name); $p->setOptions(21);
$p=$tbl->addElement("src","text",$strSource,3,$src); $p->colspan=5; $p->setOptions(21);
$p=$tbl->addElement("accountcode","text",$strAccountCode,4,$accountcode); $p->setOptions(21);
$p=$tbl->addElement("department","text",$strDepartment,4,$department); $p->colspan=5; 
$p->setOptions(40,0,"","","modules/Departments/filldeps.php");
$p=$tbl->addElement("statustype","select",$strDisposition,5,$statustype);
$p->options=array(1=>$strAll,2=>$strAnswered,3=>$strNoAnswer,4=>$strBusy,6=>$strFailed,8=>$strChanUnavail);

$p->action="rep.setFixPeriod(this.value)";
$p=$tbl->addElement("t_accountcode","select",$strTariffPlans,6,$t_accountcode);
$opt=array(); $opt[0]=$strAll;
if($list_tplans = $tplan->get_list()){
    foreach ($list_tplans as $key) $opt[$key[2]]=$key[2];
}
$p->options=$opt;
$p=$tbl->addElement("","simple","",5,$strDuration);
$p=$tbl->addElement("duration1","text",$strFrom,5,$duration1);
$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
$p=$tbl->addElement("duration2","text",$strTo,5,$duration2);
$p->setOptions(4); $p->width1="2%"; $p->width2="2%";
$p=$tbl->addElement("","simple","",5,$strSec);
$p=$tbl->addElement("val","select",$strBaseCurrency,7,$val);
$opt=array(); $opt[0]="&nbsp;---";
$opt=array(); $opt[0]="&nbsp;---";
if ($list_cur) {
    foreach($list_cur as $c) $opt[$c[8]]=$c[1];
}
$p->options=$opt;
$p=$tbl->addElement("","simple","",6,$strCost);
$p=$tbl->addElement("cost_min","text",$strFrom,6,$cost_min);
$p->setOptions(4);
$p=$tbl->addElement("cost_max","text",$strTo,6,$cost_max);
$p->setOptions(4);

$tbl->show();

?>


</FORM>
</center>


<br>
<center>
<? if ($search){ 

$months = Array ( 0 => $strJan, 1 => $strFeb, 2 => $strMar, 3 => $strApr, 4 => $strMay, 5 => $strJun, 6 => $strJul, 7 => $strAug, 8 => $strSep, 9 => $strOct, 10 => $strNov, 11 => $strDec );


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

  $db->set_sql_interval(array("ratesec","cost"),array(array($duration1,$duration2),array($cost_min,$cost_max)));
  $sql.=$db->sql_interval;
  
  if($t_accountcode) $sql.=" AND (t_accountcode = ".quote($t_accountcode).")";


  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;
  



$cur_time=strtotime($fromstatsmonth_sday."-01 00:00:00");
$cur_month=substr($fromstatsmonth_sday,5,2);
$cur_year=substr($fromstatsmonth_sday,0,4);
$end_time=strtotime("+ 1 month",$cur_time);

if($months_compare >1) $m="months"; else $m="month";
$start_time=strtotime("- $months_compare $m",$cur_time);

$date_clause=" AND UNIX_TIMESTAMP(calldate) >= $start_time AND UNIX_TIMESTAMP(calldate) < $end_time"; 
$sql.=$date_clause;


	$QUERY = "SELECT substring(calldate,1,7), SUM(ratesec),  
	 count(*) , count(IF(disposition = 'ANSWERED',1,NULL)), 
	sum(IF(disposition = 'ANSWERED', (duration-billsec),0))";

	if($list_cur) {
	    $cur_query="";
	    foreach($list_cur as $c) {	
		$cur_query.=", SUM(IF(currency = ".quote($c[8]).",cost,0))";	    

	    }
	}

	$QUERY.=$cur_query;
	$QUERY.=" FROM $table WHERE 1".$sql." GROUP BY substring(calldate,1,7) ORDER BY calldate DESC"; //extract(month from calldate) 

    $list_month = $db->get_list($QUERY);
    $num = $db->count;

if (is_array($list_month) && count($list_month)>0){

$mmax=0;
$totalcall==0;
$totalminutes=0;
$totalcallanswered=0;
$totalpdd=0;

foreach ($list_month as $data) if ($mmax < $data[1]) $mmax=$data[1];


$m=$cur_month-1;
$y=$cur_year;

for ($i=0; $i<$months_compare+1; $i++) {
    foreach ($list_month as $mon) {
        $mm = substr($mon[0],5,2);
	if($mm == ($m+1)) $data_graph[$i]=intval($mon[1]/60);	
    
    }

    if(empty($data_graph[$i])) $data_graph[$i]=0;
    else $validgraph=true;
    $ms=$months[$m]." ".$y;
    $mylegend[$i] = str_pad($ms,18).": ".$data_graph[$i]." $strMin";
    if($m) $m--; else { $m=11; $y--; }
}
//print_r($mylegend);
if($validgraph)
    include("modules/Reports/graph_pie.php");
/*
?>		

<table border=0 width="97%">
<tr><td align="right" width="100%">
<div class='module-link'>
<a href="javascript:rep.printReport();" title="<?=$strPrintReport?>">
<img src="images/print.gif" align="absmiddle" title="<?=$strPrintReport?>">
<?=$strPrint?>
<a href="javascript:rep.exportPDF();" title="<?=$strExportPDF?>">
<img src="images/pdf_icon.gif" align="absmiddle" title="<?=$strExportPDF?>">
<?=$strExport?>
</div>
</a>
</td</tr>
</table>
*/?>
<br>

<IMG SRC="modules/Reports/images/<?=$filename_res?>">
<br><br>
<div class="report-head">	
<?=$strTrafficForPeriod.$months[intval(date("m",$start_time))-1]."&nbsp;".date("Y",$start_time)." - ".$months[$cur_month-1]." $cur_year"?></td>
</div>		
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tbl">
 <tr><td>			
	<table border="0" cellspacing="1" cellpadding="1" width="100%">

	<tr id="head">
	<td align="left" >&nbsp;<?=$strMonth?></td>
        <td align="center"><?=$strMinutes?></td>

		<td align="center" nowrap><?=$strGraph?></td>
		<td align="center" nowrap><?=$strCalls?></td>
		<td align="center" nowrap><acronym title="<?=$strNoteACD?>">ACD, <?=$strMin?></acronym></td>
		<td align="center" nowrap><acronym title="<?=$strNoteASR?>">ASR %</acronym></td>
		<td align="center" nowrap><acronym title="<?=$strNotePDD?>, <?=$strSec?>">PDD, <?=$strSec?></acronym></td>
		<?if($val) {?>
		<td align="center" width="20%" nowrap><?=$strTotalSum?>, <?=$val?></td>
		
		<?}else{
		    foreach($list_cur as $c) {?>
		      <td align="center" nowrap><?=$strSum?>, <?=$c[8]?></td>
		    <?}
                  }		
	 		
		$i=0;
	foreach ($list_month as $data){	
		$totalcall+=$data[2];
		$totalminutes+=$data[1];
	    	$totalcallsanswered+=$data[3];
		$totalpdd+=$data[4];
	
	
		$i=($i+1)%2;		
		if ($data[3]) $tmc = $data[1]/$data[3];
		else $tmc=0;
		if ($data[2]) $asr = $data[3]/$data[2]*100;
		else $asr=0;
		if ($data[3]) $pdd = $data[4]/$data[3];
		else $pdd=0;
		
		$tmc = sprintf("%02d:%02d",intval($tmc/60),intval($tmc%60));
		$minutes = sprintf("%02d:%02d",intval($data[1]/60),intval($data[1]%60));
		if ($mmax) $widthbar= intval(($data[1]/$mmax)*130);
		else $widthbar=0;
		
		if($list_cur) {
    		    $i=5;
    		    if($val) {
			unset($total_month_sum);
    			foreach($list_cur as $c) {	
			    if($val == $c[8]) $base_course=$c[6];
			    if($c[6]) $total_month_sum+=$data[$i]/$c[6]; //sum in USD
			    $i++;	
    			}
    			$total_month_sum=$total_month_sum*$base_course;
			$total_sum+=$total_month_sum;
		    }else{
			unset($total_month_sum_val);
    			foreach($list_cur as $c) {	
			    if($c[6]) $total_month_sum_val[$c[8]]+=$data[$i]; //sum in USD
			    $total_sum_val[$c[8]]+=$total_month_sum_val[$c[8]];
			    $i++;	
    			}
			
		    }
		} else $total_month_sum=0;

		$ms=$months[intval(substr($data[0],5,2))-1]." ".substr($data[0],0,4);

	?>
		</tr><tr >
		<td align="left" id="head3" nowrap>
		<?=$ms?>
		</td>
		<td align="center" nowrap><?=$minutes?></td>
    		<td align="left" nowrap width="<?=$widthbar+60?>">
    		    <table cellspacing="0" cellpadding="0" width="<?=$widthbar?>">
			<tr id="bar">
    		    <td></td></tr>
		    </table>
		</td>
    		<td align="center" nowrap><?=$data[2]?></td>
    		<td align="center" nowrap><?=$tmc?></td>
		<td align="center" nowrap><? echo number_format($asr,1);?></td>
		<td align="center" nowrap><? echo number_format($pdd,1);?></td>
		<?if($val) {?>
		<td align="center" nowrap><?=number_format($total_month_sum,2)?></td>
		
		<?}else{
		    foreach($list_cur as $c) {?>
		      <td align="center" nowrap><?=number_format($total_month_sum_val[$c[8]],2)?></td>
		    <?}
                  }		
     	 } ?>                   	
	</tr>

	<tr id="end">
		<th align="left" nowrap><?=$strTotal?></td>
		<th align="center" nowrap><?=display_minute($totalminutes)?></th>
		<th>&nbsp;</th>
		<th align="center" nowrap><?=$totalcall?></th>
		<th align="center" nowrap>	

		<?
		if ($totalcallsanswered) printf("%02d:%02d",intval(($totalminutes/$totalcallsanswered)/60),intval(($totalminutes/$totalcallsanswered)%60));
		else echo "00:00";
		?>
		<th align="center" nowrap>
		<?if ($totalcall) echo number_format(($totalcallsanswered/$totalcall*100),1); 
		else echo "0.0";?>
		</th>   
		
		<th align="center" nowrap>
		<?if ($totalcallsanswered) echo number_format($totalpdd/$totalcallsanswered,1); 
		else echo "0.0"; ?>
		</th>           
		<?if($val) {?>
		<th align="center"><?=number_format($total_sum,2)?></th>
		
		<?}else{
		    foreach($list_cur as $c) {?>
		      <th align="center"><?=number_format($total_sum_val[$c[8]],2)?></th>
		    <?}
                  }?>		
		
		
		       
		
	</tr>

	  </table>


</td></tr></table>

<br><br>


<?

 } else echo "<div class='module-note'>$strNoCalls</div>";


}?>
</center>
