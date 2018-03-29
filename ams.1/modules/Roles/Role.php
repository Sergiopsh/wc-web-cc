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
class Role  {
	
	var $id="";
	var $name = '';
	var $desc = '';
	var $date_entered;
	var $date_modified;
	var $list_roles;
	var $name_prev;
	var $acl;
	var $db;
	var $created_by;
	var $modified_by;
	var $cols = array("id","date_entered","date_modified","modified_by","created_by","name","description");

	function Role($id="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id=(int) $id;    
	}
	function set_fields($name,$desc,$acl,$name_prev="") {
	    $this->name=$name;
	    $this->desc=$desc;
	    $this->acl=$acl;
	    $this->name_prev=$name_prev=$name_prev;
	}
	function get_data() {

	    if ($this->id != "") 
		$query="SELECT * FROM roles WHERE id=".quote($this->id);
	    else $query="SELECT * FROM roles WHERE name=".quote($this->name);

	    $this->db->query($query);
	    $this->db->next_record();
	    $record=$this->db->Record;
	    $this->id=$record[0];
	    $this->name=$record[5];
	    $this->desc=$record[6];
	    $this->date_entered=$record[1];
	    $this->date_modified=$record[2];
	    $this->created_by=$record[4];
	    $this->modified_by=$record[3];
	    $query="SELECT * FROM roles_acl WHERE role_id=".quote($this->id)." ORDER BY module";
	    $this->db->query($query);
	    $num=$this->db->num_rows();
	    unset($this->acl);
	    for($i=0; $i<$num; $i++) {
	    	$this->db->next_record();
		$rec=$this->db->Record;
		$m=$rec[2];$n=$rec[3];
		$this->acl[$m][$n]=$rec[4];
	    }
	    return array($this->name,$this->desc,$this->acl);
	}
	function get_acl_by_name($name) {
	    global $db;
	    if (!$db) $db=DbConnect();
	    $query="SELECT acl_name,acl,module FROM roles_acl WHERE role_id=(SELECT id FROM roles WHERE name=".quote($name).") ORDER BY module";
	    $db->query($query);
	    $num=$db->num_rows();
	    for($i=0; $i<$num; $i++) {
	    	$db->next_record();
		$rec=$db->Record;
		$m=$rec[2];$n=$rec[0];
		$acl[$m][$n]=$rec[1];
	    }
	    return $acl;
	}
	function get_list($cond="") {

	    $this->db->set_sql_like(array("name"),$cond);
	    $this->list_roles = $this->db->get_cond_list($this->cols,"roles","","name");
	    $this->num_rows = $this->db->count;
	    return $this->list_roles;
	}
	
	function insert () {
	    $user=$_SESSION['user_name'];
	    $this->db->insert("roles",array("name","date_entered","description","created_by"),array($this->name,date("Y-m-d H:i:s"),$this->desc,$user)); 
	    $query="SELECT id FROM roles WHERE name=".quote($this->name); 
	    $this->db->query($query);
	    $this->db->next_record();
	    $id=$this->db->Record[0];
	    $this->insertACL($id);
	}

	function insertACL ($id) {
	    foreach($this->acl as $key=>$al) {
	      foreach($al as $k=>$a) {
	       if($k)    
	         $this->db->insert("roles_acl",array("role_id","module","acl_name","acl"),array($id,$key,$k,$a)); 
	      }
	    }
	}


	function delete_role () {

	    $this->db->delete_value("roles","id",$this->id);
	    $this->db->delete_value("roles_acl","role_id",$this->id);

	}

	function update () {
	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("roles",array("name","description","date_modified","modified_by"),
	    array($this->name,$this->desc,date("Y-m-d H:i:s"),$user));
	    $this->db->delete_value("roles_acl","role_id",$this->id);
	    $this->insertACL($this->id);
	    if($this->name != $this->name_prev) {

		$this->db->set_sql_equal("role_name",$this->name_prev);
		$this->db->update("users",array("role_name"),array($this->name));

	    }
	}
	function is_exists () {
	    
	    return $this->db->is_exists("roles",array("name"),array($this->name),array("id",$this->id));
	    
	}

