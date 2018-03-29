<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/supervisor/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array($strReports,"images/reports.gif"),
		   array(array($strQueueReport, "javascript:loadModule('','supervisor','supervisor','QueueReport');")//,
			 //array($strInQueue, "javascript:loadModule('','supervisor','supervisor','InQUEUE');"),
			 ,array($strQueueReportSupervisor, "javascript:loadModule('','supervisor','supervisor','QueueReportSupervisor');")
			 ,array($strOperReport, "javascript:loadModule('','supervisor','supervisor','OperReport');")
			 ));
}
?>