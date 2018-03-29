<?php
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../lib/func.php");
include_once("../../lib/sysfunc.php");
$tiff=stripslashes_r($_POST['file']);
$pdf=substr($tiff,0,-3)."pdf";
if(!is_file($pdf)) {
    execute(which("tiff2pdf")." -o $pdf -p A4 $tiff");
    if(!is_file($pdf)) { echo "error"; exit(); }
}
echo "success";    
?>
