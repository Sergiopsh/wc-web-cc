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
  <center>

  <table>
   <tr>
     <td><a href=../><img src=../images/q-logo.png border=0></img></a></td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td><H1>QUEUE ANALYZER<br>MAIN MENU</H1></td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td><a href=../cdr><img src=../images/cdr-logo.png border=0></img></a></td>
   </tr>
  </table>
  <br>
  <table bgcolor=#C1E0F2 cellpadding=2 cellspacing=2 border=1>

<?php
 if ($_SESSION['Level'] == 1)  {                                     
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=calls_completed.php>CALLS COMPLETED [ALL]</a></td>";
  echo "<td align=left><a href=syscompat.php>CALLS DROPPED [INCOMPATABILITY]</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=calls_abandoned.php>ABANDONED CALLS</a></td>";
  echo "<td align=left><a href=calls_transfered.php>TRANSFERED CALLS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=caller_completed.php>CALLER COMPLETED CALLS</a></td>";
  echo "<td align=left><a href=calls_exited.php>CALLS EXITED</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=agent_completed.php>AGENT COMPLETED CALLS</a></td>";
  echo "<td align=left><a href=agent_dump.php>AGENT DUMPED CALLS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=agent_login.php>AGENT LOGINS</a></td>";
  echo "<td align=left><a href=agent_logoff.php>AGENT LOGOFFS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=agent_callbacklogin.php>CALL BACK AGENT LOGINS</a></td>";
  echo "<td align=left><a href=agent_callbacklogin.php>CALL BACK AGENT LOGOFFS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=configreload.php>CONFIGURATION RELOADS</a></td>";
  echo "<td align=left><a href=connects.php>CONNECTED CALLS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=enterqueue.php>CALLS ENTERING QUEUE</a></td>";
  echo "<td align=left><a href=queuestarts.php>QUEUE STARTS</a></td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left>&nbsp;</td>";
  echo "<td align=left>&nbsp;</td>";
  echo "</tr>";
  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=queuelogformats.php>QUEUE LOG RECORD FORMATS</a></td>";
  echo "<td align=left><a href=queue_log_list.php>QUEUE LOG LIST</a></td>";
  echo "</tr>";
 }
 else {
 }
?>
  </table>
  </center>
</BODY>
</HTML>
