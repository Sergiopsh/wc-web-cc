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

?>
<div style="visibility: inherit;">
<table cellpadding="0" cellspacing="0" border="0" class="st_menu" width="100%">
    <tr><td colspan="2" bgcolor="#999999"></td></tr>
    <tr height="10">
    <td class="st_menutdicon" width="12" style="background-image: url(themes/<?=$theme?>/images/menubg.gif); background-repeat : repeat-y;"></td>
    <td bgcolor="#dedede"></td>
    </tr>
    <tr>
    <td class='st_menutdicon' width='12' style="background-image: url(themes/<?=$theme?>/images/menubg.gif); background-repeat : repeat-y;">
    <img src=<?=$module_menu[0][1]?>></td>
    <td nowrap="nowrap" class="st_menutd_h"><?=$module_menu[0][0]?>
    </td></tr>
<?

  foreach ($module_menu[1] as $menuitem) {
   if($menuitem[1]) {
?>
    <tr><td class="st_menutdicon" width="12"  
     style="background-image: url(themes/<?=$theme?>/images/menubg.gif); background-repeat: repeat-y;">
    <?if($menuitem[2]) {?>
	<img src='<?=$menuitem[2]?>' alt="menuitem" />
	
    <?}else echo "&nbsp;";?></td>
    <td nowrap="nowrap" class="st_menutd" onMouseOver="this.style.backgroundColor='#eeeeee';"  
    onMouseOut="this.style.backgroundColor='#DFDFDF';">
    &nbsp;
    <a class="st_menulink" href="<?=$menuitem[1]?>"><?=$menuitem[0]?></a>
    </td></tr>
<?
  }
}
?>
</table>
</div>





