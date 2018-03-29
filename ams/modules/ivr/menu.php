<?
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("modules/ivr/lang/".$lang.".lang.php");

if($_SESSION['acl']['Reports']) {
$module_menu = array(array("Автообзвон","images/reports.gif"),
		   array(array($strFilesList, "javascript:loadModule('','ivr','ivr','FilesList');")//,
			 ));
}
?>