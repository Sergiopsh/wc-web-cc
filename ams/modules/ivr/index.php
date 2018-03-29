<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/ivr/lang/".$lang.".lang.php");
//include_once("modules/Recording/Recording.php");
//include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "AbonList": include("modules/ivr/abonlist.php"); break;
    case "AbonListWithSumm": include("modules/ivr/abonlistwithsumm.php"); break;
    case "deletelist": include("modules/ivr/deletelist.php"); break;
    case "deletelistwithsumm": include("modules/ivr/deletelistwithsumm.php"); break;
    case "deletetask": include("modules/ivr/deletetask.php"); break;
    case "deletetaskWithSumm": include("modules/ivr/deletetask_with_summ.php"); break;
    case "deletefile": include("modules/ivr/deletefile.php"); break;
    case "deletephone": include("modules/ivr/deletephone.php"); break;    
    case "viewlist": include("modules/ivr/viewlist.php"); break;
    case "viewlistwithsumm": include("modules/ivr/viewlistwithsumm.php"); break;
    case "FilesList": include("modules/ivr/fileslist.php"); break;
    case "TasksList": include("modules/ivr/taskslist.php"); break;    
    case "TasksListWithSumm": include("modules/ivr/taskslistwithsumm.php"); break;
    case "TaskWork": include("modules/ivr/taskwork.php"); break;    
    case "TaskWorkWithSumm": include("modules/ivr/taskwork_with_summ.php"); break;    
    case "TaskPause": include("modules/ivr/taskpause.php"); break;    
    case "TaskPauseWithSumm": include("modules/ivr/taskpause_with_summ.php"); break;  
    case "TaskStop": include("modules/ivr/taskstop.php"); break;    
    case "TaskStopWithSumm": include("modules/ivr/taskstop_with_summ.php"); break;    
    case "ViewTask": include("modules/ivr/viewtask.php"); break;    
    case "ViewTaskWithSumm": include("modules/ivr/viewtask_with_summ.php"); break;        
//    case "InQUEUE": include("modules/ivr/inqueue.php"); break;
//    case "OutQUEUE": include("modules/ivr/outqueue.php"); break;
//    case "InPause": include("modules/ivr/inpause.php"); break;
//    case "OutPause": include("modules/ivr/outpause.php"); break;
//    case "QueueReport": include("modules/ivr/queuereport.php"); break;
    default: include("modules/ivr/status.php"); break;

}
?>