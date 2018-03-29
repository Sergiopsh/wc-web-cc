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
     <td><a href=../><img src=../images/cdr-logo.png border=0></img></a></td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td><H1>CDR ANALYZER<br>MAIN MENU</H1></td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td><a href=../main><img src=../images/q-logo.png border=0></img></a></td>
   </tr>
  </table>
  <br>
  <table bgcolor=#C1E0F2 cellpadding=2 cellspacing=2 border=1>

<?php
 if ($_SESSION['Level'] == 1)  {                                     
  echo "<tr align=center valign=top>";
  echo "<td align=center>MASTER.CSV</td>";
  echo "<td align=center>DATABASE</td>";
  echo "</tr>";

  echo "<tr align=center valign=top>";
  echo "<td align=left>";
?>
<a href="javascript:void(q=prompt('SEARCH CDR RECORDS [Enter a search string]',''));if(q)location.href='cdr.php?pattern='+escape(q)">Search CDR RECORDS </a>
<?PHP
  echo "</td>";
  echo "<td align=left><a href=db_cdr_select.php>Search CDR RECORDS</a></td>";
  echo "</tr>";

  echo "<tr align=center valign=top>";
  echo "<td align=left><a href=cdrdate.php>List CDR Records by Date</a></td>";
  echo "<td align=left><a href=db_cdr_list.php>List CDR Records by Date</a></td>";
  echo "</tr>";

  echo "<tr align=center valign=top>";
  echo "<td align=left>&nbsp;</td>";
  echo "<td align=left>&nbsp;</td>";
  echo "</tr>";

  echo "<tr align=center valign=top>";
  echo "<td align=left>&nbsp;</td>";
  echo "<td align=left><a href=gen_chart.php>Graph CDR Records by Date</a></td>";
  echo "</tr>";
 }
 else {
 }
?>
  </table>
  </center>
</BODY>
</HTML>
