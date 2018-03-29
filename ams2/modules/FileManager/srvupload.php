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
include_once("../../lib/sysfunc.php");

$dir=stripslashes_r($_POST['dir']);

if($prog = which('wget')) $progtype="wget";
elseif($prog = which('curl')) $progtype="curl";
elseif(@ini_get('allow_url_fopen')) $progtype="copy";
elseif($prog = which('links')) $progtype="links";
elseif($prog = which('lynx')) $progtype="lynx";

?>
<script>
fm.uploadRemoteFile = function() {
 var file=$('upload_remote_file').value;
 if(!file) return;
 var url = "modules/FileManager/uploadfile.php";
 var pb = "upload_dir="+encodeURIComponent('<?=addslashes($dir)?>')+"&remote_file="+encodeURIComponent(file)+"&new_name="+encodeURIComponent($('new_name').value)+
 "&type=remote&prog=<?=$prog?>&progtype=<?=$progtype?>";
 new Ajax.Request(url,
	    { postBody : pb,
	      onComplete: function(t) {
	        eval(t.responseText);
	      }
	     });

}


</script>
<form name="__upload_form__" id="upload_file_form" method="post"   
 enctype="multipart/form-data" action="modules/FileManager/uploadfile.php" 
 target="fm-service-frame">
 <input type="hidden" name="upload_dir" id="upload_dir" value="<?=$dir?>">
 <input type="hidden" name="type" id="type" value="">
 <input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_file_size?>">


 <table border=0 cellpadding=1 cellspacing=2>
 <tr style="height: 1px;"><td></td></tr>
 <tr>
 <td nowrap width="15%">
 &nbsp;<?=$strUploadLocalFile?>&nbsp;
 </td>
 <td nowrap>
 <input type="file" size="80" name="upload_local_file" id="upload_local_file"  
  value="" >
 <a href="javascript:void(0)" onclick="$('__upload_form__').type.value='local';$('__upload_form__').submit();">
 <img title="<?=$strUpload?>" src="images/run.gif" align="absmiddle">
 </a>
 </td><td>&nbsp;</td> 
 </tr>
 <?if($progtype) {?>
 <tr>
 <td nowrap>
 &nbsp;<?=$strUploadRemoteFile?>&nbsp;
 </td><td width="50%" nowrap>
 <input type="text" size="80" name="upload_remote_file" id="upload_remote_file"  value="" >
 <a href="javascript:void(0)" onclick="fm.uploadRemoteFile();">
 <img title="<?=$strUpload?>" src="images/run.gif" align="absmiddle">
 </a>
 </td><td></td>
 </tr>
 <?}?>
 <tr>
 <td>&nbsp;<?=$strRenameTo?></td>
 <td colspan=3>
 <input type="text" size="40" name="new_name" id="new_name" value="" > 
 <? echo "$strPutToDir&nbsp;<b>$dir</b>";?>
 </td>
 </tr>
 <tr style="height: 1px;"><td></td></tr>
 </table>

</form>

