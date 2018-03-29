<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/autocall/lang/".$lang.".lang.php");
//include_once("modules/Recording/Recording.php");
//include_once("modules/Recording/lang/".$lang.".lang.php");


$table="cdr";

switch ($action) {
    case "AbonList": include("modules/autocall/abonlist.php"); break;
    case "AbonListWithSumm": include("modules/autocall/abonlistwithsumm.php"); break;
    case "deletelist": include("modules/autocall/deletelist.php"); break;
    case "deletelistwithsumm": include("modules/autocall/deletelistwithsumm.php"); break;
    case "deletetask": include("modules/autocall/deletetask.php"); break;
    case "deletetaskWithSumm": include("modules/autocall/deletetask_with_summ.php"); break;
    case "deletefile": include("modules/autocall/deletefile.php"); break;
    case "deletephone": include("modules/autocall/deletephone.php"); break;    
    case "viewlist": include("modules/autocall/viewlist.php"); break;
    case "viewlistwithsumm": include("modules/autocall/viewlistwithsumm.php"); break;
    case "FilesList": include("modules/autocall/fileslist.php"); break;
    case "TasksList": include("modules/autocall/taskslist.php"); break;    
    case "TasksListWithSumm": include("modules/autocall/taskslistwithsumm.php"); break;
    case "TaskWork": include("modules/autocall/taskwork.php"); break;    
    case "TaskWorkWithSumm": include("modules/autocall/taskwork_with_summ.php"); break;    
    case "TaskPause": include("modules/autocall/taskpause.php"); break;    
    case "TaskPauseWithSumm": include("modules/autocall/taskpause_with_summ.php"); break;  
    case "TaskStop": include("modules/autocall/taskstop.php"); break;    
    case "TaskStopWithSumm": include("modules/autocall/taskstop_with_summ.php"); break;    
    case "ViewTask": include("modules/autocall/viewtask.php"); break;    
    case "ViewTaskWithSumm": include("modules/autocall/viewtask_with_summ.php"); break;        
//    case "InQUEUE": include("modules/autocall/inqueue.php"); break;
//    case "OutQUEUE": include("modules/autocall/outqueue.php"); break;
//    case "InPause": include("modules/autocall/inpause.php"); break;
//    case "OutPause": include("modules/autocall/outpause.php"); break;
//    case "QueueReport": include("modules/autocall/queuereport.php"); break;
    default: include("modules/autocall/status.php"); break;

}
?>