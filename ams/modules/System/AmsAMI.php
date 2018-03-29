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

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');

class AmsAMI {

    var $socket = null;
    var $error;
    var $id = 0;
    var $action;
    var $errResponse;
    var $msg;
    var $response;

    function AmsAMI() {

    }
    function login($ip,$port,$login,$psw) {
    	global $strCantOpenSocket;
        if(!$this->socket=@fsockopen($ip, $port, $err, $err, 30)) {
	    $this->error=$strCantOpenSocket;
	    return false;
	}
	$out = "Action: Login\r\n";
	$out.="Username: ".$login."\r\n";
	$out.="ActionID: ".++$this->id."\r\n";
	$out.="Secret: ".$psw."\r\n\r\n";
	@fwrite($this->socket, $out);
	return true;
    }
    function action($action) {
	$out = "Action: ".$action."\r\n";
	$out.="ActionID: ".++$this->id."\r\n\r\n";
	@fwrite($this->socket, $out);

     }
    function getResponse() {	
     	global $strNoAnswer,$strAuthenticationFailed,$strResponseError,$strConnectionTimeout;
        @stream_set_timeout($this->socket,10);
	$info = @stream_get_meta_data($this->socket);
	$i=0;
	while(!feof($this->socket) && (!$info['timed_out'])) {
    	    $line[$i] = @fgets($this->socket,1024);
    	    $info = @stream_get_meta_data($this->socket);
    	    ob_flush();
    	    flush();
    	    if(strstr($line[$i],"END COMMAND")) {
	        array_pop($line);
    		break;
    	    }
    	    $i++;
	}
	if($info['timed_out']) {
	    $this->error=$strConnectionTimeout;
	    //echo "timeout";
	    return false;
	} 	
	if(!$line) {
	    $this->error=$strNoAnswer; 
	    return false;
	}
	$end=count($line);
	for ($i=0; $i < $end; $i++) {
	    $r=explode(":",$line[$i]);
	    $header=$r[0];
	    $res=trim($r[1]);
	switch ($header) {
	    case "Response":
		if($res=="Error") {
		    $this->errResponse=$strResponseError;
		    continue;
		}
		break;
	    case "Message":
	        $this->msg=$res;
		if(strstr($res,"Authentication failed")){
		    $this->error=$strAuthenticationFailed;
		    return false;
		}
		break;
	    case "Privilege":
		  $i++;
		  if($i < $end) {
		    $l = explode(":",$line[$i++]);
		    $id=trim($l[1]);
		    $this->response[$id]=array_slice($line,$i);
		    return $id;
		  }else {
		    $this->error=$strResponseError;
		    return false;
		   }
		  
	    } 
        }
    }
    function close() {
       @fclose ($this->socket);
    
    }
    function getFormattedResponse($id) {
	if(!$this->response[$id]) return "";
	foreach($this->response[$id] as $line) {
	      $s.="&nbsp;&nbsp;&nbsp;".str_replace(" ","&nbsp;",htmlspecialchars($line))."<br>";
	}
	return $s;
    }
    function cmd ($cmd) {
	$this->action("Command\r\nCommand: $cmd");
	return $this->getResponse();
    }
    function Dial($channel,$context,$exten,$priority,$timeout,
                  $callerid,$var,$acc,$app,$data) {
	    if(!$exten) $exten="s";
	    if(!$priority) $priority=1;
	    if(!$timeout) $timeout=30000;
	    $a="Originate\r\n";
	    $a.="Channel: $channel\r\n";
	    if($context){
		$a.="Context: $context\r\n";
		$a.="Exten: $exten\r\n";
    		$a.="Priority: $priority\r\n";
	    }
	    $a.="Timeout: $timeout\r\n";
	    if($callerid) $a.="CallerID: $callerid\r\n";
	    if($var) $a.="Variable: $var\r\n";
	    if($acc) $a.="Account: $acc\r\n";
	    if($app) $a.="Application: $app\r\n";
	    if($data) $a.="Data: $data";
	    $this->action($a);
    }
}

?>


