<?php
session_start();
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("config.php");
//error_reporting($err_reporting);
$theme=$_SESSION['theme'];
if(empty($theme)) $theme=$default_theme;
$menu_module=$_POST['menu_module'];
$action=$_POST['action'];
if(empty($menu_module)) $menu_module=$default_module;
$lang=$_SESSION['lang'];
if(empty($lang)) $lang=$default_lang;
include_once("lang/$lang.lang.php");
include_once("include/modules.php");
if($_SESSION['acl'][$menu_module]['Access'])
    include_once("themes/$theme/menu.php");
?>
	