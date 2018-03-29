<?php
include_once("../config.php");
include_once("../lib/iam_csvdump.php");


session_start();


  #  Set the parameters: SQL Query, hostname, databasename, dbuser and password                                       #
  #####################################################################################################################
  $dumpfile = new iam_csvdump;

  #  Call the CSV Dumping function and THAT'S IT!!!!  A file named dump.csv is sent to the user for download          #
  #####################################################################################################################

if (strlen($_SESSION["sql_export"])<10){
		echo "ERROR CSV EXPORT";
}else{
    $pref=$_GET['pref_export'];
    $dumpfile->dump($_SESSION["sql_export"], "$pref". date("Y-m-d"), "csv", $db_name, $db_user, $db_pass, $db_host, $db_type );
}

?>
