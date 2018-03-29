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
class Rate  {
	
	var $id;
	var $name = '';
	var $cfr;
	var $cto;
	var $min_len;
	var $max_len;
	var $rate;
	var $accountcode;
	var $name_prev;
	var $o_name;
	var $db;
	var $num_rows;
	var $num_codes;
	var $list_rates;
	
	function Rate($name="",$accountcode="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->name=$name;
	    $this->accountcode=$accountcode;
	    
	}
	function set_fields($cfr,$cto,$min,$max,$rate,$num_codes,$name_prev="") {

	    $this->cfr=$cfr;    
	    $this->cto=$cto;    
	    $this->min_len=$min ? $min : 1;
	    $this->max_len=$max ? $max : 20; 
	    $this->rate=$rate;
	    $this->num_codes=$num_codes;
	    $this->name_prev=$name_prev;
	}

	function get_data() {

	    $query="SELECT * FROM rates WHERE name=".quote($this->name)." AND accountcode=".quote($this->accountcode); 
	    $res = $this->db->query($query);
	    $num = $this->db->num_rows();
	    unset($this->cfr,$this->cto);
	    for($i=0; $i<$num;$i++) {
	        $this->db->next_record();
	        $list_rates[$i] = $this->db->Record;
	        $this->cfr[$i]=$list_rates[$i][2];
	        $this->cto[$i]=$list_rates[$i][3];
	    }
	    $this->rate=$list_rates[0][7];
	    $this->min_len=$list_rates[0][4];
	    $this->max_len=$list_rates[0][5];
	    return array($this->name,$this->cfr,$this->cto,$this->min_len,$this->max_len,$this->rate);
	}

	function get_list($cond,$page,$limit) {

	    $this->db->set_sql_like(array("name","cfr"),array($cond[0],$cond[1]));
	    $this->db->set_sql_interval("rate",array($cond[2],$cond[3]));
	    $this->db->set_sql_equal("accountcode",addslashes($this->accountcode));
	    $this->db->set_sql_export(array("name","cfr","cto","min_len","max_len","rate"),"rates","name","name");
	    $this->list_rates=$this->db->get_cond_list("","rates","name","name",$page,$limit);
	    $this->num_rows=$this->db->count;
	}

	function insert ($add_to_cd=false) {

	    $cols = array("name","cfr","cto","min_len","max_len","rate","accountcode");
	    for($i=0; $i < $this->num_codes; $i++) {
		if(!isset($this->cfr[$i]) || trim($this->cfr[$i]) == "") continue;
		$code_from=$this->cfr[$i];	    
		if(!isset($this->cto[$i]) || trim($this->cto[$i]) == "") $code_to=$code_from;
		else $code_to=$this->cto[$i];
		$vals=array($this->name,$code_from,$code_to,$this->min_len,$this->max_len,$this->rate,$this->accountcode);
		$this->db->insert("rates",$cols,$vals);
	    }	
	    $query="ALTER TABLE rates ORDER BY cfr";
	    $this->db->query($query);
	    if ($add_to_cd) $this->add_to_cd();
	}

	function add_to_cd() {

	    $cd = new CodesDirectory($this->name);
	    if($cd->is_exists()) return; 
	    $cd->set_fields($this->cfr,$this->cto,$this->min_len,$this->max_len,$this->num_codes);
	    $cd->insert();
	}

	function delete_rate($name) {

	    $query="DELETE FROM rates WHERE name=".quote($name)." AND accountcode=".quote($this->accountcode);
	    $this->db->query($query);
	}

	function delete_selected($arr) {

	    foreach($arr as $name) {
		$query="DELETE FROM rates WHERE name=".quote($name)." AND accountcode=".quote($this->accountcode);
		$this->db->query($query);
	    }
	}

	function update () {

	    $this->delete_rate($this->name_prev);
	    $this->insert();
        }
	function is_name_exists() {
	    $query="SELECT * FROM rates WHERE name=".quote($this->name)." AND accountcode=".quote($this->accountcode)." AND name <> ".quote($this->name_prev)." LIMIT 1";
	    $this->db->query($query);
	    if($this->db->num_rows())	return true;
	
	}
	function is_exists () {

	    if($this->is_name_exists()) return true;
	    if($this->is_code_exists()) return true;
	    return false;
	}
	function is_code_exists() {

	    for($i=0; $i < $this->num_codes; $i++) {
		if(!isset($this->cfr[$i]) || trim($this->cfr[$i]) == "") continue;
		$code_from=$this->cfr[$i];
		$query="SELECT * FROM rates WHERE cfr=".quote($code_from)." AND 
		".quote($this->min_len)." <= max_len AND ".quote($this->max_len)." >= min_len 
		    AND accountcode=".quote($this->accountcode)." AND name <> ".quote($this->name_prev)." LIMIT 1"; 
		$this->db->query($query);
		if($this->db->num_rows()) return true;

	    }
	    return false;
	}
	
	function update_rate($name,$rate) {
            
	    $query="UPDATE rates SET rate=".quote($rate)." WHERE name=".quote($name)." AND accountcode=".quote($this->accountcode); 
	    return $this->db->query($query);

        }
	function get_codes_list($name) {

	    $query="SELECT cfr,cto FROM rates WHERE name=".quote($name)." AND accountcode=".quote($this->accountcode);
	    return $this->db->get_list($query);
	}
	function get_rate_by_name($name) {

	    $query="SELECT rate FROM rates WHERE name=".quote($name)." AND accountcode=".quote($this->accountcode)." LIMIT 1";	
	    $this->db->query($query);
	    $this->db->next_record();
	    return $this->db->Record[0];	    
	}
} // end class 
?>

