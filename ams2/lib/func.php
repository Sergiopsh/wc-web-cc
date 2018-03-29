<?php

function DbConnect()  {
    global $db_host,$db_name,$db_user,$db_pass;
    $db = new DB_Sql();
    $db -> Database = $db_name;
    $db -> Host = $db_host;
    $db -> User = $db_user;
    $db -> Password = $db_pass;
    @$db -> connect ();
    @$db-> query("SET NAMES utf8");
    return $db;
}

function quote($val) {

    return "'".mysql_real_escape_string($val)."'";
}

function check_sql($val) {
    global $sql_protect,$sql_injections;
    if(!$sql_protect) return $val;
    if(is_array($val)) $str_orig=strtolower(implode(".",$val));
    $str_repl=str_replace($sql_injections,"*",$str_orig);
    if(strlen($str_orig) != strlen($str_repl)) die('SQL-injection detected...');
    return $val;
}
function stripslashes_arr($arr) {

    $newArr = array();
    foreach($arr as $key => $value) {
	$newArr[$key] = (is_array($value)) ? stripslashes_r($value) : stripslashes($value);
    }
    return $newArr;
}
function is_empty($val) {

    return (!isset($val) || $val == "");
}

function stripslashes_r($val) {
    if(!get_magic_quotes_gpc()) return check_sql($val);
    if(is_array($val)) return check_sql(stripslashes_arr($val));
    return check_sql(stripslashes($val));
}
function cleardir($dir) {
    if($objs = @glob($dir.'/*')) {
	foreach($objs as $obj) {
	    @unlink($obj);
	}
    }
}
function display_minute($time){
	echo sprintf("%02d",intval($time/60)).":".sprintf("%02d",intval($time%60));
}

function display_hours($time){
	$h = sprintf("%02d",intval($time/3600));
	$m = sprintf("%02d",floor(($time - $h*3600)/60));
	$s = sprintf("%02d",$time - ($h*3600 + $m*60));
	echo $h.":".$m.":".$s;
}
function to_hours($time){
	$h = sprintf("%02d",intval($time/3600));
	$m = sprintf("%02d",floor(($time - $h*3600)/60));
	$s = sprintf("%02d",$time - ($h*3600 + $m*60));
	return $h.":".$m.":".$s;	
}

function dt_to_dbformat($dt,$sec) {
    switch ($_SESSION['dateformat']) {
	case "21-12-2006":
	case "21/12/2006":
	case "21.12.2006":
	    return (substr($dt,6,4)."-".substr($dt,3,2)."-".substr($dt,0,2)." ".substr($dt,11,2).":".substr($dt,14,2).":$sec");
	case "2006-12-21":
	    return ($dt.":$sec");
	case "12-21-2006":
	    return (substr($dt,6,4)."-".substr($dt,0,2)."-".substr($dt,3,2)." ".substr($dt,11,2).":".substr($dt,14,2).":$sec");
	default: return ($dt.":$sec");

    }
}

function dbformat_to_dt($dt) {
    switch ($_SESSION['dateformat']) {
	case "21-12-2006":
	    return (substr($dt,8,2)."-".substr($dt,5,2)."-".substr($dt,0,4)." ".substr($dt,11,5));
	case "21/12/2006":
	    return (substr($dt,8,2)."/".substr($dt,5,2)."/".substr($dt,0,4)." ".substr($dt,11,5));
	case "21.12.2006":
	    return (substr($dt,8,2).".".substr($dt,5,2).".".substr($dt,0,4)." ".substr($dt,11,5));
	case "2006-12-21":
	    return substr($dt,0,16);
	case "12-21-2006":
	    return (substr($dt,5,2)."-".substr($dt,8,2)."-".substr($dt,0,4)." ".substr($dt,11,5));
	default: return substr($dt,0,16);

    }
}
function moduleHeader($head) {
    echo "<div id=\"frame-module-header\" nowrap=\"nowrap\">$head</div>";
    echo "<div id=\"module-warning\" class=\"module-warning\" style=\"display: none;\"></div><br/>";
}
function moduleForm($hidden) {
    echo "<form name=\"module_form\" id=\"module_form\" method=\"post\">";
    foreach ($hidden as $h) {
	global $$h;
	echo "<input type=\"hidden\" name=\"$h\" id=\"$h\" value=\"".hc($$h)."\"/>";
    }
}

function showMsg($str,$container="",$hide=0,$img="",$style="") {
    if(empty($container)) {
	if($style) $style="style=\"$style\"";
	echo "<div class='module-warning' $style>";
	if($img) echo "<img align='absmiddle' src='images/note.gif' alt='' />&nbsp;";
	echo "$str</div>"; return;
    }
    echo "<script> ams.showMessage('".addslashes($str)."','$container',$hide,'$img'); </script>";
}

function loadModule($form,$menu,$module,$action,$par="",$exit=1) {
    if($par) $par=",\$H($par)";
    echo "<script> loadModule($form,\"$menu\",\"$module\",\"$action\"".$par."); </script>";
    if($exit) exit();
}

function num_pages($num,$limit,&$cp=0) {
    if($num <= $limit) $np = 1;
    elseif($num % $limit == 0) $np = (int) ($num/$limit);
    else $np = (int) ($num/$limit)+1;
    if(empty($cp)) $cp=0;
    if($cp > ($np - 1)) $cp = $np-1;
    return $np;
}
function dt_format() {
    switch ($_SESSION['dateformat']) {
	case "21-12-2006": return '%d-%m-%Y';
    	case "12-21-2006": return '%m-%d-%Y';
	case "21/12/2006": return '%d/%m/%Y';
	case "21.12.2006": return '%d.%m.%Y';
	default: return '%Y-%m-%d';
    }
}

function getmicrotime () {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);

}
function ss($var) {
    return stripslashes($var);
}
function ah($var) {
    return addslashes(htmlspecialchars($var));
}

function hc($var) {
    return htmlspecialchars($var);
}
function hce($var) {
    return htmlspecialchars($var,ENT_QUOTES);
}
function hs($var) {
    return htmlspecialchars(stripslashes($var));
}
function hd($var) {
    return htmlspecialchars_decode($var);
}

function getStr($val) {
    global ${"str".$val};
    $str = ${"str".$val};
    return $str ? $str : $val;
}

function actionTime($start,$width="100%") {
    global $strActionTime,$strSec;
    $action_time=getmicrotime()-$start;
    echo "<div width=\"$width\" align=\"center\" class='module-note' style='font-size: 9px'>$strActionTime: ".number_format($action_time,3)." $strSec </div>";
}

function formatSize($bytes) {

    if($bytes > 10485760) return (int) ($bytes/1048576)." M";
    else if($bytes > 10240) return (int) ($bytes/1024)." K";
    return $bytes;
}
?>