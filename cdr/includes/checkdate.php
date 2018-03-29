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

 if (isset($_POST['start_month'])) $start_month = $_POST['start_month']; 
 if (isset($_POST['start_day']))   $start_day = $_POST['start_day']; 
 if (isset($_POST['start_year']))  $start_year = $_POST['start_year']; 
 if (isset($_POST['end_month']))   $end_month = $_POST['end_month']; 
 if (isset($_POST['end_day']))     $end_day = $_POST['end_day']; 
 if (isset($_POST['end_year']))    $end_year = $_POST['end_year']; 

 if ( (isset($_POST['date1'])) && ($_POST['date1'] != "") ) {
   list($start_month, $start_day, $start_year) = split('[/.-]', $_POST['date1']);
 }
 if ( (isset($_POST['date2'])) && ($_POST['date2'] != "") ) {
   list($end_month, $end_day, $end_year) = split('[/.-]', $_POST['date2']);
 }

 if (   !isset($start_month)  || !isset($start_day)  || !isset($start_year)
     || !isset($end_month)    || !isset($end_day)    || !isset($end_year) 
     || $start_month == "00"  || $start_day == "00"  || $start_year == "0000"
     || $end_month   == "00"  || $end_day   == "00"  || $end_year == "0000") {

    echo '<H1><a href=';
    echo $_SERVER['PHP_SELF'];
    echo '><img src=../images/q-logo.png border=0></img></a>';
    echo "&nbsp;&nbsp;";
    echo '<a href=../cdr><img src=../images/cdr-logo.png border=0></img></a>';
    echo "</H1>";
    echo "<br>Starting or Ending Date not specified!<br>";
    echo "</center>";
    echo "</BODY>";
    echo "</HTML>";
    exit();
 }
 $start_date = $start_year . "-" . $start_month . "-" . $start_day;
 $end_date   = $end_year . "-" . $end_month . "-" . $end_day;
 $start_date = strtotime($start_date);
 $end_date   = strtotime($end_date);
?>
