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

if(empty($_SESSION['ams_entry'])) die('Not a Valid Entry');

?>

<div id="menu-top">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr height="20">
<td style="padding-left: 32px; background-image: url(themes/<?=$theme?>/images/emptyTabSpace.gif);">
&nbsp;</td>
<?
    $i=0;	
    foreach($main_menu as $menuitem) {
    $mname=$menuitem[0][0];
    if (!$_SESSION['acl'][$mname]['Access']) continue; 
	if (in_array($menu_module, $menuitem[0])) {

?>
<td>
  <table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
	<tr height="20">
	<td style="background-image : url(themes/<?=$theme?>/images/menu_current_left.gif);" ><img src="themes/<?=$theme?>/images/blank.gif" width="8" height="1" border="0" alt="<?=$menuitem[1]?>"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/menu_current_middle.gif);" class="otherTab" nowrap><a class="currentTab"  href="<?=$menuitem[2]?>"><?=$menuitem[1]?></A></td>
	<td style="background-image : url(themes/<?=$theme?>/images/menu_current_right.gif);"><img src="themes/<?=$theme?>/images/blank.gif" width="8" height="1" border="0" alt="<?=$menuitem[1]?>"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/emptyTabSpace.gif);"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/emptyTabSpace.gif);"><img src="themes/<?=$theme?>/images/blank.gif" width="1" height="1" border="0" alt=""></td>
  </tr>
  </table>
</td>

    <?}else{?>
<td>
  <table cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #E5E5E5;">
	<tr height="20">
	<td style="background-image : url(themes/<?=$theme?>/images/menu_normal_left.gif);" ><img src="themes/<?=$theme?>/images/blank.gif" width="8" height="1" border="0" alt="<?=$menuitem[1]?>"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/menu_normal_middle.gif);" class="otherTab" nowrap><a   class="otherTab"  href="<?=$menuitem[2]?>"><?=$menuitem[1]?></A></td>
	<td style="background-image : url(themes/<?=$theme?>/images/menu_normal_right.gif);"><img src="themes/<?=$theme?>/images/blank.gif" width="8" height="1" border="0" alt="<?=$menuitem[1]?>"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/emptyTabSpace.gif);"></td>
	<td style="background-image : url(themes/<?=$theme?>/images/emptyTabSpace.gif);"><img src="themes/<?=$theme?>/images/blank.gif" width="1" height="1" border="0" alt=""></td>
  </tr>
  </table>
</td>
    <?}
    
   }?>
<td width="100%" style="background-image : url(themes/<?=$theme?>/images/emptyTabSpace.gif);"><img src="themes/<?=$theme?>/images/blank.gif" width="1" height="1" border="0" alt="">
</td>
</tr>
</table>
</td></tr>

<tr height="17">
<td colspan="20">

  <table border="0" cellpadding="0" cellspacing="0" width="100%"
  style="height: 17px; background-color: #acacac; border-bottom: 1px solid #000;">
  <tr>
  <td width="20%" align="left" style="color: white; font-style: italic; font-size: 11px; font-family: sans, sans-serif;">
  <a title="<?=$strExpandPage?>" href="javascript:ams.toggleScreen()" style="text-decoration: none;">
  <img src="images/fullpage.png" align="top" 
  style="margin: 0px; border: 0px;">
  </a>
  <?echo "$strWelcome ".$_SESSION['current_user_first_name'];?>
  </td>
  <td width="20%">&nbsp;</td>
  <td width="20%"><div id="module_history">
  </div>
  </td>
  <td>&nbsp;</td>
  <td align="center" id="status-message" style=" width: 160px;   color: white;
	    font-style: italic;   font-family: verdana,sans;
	    font-size: 10px; padding-right: 5px;" 
    align="center" valign="center">
  </td>
  <?/*
  <td id="img-loading" style="display: none;width: 20px; padding: 0px; margin: 0px;">
  <img src="images/wait.gif" align="absmiddle" style="margin: 0px; border: 0px;">
  </td>
  */?>
  </tr>
  </table>
  
</td>
</tr>
</table>
</div>

<?/*
 if(amiConnected) $('status-connection').innerHTML='<font color="green"><?=$strAstConnected?></font>';
 else $('status-connection').innerHTML='<font color="red"><?=$strAstNotConnected?></font>';
*/?>
