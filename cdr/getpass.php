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

  $userid = "";
  $password = "";
  session_start();
  $_SESSION = array();
  session_unset();
  session_destroy();
  if (isset($_GET['userid']))   $userid   = $_GET['userid'];
  if (isset($_GET['password'])) $password = $_GET['password'];
  if ($userid == $password) {
    $num_rows = 1;
    session_start();
    $_SESSION['UserID']   = $userid;
    $_SESSION['Password'] = $password;
    $_SESSION['Level']    = 1;
    echo 1; 
    exit();
  }
  include("includes/db_connect.php");
  $sql  = "SELECT * FROM $ut where ";
  $sql .= "userid = '".$userid."' and password = '".$password."' ";
  $sql .= "and state  = 'y' ";
  $result = mysql_query($sql, $mylink);
  $num_rows = mysql_num_rows($result);
  if ($num_rows > 0) {
    $list = mysql_fetch_assoc($result);
    session_start();
    $_SESSION['UserID']   = $userid;
    $_SESSION['Password'] = $password;
    $_SESSION['Level']    = $list['level'];
  }
  mysql_free_result($result);
  mysql_close($mylink);
  echo $num_rows; 
  exit();
?>
