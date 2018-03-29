<?php
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../lib/func.php");
$file_tiff=stripslashes_r($_GET['file']);
$file_pdf=substr($file_tiff,0,-3)."pdf";
?>
<html>
<head>
<title>Look PDF-file <?=basename($file_pdf)?></title>
</head>
<body style="margin: 0;">
<iframe frameborder=0 style="width: 100%; height: 100%;"
 src="../../lib/loadfile.php?file=<?=$file_pdf?>&type=pdf">
</iframe>
</body>
</html>
