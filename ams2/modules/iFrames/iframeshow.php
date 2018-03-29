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
//include_once("lib/func.php");
?>
<div style="width: 100%;">

<iframe name="iframe_iframe" id="iframe_iframe" frameborder=0 scrolling=yes src="<?=$iframe_link?>"
 style="border: 0px; width: 100%; height: 800px; z-index: 5000;"
</iframe>

</div>
