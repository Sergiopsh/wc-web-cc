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
class Recording  {
	
	var $id;
	var $filename = '';
	var $date_entered;
	var $date_modified;
	var $owner;
	var $list_recs;
	var $db;
	var $created_by;
	var $modified_by;
	var $t_from="";
	var $t_to="";
	var $f_source="";
	var $f_dest="";
	var $f_dir="";
	var $f_rulename;
	var $num=0;
	var $rule_id;
	var $rule_name;
	var $invert;
	var $rule_status;
	var $rule_dir;
	var $rule_data;
	var $list_rules;
	var $list_rules_dst;
	var $list_rules_src;
	var $max_interval=31;//by default max interval 31 days
	var $sort_date=0;
	var $department;
	var $total_dur=0;
	var $f_rule_name="";
	var $f_rule_dir;
	var $f_rule_src="";
	var $f_rule_dst="";

	function Recording() {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	}
	
	function setFilters($t_from,$t_to,$source,$dest,$dir,$sort_date,$department) {
	    $this->t_from=dt_to_dbformat($t_from,"00");
	    $this->t_to=dt_to_dbformat($t_to,"59");
	    $this->f_source=$source;
	    $this->f_dest=$dest;
	    $this->f_dir=$dir;
	    $this->sort_date=$sort_date;
	    $this->department=$department;
	}
	function setRulesFilters($name,$source,$dest,$dir) {
	    $this->f_rule_name=is_empty($name) ? "'%'" : quote(str_replace('*','%',$name));
	    $this->f_rule_src=is_empty($source) ?"'%'": quote(str_replace('*','%',$source));
	    $this->f_rule_dst=is_empty($dest) ?"'%'" : quote(str_replace('*','%',$dest));
	    $this->f_rule_dir= (is_empty($dir) || $dir=='a') ? "'%'" : quote($dir);

	}

	function getList($currentpage,$limit) {
	  global $monitor_dir;
	  $pattern="*-*-*-*";

	  if($this->f_source=="") $pattern.="-*";
	  else $pattern.="-".trim($this->f_source);
	  if($this->f_dest=="") $pattern.="-*";
	  else $pattern.="-".trim($this->f_dest);
	  switch ($this->f_dir) {
		case "i": $pattern.="-i";break;
		case "o": $pattern.="-o";break;
		default:  $pattern.="-*";break;
	  }
	  $pattern.="-*{-,.}*.WAV";
	  $startTime=strtotime(substr($this->t_from,0,16));
	  $endTime=strtotime(substr($this->t_to,0,16));
	  $startH=(int) substr($this->t_from,11,2);
	  $endH=(int) substr($this->t_to,11,2);
	  $i=0;$index=0;
	  $min=$currentpage*$limit;
	  $max=($currentpage+1)*$limit;
	  if($this->sort_date) {
	  $startDay=strtotime(substr($this->t_from,0,10)." 00:00");	  
	  for($c=0; (($startDay<=$endTime) && ($c<=$this->max_interval)); $c++) {
	      $pathD=$monitor_dir."/".date("Y-m-d",$startDay)."/";
	
	      for (; $startH<=23; $startH++) {
	    	    if($startH<10) $h="0$startH/"; else $h=$startH."/";
	    	    $pathH=$pathD.$h;
		    
		    foreach(@glob($pathH.$pattern,GLOB_BRACE) as $f) {
		        $cf=substr(basename($f),0,16); $cf[10]=" ";
			$cf=strtotime($cf);
			if($cf > $endTime) break 3;
			if($cf >= $startTime) {
			 $file=basename($f);
			 $size=filesize($f);
			 $dur= intval($size/1580);		
			 if($size > 50) {
			   if(($i>=$min) && ($i<$max)) {
			
			     $dt=substr($file,0,16);
			     $file=substr($file,20);
			     $file=substr($file,0,-4);
			     $p=explode("-",$file);
			
			    $this->list_recs[$index][0]=dbformat_to_dt($dt);
			    $this->list_recs[$index][1]=$f;
			    $this->list_recs[$index][2]=$size;
			    
			    $this->list_recs[$index][4]=$p[0];//src
			    $this->list_recs[$index][5]=$p[1];//dest
			    $this->list_recs[$index][6]=$p[2];//direction
			    $this->list_recs[$index][7]=$dur;//duration
			    $index++;
			  }	
			 
			  $i++; $total_dur+=$dur;
			 }
		      
		        }
		      }

	       }
		$startDay=strtotime("+1 day",$startDay);
		$startH=0;

	    }
	   }else {
	     $endDay=strtotime(substr($this->t_to,0,10)." 23:59");
	     for($c=0; (($startTime<=$endDay) && ($c<=$this->max_interval)); $c++) {
	      $pathD=$monitor_dir."/".date("Y-m-d",$endDay)."/";

	      for (; $endH>=0; $endH--) {

	    	    if($endH<10) $h="0$endH/"; else $h=$endH."/";
	    	    $pathH=$pathD.$h;
		      foreach(array_reverse(@glob($pathH.$pattern,GLOB_BRACE)) as $f) {
		        $cf=substr(basename($f),0,16); $cf[10]=" ";
			$cf=strtotime($cf);
			if($cf > $endTime) break 3;
			if($cf >= $startTime) {
			    $file=basename($f);
			    $size=filesize($f);
			    $dur=intval($size/1580);
			if($size > 50) {
			  if(($i>=$min) && ($i<$max)) {
			    $this->list_recs[$index][0]=dbformat_to_dt(substr($file,0,16));
			    $this->list_recs[$index][1]=$f;
			    $this->list_recs[$index][2]=$size;
			    $file=substr($file,20);
			    $file=substr($file,0,-4);
			    $p=explode("-",$file);
			    //$dur=0;
			    $this->list_recs[$index][4]=$p[0];//src
			    $this->list_recs[$index][5]=$p[1];//dest
			    $this->list_recs[$index][6]=$p[2];//direction
			    $this->list_recs[$index][7]=$dur;//duration
			    $index++;
		          }	
			 $i++; $total_dur+=$dur;
			 }
			}
		      }

	       }
		$endDay=strtotime("-1 day",$endDay);
		$endH=23;

	    }
	   
	   
	   }
	  //stoplist: 
	  $this->num=$i; $this->total_dur=$total_dur;	    

	}    

