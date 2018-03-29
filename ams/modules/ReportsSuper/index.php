<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/ReportsSuper/lang/".$lang.".lang.php");
include_once("modules/Recording/Recording.php");
include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "CommonReport": include("modules/ReportsSuper/commonreport.php"); break;
    case "DetailReport": include("modules/ReportsSuper/detailreport.php"); break;
    case "OutReport": include("modules/ReportsSuper/outreport.php"); break;
    case "NReport": include("modules/ReportsSuper/nigthreport.php"); break;
    case "DispReport": include("modules/ReportsSuper/dispreport.php"); break;
    case "PeackReport": include("modules/ReportsSuper/peackload.php"); break;
    case "HistoryReport": include("modules/ReportsSuper/history.php"); break;
    case "BadStatReport": include("modules/ReportsSuper/BadStatReport.php"); break;    
    case "NightReport": include("modules/ReportsSuper/nightreport.php"); break;    
    case "asa": include("modules/ReportsSuper/asa.php"); break;    
    case "NACall": include("modules/ReportsSuper/nacall.php"); break;    

    default: include("modules/ReportsSuper/commonreport.php"); break;

}
?>