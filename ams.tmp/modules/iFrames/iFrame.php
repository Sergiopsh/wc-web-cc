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
class iFrame  {
	
	var $id;
	var $link = '';
	var $date_entered;
	var $date_modified;
	var $list_frames;
	var $db;
	var $created_by;
	var $modified_by;
	var $name;
	var $num_rows;

	function iFrame() {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	}

	function get_list() {
	    $user=$_SESSION['user_name'];
	    unset($this->list_frames);
	    $this->list_frames=$this->db->get_cond_list("","iframes","","link");
	    $this->num_rows=$this->db->count;
	}
	function insert () {
	
	   $user=$_SESSION['user_name'];
	   $this->db->insert("iframes",array("link", "date_entered","created_by","name"),array($this->link,date("Y-m-d H:i:s"),$user,$this->name)); 
	    
	}
	function delete_iframe () {

	    $this->db->delete_array("iframes","id",array($this->id));
	    
	}
	function update () {

	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("iframes",array("link", "date_modified","modified_by","name"),array($this->link,date("Y-m-d H:i:s"),$user,$this->name)); 
	}

} // end class 
?>
