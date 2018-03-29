<?
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
?>
<div nowrap id="frame-module-header">
<?=$strReports?>: CDR
</div>
<br>
<?

$iformat=dt_format();

$tplan= new TariffPlan(array(""));
$cur = new Currency();
$cur->get_list();
$list_cur=$cur->list_currencies;


if(!isset($val)) $val=$_SESSION['currency'];
if (empty($current_page)) $current_page=0; 
if(!isset($sort_date)) $sort_date=0;
$currentdate=date("Y-m-d");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");


?>

<script>
var sort_date=<?=$sort_date?>;

rep = new ObjectD();

rep.setFixPeriod=function(p) {
    //$('module-warning').style.display="none";
    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
    
}

rep.printReport=function() {

}
rep.sortByDate=function() {
    if(sort_date) sort_date=0; else sort_date=1;
    loadModule(1,'Reports','Reports','ReportCDR',$H({sort_date: sort_date}));
}
rep.exportToCSV=function() {
  $('export-form').action='include/export_csv.php?pref_export=Export_cdr_'; 
  $('export-form').submit(); 
}
rep.exportToPDF=function() {
  $('export-form').action='include/export_pdf.php'; 
  $('export-form').submit(); 
}
</script>

<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->
	
<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>"/>	
<INPUT TYPE="hidden" NAME="limit_display" id="limit_display" value="<?=$limit_display?>"/>	
<INPUT TYPE="hidden" NAME="order" id="order" value="<?=$order?>"/>	
<INPUT TYPE="hidden" NAME="search" id="search" value="1"/>	
<INPUT TYPE="hidden" NAME="sort_date" id="sort_date" value="<?=$sort_date?>"/>	
<?	

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="$('module_form').current_page.value=0;loadModule(1,'','Reports','ReportCDR',\$H({search: 1}));";
$p->align2="right";
$p=$tbl->addElement("dst","text",$strDialedDigits,2,$dst); $p->setOptions(21);
$p=$tbl->addElement("userfield","text",$strUserField,2,$userfield); $p->colspan=5; $p->setOptions(21);
$p=$tbl->addElement("name","text",$strDest,3,$name); $p->setOptions(21);
$p=$tbl->addElement("src","text",$strSource,3,$src); $p->colspan=5; $p->setOptions(21);
$p=$tbl->addElement("accountcode","text",$strAccountCode,4,$accountcode); $p->setOptions(21);
$p=$tbl->addElement("department","text",$strDepartment,4,$department); $p->colspan=5; 
$p->setOptions(40,0,"","","modules/Departments/filldeps.php");
$p=$tbl->addElement("statustype","select",$strDisposition,5,$statustype);
$p->options=array(1=>$strAll,2=>$strAnswered,3=>$strNoAnswer,4=>$strBusy,6=>$strFailed,8=>$strChanUnavail);
$p=$tbl->addElement("fixperiod","select",$strFixPeriod,5,$fixperiod); $p->colspan=5; 
$p->options=array("0" => "&nbsp;---&nbsp;","1" => $strHour3, "2" => $strHour12, "3" => $strToday,
  "4" => $strYesterday,"5" => $strDay3, "6"=> $strThisWeek, "7"=> $strThisMonth, "8" => $strPrevMonth);
$p->action="rep.setFixPeriod(this.value)";
$p=$tbl->addElement("t_accountcode","select",$strTariffPlans,6,$t_accountcode);
$opt=array(); $opt[0]=$strAll;
if($list_tplans = $tplan->get_list()){
    foreach ($list_tplans as $key) $opt[$key[2]]=$key[1];
}

