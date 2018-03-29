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
class User  {
	
	var $id;
	var $user_name;
	function User($id="",$name="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id= (int) $id;
	    $this->user_name=$name;
	}

	function getList($cond,$admin,$status,$page,$limit) {

	    $list = $this->db->get_cond_list("","autocall.abon_list_with_summ","","list_name",$page,$limit);
	    $this->num_rows=$this->db->count;

	    return $list;
	}

	function get_data () {
		
	    if(!empty($this->user_name)) 
		 $query="SELECT * FROM users WHERE user_name=".quote($this->user_name);	
	    else {
			if($this->id == 0 && !$_SESSION['root']) return;
			$query="SELECT * FROM users WHERE id=".quote($this->id);	
		}
	    $this->db->query($query);
	    $this->db->next_record();
	    $record=$this->db->Record;
	    
	    $this->id=$record[0];
	    $this->user_name=$record[1];
	    $p=explode(":",$list_p[0]);
	}
	
	function deleteUser() {
	    if(!$this->id) return;

	    $this->db->delete_value("users_acl","user_id",$this->id);
	    if(!$this->isEmployee()) $this->db->delete_value("users","id",$this->id);
	    else {
		$this->db->set_sql_equal("id",$this->id);	    
		$this->db->update("users",array("user_name","user_password"),array("",""));
	    
	    }

	}
	
} // end class
?>
