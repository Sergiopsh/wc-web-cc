<?

$strHost="127.0.0.1";  
$strUser="admin";  
$strSecret="djljrfyfk";  
$strChannel="Local/23@queuemetrics";
$strWaitTime="10";  
$strReceiver="23";  
$strContext="queuemetrics";  
$agent=$_POST['agent'];


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
        fputs($oSocket, "Variable: AGENTCODE=$agent\r\n");  
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
  }  
?>

<script>
<!--
loadModule('','operator_queue','operator_queue','Status');
//-->
</script>
