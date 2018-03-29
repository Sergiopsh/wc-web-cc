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
class Module  {
	
	var $id;
	var $hidden;
	var $main;
	var $admin_only;
	var $date_entered;
	var $date_modified;
	var $created_by;
	var $modified_by;
	var $num_rows=0;
	var $list_mods;
	var $icon;
	var $action;
	var $acl;
	var $db;
	var $tbl_exists=false;

	function Module($id="") {
	    global $db,$db_name;
	    if(!$db) $db=DbConnect();
	    $this->db=$db;
	    $this->id= (int) $id;
	    $query="SHOW TABLE STATUS FROM $db_name LIKE 'modules'";
	    $db->query($query);
	    if($db->num_rows()) $this->tbl_exists=true;


	}
	function getList($cond,$page,$limit) {
	      if(!$this->tbl_exists) return;
	    //$user=$_SESSION['user_name'];
	      $min=$page * $limit;
	      $f_name=!is_empty($cond[0]) ? $cond[0] : '%';
	      $query="SELECT * FROM modules WHERE name LIKE ".quote(str_replace('*','%',$f_name))." ORDER BY menu_id";
	      unset($this->list_mods);
	      $this->list_mods=$this->db->get_limit_list($query,$min,$limit);
	      $this->num_rows=$this->db->count;
	}
	function getACL() {
	      if(!$this->tbl_exists) return array();
	      $query="SELECT name,acl FROM modules WHERE 1 ORDER BY name";
	      $this->db->query($query);
	      $this->num_rows=$num=$this->db->num_rows();
	      for($i=0; $i<$num; $i++) {
		$this->db->next_record();
		$record=$this->db->Record;
		$name=$record[0];
		$acl=$record[1];
		$acl=explode(";",$acl);

		foreach($acl as $a) {
		    $t=explode(":",$a);
		    $j=$t[0];
		    if($j) {
		      $acl_list[$name][$j][0]=$t[1];
		      $acl_list[$name][$j][1]=$t[2];
		      $acl_list[$name][$j][2]=$t[3];

		    }
		 //$j++;
	        }
	      }
	    return $acl_list;
	}



	function getData () {
	    if(!$this->tbl_exists) return;
	    $query="SELECT * FROM modules WHERE id=".quote($this->id);	
	    $this->db->query($query);
	    $this->db->next_record();
	    $record=$this->db->Record;
	    if($record[2]) $this->hidden=true;
	    if($record[3]) $this->main=true;
	    if($record[4]) $this->admin_only=true;
	    $this->name=$record[1];
	    $this->icon=$record[5];
	    $this->date_entered=$record[8];
	    $this->date_modified=$record[9];
	    $this->created_by=$record[6];
	    $this->modified_by=$record[7];
	    $this->action=$record[12];
	    $acl=$record[10];
	    $acl=explode(";",$acl);
	    $i=0;
	    unset($this->acl);
	    foreach($acl as $a) {
		$t=explode(":",$a);
		$this->acl[$i][0]=$t[0];
		$this->acl[$i][1]=$t[1];
		$this->acl[$i][2]=$t[2];
		$this->acl[$i][3]=$t[3];
	     $i++;
	    }

	}
	
	function deleteModule() {

	    $this->db->delete_value("modules","id",$this->id);

	}

