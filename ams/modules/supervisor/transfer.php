<?
    $to_agent=$_POST['agent'];
    //print $_POST['callagent'];

    //получение информации по текущим звонкам
    $queue_call_arr = explode("!",shell_exec("asterisk -rx \"show channels concise\" |egrep \"((ext-queues)|(trWM)|(trW!))\" |grep ".$_POST['callagent']));
    $callid = $queue_call_arr[0];
    $callid = explode("/",$callid);
    $callid = "SIP/".$callid[1];
    

    
$strHost="127.0.0.1";  
$strUser="admin";  
$strSecret="djljrfyfk";  
//print $callid;
//print "<br>";
//print $to_agent;
//exit;
$wrets="";  
  $oSocket = @fsockopen($strHost, 5038, $errnum, $errdesc)  
or die("Connection to host failed");  
        fputs($oSocket, "Action: login\r\n");  
        fputs($oSocket, "Events: off\r\n");  
        fputs($oSocket, "Username: $strUser\r\n");  
        fputs($oSocket, "Secret: $strSecret\r\n\r\n");  
        fputs($oSocket, "Action: Redirect\r\n");  
        fputs($oSocket, "Channel: $callid\r\n");  
        fputs($oSocket, "Exten: $to_agent\r\n");  
        fputs($oSocket, "Context: from-internal\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Logoff\r\n\r\n");  
  while (!feof($oSocket)) {  
    $wrets .= fread($oSocket, 8192);  
  }  
 fclose($oSocket);  
//  if (stripos($wrets, 'Originate successfully queued')) {  
//    echo "Call completed ";  
//  } else {  
//    echo "Ошибка!Редирект не совершен ";  
//  }  
//echo $wrets;
//exit;

?>
<script>
<!--
loadModule('','supervisor','supervisor','QueueReport');
//-->
</script>
