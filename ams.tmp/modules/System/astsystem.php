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

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
$show_action_time=false;
?>
<div id="frame-module-header" nowrap>
<?=$strAsteriskCLI?>
</div>
<br>
<script>
ams.showReloadLink();

if(!GlobalSystem.astcliHistory) GlobalSystem.astcliHistory = new Array();
<?
include("js/stack.js");
?>
astSys = new ObjectD();

astSys.stack = new _Stack(GlobalSystem.astcliHistory,Array('astcli','ast-response'),{
	       fImgId	: 'forwardAstSysImg',
	       bImgId	: 'backAstSysImg',
	       fImgOff	: 'images/forward_off.gif',
	       fImgOn	: 'images/forward2.gif',
	       bImgOff	: 'images/back_off.gif',
	       bImgOn	: 'images/back2.gif'
	    });


astSys.executeCommand=function(command){
    if(command=="") return;
    var url='modules/System/astcommand.php';
    var pb="Action="+encodeURIComponent(command);
    new Ajax.Request(url,
		{postBody: pb,
		 onComplete: function(t) {
		    if(t.responseText) {
			Element.update('ast-response',t.responseText);
			astSys.stack.store(Array(command,t.responseText));
			//$('astcli').value='';
		    }	
		 }
		});
    
    
}



astSys.showAsteriskInfo=function(){
    var action="core show version";
    var url='modules/System/astcommand.php';
    var pb="Action="+encodeURIComponent(action);
    new Ajax.Request(url,
		{postBody: pb,
		 onComplete: function(t) {
		    if(t.responseText) 
			Element.update('ast-info',t.responseText);
			}
		});
}


</script>

<table border=0 cellpadding=0 cellspacing=0 width="50%">
<tr height="16">
<td nowrap style="font-weight: 700; font-size: 11px; font-family: verdana,sans;background-color: #d5d5d5;">
<?=$strAsteriskInfo?>
&nbsp;
</td></tr>
</table>
<div id="ast-info" style="border: 1px solid #dddddd;margin-top: 0px;
height: 50px; width: 95%; font-family: courier new,monospace; font-size: 12px;
background-color: #e9e9e9;">
</div>


<br>
<table border=0 cellpadding=0 cellspacing=0 width="50%">
<tr height="16" style="background-color: #d5d5d5;">
<td width="30%" valign="center" nowrap style="font-size: 11px; font-family: verdana,sans;background-color: #d5d5d5;padding-right: 3px;">
<b>[<?=$strAsteriskCLI?>]#&nbsp;</b>
<input type="text" name="astcli" id="astcli" size="40">
</td>
<td align="left" nowrap>
<a href="javascript:void(0)" title="<?=$strExecute?>" onclick="astSys.executeCommand($('astcli').value);" style="text-decoration: none;">
<img align="absmiddle" src="images/run.gif">
</a>
<a href="javascript:void(0)" title="<?=$strClear?>" onclick="$('ast-response').innerHTML='<br><br><br>'; $('astcli').value='';" 
style="text-decoration: none;">
<img align="absmiddle" src="images/close.gif">
</a>
<a href="javascript:astSys.stack.back()" title="<?=$strBack?>" 
style="text-decoration: none;">
<img align="absmiddle" id="backAstSysImg" src="images/back_off.gif">
</a>
<a href="javascript:astSys.stack.forward()" title="<?=$strForward?>" 
style="text-decoration: none;">
<img align="absmiddle" id="forwardAstSysImg" src="images/forward_off.gif">
</a>
</td>
<td width="20%">&nbsp;
</td>
</tr></table>
</div>
<div id="ast-response" style="border: 1px solid #dddddd;margin-top: 0px;
width: 95%; font-family: courier new,monospace; font-size: 12px;
background-color: #e9e9e9;"><br><br><br>
</div>
<br>
<script>
 astSys.showAsteriskInfo();
 astSys.stack.update();
</script>
