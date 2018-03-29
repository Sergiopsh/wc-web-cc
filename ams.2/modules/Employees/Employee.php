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
class Employee  {
	
	var $id;
	var $is_user;
	var $first_name;
	var $last_name;
	var $comment;
	var $date_entered;
	var $date_modified;
	var $title;
	var $department;
	var $phone_home;
	var $phone_mobile;
	var $phone_work;
	var $phone_office;
	var $fax;
	var $email;
	var $address;
	var $messenger;
	var $created_by;
	var $modified_by;
	var $num_rows;
	var $list_emps;
	var $context;
	var $channel;
	var $db;

	function Employee($id="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id=(int) $id;
	}

	function getList($cond,$page,$limit) {

	      $this->db->set_sql_like(array("department","first_name","last_name","phone_work","phone_office",
	      "phone_mobile","email","messenger","title"),$cond);
	      $this->db->set_sql_custom(" AND (is_employee) ");
	      $this->db->set_sql_export(array("first_name","last_name","title","department","phone_home","phone_mobile","phone_work",
	      "phone_office","fax","email","messenger","address","comment"),"users","","last_name,first_name");
	      $list=$this->db->get_cond_list("","users","","last_name,first_name",$page,$limit);
	      $this->num_rows=$this->db->count;
	      return $list;
	}

	function get_data () {
	    $query="SELECT * FROM users WHERE id=".quote($this->id);
	    $this->db->query($query);
	    $this->db->next_record();	    	
	    $record=$this->db->Record;	    	
	    if($record[1]) $this->is_user=true;
	    $this->first_name=$record[4];
	    $this->last_name=$record[5];
	    $this->title=$record[12];
	    $this->department=$record[13];
	    $this->email=$record[19];
	    $this->address=$record[20];
	    $this->phone_work=$record[16];
	    $this->phone_office=$record[17];
	    $this->phone_mobile=$record[15];
	    $this->phone_home=$record[14];
	    $this->messenger=$record[24];
	    $this->comment=$record[7];
	    $this->fax=$record[18];
	    $this->date_entered=$record[8];
	    $this->date_modified=$record[9];
	    $this->created_by=$record[10];
	    $this->modified_by=$record[11];
	    return array($this->is_user,$this->first_name,$this->last_name,$this->comment,
	           $this->address,$this->phone_work,$this->phone_mobile,$this->phone_home,$this->phone_office,
		   $this->department,$this->fax,$this->email,$this->messenger,$this->title);
	}
	
	function deleteEmployee() {
	    if($this->is_user($this->id))
		$query="UPDATE users SET is_employee=0 WHERE id=".quote($this->id);	
	    else $query="DELETE FROM users WHERE id=".quote($this->id);	
	    $this->db->query($query);

	}
	function deleteSelected($list) {
	  foreach($list as $id) {    
	      if($this->is_user($id))
		$query="UPDATE users SET is_employee=0 WHERE id=".quote($id);	
	      else $query="DELETE FROM users WHERE id=".quote($id);	
	    $this->db->query($query);
	  }    
	}

	function set_fields($first_name,$last_name,
	    $title,$department,$email,$address,$phone_work,$phone_office,$phone_mobile,
	    $phone_home,$messenger,$fax,$comment) {
	    $this->first_name=$first_name;
	    $this->last_name=$last_name;
	    $this->title=$title;
	    $this->department=$department;
	    $this->email=$email;
	    $this->address=$address;
	    $this->phone_work=$phone_work;
	    $this->phone_office=$phone_office;
	    $this->phone_mobile=$phone_mobile;
	    $this->phone_home=$phone_home;
	    $this->messenger=$messenger;
	    $this->comment=$comment;
	    $this->fax=$fax;
	    
	}
	function insert() {

	    $user=$_SESSION['user_name'];
	    $uniq=uniqid(true);
	    $this->db->insert("users",array("first_name","last_name","department",
	    "comment","date_entered","created_by","title","phone_home","phone_mobile","phone_work",
	    "phone_office","fax","email","address","messenger","is_employee","uniqid"),
	    array($this->first_name,$this->last_name,$this->department,$this->comment,
	    date("Y-m-d H:i:s"),$user,$this->title,$this->phone_home,$this->phone_mobile,
	    $this->phone_work,$this->phone_office,$this->fax,$this->email,$this->address,
	    $this->messenger,1,$uniq));

	    $query="SELECT id FROM users WHERE uniqid=".quote($uniq);
	    $this->db->query($query);
	    $this->db->next_record();
	    return $this->db->Record[0];
	    
	}
	function update() {

	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("users",array("first_name","last_name","department",
	    "comment","date_modified","modified_by","title","phone_home","phone_mobile","phone_work",
	    "phone_office","fax","email","address","messenger","is_employee"),
	    array($this->first_name,$this->last_name,$this->department,$this->comment,
	    date("Y-m-d H:i:s"),$user,$this->title,$this->phone_home,$this->phone_mobile,
	    $this->phone_work,$this->phone_office,$this->fax,$this->email,$this->address,
	    $this->messenger,1));

	}
	function getListPhonesByName($name) {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $query="SELECT phone_office FROM users WHERE department LIKE ".quote($name)." AND is_employee";
	    return $db->get_list($query);
	
	}
	function is_exists() {
	
	}
	function is_user($id) {
	    $query="SELECT user_name FROM users WHERE id=".quote($id);
	    $this->db->query($query);
	    $this->db->next_record();
	    if (empty($this->db->Record[0])) return false;
	    return true;

	}
} // end class
?>
