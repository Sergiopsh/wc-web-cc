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
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../lib/func.php");
include_once("../../lib/sysfunc.php");
$action=trim(stripslashes_r($_POST['Action']));
function prepareCmd($cmd) {

    //return str_replace("\\;",";",escapeshellcmd($cmd));
    //return $cmd;
    $newcmd=$cmd;
    $c=count_chars($cmd);
    $c[ord("\"")] % 2 ? $newcmd=substr($newcmd,0,strrpos($newcmd,"\""))."\\".substr($newcmd,strrpos($newcmd,"\"")):0;
    $c[ord("'")] % 2 ? $newcmd=substr($newcmd,0,strrpos($newcmd,"'"))."\\".substr($newcmd,strrpos($newcmd,"'")):0;
    return $newcmd;
}

setenvlang();

echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td>";

$res = execute(prepareCmd($action)." 2>&1");
$res = explode("\n",$res);

foreach($res as $o) {
   echo "&nbsp;&nbsp;&nbsp;".str_replace(" ","&nbsp;",hs($o))."<br>";
}
echo "</td></tr></table><br><br>";
