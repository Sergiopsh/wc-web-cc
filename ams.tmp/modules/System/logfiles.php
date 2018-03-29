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
$logdate=date("M d");

?>
<div id="frame-module-header" nowrap>
<?=$strLogFiles?>
</div>

<br>

<script>
Calendar._SMN = new Array
("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

log = new ObjectD();
log.top = new Array();

log.loadLogFile=function(file) {
    if(this.top.indexOf(file) != -1) {
	$('module-note').hide();
	$('logfile-body').hide();
	return;
    }
    var url="modules/System/loadlogfile.php";
    var pb="logfile="+encodeURIComponent(file)+"&logdate="+encodeURIComponent($('_logdate').value);
    new Ajax.Request(url,
	    {postBody: pb,
	     onComplete: function(t){
		if(t.responseText) 
		    Element.update('logfile-body',t.responseText);

	    }});	
}

</script>

<table border=0 cellspacing=0 class="menu-panel">
<tr>
 
<td nowrap>&nbsp;<?=$strLogDate?>:</td>
<td><input type="text" id="_logdate" size="12" name="_logdate" value="<?=$logdate?>"  readonly="1">
</td><td nowrap>
<img align="absmiddle" id="button_logdate" src="images/jscalendar.gif"></td>
</td>
<script>
Calendar.setup({
	    inputField	: "_logdate",
	    ifFormat	: "%b %d",
	    showsTime	: false,
	    timeFormat	: "24",
	    button	: "button_logdate",
	    align	: "Br",
	    firstDay	: 1,
	    showOthers	: true,
	    singleClick	: true
	});
</script>
 
 <td width="20">&nbsp;</td>
 <?$i=0;
 foreach($logfiles_dirs as $ld) {?>
 <td>
 <select id="logdirs[<?=$i?>]" onchange="log.loadLogFile(this.value);">
    <option selected value="<?=$ld?>"><?=$ld?>
    <script>log.top.push('<?=$ld?>');</script>
    <?foreach(@glob($ld.'/*') as $logfile) {
	if(!is_dir($logfile)) {?>
    <option value='<?=$logfile?>'><?=basename($logfile)?>
    <?  }
     }?>
 </select>
 </td>
 <td >
 <a href="javascript:void(0)" onclick="log.loadLogFile($('logdirs[<?=$i?>]').value);">
 <img align="absmiddle" src="images/run.gif">
 </a>
 </td>
 <?$i++;
 }?>
</tr>
</table>

<br>
<div id="module-note" class="module-note" style="display: none;">
</div>
<div id="logfile-body" style="border: 1px solid #999999; display: none; height: 495px;
width: 95%; font-size: 12px; font-family: verdana,sans; overflow: auto;" nowrap>
</div>
