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
class Currency  {
	
	var $id;
	var $name = '';
	var $date_entered;
	var $date_modified;
	var $name_prev;
	var $db;
	var $created_by;
	var $modified_by;
	var $rate;
	var $symbol;
	var $code;
	var $list_currencies;


	function Currency($id="") {

	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id=(int) $id;
	}
	function get_data() {

	    if($this->id) $this->db->set_sql_equal("id",$this->id);
	    else $this->db->set_sql_equal("name",$this->name);
	    $list=$this->db->get_cond_list("","currencies");
	    $this->id=$list[0][0];
	    $this->name=$list[0][1];
	    $this->rate=$list[0][6];
	    $this->symbol=$list[0][7];
	    $this->code=$list[0][8];
	    $this->date_entered=$list[0][2];
	    $this->date_modified=$list[0][3];
	    $this->created_by=$list[0][4];
	    $this->modified_by=$list[0][5];
	    return array($this->name,$this->rate,$this->symbol,$this->code);
	}
	function set_fields($name,$rate,$symbol,$code) {
	    $this->name=$name;
	    $this->rate=$rate;
	    $this->symbol=$symbol;
	    $this->code=$code;
	}
	function get_list() {
	    global $default_currency;
	    $list = $this->db->get_cond_list("","currencies","","name");
	    $this->num_rows = $this->db->count;
	    if(empty($list)) {
		$list=array();
		$list[0]=array(1,$default_currency,"","","","",1,$default_currency,$default_currency);
	    }
	    $this->list_currencies=$list;
	    return $list;
	}
	function insert () {

	    $user=$_SESSION['user_name'];
	    $this->db->insert("currencies",array("name","date_entered","created_by","rate","symbol","code"),
		       array($this->name,date("Y-m-d H:i:s"),$user,$this->rate,$this->symbol,$this->code));
	    
	}
	function delete_currency () {

	    $this->db->delete_array("currencies","id",array($this->id));
	    
	}
	function update () {

	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("currencies",array("name","date_modified","rate","symbol","code","modified_by"),
		array($this->name,date("Y-m-d H:i:s"),$this->rate,$this->symbol,$this->code,$user));
	    
	    
	}

	function is_exists () {

	    return $this->db->is_exists("currencies",array("name","symbol","code"),array($this->name,$this->symbol,$this->code),array("id",$this->id));
	    
	}
	function getNameByCode($code) {
	    $this->db->set_sql_equal("code",$code);
	    $list=$this->db->get_cond_list(array("name"),"currencies");
	    return $list[0][0];
	    
	}

} // end class 
?>
