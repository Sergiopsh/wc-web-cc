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

//Fields of the CDR in Asterisk
//-----------------------------
//
//   1. accountcode: What account number to use, (string, 20 characters)
//   2. src: Caller*ID number (string, 80 characters)
//   3. dst: Destination extension (string, 80 characters)
//   4. dcontext: Destination context (string, 80 characters)
//   5. clid: Caller*ID with text (80 characters)
//   6. channel: Channel used (80 characters)
//   7. dstchannel: Destination channel if appropriate (80 characters)
//   8. lastapp: Last application if appropriate (80 characters)
//   9. lastdata: Last application data (arguments) (80 characters)
//  10. start: Start of call (date/time)
//  11. answer: Answer of call (date/time)
//  12. end: End of call (date/time)
//  13. duration: Total time in system, in seconds (integer), from dial to hangup
//  14. billsec: Total time call is up, in seconds (integer), from answer to hangup
//  15. disposition: What happened to the call: ANSWERED, NO ANSWER, BUSY
//  16. amaflags: What flags to use: DOCUMENTATION, BILL, IGNORE etc, 
//      specified on a per channel basis like accountcode.
//  17. user field: A user-defined field, maximum 255 characters 

if (isset($_GET['pattern'])) $pattern = $_GET['pattern'];
else $pattern = "";

$logfile = "/var/log/asterisk/cdr-csv/Master.csv";

echo "<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>";
echo "&nbsp;&nbsp;";
echo "<b>";
echo "CDR RECORDS [Master.csv] LIST";
echo "</b>";
echo "&nbsp;&nbsp;";
echo "<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>";
echo "<br>Records Containing Search String: $pattern";
echo "</H1>";

$fname = $logfile;
$fd = fopen ($fname, "r");
if (!$fd) {
 echo "<br><br>";
 echo "Error opening $fname"; 
 echo "</body></html>";
 exit(0);
}
echo '<table width="100%" cellpadding=2 cellspacing=0 border=1>';
echo "<tr>";
echo "<th>Account</th>";
echo "<th>Src</th>";
echo "<th>Dst</th>";
echo "<th>Dst Context</th>";
echo "<th>Caller ID</th>";
echo "<th>Channel</th>";
echo "<th>Dst Channel</th>";
echo "<th>Last App</th>";
echo "<th>Last Data</th>";
echo "<th>Start</th>";
echo "<th>Answer</th>";
echo "<th>End</th>";
echo "<th>Duration</th>";
echo "<th>Bill Secs</th>";
echo "<th>Disposition</th>";
echo "<th>AMA flags</th>";
echo "<th>User Field</th>";
echo "</tr><tr></tr>";
$limit  = 0;
$d = 0;
$recs = 0;
while (!feof ($fd)) {
  $buffer = fgets($fd, 4096);
  $l = trim($buffer);
  if ($pattern != "") {
    if (!strstr($l,$pattern)) continue;
  }
  $recs += 1;
  $badcommapat = '/\"[^\"]+,[^\",]+\"/';
  if (preg_match($badcommapat,$l,$matches)) {
      $fixcomma = str_replace(",","-",$matches[0]);
      $l = str_replace($matches[0],$fixcomma,$l);
  }
  $e = explode(",",$l);
  $len = sizeof($e);
  echo "<tr>";
  for ($c=0;$c<$len;++$c) {
    echo "<td nowrap>";
    $e[$c] = trim($e[$c],"\r\n \"");
    if ($c == 4) $e[$c] = str_replace ("\"", "", $e[$c]);
    if ($e[$c] == "") echo "&nbsp;";
    else echo htmlspecialchars($e[$c]);
    echo "</td>";
  }
  while ($c < 17) { echo "<td>&nbsp;</td>"; ++$c; }
  echo "</tr>\n";
  flush();
  ++$d;
  if ($limit != 0) {
      if ($d >= $limit) break;
  }
}
echo "</table>";
echo "<br><br>Total Record Count: $recs";
fclose ($fd); 
echo "</body></html>";
?>
