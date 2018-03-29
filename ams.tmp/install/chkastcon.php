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
if(!$_SESSION['ams_install']) die('Not a Valid Entry');
include_once("lang/".$_SESSION['install_lang'].".lang.php");

extract($_POST);
$action="core show version";
$fp = @fsockopen ($host, $port, $err, $err, 30);
   if (!$fp) {
    echo "<font color=red>$strCantOpenSocket</font>";
    exit();
   } 

$out = "Action: Login\r\nUsername: $user\r\nSecret: $pass\r\n\r\n";
$out.= "Action: Command\r\nCommand: $action\r\n\r\n";

 @fwrite($fp, $out);
 //stream_set_blocking($fp,TRUE);
 stream_set_timeout($fp,4);
 $info = stream_get_meta_data($fp);
 $i=0;
 while(!feof($fp) && (!$info['timed_out'])) {
     $line[$i] = @fgets($fp,1024);
     $info = stream_get_meta_data($fp);
      ob_flush();
      flush();
      if(strstr($line[$i],"END COMMAND")) {
        break;
      }
     $i++;
     
 }

// fclose ($fp);
// if($info['timed_out']) {
//    echo $strConnectionTimeout;
//    exit();
// }
 if(!$line) {
    echo $strNoAnswer; 
    @fclose ($fp);
    exit();
 }
 $end=count($line);
    for ($i=0; $i<$end;$i++) {
	$r=explode(":",$line[$i]);
	$header=$r[0];
	$res=trim($r[1]);
	switch ($header) {
	    case "Response":
		//echo $res;
		if($res=="Error") {
		    //echo "<font color=red>$strResponseError</font>";
		    echo "<font color=red>$strAuthenticationFailed</font>";
		    @fclose ($fp);
		    exit();
		}
		break;
	    case "Message":
		if(strstr($res,"Authentication failed")){
		    echo "<font color=red>$strAuthenticationFailed</font>";
		    @fclose ($fp);
		    exit();
		}
		break;
	    case "Privilege":
		  $i++;
		  echo "<font color=green>$strConnectionSuccess</font><br>";
		  //echo $strAstResponse.": ";
		  for(;$i<$end;$i++) {
			if(strstr($line[$i],"END COMMAND")) break 2;
			echo $line[$i];
		  }
		  break 2;
	} 

	//echo $l."<br>";
    }
@fclose ($fp);
 if($info['timed_out']) {
    echo "<font color=red>$strConnectionTimeout</font>";
    exit();
 } 

?>