	function prepareACLTable($acl_list) {
	    global $registered_modules,$admin_only_modules,$hidden_modules;

	    $acl_names=array();
	    $i=0;
	    if(!empty($acl_list)) {
		foreach($acl_list as $key=>$al) {
		    if(!in_array($key,$registered_modules) || in_array($key,$hidden_modules)) continue;
		    foreach($al as $k=>$a) $acl_names[$k]++;
		    
		}
	    }
	    arsort($acl_names);
	    $acl_names=array_keys($acl_names);
	    //print_r($acl_names);
	    $num_td=count($acl_names)+1;
	    $width_tbl=15*($num_td+1);
	    if($width_tbl < 70) $width_tbl=70;
	    if($width_tbl > 100) $width_tbl=100;
	    $w1=round(100/($num_td+1))+10;
	    if($w1>40) $w1=40;
	    $w_td=round((100 - $w1)/$num_td);
	    $i=0; $l=count($registered_modules);
	    $arr=array_diff(array_merge(array_diff($registered_modules,$admin_only_modules),$admin_only_modules) ,$hidden_modules);
	    return array($acl_names,$arr,$width_tbl,$w1,$w_td,$num_td);
	}

	function showACLTable($acl_list,$acl,$func="",$disable=true) {
	    global $admin_only_modules,$strACL,$strAccess,$strFull,$strDisable,$strView,$strModule,$strDblClkEditACL;
	    list($acl_names,$arr,$width_tbl,$w1,$w_td,$num_td)=$this->prepareACLTable($acl_list);
	    echo "<table width=\"$width_tbl%\" border=\"0\"  class=\"data-tbl2\" cellpadding=\"2\" cellspacing=\"0\">";
	    echo "<tr><th nowrap width=\"$w1%\" id=\"th_1\" rowspan=2 align=center>$strModule</th>";
	    echo "<th colspan=20 id=\"th_2\" align=center>$strACL</th></tr>";
	    echo "<tr><th align=center>$strAccess</th>";
	    foreach($acl_names as $a) {
		echo "<th width=\"$w_td%\" align=center>".getStr($a)."</th>";
	    }
	    echo "</tr>";
	    foreach ($arr as $rm) {
	    if(!$acl[$rm]) $acl[$rm]['Access']=$disable ? "0:Disable" : "2:Full";
                echo "<tr><td id=\"td_1\" align=\"center\"";
		if(in_array($rm,$admin_only_modules)) echo "style='color: red';";
		echo ">".getStr($rm)."</td>";
		echo "<td align=center ondblclick=\"$func(this,'$rm','Access','Disable','View','Enable','$strDisable','$strView','$strFull')\">";
		    $a=explode(":",$acl[$rm]['Access']);
		    switch ($a[0]) {
			case "0"; echo "<font color=red>$strDisable</font>";break;
			case "1"; echo "<font color=blue>$strView</font>";break;
		    	case "2"; echo "<font color=green>$strFull</font>";break;
		    }
		    echo "<input type=\"hidden\" name=\"acl[$rm][Access]\" id=\"acl[$rm][Access]\" value=\"".$acl[$rm]['Access']."\">";
		    echo "</td>";
		
		foreach($acl_names as $an) {
		
			if(!$acl_list[$rm][$an]) { echo "<td>&nbsp;</td>"; continue; }
			$a1=$acl_list[$rm][$an][0]; 
			$a2=$acl_list[$rm][$an][1];
			$a3=$acl_list[$rm][$an][2];	        
			$s1=getStr($a1);
			$s2=getStr($a2);
			$s3=getStr($a3);
			echo "<td style=\"color: blue;\" align=\"center\" ondblclick=\"$func(this,'$rm','$an','$a1','$a2','$a3','$s1','$s2','$s3')\">";
			if($acl[$rm][$an]) {
		  //
			    $a=explode(":",$acl[$rm][$an]);
			    echo getStr($a[1]);
			    echo "<input type=\"hidden\" name=\"acl[$rm][$an]\" id=\"acl[$rm][$an]\" value=\"".$acl[$rm][$an]."\">";
			}else {
			    if($a3) { echo $s3;$val="3:".$a3; }
			    elseif ($a2) { echo $s2;$val="1:".$a2; } 
			    else { echo $s1; $val="0:".$a1; }
			    echo "<input type=\"hidden\" name=\"acl[$rm][$an]\" id=\"acl[$rm][$an]\" value=\"$val\">";
			}
			echo "</td>";
        
        	    }	
		    echo "</tr>";
		}
		echo "</table><div class='module-note'>&nbsp;<font size=1>$strDblClkEditACL</font></div>";
	    	
	}

} // end class 
?>