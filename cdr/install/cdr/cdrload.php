<?PHP
  $locale_db_host  = 'localhost';
  $locale_db_name  = 'asterisk';
  $locale_db_login = 'asteriskuser';
  $locale_db_pass  = 'asterisk';
  $logfile = "/var/log/asterisk/cdr-csv/Master.csv";
//  $logfile = "Master.csv";
  $linkmb = mysql_connect($locale_db_host, $locale_db_login, $locale_db_pass) 
       or die("Could not connect : " . mysql_error());
  mysql_select_db($locale_db_name, $linkmb)
       or die("Could not select database $locale_db_name");
  $sql="SELECT UNIX_TIMESTAMP(calldate) as calldate FROM cdr ORDER BY calldate DESC LIMIT 1";
  
  if(!($result = mysql_query($sql, $linkmb))) {
     print("Invalid query: " . mysql_error()."\n");
     print("SQL: $sql\n"); 
     die();
  }
  $result_array = mysql_fetch_array($result);
  $lasttimestamp = $result_array['calldate'];
  $rows = 0;
  $handle = fopen($logfile, "r");
  while ( ($data = fgetcsv($handle, 1000, ",") ) !== FALSE) {
    list($accountcode, $src, $dst, $dcontext, $clid, $channel, $dstchannel, $lastapp,
         $lastdata, $start, $answer, $end, $duration, $billsec, $disposition,
         $amaflags ) = $data;
    if (strtotime($end) > $lasttimestamp) {
      $sql = "INSERT INTO cdr (calldate, clid, src, dst, dcontext, channel, dstchannel, lastapp, lastdata, duration, billsec, disposition, amaflags, accountcode) VALUES('$end', '".mysql_real_escape_string($clid)."', '$src', '$dst', '$dcontext', '$channel', '$dstchannel', '$lastapp', '$lastdata', '$duration', '$billsec','$disposition', '$amaflags', '$accountcode')";
      if(!($result2 = mysql_query($sql, $linkmb))) {
        print("Invalid query: " . mysql_error()."\n");
        print("SQL: $sql\n");
        die();
      }
      $rows++;
    }
  }
  fclose($handle);
  print("$rows imported\n");
?>
