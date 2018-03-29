<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/supervisor/lang/".$lang.".lang.php");
//include_once("modules/Recording/Recording.php");
//include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "OperReport": include("modules/supervisor/operreport.php"); break;
    case "QueueReport": include("modules/supervisor/queuereport.php"); break;
    case "QueueReportSupervisor": include("modules/supervisor/QueueReportSupervisor.php"); break;
    case "transfer": include("modules/supervisor/transfer.php"); break;
    case "conference": include("modules/supervisor/conference.php"); break;
    default: include("modules/supervisor/queuereport.php"); break;

}
?>