	function insert() {
	    if(!$this->tbl_exists) return;
	    $user=$_SESSION['user_name'];
	    $str_acl="";
	    if(is_array($this->acl)) {
	       foreach($this->acl as $a) {
		if ($a[0]) $str_acl.=$a[0].":".$a[1].":".$a[2].":".$a[3].";";
	       }
	     }
	    $str_acl=substr($str_acl,0,strlen($str_acl)-1);
	    $this->db->insert("modules",array("name","hidden","main","admin","icon","date_entered","created_by","action","acl"), 
		array($this->name,$this->hidden,$this->main,$this->admin,$this->icon,date("Y-m-d H:i:s"),$user,$this->action,$str_acl));

	    
	}
	function update() {
	    if(!$this->tbl_exists) return;
	    $user=$_SESSION['user_name'];
	    $str_acl="";
	    if(is_array($this->acl)) {
	       foreach($this->acl as $a) {
	        if($a[0]) $str_acl.=$a[0].":".$a[1].":".$a[2].":".$a[3].";";
	       }
	     }
	    $str_acl=substr($str_acl,0,strlen($str_acl)-1);
	    $this->db->set_sql_equal("id",$this->id);
	    $this->db->update("modules",array("name","hidden","main","admin","icon","date_modified","modified_by","action","acl"), 
		array($this->name,$this->hidden,$this->main,$this->admin,$this->icon,date("Y-m-d H:i:s"),$user,$this->action,$str_acl));	    

	}
	function makeConfig() {
	    if(!$this->tbl_exists) return;
	     $query="SELECT name,hidden,main,admin,icon,action FROM modules ORDER BY menu_id";
	      $this->db->query($query);
	      $num=$this->db->num_rows();
	      for($i=0; $i<$num; $i++) {
		$this->db->next_record();
		$list[$i] = $this->db->Record;
    	      }
	      $file=fopen("include/modules.php","w");	    
	      fputs($file,"<?php\n\$registered_modules=array(");
	      for($i=0; $i<$num; $i++) {
		fputs($file,"\"".$list[$i][0]."\",");
    	      }
	      fseek($file,-1,SEEK_CUR); 
	      fputs($file,");\n\$admin_only_modules=array(");
	      for($i=0; $i<$num; $i++) {
	        if($list[$i][3]) {
	    	   fputs($file,"\"".$list[$i][0]."\",");
		}
    	      }
	      fseek($file,-1,SEEK_CUR); 
	      fputs($file,");\n\$aux_modules=array(");
	      for($i=0; $i<$num; $i++) {
	        if(empty($list[$i][2]) && ($list[$i][0]<>"Administration")) {
	    	   fputs($file,"\"".$list[$i][0]."\",");
		}
    	      }
	      fseek($file,-1,SEEK_CUR); 
	      fputs($file,");\n\$hidden_modules=array(");
	      for($i=0; $i<$num; $i++) {
	        if($list[$i][1]) {
	    	   fputs($file,"\"".$list[$i][0]."\",");
		}
    	      }
	      fseek($file,-1,SEEK_CUR); 
	      fputs($file,");\n\$main_menu=array(");
	      for($i=0; $i<$num; $i++) {
	        if($list[$i][2] && ($list[$i][0]<>"Administration")) {
	    	   fputs($file,"array(array(\"".$list[$i][0]."\",");
		   if($list[$i][4]) fputs($file,"\"images/".$list[$i][4]."\"),");
		   else fputs($file,"\"\"),");
		   fputs($file,"\$str".$list[$i][0].",\"".$list[$i][5]."\"),");
		}
    	      }
	      fseek($file,-1,SEEK_CUR); fputs($file,");\n?>");
	      fclose($file);
	}

	function sortMenu($s) {
	    if(!$this->tbl_exists) return;
	      $query="SELECT id FROM modules WHERE (main) ORDER BY menu_id";
	      $this->db->query($query);
	      $num=$this->db->num_rows();
	      for($i=0; $i<$num; $i++) {
		$this->db->next_record();
		$rec = $this->db->Record;
		$id[$i]=$rec[0];
	    
	      }
	    $s=explode(",",$s);
	    $i=0;
	    foreach($s as $sort_id) {
		$j=$id[$sort_id];
		//echo "new menu_id=$sort_id  i=$j <br>";
	      $query="UPDATE modules SET menu_id=$i WHERE id=$j"; 
	      $this->db->query($query);
	      $i++;
	    }
	}
	function insertACL() {
	
	  $str_acl="";
	  if(is_array($this->acl)) {
	    foreach($this->acl as $a) {
		$str_acl.=$a[0]."-".$a[1].":".$a[2].":".$a[3].";";
	    
	    }
	  }
	}
	


} // end class
?>
