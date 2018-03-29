<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/operator_queue/lang/".$lang.".lang.php");
//include_once("modules/Recording/Recording.php");
//include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "Status": include("modules/operator_queue/status.php"); break;
    case "InQUEUE": include("modules/operator_queue/inqueue.php"); break;
    case "OutQUEUE": include("modules/operator_queue/outqueue.php"); break;
    case "InPause": include("modules/operator_queue/inpause.php"); break;
    case "OutPause": include("modules/operator_queue/outpause.php"); break;
    case "QueueReport": include("modules/operator_queue/queuereport.php"); break;
    default: include("modules/operator_queue/status.php"); break;

}
?>