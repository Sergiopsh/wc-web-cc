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
include_once("../../config.php");
include_once("../../lib/func.php");
include_once("../../modules/CodesDirectory/CodesDirectory.php");

$name=(stripslashes_r($_POST['name']));

$cd = new CodesDirectory($name);

list($r_cfr,$r_cto,$r_min_len,$r_max_len,$num_codes)=$cd->getData();
print_r($list);

    ?>
	$('module_form').num_codes.value=<?=$num_codes?>;
	$('r_name').value='<?=addslashes($name)?>';
	$('r_min_len').value='<?=$r_min_len?>';
	$('r_max_len').value='<?=$r_max_len?>';
	var s="<table border=0 cellpadding=0 cellspacing=1>";
	<?
	for($i=0; $i < $num_codes;$i++) { 	    
		if (!isset($r_cfr[$i])) $r_cfr[$i]="";
		if (!isset($r_cto[$i])) $r_cto[$i]=$r_cfr[$i];
       ?>
        s+="<tr><td align='right'>";
        s+="<INPUT TYPE='text' NAME='r_cfr[<?=$i?>]' id='r_cfr[<?=$i?>]' size='15' value='<?=$r_cfr[$i]?>'>";
        s+="</td><td align='left'>";
        s+="<INPUT TYPE='text' NAME='r_cto[<?=$i?>]' id='r_cto[<?=$i?>]' size='15' value='<?=$r_cto[$i]?>'>";
        s+="</td></tr>";
    <? } ?>
	$('tbl-dst-codes').innerHTML=s;
<?
exit();
?>