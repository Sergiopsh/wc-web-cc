<?PHP
/*
 Copyright (C) 2006 Earl C. Terwilliger
 Email contact: earl@micpc.com

    This file is part of The Asterisk Queue/CDR Log Analyzer WEB Interface.

    These files are free software; you can redistribute them and/or modify
    them under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    These programs are distributed in the hope that they will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

 session_start();
 $_SESSION = array();
 session_unset();
 session_destroy();
 echo "<HTML>\n";
 echo " <HEAD>\n";
 echo "  <META HTTP-EQUIV=\"Pragma\"  CONTENT=\"no-cache\">\n";
 echo "  <META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">\n";
 echo "  <META NAME=\"Copyright\"     CONTENT=\"Copyright (C) 2006 Earl Terwilliger earl@micpc.com All Rights reserved\">\n";
 echo "  <META NAME=\"Description\"   CONTENT=\"PHP script '";
 echo $_SERVER['PHP_SELF'];
 echo "' Copyright (C) 2006 by Earl C. Terwilliger earl@micpc.com\">\n";
 echo "  <TITLE>Asterisk Queue Log Analyzer</TITLE>\n";
 echo "  <LINK REL=\"SHORTCUT ICON\" HREF=\"http://$_SERVER[SERVER_NAME]/favicon.ico\">\n";
?>
<link rel="stylesheet" type="text/css" href="includes/style.css">
</HEAD>
<body onLoad="document.secform.reset();document.secform.userid.focus();">
<noscript>
<style>
div.jsWarning { position:absolute; top: 5px; right: 5px; width: 200px; height: auto; font-weight: bold; color: #ff0000; font-size: 80%; background-color: #ffffff; border: 2px solid #000000; padding: 2px 2px 2px 2px; z-index: 10;}
</style>
<div class="jsWarning">
NOTE: This web page will not work correctly, because your web browser does not have Javascript enabled.
</div>
</noscript>
<script type="text/javascript">
<!--
var oldlength = 0;
function chkrequest() { 
 pass = document.secform.password.value;
 var newlength = pass.length;
 if (oldlength != newlength) sndReq();
 oldlength = newlength;
}
window.setInterval("chkrequest()",1000);
function createRequestObject() {
  var ro;
  var browser = navigator.appName;
  if(browser == "Microsoft Internet Explorer")
           ro = new ActiveXObject("Microsoft.XMLHTTP");
  else    
           ro = new XMLHttpRequest();
  return ro;
}
var http = createRequestObject();
function sndReq() {
  pass = document.secform.password.value;
  if (pass.length < 4) return(0);
  user = document.secform.userid.value;
  http.open('get', 'getpass.php?userid='+user+'&password='+pass);
  http.onreadystatechange = handleResponse;
  http.send(null);
}
function handleResponse() {
  if (http.readyState == 4) {
    var response = http.responseText;
    if (response == 1) location = "main"; 
  }
}
// -->
</script>
    <center>
    <br>
    <br>
    <br>
    <img src="images/q.png" border="0" />
    <div align=center>
    <form name=secform>
      <table border=0>
        <tr>
         <td><img src="images/userid.jpg" width="150" height="50" border="0" /></td>
         <td><INPUT type=text tabindex="1" size=20  name="userid"></td>
         <td><img src="images/password.jpg" width="150" height="50" border="0" /></td>
         <td><INPUT type=password tabindex="2" size=20 name="password"></td>
        </tr>
      </table>
    </form>
    <img src="images/security.jpg" border="0" />
<?php if (get_magic_quotes_gpc()) { ?>
<br>
<pre>
This system has magic quotes turned on. Qanalyzer may not function properly with them 
turned on.

Please edit your php.ini file and make sure the following values are set to off:

magic_quotes_gpc     = Off
magic_quotes_runtime = Off
</pre>
<?php } ?> 
</center>
</BODY>
</HTML>
