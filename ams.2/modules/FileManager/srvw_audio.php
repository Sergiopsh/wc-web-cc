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

extract(stripslashes_r($_POST));

?>
<script>
fm.trackWriting = function() {
 var url='modules/FileManager/trackchannel.php';
 var pb="channel="+fm.ast_chan+"/"+encodeURIComponent(fm.dial_exten);
    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    var s=t.responseText;
	    if(!$('track_write_msg') || $('fm-service').style.display == 'none' ||
		(s.indexOf("error") != -1)) {
	       clearTimeout(fm.trackTimer);	    
	       return;
	    }
	    if(s.indexOf("completed") != -1) {
	       clearTimeout(fm.trackTimer);
	       $('track_write_msg').innerHTML="Completed...";    
	       fm.hlfiles.push(fm.write_file);
	       fm.showDir(fm.dirName(fm.write_file));
	       return;
	    }
	    $('track_write_msg').innerHTML=s;
	    fm.trackTimer=setTimeout("fm.trackWriting()",1000);
	}
    });
}


fm.writeAudioFile = function(action) {
 if(!$('dial_exten').value) return;
 if(!$('write_file').value) return;
 var file='<?=addslashes($dir)?>/'+$('write_file').value;

 fm.dial_exten = $('dial_exten').value;
 fm.ast_chan = $('ast_chan').value;
 var ext = ($('audio_ext').value == 'wav49') ? 'WAV' : $('audio_ext').value;
 fm.write_file=file+"."+ext;
 var url='modules/FileManager/writefile.php';
 var pb="action="+action+"&file="+encodeURIComponent(file)+"&channel="+fm.ast_chan+"&exten="+
 encodeURIComponent(fm.dial_exten)+"&ext="+$('audio_ext').value;

    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    if(t.responseText.indexOf("success") == -1) 
		fm.showMessage(t.responseText,0,'warning.gif');	    
	    else    fm.trackWriting();
	}
    });
}
</script>




<table border=0>
 <tr style="height: 1px;"><td></td></tr>
 <tr>
 <td nowrap>
 &nbsp;<?=$strWriteFile?>&nbsp;
 </td>
 <td>
 <input type="text" size="15" name="write_file" id="write_file"  value="write" style="font-style: normal;">
 </td><td>
 <select name="audio_ext" id="audio_ext" style="font-style: normal;">
<?
 foreach($audio_exts as $ext) {?>
    <option value="<?=$ext?>"><?=$ext?>
<?}

   ?>
 </select>
</td>


 </td>
 <td><?=$strServiceTel?></td>
 <td>
 <select name="ast_chan" id="ast_chan" style="font-style: normal;">
    <option value="SIP">SIP
    <option value="IAX2">IAX
    <option value="Zap">Zap
 </select>
 </td><td>/
 <input type="text" size="10" name="dial_exten" id="dial_exten"  value="" style="font-style: normal;">
 </td><td>
 <a href="javascript:fm.writeAudioFile('write');">
 <img src="images/run.gif" align="absmiddle">
 </a>

 </td>
 </tr>
 <tr>
 <?/*
 <td>
  <?=$strWriteFileFormat?>
  </td><td>
 <select name="audio_ext" id="audio_ext">
<?
 foreach($audio_exts as $ext) {?>
    <option value="<?=$ext?>"><?=$ext?>
<?}

   ?>
 </select>
 <script>
  // for (var o,i=0; i < fm.callToPhoneAudio.length ; i++) {
    // o=document.createElement('option')
    // o.value=o.text=fm.callToPhoneAudio[i];
    // $('audio_ext').options[i] = o;
  // }
 </script>
 </td>
 */?>
 <td align="left" nowrap colspan="3">
 &nbsp;<? echo "$strToDir&nbsp;<b>$dir</b>";?>
 </td>
 <td id="track_write_msg">

 </td>
 </tr>
 <tr style="height: 1px;"><td></td></tr>
</table>

<script>
  if(fm.ast_chan) $('ast_chan').value=fm.ast_chan;
  if(fm.dial_exten) $('dial_exten').value=fm.dial_exten;
</script>

<?
?>
