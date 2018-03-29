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

 $title      = $_POST['title'];
 $sql        = $_POST['query'];
 $start_date = $_POST['start'];
 $end_date   = $_POST['end'];

 include("../includes/header.php");
 include("../includes/db_connect.php");

 $result = mysql_query($sql);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) {
     echo "<br>" . $erno . ": " . $err . "<br>\n";
 }
 $count = mysql_num_rows($result);
 $numfields = mysql_num_fields($result);
 $rows = 0;
 $recs = 0;

 echo "<center>";

 echo '<H1><a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>';
 echo "&nbsp;&nbsp;";
 echo "<b>";
 echo $title;
 echo "</b>";
 echo "&nbsp;&nbsp;";
 echo '<a href=../cdr><img src=../images/cdr-logo.png border=0></img></a>';
 echo "<br>";
 echo "From " . date('Y-m-d',$start_date) . " To " . date('Y-m-d',$end_date);
 echo "</H1>";

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
