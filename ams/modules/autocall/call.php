<?


function call($number,$filename,$wait,$id){
$supervisor = $number;
$wait *= 1000;

$file_dir = "/var/lib/asterisk/sounds/";
$filename = substr($filename,strlen($file_dir)-1);
$filename = substr($filename,1,strlen($filename)-5);
$filename = "./".$filename;

$strHost="127.0.0.1";  
$strUser="admin";  
$strSecret="djljrfyfk";  
$strChannel="Local/$supervisor@from-internal";
$strWaitTime="60";  
$strCallerId='"Oper 500" <500>';
$strContext="from-internal";  
$strReceiver = $supervisor+209;

$wrets="";  
  $oSocket = @fsockopen($strHost, 5038, $errnum, $errdesc)  
or die("Connection to host failed");  
        fputs($oSocket, "Action: login\r\n");  
        fputs($oSocket, "Events: off\r\n");  
        fputs($oSocket, "Username: $strUser\r\n");  
        fputs($oSocket, "Secret: $strSecret\r\n\r\n");  
        fputs($oSocket, "Action: originate\r\n");  
        fputs($oSocket, "Channel: $strChannel\r\n");
        fputs($oSocket, "CallerID: $strCallerId\r\n");
        fputs($oSocket, "WaitTime: $strWaitTime\r\n");  
        fputs($oSocket, "Timeout: $wait\r\n");  
        fputs($oSocket, "Variable: var1=$filename\r\n");
        fputs($oSocket, "Variable: var2=$id\r\n");
        fputs($oSocket, "Exten: 821\r\n");  
        fputs($oSocket, "Context: $strContext\r\n");  
        fputs($oSocket, "Priority: 1\r\n\r\n");  
        fputs($oSocket, "Action: Logoff\r\n\r\n");  
  while (!feof($oSocket)) {  
    $wrets .= fread($oSocket, 8192);  
  }  
 fclose($oSocket);  
  if (stripos($wrets, 'Originate successfully queued')) {  
    //echo "Call completed ";
    return 1;  
  } else {  
    //echo "No accept call ";
    //exit;
    return 0;  
  }  
}
//echo call("989236412125","/var/lib/asterisk/sounds/autocall/info1.gsm",15)."\n";
?>
