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
include_once("lib/func.php");
include_once("modules/Home/lang/".$lang.".lang.php");
include_once("modules/Home/Home.php");


$module_menu = array(array($strHome,"images/home.gif"),
	       array(array("")));

//$home= new Home();
//$home->getMenu();

if($_SESSION['home']['menu']) { 

  foreach($_SESSION['home']['menu'] as $home_item) {
    //$home_item[0]=${"str".$home_item[0]};
    array_push($module_menu[1],$home_item);

  }

}
else 
    array_push($module_menu[1],array($strTuneHomeMenu,"javascript:loadModule('','Home','Home','TuneHomeMenu');")); 



?>