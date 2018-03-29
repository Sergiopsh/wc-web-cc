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

include_once("../../config.php");
include_once("../../lib/func.php");

$c_name=stripslashes_r($_POST['name']);

if(!$_SESSION['ams_entry'] || empty($c_name)) exit();

$lang=$_SESSION['lang'];

include_once("../../lang/".$lang.".lang.php");
include_once("lang/".$lang.".lang.php");
include_once("CodesDirectory.php");


$code = new CodesDirectory();
$list_codes=$code->getCodesByName($c_name);
$num = count($list_codes);

?>
<center>
<div style="width: 175px;"><h3><?=hc($c_name)?></h3>
<table border=0 class="list-tbl" width="175" cellpadding="0" cellspacing="0" style="font-size: 11px;">
<tr><th align=center><?=$strCodeFrom?></th>
<th align=center><?=$strCodeTo?></th>
</tr>
<?

for ($i=0; $i<$num; $i++) {
  $class= "trcls1";
  $i % 2 ? 0: $class="trcls2";?>
  <tr class="<?=$class?> align="center"  onMouseover="this.className='trmover'"
  onMouseout="this.className='<?=$class?>'">
  <td align="center"><?=$list_codes[$i][0]?></td>
  <td align="center"><?=$list_codes[$i][1]?></td>
  </tr>
<?
}
?>
<tr><th colspan=2>&nbsp;</th>
</tr>
</table>
</center>
<br>


