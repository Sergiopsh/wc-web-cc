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
include_once("../../lang/".$_SESSION['lang'].".lang.php");
include_once("lang/".$_SESSION['lang'].".lang.php");
include_once("../../config.php");
include_once("../../lib/func.php");
include_once("../../lib/Class.datatbl.php");

$theme=$_SESSION['theme'];
$faxes=stripslashes_r($_POST['faxes']);

if(empty($faxes)) die('There is nothing to send...');
$files=explode(",",$faxes);
$ar_file=date("Y-m-d_H-i")."_archive.zip";
foreach($files as $f) {
    $f_size+=filesize($f);
}
$f_size *= 0.92;
if(!isset($subject)) $subject=$strDefaultSubject; 
if(!isset($message)) $message=$strDefaultMessage; 

?>
<script>
vf.validateAndSend = function(){
    var addr = $F('address_to');
    if(!addr) return;
    var url='<?=$www_dir?>/lib/sendemail.php';
    var pb = "files="+encodeURIComponent('<?=addslashes($faxes)?>')+"&address="+encodeURIComponent(addr)+"&subject="+encodeURIComponent($F('subject'))+
    "&body="+encodeURIComponent($F('message'))+"&msg=fax-warning";
    new Ajax.Request(url,{ postBody: pb, 
			   method: 'post',
			   onComplete: function(t) {
			        if(t.responseText.indexOf("success") == -1) 
					ams.showMessage(t.responseText,'fax-warning');
				else ams.showMessage('<?=$strSuccessfullySent?>','fax-warning');
				Element.hide('service-div');
			   }    
			 });
}
</script>
<table border=0 width="100%">
<tr><td align=right>
<div class="module-warning" nowrap>
&nbsp;<b><?=$strAttachedFile?>:</b>&nbsp;
<img src="images/file-zip-16.gif" align="absmiddle">&nbsp;
<?=$ar_file?>&nbsp;~<?=number_format(($f_size/1024),1)?>&nbsp;<?=$strKb?>
</div>
</td></tr>
</table>
<?
$tbl = new DataTbl("100%",0);
$p=$tbl->addElement("address_to","text",$strAddressTo,1,$address_to);
$p->setOptions(35,1,"","email");
$p=$tbl->addElement("message","textarea",$strMessage,1,$message);
$p->setOptions(40,3); $p->rowspan=2;
$p=$tbl->addElement("","button","",1,$strSendEmail);
$p->action="vf.validateAndSend()"; $p->align2="right";
$p=$tbl->addElement("subject","text",$strSubject,2,$subject);
$p->setOptions(35);
$p=$tbl->addElement("","button","",2,$strClose); $p->colspan=3;$p->align2="right";
$p->action="Element.hide('service-div','fax-warning');"; $p->submit=false;
$tbl->show();
exit();