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
$file=$par[0];

?>

<script>

fm.playToPhone = function(action,file) {
 if(!$('dial_exten').value) return;
 fm.dial_exten = $('dial_exten').value;
 fm.ast_chan = $('ast_chan').value;
 var url='modules/FileManager/playtophone.php';
 var pb="action="+action+"&file="+encodeURIComponent(file)+"&channel="+fm.ast_chan+"&exten="+encodeURIComponent(fm.dial_exten);
    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	}
    });
}
</script>


<table border=0>
 <tr style="height: 1px;"><td></td></tr>
 <tr>
 <td nowrap>
 &nbsp;<?=$strPlayFile?>&nbsp;
 <b><?=basename($file)?></b>
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
 <a href="javascript:fm.playToPhone('call','<?=addslashes(htmlspecialchars($file))?>')">
 <img src="images/run.gif" align="absmiddle">
 </a>

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
