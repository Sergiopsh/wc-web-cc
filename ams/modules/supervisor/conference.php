<?


    $callagent = $_POST['callagent'];

$supervisor = 500;

if (strstr($_SESSION['user_name'],"supervisor2")){
    $supervisor = 501;
};

$strHost="127.0.0.1";  
$strUser="admin";  
$strSecret="djljrfyfk";  
$strChannel="Local/$supervisor@from-internal";
$strWaitTime="10";  
$strCallerId="801";  
$strContext="from-internal";  
$strReceiver = $supervisor+20;

$wrets="";  
  $oSocket = @fsockopen($strHost, 5038, $errnum, $errdesc)  
or die("Connection to host failed");  
        fputs($oSocket, "Action: login\r\n");  
        fputs($oSocket, "Events: off\r\n");  
        fputs($oSocket, "Username: $strUser\r\n");  
        fputs($oSocket, "Secret: $strSecret\r\n\r\n");  
        fputs($oSocket, "Action: originate\r\n");  
        fputs($oSocket, "Channel: $strChannel\r\n");  
        fputs($oSocket, "WaitTime: $strWaitTime\r\n");  
        fputs($oSocket, "CallerID: $strCallerId\r\n");  
        fputs($oSocket, "Exten: $strReceiver\r\n");  
        fputs($oSocket, "Context: $strContext\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Logoff\r\n\r\n");  
  while (!feof($oSocket)) {  
    $wrets .= fread($oSocket, 8192);  
  }  
 fclose($oSocket);  
  if (stripos($wrets, 'Originate successfully queued')) {  
    echo "Call completed ";  
  } else {  
    echo "No accept call ";
    exit;  
  }  
  
    //sleep(5);   
    $queue_call_arr = explode("!",shell_exec("asterisk -rx \"show channels concise\"|grep  \"!from-internal!\"|grep \"SIP/$supervisor\""));
    $callid = $queue_call_arr[0];
    $callid = explode("/",$callid);
    $callid = "SIP/".$callid[1];
  $wrets="";  
  $oSocket = fsockopen($strHost, 5038, $errnum, $errdesc)  
or die("Connection to host failed");  
        fputs($oSocket, "Action: login\r\n");  
        fputs($oSocket, "Events: off\r\n");  
        fputs($oSocket, "Username: $strUser\r\n");  
        fputs($oSocket, "Secret: $strSecret\r\n\r\n");  
        fputs($oSocket, "Action: Redirect\r\n");  
        fputs($oSocket, "Channel: $callid\r\n");  
        fputs($oSocket, "Exten: 801\r\n");  
        fputs($oSocket, "Context: from-internal\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Logoff\r\n\r\n");  
  while (!feof($oSocket)) {  
    $wrets .= fread($oSocket, 8192);  
  }  
   
 fclose($oSocket);  
  if (stripos($wrets, 'success')) {  
    echo "Call completed ";  
  } else {  
    echo "No accept call ";
    exit;  
  } 
//подключаем агента и клиента
$queue_call_arr = explode("\n",shell_exec("asterisk -rx \"show channels concise\"|grep SIP/$callagent"));
foreach($queue_call_arr as $queue_call){
    if(strlen($queue_call)<10){
	continue;
    };
    if (strstr($queue_call,"!SIP/10.10.11.38")){
	$agent_ch = explode("!",$queue_call);
	$agent_ch = $agent_ch[0];
	continue;
    };
    if (strstr($queue_call,"!SIP/$callagent")){
	$abon_ch = explode("!",$queue_call);
	$abon_ch = $abon_ch[0];
	continue;
    };
};

print "<BR>";
print "abon_ch=$abon_ch";
print "<BR>";
print "agent_ch=$agent_ch";


$wrets="";  
  $oSocket = @fsockopen($strHost, 5038, $errnum, $errdesc)  
or die("Connection to host failed");  
        fputs($oSocket, "Action: login\r\n");  
        fputs($oSocket, "Events: off\r\n");  
        fputs($oSocket, "Username: $strUser\r\n");  
        fputs($oSocket, "Secret: $strSecret\r\n\r\n");  
        fputs($oSocket, "Action: Redirect\r\n");  
        fputs($oSocket, "Channel: $abon_ch\r\n");  
        fputs($oSocket, "Exten: 801\r\n");  
        fputs($oSocket, "Context: from-internal\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Redirect\r\n");  
        fputs($oSocket, "Channel: $agent_ch\r\n");  
        fputs($oSocket, "Exten: 801\r\n");  
        fputs($oSocket, "Context: from-internal\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Logoff\r\n\r\n");  
  while (!feof($oSocket)) {  
    $wrets .= fread($oSocket, 8192);  
  }  
 fclose($oSocket);  
  if (stripos($wrets, 'Originate successfully queued')) {  
    echo "Call completed ";  
  } else {  
    echo "No accept call ";  
  }  
  echo $wrets;

exit;
?>
