<?
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
cleardir("modules/Reports/images");
?>
<div id="frame-module-header" nowrap>
<?=$strReports?>: <font color=#eeeecc><?=$strAggregateTraffic?></font>
</div>
<br>
<?
$cur= new Currency();
$tplan = new TariffPlan();
$cur->get_list();
$list_cur=$cur->list_currencies;
if(!isset($val)) $val=$_SESSION['currency'];
$currentdate=date("Y-m-d");
if(!isset($periodfrom)) $periodfrom=dbformat_to_dt("$currentdate 00:00");
if(!isset($periodto)) $periodto=dbformat_to_dt("$currentdate 23:59");


$iformat=dt_format();
?>
<script>
rep = new ObjectD();

rep.setFixPeriod=function(p) {

    if(p == '0') return;
    var dt = setFixPeriod(p);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
    
}


rep.printReport=function() {

}
rep.exportPDF=function() {

}

</script>

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>">	
<INPUT TYPE="hidden" NAME="order" id="order" value="<?=$order?>">	
<INPUT TYPE="hidden" NAME="search" id="search" value="1">	
<INPUT TYPE="hidden" NAME="sort_date" id="sort_date" value="<?=$sort_date?>">	
<INPUT TYPE="hidden" NAME="search_day_graph" id="search_day_graph" value="0">	
<?
if(!isset($statustype)) $statustype==2;

$tbl = new DataTbl("100%",0,0,4,"margin-bottom: 10px;");
$p=$tbl->addElement("periodfrom","time",$strPeriodFrom,1,$periodfrom);
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("periodto","time",$strPeriodTo,1,$periodto); $p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("","button","",1,$strShowReport); $p->width2="15%";
$p->action="loadModule(1,'','Reports','Aggregate',\$H({search: 1}));"; $p->align2="right";
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
$p=$tbl->addElement("cost_min","text",$strFrom,7,$cost_min);
$p->setOptions(4);
$p=$tbl->addElement("cost_max","text",$strTo,7,$cost_max);
$p->setOptions(4);
$p=$tbl->addElement("aggregateby","select",$strAggregateBy,8,$aggregateby);
$p->options=array(1=>$strByDestinations,2=>$strByTariffPlans,3=>$strByDepartments);

$tbl->show();


?>

</FORM>

<?

if($search) {
    switch($aggregateby) {
	case 2:  include("modules/Reports/aggrbytplans.php"); break;
	case 3:  include("modules/Reports/aggrbydepartments.php"); break;
	default: include("modules/Reports/aggrbydest.php"); break;
    }
}

