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

echo "<div id='frame-header'>";
echo "<form name='form_logout' id='form_logout' method='post'>";
echo "<input type='hidden' name='module' id='module' value='Users'>";
echo "<input type='hidden' name='action' id='action' value='Logout'>";
echo "</form>";
echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
echo "<tr><td width='4%' align='right'><img src='images/ast_logo2.gif' id='h_logo' align='absmiddle' alt='logo' />";
echo "</td><td align='left' class='h_logo'>&nbsp;$label</td>";

if($_SESSION['is_admin'])  echo "<td width='25%' align='right' valign='top'>";
else echo "<td width='18%' align='right' valign='top'>";

if($_SESSION['auth_user_id']) {
    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='top-menu'>";
    echo "<tr height='20'>";
    echo "<td width='14' style='background-image: url(themes/$theme/images/top_left2.gif);";
    echo " background-repeat: no-repeat; background-position: 100% 0%;'></td>";

    if($_SESSION['is_admin']) {
	echo "<td width='35%' align='center'  nowrap align='left' style='background-image: url(themes/$theme/images/top_middle.gif);";
	echo " background-repeat: repeat-x; background-position: top;'>";
	echo "<a href=\"javascript:loadModule('','Administration','Administration','UsersList');\">$strAdministration</a></td>";
    }
    echo "<td align='center' nowrap='nowrap' align='left' style='background-image: url(themes/$theme/images/top_middle.gif);";
    echo " background-repeat: repeat-x;  background-position: top;'>";
    echo "<a href=\"javascript:loadModule('',menu_module,'MySettings','Profile');\">";
    echo "$strProfile</a></td>";
    echo "<td align='center'  nowrap='nowrap' align='left' style='background-image: url(themes/$theme/images/top_middle.gif);";
    echo " background-repeat: repeat-x;   background-position: top;'>";
    echo "<a href='#' onclick=\"$('form_logout').submit()\">$strLogout</a></td></tr>";
    echo "</table>";

}else echo " ";

echo "</td></tr></table></div>";
