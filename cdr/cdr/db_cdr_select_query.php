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
 include("../includes/db_connect.php");
 GLOBAL $NUMOPS, $PREVOP;
 $NUMOPS = 0;
 $PREVOP = "";
 include("../includes/functions.php");

// while (list($key,$val) = each($_POST)) {
//  echo $key,"=",$val,"<br>";
// }

  $billed = $_POST['billed'];
  $op_billed = $_POST['op_billed'];
  $andor_billed = $_POST['andor_billed'];
  $uniqueid = $_POST['uniqueid'];
  $op_uniqueid = $_POST['op_uniqueid'];
  $andor_uniqueid = $_POST['andor_uniqueid'];
  $userfield = $_POST['userfield'];
  $op_userfield = $_POST['op_userfield'];
  $andor_userfield = $_POST['andor_userfield'];
  $accountcode = $_POST['accountcode'];
  $op_accountcode = $_POST['op_accountcode'];
  $andor_accountcode = $_POST['andor_accountcode'];
  $src = $_POST['src'];
  $op_src = $_POST['op_src'];
  $andor_src = $_POST['andor_src'];
  $dst = $_POST['dst'];
  $op_dst = $_POST['op_dst'];
  $andor_dst = $_POST['andor_dst'];
  $dcontext = $_POST['dcontext'];
  $op_dcontext = $_POST['op_dcontext'];
  $andor_dcontext = $_POST['andor_dcontext'];
  $clid = $_POST['clid'];
  $op_clid = $_POST['op_clid'];
  $andor_clid = $_POST['andor_clid'];
  $channel = $_POST['channel'];
  $op_channel = $_POST['op_channel'];
  $andor_channel = $_POST['andor_channel'];
  $dstchannel = $_POST['dstchannel'];
  $op_dstchannel = $_POST['op_dstchannel'];
  $andor_dstchannel = $_POST['andor_dstchannel'];
  $lastapp = $_POST['lastapp'];
  $op_lastapp = $_POST['op_lastapp'];
  $andor_lastapp = $_POST['andor_lastapp'];
  $lastdata = $_POST['lastdata'];
  $op_lastdata = $_POST['op_lastdata'];
  $andor_lastdata = $_POST['andor_lastdata'];
  $calldate = $_POST['calldate'];
  $op_calldate = $_POST['op_calldate'];
  $andor_calldate = $_POST['andor_calldate'];
  $duration = $_POST['duration'];
  $op_duration = $_POST['op_duration'];
  $andor_duration = $_POST['andor_duration'];
  $billsec = $_POST['billsec'];
  $op_billsec = $_POST['op_billsec'];
  $andor_billsec = $_POST['andor_billsec'];
  $disposition = $_POST['disposition'];
  $op_disposition = $_POST['op_disposition'];
  $andor_disposition = $_POST['andor_disposition'];
  $amaflags = $_POST['amaflags'];
  $op_amaflags = $_POST['op_amaflags'];
  $andor_amaflags = $_POST['andor_amaflags'];


$query = "select *  from $ct  where ";
$query = mk_query($query,$billed,"billed",$op_billed,$andor_billed);
$query = mk_query($query,$uniqueid,"uniqueid",$op_uniqueid,$andor_uniqueid);
$query = mk_query($query,$userfield,"userfield",$op_userfield,$andor_userfield);
$query = mk_query($query,$accountcode,"accountcode",$op_accountcode,$andor_accountcode);
$query = mk_query($query,$src,"src",$op_src,$andor_src);
$query = mk_query($query,$dst,"dst",$op_dst,$andor_dst);
$query = mk_query($query,$dcontext,"dcontext",$op_dcontext,$andor_dcontext);
$query = mk_query($query,$clid,"clid",$op_clid,$andor_clid);
$query = mk_query($query,$channel,"channel",$op_channel,$andor_channel);
$query = mk_query($query,$dstchannel,"dstchannel",$op_dstchannel,$andor_dstchannel);
$query = mk_query($query,$lastapp,"lastapp",$op_lastapp,$andor_lastapp);
$query = mk_query($query,$lastdata,"lastdata",$op_lastdata,$andor_lastdata);
$query = mk_query($query,$calldate,"calldate",$op_calldate,$andor_calldate);
$query = mk_query($query,$duration,"duration",$op_duration,$andor_duration);
$query = mk_query($query,$billsec,"billsec",$op_billsec,$andor_billsec);
$query = mk_query($query,$disposition,"disposition",$op_disposition,$andor_disposition);
$query = mk_query($query,$amaflags,"amaflags",$op_amaflags,$andor_amaflags);
$query = $query . " ORDER BY calldate";
$query = stripslashes($query);

 echo "<center>";

 echo "<H1><a href=index.php><img src=../images/cdr-logo.png border=0></img></a>";
 echo "&nbsp;&nbsp;";
 echo "<b>";
 echo "Selected CDR Records List";
 echo "</b>";
 echo "&nbsp;&nbsp;";
 echo "<a href=../main/index.php><img src=../images/q-logo.png border=0></img></a>";
 echo "</H1>";

 echo "$query<br><br>";

 $result = mysql_query($query);
 $erno = mysql_errno();
 $err  = mysql_error();
 if ($erno <> 0) {
   echo "<br>" . $erno . ": " . $err . "<br>\n";
   echo"</center></BODY></HTML>";
   exit();
 }
 $count = mysql_num_rows($result);
 if ($count == 0) {
   echo "<br><br><br><br>";
   echo "<H1>No Data Records match the selected options!</H1>";
   echo "</center></BODY></HTML>";
   exit();
 }

 $numfields = mysql_num_fields($result);
 $rows = 0;
 $recs = 0;

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
    $duration = $list['duration'];
    $billsec = $list['billsec'];
    $disposition = $list['disposition'];
    $amaflags = $list['amaflags'];

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
       $rows++;
  }

 echo '</table>';
 echo '<br><br>Total Record Count: ' . $lcount . '<br>';
 mysql_close($mylink);
?>
</center>
</BODY>
</HTML>
