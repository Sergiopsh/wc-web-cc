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
$df_opt="h";
?>
<div id="frame-module-header" nowrap>
<?=$strSystem?>
</div>
<br>
<script>
ams.showReloadLink();

if(!GlobalSystem.cmdHistory) GlobalSystem.cmdHistory = new Array();

<?
include("js/stack.js");
?>




sys = new ObjectD();

sys.stack = new _Stack(GlobalSystem.cmdHistory,Array('shell','shell-response'),{
	       fImgId	: 'forwardCmdImg',
	       bImgId	: 'backCmdImg',
	       fImgOff	: 'images/forward_off.gif',
	       fImgOn	: 'images/forward2.gif',
	       bImgOff	: 'images/back_off.gif',
	       bImgOn	: 'images/back2.gif'
	    });




sys.executeShell=function(command) {
    if(command=="") return;
    var url="modules/System/shell.php";
    var pb="Action="+encodeURIComponent(command);
    new Ajax.Request(url,
	    { postBody: pb, 
	      onFailure: function(t){},
	      onComplete: function(t){
	        if(t.responseText) {
		    Element.update('shell-response',t.responseText)
	            sys.stack.store(Array(command,t.responseText));
		    //$('shell').value='';
		}
	      }
	    });
}
sys.systemInfo=function(){
    $('system-info').style.display='inline';
    $('hide-icon').src="images/minus2.gif";
    var url="modules/System/systeminfo.php";
    var pb='';
    if($('free_opt')) pb="free_opt="+encodeURIComponent($('free_opt').value);
    if($('df_opt')) pb+="&df_opt="+encodeURIComponent($('df_opt').value);
    new Ajax.Request(url,
	    { postBody: pb,
	      onFailure: function(t){},
	      onComplete: function(t){
	        if(t.responseText) 
		    Element.update('system-info',t.responseText);
	      }
	    });
}
sys.toggleInfo=function(){
 if($('system-info').style.display=='none'){
    $('system-info').style.display='inline';
    $('hide-icon').src="images/minus2.gif";
    $('hide-icon').title="<?=$strHide?>";
 }else{
    $('system-info').style.display='none';
    $('hide-icon').src="images/plus2.gif";
    $('hide-icon').title="<?=$strShow?>";
 }
}
</script>

<table border=0 cellpadding=0 cellspacing=0 width="65%">
<tr height="18">
<td nowrap style="font-weight: 700; font-size: 11px; font-family: verdana,sans;background-color: #d5d5d5;">
<?=$strSystemInfo?>
&nbsp;
<a href="javascript:sys.systemInfo();" 
style="text-decoration: none; border: 0;">
<img align="absmiddle" src="images/refresh2.gif" title="<?=$strRefreshInfo?>">
</a>
<a href="javascript:sys.toggleInfo()" style="text-decoration: none;">
<img align="absmiddle" id="hide-icon" src="images/minus2.gif" title="<?=$strHide?>">
&nbsp;
</a>
</td></tr>
</table>
<div id="system-info" 
style="height: 50px; display: none;background-color: #e9e9e9; 
font-family: courier new,monospace; font-size: 13px;
border: 1px solid #dddddd;">
</div>

<br>
<table border=0 cellpadding=0 cellspacing=0 width="65%">
<tr height="18" style="background-color: #d5d5d5; ">
<td width="30%" valign="center" nowrap style="font-size: 11px; font-family: verdana,sans;background-color: #d5d5d5;padding-right: 3px;">
<b>[<?=$strShell?>]#</b>&nbsp;

<input type="text" name="shell" id="shell" size="55">
</td><td nowrap>
<a href="javascript:sys.executeShell($('shell').value);" style="text-decoration: none;border: 0px;">
<img align="absmiddle" src="images/run.gif" title="<?=$strExecute?>">
</a>
<a href="javascript:void(0)" onclick="$('shell-response').innerHTML='<br><br><br>'; $('shell').value=''; " 
style="text-decoration: none;">
<img style="margin-bottom: 1px;" align="absmiddle" src="images/close.gif" title="<?=$strClear?>">
</a>

<a href="javascript:sys.stack.back()" 
style="text-decoration: none;">
<img align="absmiddle" id="backCmdImg" src="images/back2.gif" title="<?=$strBack?>">
</a>

<a href="javascript:sys.stack.forward()" 
style="text-decoration: none;">
<img align="absmiddle" id="forwardCmdImg" src="images/forward2.gif" title="<?=$strForward?>">
</a>
&nbsp;
</td>
</tr>
</table>
<div id="shell-response" style="background-color: #e9e9e9; 
 font-family: courier new, monospace; font-size: 13px; overflow: visible;
border: 1px solid #dddddd;"><br><br><br>
</div>
<script>
 sys.stack.update();
</script>
