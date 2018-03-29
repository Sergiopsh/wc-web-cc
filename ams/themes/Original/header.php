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
echo "<form name='form_logout' id='form_logout' method='post' action='index.php'>";
echo "<input type='hidden' name='module' id='module' value='Users'/>";
echo "<input type='hidden' name='action' id='action' value='Logout'/>";
echo "</form>";
echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
echo "<tr><td width='4%' align='right'><img id='h_logo' src='images/ast_logo2.gif' alt='logo' /></td>";
echo "<td align='left' class='h_logo'>&nbsp;$label</td>";
echo "<td width='20%' align='center' valign='top'>";

if($_SESSION['auth_user_id']) {

    echo "<div id='top-menu' class='top-menu'>";
    echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
    echo "<tr height='19'><td style='background-image: url(themes/$theme/images/top_mn_l.gif);width: 4px;'></td>";
    echo "<td width='50%' align='center' style='background-color: #999999;'>";
    echo "<a href=\"javascript:loadModule('',menu_module,'MySettings','Profile');\">";
    echo "$strProfile</a></td>";
    echo "<td align='center' style='background-color: #999999;'>";
    echo "<a href='#' onclick=\"$('form_logout').submit();\">";
    echo "$strLogout</a></td></tr>";//<td style='background-image: url(themes/$theme/images/top_mn_r.gif); width: 4px;'></td></tr>";
    echo "</table></div>";

} else echo "";


echo  "</td></tr></table></div>";
