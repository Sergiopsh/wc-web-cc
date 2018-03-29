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
if(!$_SESSION['ams_entry']) {
    echo "<script type=\"text/javascript\">window.close();</script>";   exit(); 
}
include_once("../../config.php");
include_once("../../lib/func.php");
if(!$_SESSION['lang']) $lang=$default_lang; else $lang=$_SESSION['lang'];
include_once("../../lang/".$lang.".lang.php");
include_once("lang/".$lang.".lang.php");
include_once("Rate.php");
if(!$_SESSION['theme']) $theme=$default_theme; else $theme=$_SESSION['theme'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>		
	<title>Change Rates</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<style type="text/css" media="screen">
		@import url("../../themes/<?=$theme?>/style.css");
	</style>
	<meta name="MSSmartTagsPreventParsing" content="TRUE">
</head>
<?


extract(stripslashes_r($_REQUEST));
if(!isset($current_page)) $current_page=0;

$rt = new Rate("",$accountcode);
if ($save_rates) {
	//echo "save_rates <br>";
	$i=0;
	foreach ($r_name as $rn) {
	    if ($rn) $rt->update_rate($rn,$r_rate[$i++]);	

	}
    ?>
    <body style="background-color: #ffffff;">
    <script type="text/javascript">
      if(!opener) window.close();
	var cp=0;
	if(opener.document.getElementById('current_page'))
	   cp=opener.document.getElementById('current_page').value;
	if(opener.current_module == 'Rates' && opener.action == 'RatesList' && cp == <?=$current_page?>) {
    	    alert("here");
	    if(opener.isIE) opener.loadModule(1);
	}
	
        setTimeout("window.close()",2000);
    </script>
    <br><br><center>
    <div class="module-warning"><?=$strRatesSaved?></div>
    </center>
    </body>
    </html>
    <?
    exit();
}

?>

<body style="background-color: #ffffff;">
<script>
<?
$num=count($rates);
if (!$num) { echo "window.close();";   exit(); }
?>
num=<?=$num?>;
wh=(num * 20) + 180;
if (wh > 570) wh=570;
window.resizeTo(350,wh); 

function checkAndSave() {
    var id = document.getElementById('common_price'); 
    if(!opener.ams.checkInput(id,'float','<?=$strFieldMustBeFloat?>',{hlight: 0, offsetLeft: -120, img: '../../images/warning2.gif', document: document})) return;
    for(var i=0; i < num; i++) {
	var id=document.getElementById("r_rate["+i+"]"); 
	if(!opener.ams.checkInput(id,'float','<?=$strFieldMustBeFloat?>',{hlight: 0,offsetLeft: -120, img: '../../images/warning2.gif', document: document})) return;
    }
    document.getElementById('save_rates').value=1;
    document.getElementById('form_change_rates').submit();
}

function setRates() {

    for (i=0; i < num; i++) {
	if (document.getElementById("r_rate[" + i + "]")) 
	    document.getElementById("r_rate[" + i + "]").value=document.getElementById("common_price").value;
	
    }
}
</script>



<center>
<br>
<form name="form_change_rates" id="form_change_rates" method="post">
<input type="hidden" id="current_page" name="current_page" value="<?=$current_page?>">
<input type="hidden" id="save_rates" name="save_rates" value="">
<table border=0 class="codes-tbl" width="280" cellpadding="0" cellspacing="0">
<tr><td colspan=2 align=center><h3><?=$strTariffPlan?> - <?=$t_name?></h3></td></tr>
<tr><th align=left width=200>&nbsp;<?=$strPriceAll?></th>
<th align=right><input type="text" size="10" id="common_price" name="common_price" value="0.00000" onkeyup="setRates()"></th>
</tr>

<?
$i=0;
foreach ($rates as $r) {
	$r_rate = $rt->get_rate_by_name($r);
	$class="trcls1";
	$i % 2 ? 0: $class="trcls2";
	?>
	<tr align="left" onmouseover="this.className='trmover'" 
	onmouseout="this.className='<?=$class?>'" class='<?=$class?>'>
	<td><?=hc($r)?></td><td align="right">
	<input type="text" size="10" id="r_rate[<?=$i?>]" name="r_rate[<?=$i?>]" value="<?=$r_rate?>">
	<input type="hidden" name="r_name[<?=$i?>]" id="r_name[<?=$i?>]" value="<?=hs($r)?>">
	</td>
	</tr>
    <?$i++;
    
}

?>
<tr><th colspan=2>&nbsp;</th>
</tr>
</table>

<table border=0>
    <tr><td>
    <input type="button" onclick="checkAndSave();" value="<?=$strChange?>" class="sbutton">
    </td><td>
    <input type="reset" name="Reset" value="<?=$strReset?>" class="sbutton">
    </td></tr>
</table>
</form>
</center>
</body>
</html>

