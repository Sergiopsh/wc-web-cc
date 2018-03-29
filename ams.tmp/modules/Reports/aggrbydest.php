<?



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

  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;
  
  $db->set_sql_interval(array("ratesec","cost"),array(array($duration1,$duration2),array($cost_min,$cost_max)));
  $sql.=$db->sql_interval;
  
  if($t_accountcode) $sql.=" AND (t_accountcode = ".quote($t_accountcode).")";

 
  $db->set_sql_time("calldate",dt_to_dbformat($periodfrom,"00"),dt_to_dbformat($periodto,"59"));
  $sql.=$db->sql_time;

	/************************/
	$QUERY = "SELECT name, SUM(ratesec),  
	 count(*) , count(IF(disposition = 'ANSWERED',1,NULL)), 
	sum(IF(disposition = 'ANSWERED', (duration-billsec),0))";

	if($list_cur) {
	    $cur_query="";
	    foreach($list_cur as $c) {	
		$cur_query.=", SUM(IF(currency = ".quote($c[8]).",cost,0))";	    
	    }
	}

	$QUERY.=$cur_query;
	$QUERY.=" FROM $table WHERE 1".$sql." GROUP BY name ORDER BY name"; 

	$list_bydest=$db->get_list($QUERY);
	$num = $db->count;

?>


<br>

<?


if ($num){

$mmax=0;
$totalcall==0;
$totalminutes=0;
$totalcallanswered=0;
$totalpdd=0;

foreach ($list_bydest as $data) if ($mmax < $data[1]) $mmax=$data[1];
/*
?>
<table border=0 width="98%">
<tr><td align="right" width="100%">
<div class='module-link'>
<a href="javascript:rep.printReport();" title="<?=$strPrintReport?>">
<img src="images/print.gif" align="absmiddle" title="<?=$strPrintReport?>">
<?=$strPrint?>
<a href="javascript:rep.exportPDF();" title="<?=$strExportPDF?>">
<img src="images/pdf_export.gif" align="absmiddle" title="<?=$strExportPDF?>">
<?=$strExport?>
</div>
</a>
</td</tr>
</table>
*/?>

<center>
<div class="report-head" nowrap>
<?=$strTrafficForPeriod." $periodfrom - $periodto";?>
</div>

<table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tbl">
 <tr><td>			
	<table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	<td align="left">&nbsp;<?=$strDest?>
	</td>
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
	$bg=0;
	foreach ($list_bydest as $data){	
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
		if ($mmax) $widthbar= intval(($data[1]/$mmax)*100);
		else $widthbar=0;
		
		if($list_cur) {
    		    $i=5;
    		    if($val) {
			unset($total_bydest_sum);
    			foreach($list_cur as $c) {	
			    if($val == $c[8]) $base_course=$c[6];
			    if($c[6]) $total_bydest_sum+=$data[$i]/$c[6]; //sum in USD
			    $i++;	
    			}
    			$total_bydest_sum=$total_bydest_sum*$base_course;
			$total_sum+=$total_bydest_sum;
		    }else{
			unset($total_bydest_sum_val);
    			foreach($list_cur as $c) {	
			    if($c[6]) $total_bydest_sum_val[$c[8]]+=$data[$i]; //sum in USD
			    $total_sum_val[$c[8]]+=$total_bydest_sum_val[$c[8]];
			    $i++;	
    			}
			
		    }
		} else $total_bydest_sum=0;

		
		$class= "trcls1";
		$bg++ % 2 ? 0 : $class="trcls2";
	?>	
		</tr>
		<tr >
		<td align="left" id="head3" nowrap>
		<?=$data[0]?></td>
		<td align="center" nowrap><?=$minutes?></td>
    		<td align="left" nowrap width="<?=$widthbar+20?>">
    		    <table cellspacing="0" cellpadding="0" width="<?=$widthbar?>">
			<tr id="bar">
    		    <td >
		    </td></tr>
		    </table>
		</td>
    		<td align="center" nowrap><?=$data[2]?></td>
    		<td align="center" nowrap><?=$tmc?></td>
		<td align="center" nowrap><? echo number_format($asr,1);?></td>
		<td align="center" nowrap><? echo number_format($pdd,1);?></td>
		<?if($val) {?>
		<td align="center" nowrap><?=number_format($total_bydest_sum,2)?></td>
		
		<?}else{
		    foreach($list_cur as $c) {?>
		      <td align="center" nowrap><?=number_format($total_bydest_sum_val[$c[8]],2)?></td>
		    <?}
                  }		
		
     	 } ?>                   	
	</tr>

	<tr id="end">
		<th align="left" nowrap>&nbsp;<?=$strTotal?></td>
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
	<!-- FIN TOTAL -->

	  </table>
	  <!-- Fin Tableau Global //-->

</td></tr></table>

<br><br>


</FORM>

<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>
<? 

 } else echo "<br><center><div class='module-note'>$strNoCalls</div></center>";

?>
</center>
