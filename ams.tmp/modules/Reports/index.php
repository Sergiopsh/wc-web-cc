<?
header("Cache-Control: no-store"); 
header("Expires: " .  date("r"));
    
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/Reports/lang/".$lang.".lang.php");
include_once("modules/TariffPlans/TariffPlan.php");
include_once("modules/Operators/Operator.php");
include_once("modules/Currencies/Currency.php");

$table="cdr";

switch ($action) {
    case "ReportCDR": include("modules/Reports/cdrreport.php"); break;
    case "CompareCalls": include("modules/Reports/call-comp.php"); break;
    case "HourlyLoad": include("modules/Reports/hourlyload.php"); break;
    case "MonthlyReport": include("modules/Reports/monthlyreport.php"); break;
    case "Aggregate": include("modules/Reports/aggregate.php"); break;
    case "DailyReport": include("modules/Reports/dailyreport.php"); break;
    case "TechCDR": include("modules/Reports/operators.php"); break;
    default: include("modules/Reports/cdrreport.php"); break;

}
?>