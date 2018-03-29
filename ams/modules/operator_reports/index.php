<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/operator_reports/lang/".$lang.".lang.php");
include_once("modules/Recording/Recording.php");
include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "CommonReport": include("modules/operator_reports/commonreport.php"); break;
    case "CommonReportAll": include("modules/operator_reports/commonreport_all.php"); break;

    case "DetailReport": include("modules/operator_reports/detailreport.php"); break;
    case "OutReport": include("modules/operator_reports/outreport.php"); break;
    //case "NReport": include("modules/operator_reports/nigthreport.php"); break;
    //case "DispReport": include("modules/operator_reports/dispreport.php"); break;
    case "QueueReport": include("modules/operator_reports/queuereport.php"); break;
    //case "HistoryReport": include("modules/operator_reports/history.php"); break;
    //case "BadStatReport": include("modules/operator_reports/BadStatReport.php"); break;    
    default: include("modules/operator_reports/commonreport.php"); break;

}
?>