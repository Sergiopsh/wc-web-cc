<?php
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");
cleardir("modules/ReportsSuper/images");
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: Плохие звонки
</div>
<?
$iformat=dt_format();
if(!isset($dateperiod)) $dateperiod=0;
//if(!isset($val)) $val=$_SESSION['currency'];
?>
<br>
<script>
rep = new ObjectD();

rep.setPeriod=function(z) {
    //if(z == '0') return;
    var dt = setPeriod(z);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
}

rep.printReport=function() {

}

rep.exportPDF=function() {

}
</script>

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="pos" id="pos" value=<?=$dateperiod?>/>

<?
$currentdate=date("Y-m-d");
$current_time=date(" H:i");
if (!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if (!isset($periodto)) $periodto=dbformat_to_dt("$currentdate"."$current_time");

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");                     						 //запросная форма
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);                                      		 //новый кусок
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="loadModule(1,'','ReportsSuper','NReport',\$H({search: 1}));"; $p->align2="right";
$tbl->show();
if (!$search) exit;

?>

</form>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="report-tabl">
    <tr>
    <td>
    <table border="0" cellspacing="1" cellpadding="1" width="100%">
	<tr id="head">
	    <td align="center" >Дата</td>
	    <td align="center" >Источник</td>
	    <td align="center" >Время простоя</td>
	    <td align="center" >Оператор</td>
	</tr>
    </td>


<?
if ($search){
if(!$db) $db=DbConnect();
$start_time=strtotime($periodfrom);
$end_time=strtotime($periodto);
$date_clause=" AND b1.time_id >= $start_time AND b2.time_id < $end_time";
//для каждого оператора будем делать выборки

   $QUERY = "select 
		from_unixtime(b1.time_id) as data,
		b1.data2 as src,
		b2.data3 as time,
		b2.agent as dst 
	    from queuemetrics.bad_log b1 
	    inner join queuemetrics.bad_log b2 
	    on b1.call_id=b2.call_id 
	    where b1.verb='ENTERQUEUE' and b2.verb='ABANDON'
            ";
    $QUERY.=$date_clause." order by 1";
    $arr = $db->get_list($QUERY);
    foreach ($arr as $data){
	print"<tr>";
	print "<td align=\"center\" >$data[0]</td>";
	print "<td align=\"center\" >$data[1]</td>";	
	print "<td align=\"center\" >$data[2]</td>";	
	print "<td align=\"center\" >$data[3]</td>";	
    };
print"        </table>";
print"        </td>";
print"        </tr>";
print" </table>";

exit;


?>

<?
 } //else echo "<div class='module-note' align='center'>$strNoCalls</div>";
?>
