<?php
/*
 * Asterisk Management System - An open source toolkit for Asterisk PBX.
 * See http://www.asterisk.org for more information about
 * the Asterisk project.
 *
 * Copyright (C) 2006 - 2007, West-Web Limited.
 *
 * Nickolay Shestakov <ns@ampex.ru>
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 */

if(!is_file("config.php")) {
    session_start();
    $_SESSION['ams_install']=true;
    include("install/index.php");
    exit();
}
session_start();
$_SESSION['ams_entry']=true;

require_once("config.php");
require_once("lib/func.php");

$module=$_POST['module'];
$action=$_POST['action'];
$menu_module=$_POST['menu_module'];
$user_password=$_POST['user_password'];
$user_name=$_POST['user_name'];

if (empty($_SESSION['theme'])) $theme=$_SESSION['theme']=$default_theme;
else $theme=$_SESSION['theme'];
if(empty($_SESSION['lang'])) $lang=$_SESSION['lang']=$default_lang;
else $lang=$_SESSION['lang'];

foreach(@glob("themes/*") as $t) $themes[]=basename($t);

if(!in_array($theme,$themes) || !in_array($lang,array_keys($languages)))
    die('Not a Valid Entry');

include("lang/$lang.lang.php");
include("include/modules.php");

if(!in_array($module,Array('Users','MySettings'))) {
		$_SESSION['user_password']="";
		$action="Login"; 
		include_once("modules/Users/index.php");
		exit();
} 

if($module=="Users") {
    if($action=="Authenticate") {
	$_SESSION['user_name']=$user_name;
	//$_SESSION['user_password']=$user_password;
	if($user_name && $user_password) include_once("modules/Users/index.php");
	else {
	    $_SESSION['login_error']=$strLoginErrorEmpty;
	    $action="Login";
	    include_once("modules/Users/index.php");
	    exit();
	}
    }elseif(($action=="Login") OR ($action=="Logout")) {
	    include_once("modules/Users/index.php");
	    exit();
    }
}

if(!isset($_SESSION['auth_user_id'])) {
		$_SESSION['user_password']="";
		$action="Login"; 
		include_once("modules/Users/index.php");
		exit();
}

$cash_level=2;
setcookie('user_name',$_SESSION['user_name'],time()+(60*60*24*30));

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>		
    <title><?=$label?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Script-Type" content="text/javascript"/>
    <meta name="MSSmartTagsPreventParsing" content="TRUE"/>
    <link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-2"/>
    <link id="style_css" rel="stylesheet" type="text/css" media="all" href="themes/<?=$theme?>/style.css"/>
    <link id="main_menu_css" rel="stylesheet" type="text/css" media="all" href="themes/<?=$theme?>/main_menu.css"/>
    <link id="module_menu_css" rel="stylesheet" type="text/css" media="all" href="themes/<?=$theme?>/module_menu.css"/>
    <link id="header_css" rel="stylesheet" type="text/css" media="all" href="themes/<?=$theme?>/header.css"/>
    <script type="text/javascript" src="jscalendar/calendar_stripped.js"></script>
<? if(is_file("jscalendar/lang/calendar-".substr($lang,0,2).".js"))
       echo "<script type=\"text/javascript\" src=\"jscalendar/lang/calendar-".substr($lang,0,2).".js\"></script>";
  else echo "<script type=\"text/javascript\" src=\"jscalendar/lang/calendar-en.js\"></script>"; ?>
    <script type="text/javascript" src="jscalendar/calendar-setup_stripped.js"></script>
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous.js?load=effects,controls,dragdrop"></script>
    <script type="text/javascript" src="js/func.js"></script>
    <script type="text/javascript" src="js/ams.js"></script>
    <script type="text/javascript" src="js/toolbar.js"></script>
    <script type="text/javascript" src="js/filemanager.js"></script>
<?/*
    <script src="js/AMISocket.js"></script>
    <script src="js/astman.js"></script>
    
*/?>
</head>
<body id="MainBody">
<div id="__modal-div__"  align="center" style="display: none; position: absolute; 
 z-index: 3999; width: 100%; background-color: white; filter: progid:DXImageTransform.Microsoft.Alpha(opacity=50); opacity: 0.5; -moz-opacity: 0.5;"></div>
<div id="__loading-div__" align="center" class="loading-div"><?=$strHttpLoading?><img align="absmiddle" src="images/spinner.gif"></div>
<div id="__modal-box__" class="modal-box" style="display: none;"></div>
<a title='<?=$strCollapsePage?>' href='javascript:ams.toggleScreen()' id='_toggle_screen_'  
style="position: absolute; top: 0px; left: 0px; display: none; z-index: 2000;">
<img src='images/fullpage.png' style="border: 0;">
</a>

<script type="text/javascript">
$('__loading-div__').hide();
ams.httpTimeout=<?=$httpTimeout?>;
ams.ami_ip='<?=$ami_ip?>';
ams.ami_port='<?=$ami_port?>';
ams.www_dir='<?=$www_dir?>';
ams.strReloadConfigFailed='<?=$strReloadConfigFailed?>';
ams.strReloadConfigSuccess='<?=$strReloadConfigSuccess?>';
ams.strHide='<?=$strHide?>';
ams.strFieldMustNotBeEmpty='<?=$strFieldMustNotBeEmpty?>';
ams.strHttpTimeout='<?=$strHttpTimeout?>';
ams.strLoadError='<?=$strLoadError?>';

Ajax.Responders.register(ams.globalHandler);

limit_display=<?=$limit_display?>;
menu_module='<?=$menu_module?>';
action='';
current_module='';
debug=0;
obj_time=0;
start_time=0;


<?foreach ($registered_modules as $rm) {?>
  Global<?=$rm?> = new Object;
<?}?>

//Event.observe(window,'load',initConnection,false);


</script>

<div id="frame-top">
  <?include("themes/$theme/header.php");?>
</div>

<div id="ams-menu">
  <? include("themes/$theme/menu.php"); ?>
</div>
 
<div id="frame-module" style="width: auto;top: 0; left: 0;">
    <div id="frame-module-content" style="zoom: 1;">
        <div id="reload-div" style="display: none; float:right; font-size: 10px; font-family: verdana,sans; font-style: italic; color: white;">
           <a href="javascript:ams.reloadConfig()" title="<?=$strReloadConfig?>">
           <img src="images/reload.gif" alt="reload">
           </a>
        </div>
	<div id="ams-module">
	</div>		
	<script type="text/javascript">
	    loadModule('','<?=$menu_module?>','<?=$module?>','<?=$action?>');
	</script>
	<br/><br/><br/><br/><br/>
	<div id="ams-footer">
	    <? include("themes/$theme/footer.php");?>
	</div>
    </div>
</div>

<div id="debug" style="width: 500px; background-color: white; height: 250px;
    overflow: auto; position: absolute; bottom: 2px; display: none; font-size: 10px; font-family: verdana,sans; border: 1px solid #555555;">  
</div>  
<script>
  if(debug) $('debug').style.display="inline";
</script>

<div id="xml-socket-div"></div>
</body>
</html>
