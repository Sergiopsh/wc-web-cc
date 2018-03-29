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

 echo '<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>';
 echo "&nbsp;&nbsp;";
 echo "<b>";
 echo $title;
 echo "</b>";
 echo "&nbsp;&nbsp;";
 echo '<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>';
 echo "<br>From " . date('Y-m-d',$start_date) . " To " . date('Y-m-d',$end_date);
 echo "</H1>";

 echo '<table width="100%" cellpadding=2 cellspacing=0 border=1>';
 echo "<tr>";

 echo "<th>id</th>";
 echo "<th>Call Date</th>";
 echo "<th>Src</th>";
 echo "<th>Dst</th>";
 echo "<th>Dst Context</th>";
 echo "<th>Caller Id</th>";
 echo "<th>Channel</th>";
 echo "<th>Dst Channel</th>";
 echo "<th>Last App</th>";
 echo "<th>Last Data</th>";
 echo "<th>Duration</th>";
 echo "<th>Bill secs</th>";
 echo "<th>Disposition</th>";
 echo "<th>AMA Flags</th>";
 echo "<th>Billed</th>";
 echo "<th>Unique Id</th>";
 echo "<th>User Field</th>";
 echo "<th>Account Code</th>";

 echo "</tr><tr></tr>";

  $lcount = 0;
  while($rows < $count) {
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
      $lcount += 1;
      echo '<tr>';
       if ($id != '') 
            echo "<td nowrap>$id</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($calldate != '') 
            echo "<td nowrap>$calldate</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($src != '') 
            echo "<td nowrap>$src</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($dst != '') 
            echo "<td nowrap>$dst</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($dcontext != '') 
            echo "<td nowrap>$dcontext</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($clid != '') 
            echo "<td nowrap>$clid</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($channel != '') 
            echo "<td nowrap>$channel</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($dstchannel != '') 
            echo "<td nowrap>$dstchannel</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($lastapp != '') 
            echo "<td nowrap>$lastapp</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($lastdata != '') 
            echo "<td nowrap>$lastdata</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($duration != '') 
            echo "<td nowrap>$duration</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($billsec != '') 
            echo "<td nowrap>$billsec</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($disposition != '') 
            echo "<td nowrap>$disposition</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($amaflags != '') 
            echo "<td nowrap>$amaflags</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($billed != '') 
            echo "<td nowrap>$billed</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($uniqueid != '') 
            echo "<td nowrap>$uniqueid</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($userfield != '') 
            echo "<td nowrap>$userfield</td>";
       else echo "<td nowrap>&nbsp;</td>";
       if ($accountcode != '') 
            echo "<td nowrap>$accountcode</td>";
       else echo "<td nowrap>&nbsp;</td>";

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
