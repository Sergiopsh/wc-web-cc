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

 include("../includes/header.php");
?>
<SCRIPT LANGUAGE="JavaScript"  SRC="../javascript/CalendarPopup.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript"> var cal = new CalendarPopup();       </SCRIPT>
<center>
<?PHP
 if (!isset($_POST['submit'])) {
   echo '<H1><a href=index.php><img src=../images/q-logo.png border=0></img></a>';
   echo "&nbsp;&nbsp;";
   echo '<b>QUEUE LOG LIST DATE SELECT</b>';
   echo "&nbsp;&nbsp;";
   echo '<a href=../cdr><img src=../images/cdr-logo.png border=0></img></a>';
   echo '</H1>';
   include("../includes/getdate.php");
   echo "</center>\n</BODY>\n</HTML>\n";
   exit();
 }
 include("../includes/checkdate.php");
 include("../includes/db_connect.php");

 $query = "SELECT * FROM $lt ;";
 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) {
     echo "<br>" . $erno . ": " . $err . "<br>\n";
 }
 $count = mysql_num_rows($result);
 $numfields = mysql_num_fields($result);
 $rows = 0;
 $recs = 0;

//
//ABANDON(position|origposition|waittime)
//AGENTDUMP
//AGENTLOGIN(channel)
//AGENTCALLBACKLOGIN(exten@context)
//AGENTLOGOFF(channel|logintime)
//AGENTCALLBACKLOGOFF(exten@context|logintime|reason)
//COMPLETEAGENT(holdtime|calltime|origposition)
//COMPLETECALLER(holdtime|calltime|origposition)
//CONFIGRELOAD
//CONNECT(holdtime)
//ENTERQUEUE(url|callerid)
//EXITWITHKEY(key|position)
//EXITWITHTIMEOUT(position)
//QUEUESTART
//SYSCOMPAT
//TRANSFER(extension,context)
//

 echo '<H1><a href=index.php><img src=../images/q-logo.png border=0></img></a>';
 echo "&nbsp;&nbsp;";
 echo "<b>QUEUE LOG LIST</b>";
 echo "&nbsp;&nbsp;";
 echo '<a href=../cdr><img src=../images/cdr-logo.png border=0></img></a>';
 echo "<br><b>From " . date('Y-m-d',$start_date) . " to " . date('Y-m-d',$end_date);
 echo "</b></H1>";

 echo '<table width="100%" cellpadding=0 cellspacing=0 border=1>';
 echo '<tr>';
 echo "<th>Date</th>";
 for ($c=0;$c<$numfields;++$c) {
   echo '<th>';  echo strtoupper(mysql_field_name($result,$c));  echo '</th>';
 }
 echo '</tr>';

  $lcount = 0;
  while($rows < $count) {
    $list = mysql_fetch_assoc($result);
    $timestamp = $list['timestamp'];
    if (($timestamp >= $start_date) && ($timestamp <= $end_date)) {
      $lcount += 1;
      echo '<tr>';
      echo "<td>" . date('Y-m-d H:i:s',$timestamp) . "</td>";
      for($c=0;$c<$numfields;++$c) {
        echo '<td>';
        if ($list[mysql_field_name($result,$c)] == "") echo "&nbsp;";
        else echo $list[mysql_field_name($result,$c)];
        echo '</td>';
      }
      echo '</tr>';
    }
    $rows++;
  }

 echo '</table>';
 echo '<br><br>Total Record Count: ' . $lcount . '<br>';
 mysql_close($mylink);
?>
</center>
</BODY>
</HTML>
