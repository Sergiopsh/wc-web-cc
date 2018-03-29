<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/operator_reports/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array($strReports,"images/reports.gif"),
		   array(array($strCommonReport, "javascript:loadModule('','operator_reports','operator_reports','CommonReport');"),
			 array("Сводный общий отчёт", "javascript:loadModule('','operator_reports','operator_reports','CommonReportAll');"),
			 array($strDetailReport, "javascript:loadModule('','operator_reports','operator_reports','DetailReport');"),
			 array($strOutReport, "javascript:loadModule('','operator_reports','operator_reports','OutReport');")
			 //,array($strQueueReport, "javascript:loadModule('','operator_reports','operator_reports','QueueReport');")
			 //,array($strHistoryReport, "javascript:loadModule('','operator_reports','operator_reports','HistoryReport');")
			 //,array($strQueueReport, "javascript:loadModule('','operator_reports','operator_reports','QueueReport');")
			 ));
}
?>