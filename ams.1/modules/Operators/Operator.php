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
class Operator  {
	
	var $id;
	var $fname = '';
	var $name = '';
	var $tel1;
	var $tel2;
	var $fax;
	var $email;
	var $contact_person;
	var $address;
	var $comment;
	var $db;
	var $name_prev;
	var $num_rows;

	function Operator($id="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id= (int) $id;    
	}
	function set_fields($fname,$name,$tel1,$tel2,$fax,$email,$cperson,$address,$comment,$name_prev="") {

	    $this->fname=$fname;    		    
	    $this->name=$name;    
	    $this->tel1=$tel1;
	    $this->tel2=$tel2;    	        
	    $this->fax=$fax;    
	    $this->email=$email;    
	    $this->contact_person=$cperson;    
	    $this->address=$address;    
	    $this->comment=$comment;    
	    $this->name_prev=$name_prev;    
	}

	function get_data() {
	    $this->db->set_sql_equal("id",$this->id);
	    $list = $this->db->get_cond_list("","providers");
	    return $list[0];
	}

	function get_list($cond="") {
	    $name=!is_empty($cond[0]) ? quote(str_replace('*','%',$cond[0])) : "'%'";
	    $tel=!is_empty($cond[1]) ? quote(str_replace('*','%',$cond[1])) : "'%'";
	    $query="SELECT * FROM providers WHERE (fullname LIKE ".$name." OR name LIKE ".$name.") 
	    AND (tel1 LIKE ".$tel." OR tel2 LIKE ".$tel." OR fax LIKE ".$tel.")  ORDER BY fullname";
	    $list=$this->db->get_list($query);
	    $this->num_rows=$this->db->count;
	    return $list;
	}
	
	function insert () {
	
	    $this->db->insert("providers",array("fullname","name","tel1","tel2","fax","email","person","address","comment"),
	    array($this->fname,$this->name,$this->tel1,$this->tel2,$this->fax,$this->email,$this->contact_person,$this->address,$this->comment));

	}

	function delete_operator () {

	    $this->db->delete_value("providers","id",$this->id);

	}

	function update () {
	
	    $this->db->set_sql_equal("id",$this->id);	    
	    $this->db->update("providers",array("fullname","name","tel1","tel2","fax","email","person","address","comment"),
		array($this->fname,$this->name,$this->tel1,$this->tel2,$this->fax,$this->email,$this->contact_person,$this->address,$this->comment));
	    $this->db->set_sql_equal("provider",$this->name_prev);	    
	    $this->db->update("tplans",array("provider"),array($this->name));

	}

	function is_exists () {

	    return $this->db->is_exists("providers",array("fullname","name"),array($this->fname,$this->name),array("id",$this->id));
	    
	}

} // end class 
?>
