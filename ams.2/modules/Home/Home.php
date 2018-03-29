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
class Home  {
	
	var $id;
	var $module;
	var $action;
	var $user_name;
	var $user_id;
	var $list_menu;
	var $db;

	function Home() {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;

	}
	function getMenu() {
	      $user=$_SESSION['user_name'];
	      $query="SELECT name,action FROM homemenu WHERE user_name LIKE ".quote($user)." ORDER BY id";
	      $list=$this->db->get_list($query);
	      $num=$this->db->count;
	      unset($_SESSION['home']['menu']);
	      if($num) {
	        $i=0;
	        foreach($list as $l) {
		    //$_SESSION['home']['menu'][$i]=array("str".$l[0],$l[1]);
		    $a=stripslashes($l[1]);
		    $_SESSION['home']['menu'][$i]=array($l[0],$a);
		    $i++;
		}
	      }
	      return $num;
	}
	
	function saveMenu($menu) {
	    if(!count($menu)) return;
	    $user=$_SESSION['user_name'];
	    $this->db->delete_value("homemenu","user_name",$user);
	    $i=0;
	    unset($_SESSION['home']['menu']);
	    foreach($menu as $m) {
		$n=$m[0];
		$a=$m[1];
		if(stripos($a,"javascript:loadModule")!==false) {
		    $a=strstr($a,","); $a[0]=' ';
		    $a=strstr($a,",");
		    $a="javascript:loadModule(\'\',\'Home\'".$a;
		
		}
		//echo "$n $a <br>";
		if($i==0) {
		    
		}
		$this->db->insert("homemenu",array("user_name","name","action"),array($user,$n,$a));    
		$_SESSION['home']['menu'][$i]=array($n,stripslashes($a));
		$i++;
	    }
	    
	    return 1;
	}


} // end class
?>
