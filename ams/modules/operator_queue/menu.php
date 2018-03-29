<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/operator_queue/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array($strReports,"images/reports.gif"),
		   array(array($strStatus, "javascript:loadModule('','operator_queue','operator_queue','Status');")//,
			 //array($strInQueue, "javascript:loadModule('','operator_queue','operator_queue','InQUEUE');"),
			 //array($strInQueue, "javascript:loadModule('','operator_queue','operator_queue','OutQUEUE');")
			 ,array($strOutQueue, "javascript:loadModule('','operator_queue','operator_queue','QueueReport');")
			 ));
}
?>