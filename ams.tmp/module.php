<?php
session_start();
if(empty($_SESSION['ams_entry'])) {
    include_once("include/expired.php");
    exit();
}
$theme=$_SESSION['theme'];
if(empty($theme)) $theme=$default_theme;
$lang=$_SESSION['lang'];
if(empty($lang)) $lang=$default_lang;
include_once("lang/$lang.lang.php");
include_once("config.php");
include_once("include/modules.php");
include_once("lib/func.php");

$action_start_time=getmicrotime();
//error_reporting($err_reporting);

extract(stripslashes_r($_POST));

if(empty($current_module)) $current_module=$default_module;
if($_SESSION['acl'][$current_module]['Access']) {
    include_once("modules/$current_module/index.php");
    echo "<br><br>";
    if($show_action_time) 
	echo "<script>$('_action_time_').innerHTML='$strActionTime: ".number_format((getmicrotime()-$action_start_time),3)." $strSec';</script>";
} else include_once("include/norights.php");
?>
	