$p->options=$opt;
$p=$tbl->addElement("","simple","",6,$strDuration);
$p=$tbl->addElement("duration1","text",$strFrom,6,$duration1);
$p->setOptions(4); $p->width1="1%"; $p->width2="2%";
$p=$tbl->addElement("duration2","text",$strTo,6,$duration2);
$p->setOptions(4); $p->width1="1%"; $p->width2="2%";
$p=$tbl->addElement("","simple","",6,$strSec);
$p=$tbl->addElement("val","select",$strBaseCurrency,7,$val);
$opt=array(); $opt[0]="&nbsp;---";
$opt=array(); $opt[0]="&nbsp;---";
if ($list_cur) {
    foreach($list_cur as $c) $opt[$c[8]]=$c[1];
}
$p->options=$opt;
$p=$tbl->addElement("","simple","",7,$strCost);
$p=$tbl->addElement("cost_min","text",$strFrom,7,$cost_min); $p->setOptions(4);
$p=$tbl->addElement("cost_max","text",$strTo,7,$cost_max); $p->setOptions(4);

$tbl->show();

echo "</form>";

if ($search){

  if(!$db) $db=DbConnect();
  $sql="";
  switch($statustype) {
		case 2: $status_sql = " AND disposition = 'ANSWERED'"; break;
		case 3: $status_sql = " AND disposition = 'NO ANSWER'"; break;
		case 4: $status_sql = " AND disposition = 'BUSY'"; break;
		case 6: $status_sql = " AND disposition = 'FAILED'"; break;
		case 8: $status_sql = " AND disposition = 'CHANANVAIL'"; break;
		default: $status_sql = "  "; break;
  }
  $sql.=$status_sql;

  $db->set_sql_interval(array("ratesec","cost"),array(array($duration1,$duration2),array($cost_min,$cost_max)));
  $sql.=$db->sql_interval;

  if($t_accountcode) $sql.=" AND (t_accountcode = '$t_accountcode')";

  $db->set_sql_like(array("name","accountcode","dst","src","userfield","department"),array($name,$accountcode,$dst,$src,$userfield,$department)); 
  $sql.=$db->sql_like;
 
  $db->set_sql_time("calldate",dt_to_dbformat($periodfrom,"00"),dt_to_dbformat($periodto,"59"));
  $sql.=$db->sql_time;


  if($sort_date) $order_clause=" ORDER BY calldate ASC";
  else $order_clause.=" ORDER BY calldate DESC";
  $query="SELECT count(*),SUM(billsec),SUM(ratesec),count(IF(disposition = 'ANSWERED',1,NULL)), 
          sum(IF(disposition = 'ANSWERED', (duration-billsec),0)) ";

	if($list_cur) {
	    $cur_query="";
	    foreach($list_cur as $c) {	
		$cur_query.=", SUM(IF(currency = '".$c[8]."',cost,0))";	    

	    }
	}
  
  $query.=$cur_query." FROM $table WHERE 1".$sql.$order_clause;

  $db->query($query);
  $db->next_record();
  $total_list=$db->Record;
  $total_calls=$total_list[0];
  $total_dur=$total_list[2];
  $total_calls_ans=$total_list[3];
  if($total_calls) $total_asr=($total_calls_ans/$total_calls)*100;
  else $total_asr=0;
  if($total_calls_ans) $total_pdd=$total_list[4]/$total_calls_ans;
  else $total_pdd=0;
  if($total_calls_ans) $total_acd=$total_dur/$total_calls_ans;
  else $total_acd;

  if($list_cur) {
     $i=5;
     if($val) {
        foreach($list_cur as $c) {	
	    if($val == $c[8]) $base_course=$c[6];
	    if($c[6]) $total_sum+=$total_list[$i]/$c[6]; 
	    $i++;	
        }
        $total_sum=$total_sum*$base_course;
    }else{
        foreach($list_cur as $c) {	
	    if($c[6]) $total_sum_val[$c[8]]+=$total_list[$i]; 
	    $i++;	
        }
    }
  } else $total_sum=0;


  $query="SELECT calldate,name,src,dst,ratesec,disposition,userfield,department,currency,cost FROM $table WHERE 1 ".$sql.$order_clause;  

  $_SESSION['sql_export']=$query;

 $list = $db->get_limit_list($query,($current_page*$limit_display),$limit_display);
 $total_calls=$db->count;
 $num_disp = count($list);

$n_pages = num_pages($total_calls,$limit_display,$current_page);


?>

<br>
<!-- ** ** ** ** ** Part to display the CDR ** ** ** ** ** -->
<?
$tbl = new ListTbl();
if(!$total_calls) $tbl->emptyTbl($strNoCalls);
?>
 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
     <td align=center width="40%" class="module-note">
	<?=$strNumCalls?> : <?=$total_calls?>
      </td>
      <td></td>
      <td align=right nowrap>
      <?$tbl->exportLink($strExportCalls,"rep.exportToCSV()");?>
    </td></tr>
</table>      
<?
$img = $sort_date ? "up.gif" : "down.gif";
$dh = "<a href=\"javascript:rep.sortByDate();\" title=\"$strSortByDate\">$strDate&nbsp;<img id=\"sort-date-img\" align=\"absmiddle\" src=\"images/$img\" style=\"margin: 0; border: 0;\"></a>";  
$tbl->tblHead(array("","11","10","15","","","7","5","","4"),array($dh,$strDest,$strDepartment,$strSource,$strDialedDigits,$strDisposition,$strMinH,$strUserField,$strCostShort),"100%");


	for($j=0; $j < $num_disp; $j++) {

	    $tbl->tblTr($j,"font-size: 11px;font-family: arial,sans;");
	?>
	<td align="center" nowrap>
	<?=dbformat_to_dt(substr($list[$j][0],0,10)).substr($list[$j][0],11)?>&nbsp;</td>
	<td align="center" nowrap>&nbsp;<?=$list[$j][1]?>&nbsp;</td>
	<td align="center"><?if($list[$j][7]) echo $list[$j][7];?></td>
	<td align="center" nowrap><?=hc($list[$j][2])?></td>
	<td align="center" nowrap><?=hc($list[$j][3])?></td>
	<td align="center" nowrap><?=hc($list[$j][5])?></td>
	<td align="center" nowrap><?=display_minute($list[$j][4])?></td>
	<td align="center" nowrap><?=hc($list[$j][6])?></td>
	<td align="left" nowrap>&nbsp;<?
	    if($list[$j][9]>0) { printf("%.2f",$list[$j][9]); echo " ".$list[$j][8];}
	    else echo "0.00";
	?></td>
	</tr>

        <?	

	}
$tbl->tblEnd($current_page,$n_pages);

?>
<br><br>

<center>
<div class="report-head">

 <?=$strTrafficForPeriod." $periodfrom - $periodto";?>

</div>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tbl">
 <tr><td>			
	<table cellspacing="1" cellpadding="1" width="100%">
	<tr id="head2" align="center">
        <td align="center" width="20%"><?=$strMinutes?></td>
		<td><?=$strCalls?></td>
		<td><acronym title="<?=$strNoteACD?>">ACD, <?=$strMin?></acronym></td>
		<td><acronym title="<?=$strNoteASR?>">ASR %</acronym></td>
		<td><acronym title="<?=$strNotePDD?>, <?=$strSec?>">PDD, <?=$strSec?></acronym></td>
		<?if($val) {?>
		<td width="20%"><?=$strTotalSum?>, <?=$val?></td>
		<?}else {
		    foreach ($list_cur as $c) {?>
		    	<td width="12%"><?=$strSum?>, <?=$c[8]?></td>
		  <?}
		}?>
		</tr><tr >
		<td nowrap align="center">
		<?=display_minute($total_dur)?></td>
    		<td nowrap align="center" ><?=$total_calls?></td>
    		<td nowrap align="center"><?=display_minute($total_acd)?></td>
		<td nowrap align="center"><?=number_format($total_asr,1)?></td>
		<td nowrap align="center"><?=number_format($total_pdd,1)?></td>

		<?if($val) {?>
		<td nowrap align="center"><?=number_format($total_sum,2)?></td>	 	 	
		<?}else {
		    foreach ($list_cur as $c) {?>
		    	<td><?=number_format($total_sum_val[$c[8]],2)?></td>
		  <?}
		}?>		

    	</tr>

	</table>
</td></tr>
</table>
<br><br>

</center>
<form name="export-form" id="export-form" method="post" target="export-frame">
</form>
<iframe name="export-frame" id="export-frame" frameborder=0 width="0" height="0">
</iframe>

<?}?>
