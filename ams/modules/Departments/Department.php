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

if(empty($_SESSION['ams_entry'])) die ('Not a Valid Entry');
class Department  {
	
	var $id;
	var $name = '';
	var $tel;
	var $fax;
	var $email;
	var $chief;
	var $desc;
	var $db;
	var $name_prev;
	var $list_deps;
	var $created_by;
	var $modified_by;
	var $date_entered;
	var $date_modified;
	var $status;
	var $num_rows;
	var $test;

	function Department($id="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id= (int) $id;
	}
	
	function get_data() {
	    if($this->id) $query="SELECT * FROM departments WHERE id=".quote($this->id);
	    else $query="SELECT * FROM departments WHERE name=".quote($this->name);
	    $res=$this->db->query($query);
	    $this->db->next_record();
	    $r=$this->db->Record;
	    $this->name=$r[1];
	    $this->short_name=$r[2];
	    $this->phone_work=$r[3];
	    $this->phone_office=$r[4];
	    $this->fax=$r[5];
	    $this->email=$r[6];
	    $this->chief=$r[7];
	    $this->desc=$r[8];
	    $this->created_by=$r[9];
	    $this->modified_by=$r[10];
	    $this->date_entered=$r[11];
	    $this->date_modified=$r[12];
	    $this->status=$r[12];
	    return array($this->name,$this->phone_work,$this->phone_office,$this->desc,$this->chief,$this->fax);
	}

	function get_list($cond,$page,$limit) {
	
	    $name=is_empty($cond[0]) ? "'%'" : quote(str_replace('*','%',$cond[0]));
	    $tel=is_empty($cond[1]) ? "'%'" : quote(str_replace('*','%',$cond[1]));
	    $chief=is_empty($cond[2]) ?  "'%'" : quote(str_replace('*','%',$cond[2]));
	    $query="SELECT * FROM departments WHERE name LIKE ".$name." AND 
	    ((phone_work LIKE ".$tel.") OR (phone_office LIKE ".$tel.") OR 
	    (fax LIKE ".$tel.")) AND chief LIKE ".$chief." AND status ORDER BY name";

	    $_SESSION['sql_export']="SELECT name,phone_work,phone_office,fax,email,chief,description FROM departments WHERE name LIKE ".quote(str_replace('*','%',$name))." AND 
	    ((phone_work LIKE ".quote(str_replace('*','%',$tel)).") OR (phone_office LIKE ".quote(str_replace('*','%',$tel)).") OR 
	    (fax LIKE ".quote(str_replace('*','%',$tel)).")) AND chief LIKE ".quote(str_replace('*','%',$chief))." AND status ORDER BY name";
	    $min=$page * $limit;	    
	    unset($this->list_deps);
	    $list=$this->db->get_limit_list($query,$min,$limit);
	    $this->num_rows=$this->db->count;
	    //print_r($list);
	    return $list;

	}
	function set_fields($name,$chief,$phone_work,$phone_office,$fax,$desc,$name_prev="") {
	    $this->name=$name;
	    $this->phone_work=$phone_work;
	    $this->phone_office=$phone_office;
	    $this->fax=$fax;
	    $this->chief=$chief;
	    $this->desc=$desc;
	    $this->name_prev=$name_prev; 
		
	}

	function insert () {
	    $user=$_SESSION['user_name'];	    
	    $this->db->insert("departments",array("name","phone_work","phone_office","fax","chief","description","date_entered","created_by","status"),
	      array($this->name,$this->phone_work,$this->phone_office,$this->fax,$this->chief,$this->desc,date("Y-m-d H:i:s"),$user,1)); 

	}

	function delete_department () {

	    $this->db->delete_value("departments","id",$this->id);

	}
	function deleteSelected ($mark) {
	   foreach($mark as $m){
	        $this->db->delete_value("departments","id",$m);
	   }

	}

	function update () {
	    $user=$_SESSION['user_name'];	    
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("departments",array("name","phone_work","phone_office","fax","chief","description","date_modified","modified_by","status"),
	      array($this->name,$this->phone_work,$this->phone_office,$this->fax,$this->chief,$this->desc,date("Y-m-d H:i:s"),$user,1)); 
	
	    if($this->name_prev != $this->name && !empty($this->name_prev)) {
		$this->db->set_sql_equal("department",$this->name_prev);
		$this->db->update("users",array("department"),array($this->name));

	    }
	}
	function is_exists () {

	    return $this->db->is_exists("departments",array("name"),array($this->name),array("id",$this->id));
	
	}
	function getPhonesByDepName($name) {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $query="SELECT phone_office FROM departments WHERE name=".quote($name);
	    return $db->get_list($query);
	}
} // end class 
?>
