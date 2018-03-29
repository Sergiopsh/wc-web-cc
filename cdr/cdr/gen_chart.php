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
 $function_title      = "CDR Records";
 $function_title_long = $function_title;
 $sql                 = "SELECT * from cdr";
 $function_key_th     = "Date / Extension";
 $function_val_th     = "Records";

 if (!isset($_POST['submit'])) {
   echo '<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>';
   echo "&nbsp;&nbsp;";
   echo "<b>$function_title Date Select</b>";
   echo "&nbsp;&nbsp;";
   echo '<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>';
   echo '</H1>';
   include("../includes/getdate.php");
   echo "</center>\n</BODY>\n</HTML>\n";
   exit();
 }
 include("../includes/checkdate.php");
 include("../includes/db_connect.php");

 echo "<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>";
 echo "&nbsp;&nbsp;";
 echo "<b>";
 echo $function_title;
 echo "</b>";
 echo "&nbsp;&nbsp;";
 echo "<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>";
 echo "<br>";
 echo "From " . date('Y-m-d',$start_date) . " To " . date('Y-m-d',$end_date);
 echo "</H1>";

 $header  = "$function_title From " . date('Y-m-d',$start_date) . " To ";
 $header .=  date('Y-m-d',$end_date);
 $xt       = urlencode($function_title_long);
 $qdata    = array();
 $title    = urlencode(serialize(array($function_title_long,12)));
 $subtitle = urlencode(serialize(array($header,8)));

 include("../includes/db_connect.php");

 $result = mysql_query($sql);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) {
     echo "<br>" . $erno . ": " . $err . "<br>\n";
 }
 $count = mysql_num_rows($result);
 $rows = 0;
 $recs = 0;

 if ($count > 0) {
   while($rows < $count) {
     $rows++;
     $list = mysql_fetch_assoc($result);
     $id = $list['id'];
     $billed = $list['billed'];
     $uniqueid = $list['uniqueid'];
     $userfield = $list['userfield'];
     $accountcode = $list['accountcode'];
     $src = $list['src'];
     $dst = $list['dst'];
     $dcontext = $list['dcontext'];
     $clid = $list['clid'];
     $channel = $list['channel'];
     $dstchannel = $list['dstchannel'];
     $lastapp = $list['lastapp'];
     $lastdata = $list['lastdata'];
     $calldate = $list['calldate'];
     $timestamp = strtotime($calldate);
     $duration = $list['duration'];
     $billsec = $list['billsec'];
     $disposition = $list['disposition'];
     $amaflags = $list['amaflags'];

     if (($timestamp >= $start_date) && ($timestamp <= $end_date)) {
       $date   = date('m-d',$timestamp);
       $key    = date('D',$timestamp) . "\n" . $date;
       if (isset($qdata[$key]))  $qdata[$key] += 1;  
       else                      $qdata[$key]  = 1;  
       $recs += 1;
     }
   }
   if ($recs > 0) {
     $data  = urlencode(serialize($qdata));
     echo "<img src=../includes/bar_chart.php?xt=$xt&data=$data&ti=$title&st=$subtitle></img>";
   }
 }
 mysql_close($mylink);

 if ($recs > 0) {
   echo "<H1>SUMMARY</H1>";
   echo "<table><th>$function_key_th</th><th>$function_val_th</th>";
   while (list($key,$val) = each($qdata)) {
     echo "<tr>";
     echo "<td align=left>"  . $key . "</td>";
     echo "<td align=right>" . $val . "</td>";
     echo "<tr>";
   }
   echo "</table>";
   echo '<br>';
 
   echo "<table>";
   echo "<tr>";
   echo "<td>Total Record Count: $recs <td>";
   echo "<td>&nbsp;&nbsp;&nbsp;<td>";
   echo "<td>";
     include("details.php");
   echo "</td>";
   echo "</tr>";
   echo "</table>";
 }
 else { 
   echo "<br><br><br><br>";
   echo "<H1>No Data Available for the time period selected!</H1>";
 }

 echo "</center>";
 echo "</body>";
 echo "</html>";
?>
