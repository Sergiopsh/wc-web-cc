<?php
session_start();
if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');
include_once("config.php");
$theme=$_SESSION['theme'];
if(empty($theme)) $theme=$default_theme;
$lang=$_SESSION['lang'];
if(empty($lang)) $lang=$default_lang;
include_once("lang/".$lang.".lang.php");
include_once("themes/$theme/header.php");
?>


