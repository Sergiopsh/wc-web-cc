<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/autocall/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array("Автообзвон","images/reports.gif"),
		   array(array("Список заданий", "javascript:loadModule('','autocall','autocall','TasksList');")//,
			 ,array($strFilesList, "javascript:loadModule('','autocall','autocall','FilesList');")//,
			 //array($strInQueue, "javascript:loadModule('','autocall','autocall','InQUEUE');"),
			 //array($strInQueue, "javascript:loadModule('','autocall','autocall','OutQUEUE');")
			 ,array($strAbonList, "javascript:loadModule('','autocall','autocall','AbonList');")
			 ,array("Список заданий с суммами", "javascript:loadModule('','autocall','autocall','TasksListWithSumm');")
			 ,array("Список абонентов с суммами", "javascript:loadModule('','autocall','autocall','AbonListWithSumm');")
			 ));
}
?>