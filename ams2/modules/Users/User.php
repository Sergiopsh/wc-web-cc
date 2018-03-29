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
	var $pswd;
	var $user_auth_id;
	var $first_name;
	var $last_name;
	var $is_admin;
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
	var $status;
	var $preferences;
	var $is_employee;
	var $messenger;
	var $role_name;
	var $acl;
	var $created_by;
	var $modified_by;
	var $theme;
	var $lang;
	var $dateformat;
	var $currency;
	var $num_rows;
	var $list_users;
	var $name_prev;
	var $change_pswd;

	function User($id="",$name="") {
	    global $db;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id= (int) $id;
	    $this->user_name=$name;
	}
	function authenticate($password) {
	    global $strLoginErrorName,$strLoginErrorPassword;

	    $query="SELECT id,user_password,first_name,status,preferences,is_admin,role_name,department,phone_office FROM users WHERE user_name=".quote($this->user_name);
	    $this->db->query($query);
	    if(!$this->db->num_rows()) {

		$_SESSION['login_error']=$strLoginErrorName;
		return false;
	    }
	    $this->db->next_record();
	    $r=$this->db->Record;
	    if(!$r[1] || !$r[3]) {
	    	$_SESSION['login_error']=$strLoginErrorName;
		return false;
	    }
	    if(md5($r[1].md5(md5(session_id()))) != $password) {

	    	$_SESSION['login_error']=$strLoginErrorPassword;
		return false;
	    }


	    unset($_SESSION['login_error']);
	    $this->id=$r[0];
	    $this->first_name=$r[2];	    
	    $list_p=explode(";",$r[4]);
	    $p=explode(":",$list_p[0]);
	    $this->theme=$p[1];
	    $p=explode(":",$list_p[1]);
	    $this->lang=$p[1];
	    $p=explode(":",$list_p[2]);
	    $this->dateformat=$p[1];
	    $p=explode(":",$list_p[3]);
	    $this->currency=$p[1];
	    $this->role_name=$r[6];
	    $this->is_admin=$r[5];
	    $this->department=$r[7];	    		    
	    $this->phone_office=$r[8];	 
	    $this->acl=$this->getACL();
	    if(!$this->acl && ($this->id != 0)) {
			$_SESSION['login_error']=$strLoginErrorName;
			return false;
	    }
	    //$this->pswd=$password;	    
	    return true;
	}
	function Initialize() {
	    global $default_lang,$default_dateformat,$default_theme,$default_currency;
	    $_SESSION['current_user_first_name']=$this->first_name;
	    $_SESSION['auth_user_id']=uniqid(true);
	    $_SESSION['user_name']=$this->user_name;
	    $p=md5($_SESSION['auth_user_id']);
	    $query="UPDATE users SET user_auth_id=".quote($p)." WHERE user_name=".quote($this->user_name);
	    $this->db->query($query);
	    if($this->lang) $_SESSION['lang']=$this->lang;
	    else $_SESSION['lang']=$default_lang;
	    if($this->theme) $_SESSION['theme']=$this->theme;
	    else $_SESSION['theme']=$default_theme;
	    if($this->currency) $_SESSION['currency']=$this->currency;
	    else $_SESSION['currency']=$default_currency;
	    if($this->dateformat) $_SESSION['dateformat']=$this->dateformat;
	    else $_SESSION['dateformat']=$default_dateformat;
	    $_SESSION['is_admin']=$this->is_admin;
	    $_SESSION['department']=$this->department;	    		    
	    $_SESSION['phone_office']=$this->phone_office;	    		    
	    $this->initACL();
	    unset($_SESSION['login_error']);
	    
	}
	

	function initACL() {     
	    global $registered_modules,$admin_only_modules,$hidden_modules,$aux_modules;
	    if($this->id == 0) {//set full access for superuser
		  $mod= new Module(null);		
		  $acl_list=$mod->getACL();
		  foreach ($registered_modules as $rm) {    
		    $_SESSION['acl'][$rm]['Access']=2;
		    if($acl_list[$rm]) {
			foreach($acl_list[$rm] as $key=>$a) {
			    $_SESSION['acl'][$rm][$key]=2;//
			}    
		    }
		  }
		  $_SESSION['root']=true; 
		  return;

	    }
	    foreach ($registered_modules as $rm) {
	      if(in_array($rm,$hidden_modules)) { $_SESSION['acl'][$rm]['Access']=2; continue; }
	      if($this->acl[$rm]) {
		    foreach($this->acl[$rm] as $key=>$a){
			$a=explode(":",$a);
			$_SESSION['acl'][$rm][$key]=$a[0];
		    }	
	      }else $_SESSION['acl'][$rm]['Access']=0;
	    }
	    if($_SESSION['is_admin']) return;
	    //disable admin modules if not admin
	    foreach($admin_only_modules as $am) {
		$_SESSION['acl'][$am]['Access']=0;	    
	    
	    }

	}


	function getList($cond,$admin,$status,$page,$limit) {

	    if($admin) $sql=" AND is_admin ";
	    if ($status == 1) $sql.=" AND status ";
	    elseif ($status == 0) $sql.=" AND (status=0) ";
	    $sql.=" AND LENGTH(user_name) ";
	    if(!$_SESSION['root']) $sql.= " AND id > 0 ";
	    $this->db->set_sql_custom($sql); 
	    $this->db->set_sql_like(array("user_name","first_name","last_name","phone_work","phone_office",
	    "email","messenger","department"),$cond);	    
	    
	    $this->db->set_sql_export(array("user_name","first_name","last_name","department","phone_home","phone_mobile","phone_work","phone_office",
	    "email","messenger","address","comment","status"),"users","","user_name",$page,$limit);
	    
	    $list = $this->db->get_cond_list("","users","","user_name",$page,$limit);
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
	    $this->first_name=$record[4];
	    $this->last_name=$record[5];
	    if($record[6]) $this->is_admin=true;
	    if($record[23]) $this->is_employee=true;
	    $this->title=$record[12];
	    $this->department=$record[13];
	    $this->email=$record[19];
	    $this->address=$record[20];
	    if($record[21]) $this->status=true;
	    $this->phone_work=$record[16];
	    $this->phone_office=$record[17];
	    $this->phone_mobile=$record[15];
	    $this->phone_home=$record[14];
	    $this->messenger=$record[24];
	    $this->comment=$record[7];
	    $this->fax=$record[18];
	    $this->role_name=$record[25];
	    $this->date_entered=$record[8];
	    $this->date_modified=$record[9];
	    $this->created_by=$record[10];
	    $this->modified_by=$record[11];
	    $list_p=explode(";",$record[22]);
	    $p=explode(":",$list_p[0]);
	    $this->theme=$p[1];
	    $p=explode(":",$list_p[1]);
	    $this->lang=$p[1];
	    $p=explode(":",$list_p[2]);
	    $this->dateformat=$p[1];
	    $p=explode(":",$list_p[3]);
	    $this->currency=$p[1];

	    unset($this->acl);
	    if($this->id) $this->acl=$this->getACL();

	}
	
	function getACL() {

	    if($this->role_name) 
		return Role::get_acl_by_name($this->role_name);	    
	    $query="SELECT * FROM users_acl WHERE user_id=".quote($this->id)." ORDER BY module";
	    $this->db->query($query);
	    $num=$this->db->num_rows();
	    for($i=0; $i<$num; $i++) {
		  $this->db->next_record();
	          $rec=$this->db->Record;
		  $m=$rec[2];$n=$rec[3];
		  $acl[$m][$n]=$rec[4];
	    }
	    return $acl;
	}	
	function is_exists($action) {
	    if($action=="EditUser") 
		return $this->db->is_exists("users",array("user_name"),array($this->user_name),array("id",$this->id));
	    else
		return $this->db->is_exists("users",array("user_name"),array($this->user_name));
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
	function isEmployee() {
	      $query="SELECT is_employee FROM users WHERE id=".quote($this->id);
	      $this->db->query($query);
	      $this->db->next_record();
	      return $this->db->Record[0];
	
	}
	
	function deleteMarked($arr) {
	  foreach($arr as $id) {    
	    if(!$id) continue;
	    $this->db->delete_value("users","id",$id);
	    $this->db->delete_value("users_acl","user_id",$id);

	  }    

	}
	function setPreferences($theme,$lang,$dateformat,$currency) {
	    $this->theme=$theme;
	    $this->lang=$lang;
	    $this->dateformat=$dateformat;
	    $this->currency=$currency;
	}
	function set_fields($name,$pswd,$first_name,$last_name,
	    $is_admin,$is_employee,$title,$department,$email,$address,$status,
	    $phone_work,$phone_office,$phone_mobile,$phone_home,$messenger,
	    $fax,$comment,$role_name) {
	    $this->user_name=$name;
	    $this->pswd=$pswd;
	    $this->first_name=$first_name;
	    $this->last_name=$last_name;
	    if($is_admin) $this->is_admin=true;
	    if($is_employee) $this->is_employee=true;
	    $this->title=$title;
	    $this->department=$department;
	    $this->email=$email;
	    $this->address=$address;
	    $this->status=$status;
	    $this->phone_work=$phone_work;
	    $this->phone_office=$phone_office;
	    $this->phone_mobile=$phone_mobile;
	    $this->phone_home=$phone_home;
	    $this->messenger=$messenger;
	    $this->comment=$comment;
	    $this->fax=$fax;
	    $this->role_name=$role_name;
	    
	}
	function setACL($acl) {
	    $this->acl=$acl;
	}
	function insert() {
	    $user=$_SESSION['user_name'];
	    $pswd=md5($this->pswd);
	    $preferences="theme:".$this->theme.";lang:".$this->lang.";dateformat:".$this->dateformat.";currency:".$this->currency;
	    $cols=array("user_name","user_password","first_name","last_name","is_admin",
	    "comment","date_entered","created_by","title","department","phone_home","phone_mobile","phone_work",
	    "phone_office","fax","email","address","status","preferences","is_employee","messenger","role_name");
	    $vals=array($this->user_name,$pswd,$this->first_name,$this->last_name,$this->is_admin,
	    $this->comment,date("Y-m-d H:i:s"),$user,$this->title,$this->department,$this->phone_home,
	    $this->phone_mobile,$this->phone_work,$this->phone_office,$this->fax,$this->email,
	    $this->address,$this->status,$preferences,$this->is_employee,$this->messenger,$this->role_name);
	    $this->db->insert("users",$cols,$vals);
	    if($this->role_name) return;
	    $this->insertACL();
	}
	function insertACL() {
	    if(!$this->id) {
		$query="SELECT id FROM users WHERE user_name=".quote($this->user_name);
		$this->db->query($query);
		$this->db->next_record();
		$id=$this->db->Record[0];
	    }else $id=$this->id;
	    if(!$id) return;

	    foreach($this->acl as $key=>$al) {
	   	foreach($al as $k=>$a) {
		    if(!$k) continue;
		    $this->db->insert("users_acl",array("user_id","module","acl_name","acl"),
		       array($id,$key,$k,$a));
		}     
	    }

	}
	function update() {

	    $preferences="theme:".$this->theme.";lang:".$this->lang.";dateformat:".$this->dateformat.";currency:".$this->currency;
	    $user=$_SESSION['user_name'];
	    $set_pswd="";

	    $this->db->set_sql_equal("id",$this->id);
	    $cols=array("user_name","first_name","last_name","is_admin",
	    "comment","date_modified","modified_by","title","department","phone_home","phone_mobile","phone_work",
	    "phone_office","fax","email","address","status","preferences","is_employee","messenger","role_name");
	    $vals=array($this->user_name,$this->first_name,$this->last_name,$this->is_admin,
	    $this->comment,date("Y-m-d H:i:s"),$user,$this->title,$this->department,$this->phone_home,
	    $this->phone_mobile,$this->phone_work,$this->phone_office,$this->fax,$this->email,
	    $this->address,$this->status,$preferences,$this->is_employee,$this->messenger,$this->role_name);
	    if($this->change_pswd) {
		$pswd=md5($this->pswd);
		array_push($cols,"user_password");
		array_push($vals,$pswd);
	    }

	    $this->db->update("users",$cols,$vals);
	    if(!$this->id) return;
	    $this->db->delete_value("users_acl","user_id",$this->id);
	    if($this->role_name) return;
	    $this->insertACL();

	}
	function update_profile() {

	    $preferences="theme:".$this->theme.";lang:".$this->lang.";dateformat:".$this->dateformat.";currency:".$this->currency;
	    $user=$_SESSION['user_name'];
	    
		if(empty($this->first_name)) $this->first_name=$user;
		$this->db->set_sql_equal("user_name",$user);
	    $cols=array("user_name","first_name","last_name",
	    "comment","date_modified","modified_by","title","department","phone_home","phone_mobile","phone_work",
	    "phone_office","fax","email","address","preferences","messenger");
	    $vals=array($this->user_name,$this->first_name,$this->last_name,
	    $this->comment,date("Y-m-d H:i:s"),$user,$this->title,$this->department,$this->phone_home,
	    $this->phone_mobile,$this->phone_work,$this->phone_office,$this->fax,$this->email,
	    $this->address,$preferences,$this->messenger);
	    if($this->change_pswd) {
		
		$pswd=md5($this->pswd);
		array_push($cols,"user_password");
		array_push($vals,$pswd);

	    }
	    $this->db->update("users",$cols,$vals);
	}
	function update_preferences($theme,$lang,$dateformat,$currency) {
	    $preferences="theme:".$theme.";lang:".$lang.";dateformat:".$dateformat.";currency:".$currency;
	    $user=$_SESSION['user_name'];
	    $this->db->set_sql_equal("user_name",$user);
	    $this->db->update("users",array("preferences"),array($preferences));

	}
	
	
} // end class
?>
