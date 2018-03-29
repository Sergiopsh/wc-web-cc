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
$show_action_time=false;

moduleHeader($strAstMonitor);
$iframe_src="$asterisk_http_url/static/ams/monitor.html?username=$ami_login&secret=$ami_psw";

?>

<div style="width: 90%;">
<iframe name="astmon-frame" id="astmon-frame" frameborder="0" scrolling="no" 
style="border: 0; width: 100%; height: 550px;" src="<?=$iframe_src?>">
</iframe> 
</div>

