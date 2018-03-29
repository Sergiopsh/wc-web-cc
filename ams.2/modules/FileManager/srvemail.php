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
include_once("lang/".$_SESSION['lang'].".lang.php");
include_once("../../config.php");
include_once("../../lib/func.php");
//echo "<font color=black>begin ...<br></font>";
$dir=stripslashes_r($_POST['dir']);
?>


<form name="upload_file_form" id="upload_file_form" method="post" onsubmit="alert('test');"  
 enctype="multipart/form-data" action="modules/FileManager/uploadfile.php" 
 target="fm-service-frame">
 <input type="hidden" name="upload_dir" id="upload_dir" value="<?=$dir?>">
 <input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_file_size?>">


 <table border=0>
 <tr style="height: 1px;"><td></td></tr>
 <tr>
 <td width="50%" nowrap>
 &nbsp;<?=$strUploadFile?>&nbsp;
 <input type="file" size="35" name="upload_file" id="upload_file"  value="">
 <a href="#" onclick="$('upload_file_form').submit();">
 <img src="images/run.gif" align="absmiddle">
 </a>
 <td align="left" nowrap>
 <? echo "$strToDir&nbsp;<b>$dir</b>";?>
 </td>
 </tr>
 <tr style="height: 1px;"><td></td></tr>
 </table>

</form>