	function insertRule() {
	    $user=$_SESSION['user_name'];

	    $this->db->insert("recrules",array("name","created_by","date_entered","direction","status","invert"),
	    array($this->rule_name,$user,date("Y-m-d H:i:s"),$this->rule_dir,$this->rule_status,$this->invert));

	    $query="SELECT id FROM recrules WHERE name = ".quote($this->rule_name);
	    $this->db->query($query);
	    $this->db->next_record();
	    $rule_id= (int) $this->db->Record[0];

	    $dst=explode(",",$this->list_rules_dst);
	    $src=explode(",",$this->list_rules_src);
	    foreach($dst as $d) {
		$d=trim($d);
		$this->db->insert("recrules_dst",array("rule_id","dst"),array($rule_id,$d));
	    }
	    foreach($src as $s) {
	    	$s=trim($s);
		$this->db->insert("recrules_src",array("rule_id","src"),array($rule_id,$s));

	    }
	}
	function updateRule() {
	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("id",$this->rule_id);
	    $this->db->update("recrules",array("name","modified_by","date_modified","direction","status","invert"),
	    array($this->rule_name,$user,date("Y-m-d H:i:s"),$this->rule_dir,$this->rule_status,$this->invert));
	    $this->db->delete_value("recrules_dst","rule_id",$this->rule_id);
	    $this->db->delete_value("recrules_src","rule_id",$this->rule_id);

	    $dst=explode(",",$this->list_rules_dst);
	    $src=explode(",",$this->list_rules_src);
	    $rule_id= (int) $this->rule_id;
	    foreach($dst as $d) {
	    	$d=trim($d);
		$this->db->insert("recrules_dst",array("rule_id","dst"),array($rule_id,$d));
	    }
	    foreach($src as $s) {
	    	$s=trim($s);
		$this->db->insert("recrules_src",array("rule_id","src"),array($rule_id,$s));

	    }
	}
	function getRulesList($page,$limit) {
	
	    $query="SELECT * FROM recrules WHERE name LIKE ".$this->f_rule_name." AND 
	    direction LIKE ".$this->f_rule_dir." AND 
	    (SELECT id FROM recrules_dst WHERE (rule_id=recrules.id AND dst LIKE ".$this->f_rule_dst.") LIMIT 1) AND
	    (SELECT id FROM recrules_src WHERE (rule_id=recrules.id AND src LIKE ".$this->f_rule_src.") LIMIT 1) ORDER BY name";

	    unset($this->list_rules); $min=$page*$limit;
	    $this->list_rules=$this->db->get_limit_list($query,$min,$limit);
	    $this->num=$this->db->count;
	    unset($this->list_rules_dst,$this->list_rules_src);
	    if($this->num) {
	      foreach($this->list_rules as $lr) {
		$id=$lr[0];
		$this->getDstSrcById($id);
	      }
	    }
	}
	function getRuleData() {
	    $query="SELECT * FROM recrules WHERE id=".quote($this->rule_id);
	    $this->db->query($query);
	    if($this->db->num_rows()) {
		$this->db->next_record();
		$this->rule_data=$this->db->Record;
	    }
	}    
	function getDstSrcById($id) {
		$query="SELECT * FROM recrules_dst WHERE rule_id=".quote($id)." ORDER BY dst";
		$this->list_rules_dst[$id]=$this->db->get_list($query);
		$query="SELECT * FROM recrules_src WHERE rule_id=".quote($id)." ORDER BY src";
		$this->list_rules_src[$id]=$this->db->get_list($query);
	}
	function deleteRule() {

	    $this->db->delete_value("recrules","id",$this->rule_id);
	    $this->db->delete_value("recrules_dst","rule_id",$this->rule_id);
	    $this->db->delete_value("recrules_src","rule_id",$this->rule_id);

	}
	function isRuleExists($name,$id) {

	    return $this->db->is_exists("recrules",array("name"),array($name),array("id",$id));
	}
	
} // end class 
?>
