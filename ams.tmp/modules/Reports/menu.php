<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/Reports/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array($strReports,"images/reports.gif"),
		   array(array($strCDRReports, "javascript:loadModule('','Reports','Reports','ReportCDR');"),
		   	 array($strDailyReport, "javascript:loadModule('','Reports','Reports','DailyReport');"),
			 array($strAggregateTraffic, "javascript:loadModule('','Reports','Reports','Aggregate');"),
			 array($strMonthlyReport,"javascript:loadModule('','Reports','Reports','MonthlyReport');"),
			 array($strHourlyLoad, "javascript:loadModule('','Reports','Reports','HourlyLoad');"),
			 array($strCompareCalls, "javascript:loadModule('','Reports','Reports','CompareCalls');"),
			 array($strTechCDRReport, "javascript:loadModule('','Reports','Reports','TechCDR');")));
			 
}
?>