<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/ReportsSuper/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array($strReports,"images/reports.gif"),
		   array(array($strCommonReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','CommonReport');"),
			 array($strDetailReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','DetailReport');"),
			 array($strOutReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','OutReport');"),
			 array($strPeackReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','PeackReport');"),
			 array($strHistoryReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','HistoryReport');"),
			 array($strNigthReport, "javascript:loadModule('','ReportsSuper','ReportsSuper','NReport');")));
}
?>