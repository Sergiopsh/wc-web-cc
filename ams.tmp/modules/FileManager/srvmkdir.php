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
include_once("../../lib/func.php");

$dir=stripslashes_r($_POST['dir']);
?>
<script>

fm.makeDir = function() {

 var newdir=$('make_dir_div_input').value;
 if(!fm.isValidDir(newdir)) {ams.toolTip('make_dir_div_input',0,'<?=$strNotValidDir?>',{img: 'warning.gif',hlight: 1}); return; }
 var url='modules/FileManager/mkdir.php';
 var pb="dir="+encodeURIComponent(newdir);
    new Ajax.Request(url,{
	postBody: pb,
	onComplete: function(t) {
	    if(t.responseText == "success") fm.postMkDir('<?=addslashes($dir)?>',newdir);
	    else ams.showMessage(t.responseText,'filemanager-message',1,'warning.gif');	    
	}
    });
}
</script>

<table border=0>
    <tr style="height: 1px;"><td></td></tr>
    <tr>
    <td width="50%" nowrap>
    <?=$strMakeDir?>&nbsp;
    <input type="text" size="45" id='make_dir_div_input' value="<?=hc($dir)?>" style="font-style: normal;">
    <a href="javascript:fm.makeDir();">
    <img src="images/run.gif" align="absmiddle">
    </td>
    </tr>
    <tr style="height: 1px;"><td></td></tr>
</table>

