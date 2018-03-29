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
class TariffPlan  {
	
	var $id;
	var $name = '';
	var $accountcode = '';
	var $bstep;
	var $currency;
	var $provider;
	var $pr_orig;
	var $pr_repl;
	var $accode_prev;
	var $db;
	var $o_fname;
	
	function TariffPlan($id="",$accountcode="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id=(int) $id;    
	    $this->accountcode=$accountcode;
	}	    
	function set_fields($name,$bstep,$currency,$provider,$pr_orig,$pr_repl,$accode_prev="") {    
	    $this->name=$name;    
	    $this->bstep=$bstep;    
	    $this->currency=$currency;
	    $this->provider=$provider;    	        
	    $this->pr_orig=$pr_orig;    
	    $this->pr_repl=$pr_repl;    
	    $this->accode_prev=$accode_prev;    

	}

	function get_rules () {
	    $query="SELECT prefix, pr_replace FROM trules WHERE accountcode=".quote($this->accountcode);
	   	    
	    $this->db->query($query);    
	    $num=$this->db->num_rows();
	    unset($this->pr_orig,$this->pr_repl);
	    for($i=0; $i < $num; $i++) {
		$this->db->next_record();
		$list_pr=$this->db->Record;
		$this->pr_orig[$i]=$list_pr[0];
		$this->pr_repl[$i]=$list_pr[1];
	    }
	}
	function get_data() {

	    if (!empty($this->accountcode)) 
		    $query="SELECT * FROM tplans WHERE accountcode=".quote($this->accountcode);
	    else $query="SELECT * FROM tplans WHERE id=".quote($this->id);

	    $this->db->query($query);
	    $this->db->next_record();
	    $data=$this->db->Record;
	    $this->id=$data[0];
	    $this->name=$data[1];
	    $this->accountcode=$data[2];
	    $this->bstep=$data[3];
	    $this->currency=$data[4];
	    $this->provider=$data[5];
	    $this->get_rules();
	    return array($this->name,$this->accountcode,$this->bstep,$this->currency,$this->provider,$this->pr_orig,$this->pr_repl);
	}

	function get_list($cond="") {
	    if ($this->provider) 
		$this->db->set_sql_equal("provider",$this->provider);
	    if($cond)
		$this->db->set_sql_like(array("name","accountcode"),$cond);
	    return $this->db->get_cond_list("","tplans","","name");

	}
	
	function insert () {
	    
	    $this->db->insert("tplans",array("name","accountcode","bstep","currency","provider"),
		array($this->name,$this->accountcode,$this->bstep,$this->currency,$this->provider));
	    $this->insert_rules();
	    return true;
	}
	function insert_rules() {
	    
	    for($i=0;$i<5;$i++) {
		$orig=isset($this->pr_orig[$i]) ? $this->pr_orig[$i] : ""; 
		$repl=isset($this->pr_repl[$i]) ? $this->pr_repl[$i] : "";
		if(($orig != $repl) && $orig != "") {
		    $this->db->insert("trules",array("prefix","pr_replace","accountcode"), 
		    array($orig,$repl,$this->accountcode));
		}
	    }
		
	}
	function delete_rules() {

	    $this->db->delete_value("trules","accountcode",$this->accountcode);
	}
	function delete_tplan () {

	    $this->get_data();
	    $this->db->delete_value("tplans","accountcode",$this->accountcode);
	    $this->delete_rules();
	    $this->db->delete_value("rates","accountcode",$this->accountcode);

	}

	function update () {
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("tplans",array("name","accountcode","bstep","currency","provider"),
		array($this->name,$this->accountcode,$this->bstep,$this->currency,$this->provider));
	    $this->delete_rules();
	    $this->insert_rules();
	    $this->db->set_sql_equal("accountcode",$this->accountcode);
	    $this->db->update("rates",array("accountcode"),array($this->accountcode));

	}

	function is_exists () {
	    return $this->db->is_exists("tplans",array("name","accountcode"),array($this->name,$this->accountcode),array("id",$this->id));
	}

} // end class 
?>
