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
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="tb-top-header" style="height: 17px;">
    <tr>
    <th id="welcome" width="20%" align=left nowrap="nowrap" >
    <a title="<?=$strExpandPage?>" href="javascript:ams.toggleScreen()" style="text-decoration: none;">
    <img src="images/fullpage.png" align="top" style="margin: 0px;border: 0px;"/>
    </a>&nbsp;
    <?echo "$strWelcome ".$_SESSION['current_user_first_name'];?>
    </th>
    <th width="20%">&nbsp;</th>
    <th width="20%" id="module_history">
    
    </th>
    <th >&nbsp;</th>
    <th align="center" id="status-message" style="display: none; z-index: 5000;
	    padding: 0px; padding-right: 5px; width: 160px; color: white;
	    font-style: italic; font-weight: normal; font-family: verdana,sans;
	    font-size: 10px;" 
    valign="middle">
    </th>
    <?/*
    <th align="right" width="20" id="img-loading" style="display: none;
     padding: 0px; margin: 0px; 
     display:; padding-right: 5px;">
    <img align="absmiddle" src="images/wait.gif" style="margin: 0px; border: 0px">
    </th>
    </tr>
    */?>
</table>
</div>

