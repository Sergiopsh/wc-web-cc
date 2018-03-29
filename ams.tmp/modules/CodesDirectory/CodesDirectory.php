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
class CodesDirectory  {
	
	var $id;
	var $name;
	var $name_prev;
	var $code;
	var $num_rows;
	var $num_codes;
	var $list_codes;
	var $lang;
	var $col_name;
	var $fr;
	var $to;
	var $min_len=1;
	var $max_len=20;
	var $db;

	function CodesDirectory($name="") {
	    global $db, $lang;
	    if(empty($lang)) $lang=$_SESSION['lang'];
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    if($lang == "ru_ru") $this->col_name='name';
	    else $this->col_name='name_en';
	    $this->name=$name;

	}
	function getList($cond,$page,$limit) {

	    $cols=array("id",$this->col_name,"cfr","cto","min_len","max_len");
	    $this->db->set_sql_like(array($this->col_name,"cfr"),$cond);
	    $this->db->set_sql_export($cols,"codes","",$this->col_name);	
	    $list = &$this->db->get_cond_list($cols,"codes",$this->col_name,$this->col_name,$page,$limit);
	    $this->num_rows=$this->db->count;
	    return $list;
	    
	}
	function set_fields($fr,$to,$min,$max,$num=0,$name_prev="") {
	    $this->fr=$fr;
	    $this->to=$to;
	    $this->min_len=$min ? $min : 1;
	    $this->max_len=$max ? $max : 20;
	    $this->num_codes=$num;
	    $this->name_prev=$name_prev;
	}	
	
	function getData () {
	    $this->db->set_sql_equal($this->col_name,$this->name);
	    $list = $this->db->get_cond_list(array(id,$this->col_name,"cfr","cto","min_len","max_len"),"codes");
	    $num = $this->db->count;
	    unset($this->fr,$this->to);
	    for($i=0; $i < $num; $i++) {
    		$this->fr[$i]=$list[$i][2];
		$this->to[$i]=$list[$i][3];
	    }
	    $this->min_len=$list[0][4];
	    $this->max_len=$list[0][5];
	    $this->num_codes=$num;
	    return array($this->fr,$this->to,$this->min_len,$this->max_len,$this->num_codes);

	}
	
	function deleteCodes($val) {
	    if(!is_array($val)) $val=array($val);
	    $this->db->delete_array("codes",$this->col_name,$val);
	}

	function insert() {
	    $cols = array("name","name_en", "cfr" ,"cto", "min_len", "max_len"); 
	    for($i=0; $i < $this->num_codes; $i++) { 	
		if(!isset($this->fr[$i]) || trim($this->fr[$i]) == "") continue;
		$code_from=$this->fr[$i];	
		if(!isset($this->to[$i]) || trim($this->to[$i]) == "") $code_to=$code_from;
		else $code_to=$this->to[$i];	
		$vals = array($this->name,$this->name,$code_from,$code_to,$this->min_len,$this->max_len);
		$this->db->insert("codes",$cols,$vals);
		
		
	    }
	    $query="ALTER TABLE codes ORDER BY cfr";
	    $this->db->query($query); 
	}

	function update() {

	    $this->deleteCodes($this->name_prev);
	    $this->insert();
	    $query="ALTER TABLE codes ORDER BY cfr";
	    $this->db->query($query); 
	}
	function is_exists() {

	    return $this->db->is_exists("codes",array($this->col_name),array($this->name),array($this->col_name,$this->name_prev));
	}

	function getCodesByName($name) {

	    $this->db->set_sql_equal($this->col_name,$name);
	    return $this->db->get_cond_list(array("cfr","cto","min_len","max_len"),"codes");
	
	}
} // end class
?>

	
	



