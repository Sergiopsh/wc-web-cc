<?php
/*
 * Asterisk Management System - An open source toolkit for Asterisk PBX.
 * See http://www.asterisk.org for more information about
 * the Asterisk project.
 *
 * Copyright (C) 2006 - 2007, West-Web Limited.
 *
 * Nickolay Shestakov <ns@ampex.ru>
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 */
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../config.php");
include_once("../../lib/func.php");
$conffile=stripslashes_r($_POST['file']);
if(empty($conffile)) exit();?>
<html>
<head>		
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<?
foreach($configfiles_dirs as $cd) {
  if($conffile == $cd[0]) {?>   
    <body>
    <script language="javascript">
      parent.eConf.win=null;
      parent.eConf.tArea=null;
      parent.document.getElementById('ce-frame').style.display="none";
   </script>
   </body>
  </html>
 <?exit();
  }

}

$file=@fopen($conffile,"rb");
$backupfile=@fopen($conffile."~","wb");
?>
<body style="margin: 0px;" 
onload="parent.eConf.win=window; document.getElementById('editor-ta').focus();
parent.eConf.tArea=document.getElementById('editor-ta');">
<textarea name="editor-ta" id="editor-ta"  rows="30" wrap="off" style="border-style: solid; width: 95%;
border-color: #aaaaaa; border-width: 1px; margin: 0px;">
<?
while(!feof($file)) {
    $line=fgets($file);
    echo $line;
    @fwrite($backupfile,$line);
    
}
@fclose($file);
@fclose($backupfile);
?>
</textarea>
</body>
</html>
