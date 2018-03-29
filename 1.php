<?php
$username="root";
$passwd="";
$dbname="asteriskcdrdb";
$hostname="localhost";
@mysql_connect ($hostname, $username, $passwd) or
       die ("Cannot connect to database...");
  @mysql_select_db($dbname)
or die("Could not select products database!");
$vopr=mysql_query("select * from cdr") or
       die ("No way. Can't select.");
   while ($line = mysql_fetch_array ($vopr))
       {
         print "<hr>";
           extract($line);
     print"$calldate,$src,$dst,$lastapp,$lastdata,$duration,$billsec,$disposition";
     }
       print "<hr>";
    mysql_close();
?>