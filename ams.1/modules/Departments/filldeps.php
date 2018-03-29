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
include_once("Department.php");

$theme=$_SESSION['theme'];
$f_name=stripslashes_r($_POST['department']);

$dep = new Department();
$list=$dep->get_list(array($f_name."*","",""),0,20);

?>
<ul style="list-style-position: outside; list-style-type: none; margin: 0px;padding: 1px;"><?
if($list) {
 $i=0;
 foreach ($list as $l) {
 	$bgc = "#d9d9d9";
	$i++ % 2 ? 0: $bgc="#ffffff";?>
 	<li 
	onMouseover="this.style.backgroundColor='#cccccc';" 
 	onMousedown="this.style.backgroundColor='#cccccc';" 
	onMouseout="this.style.backgroundColor='<?=$bgc?>';" 
		    style="font-color: black; 
		    background-color: '<?=$bgc?>';  
		    font-family: verdana, sans;
		    font-size: 11px;"><?=hc($l[1])?>
</li>
<?$i++;
 }
}
?>
</ul>

