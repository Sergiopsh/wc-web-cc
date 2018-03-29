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
class VirtualFax  {
	
	var $id;
	var $filename = '';
	var $date_entered;
	var $date_modified;
	var $owner;
	var $list_faxes;
	var $db;
	var $created_by;
	var $modified_by;
	var $t_from="";
	var $t_to="";
	var $f_source="";
	var $f_dest="";
	var $max_interval=31;//by default max interval 30 days
	var $sort_date=0;
	var $department;
	var $dep_phones=0;

	function VirtualFax() {
	    global $db;
	    if(!$db) $db=DbConnect;
	    $this->db=$db;
	}
	
	function setFilters($t_from,$t_to,$source,$dest,$sort_date,$department) {
	    $this->t_from=dt_to_dbformat($t_from,"00");
	    $this->t_to=dt_to_dbformat($t_to,"59");
	    $this->f_source=$source;
	    $this->f_dest=$dest;
	    $this->sort_date=$sort_date;
	    $this->department=$department;

	}

	function getList($currentpage,$limit) {
	  global $faxes_dir;
	  $pattern="*-*-*-*";

	  if($this->f_source=="") $pattern.="-*";
	  else $pattern.="-".trim($this->f_source);
	  if($this->f_dest=="") $pattern.="-*";
	  else $pattern.="-".trim($this->f_dest);

	  $pattern.="-*.*.tif";
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

	      $pathD=$faxes_dir."/".date("Y-m-d",$startDay)."/";
	      for (; $startH<=23; $startH++) {
	    	    if($startH<10) $h="0$startH/"; else $h=$startH."/";
	    	    $pathH=$pathD.$h;
		    foreach(@glob($pathH.$pattern) as $f) {
		        $cf=substr(basename($f),0,16); $cf[10]=" ";
			$cf=strtotime($cf);
			if($cf > $endTime) break 3;
			if($cf >= $startTime) {
			    $file=basename($f);
			     $size=filesize($f);
			   if(($i>=$min) && ($i<$max)) {
			
			     $dt=substr($file,0,16);
			     $file=substr($file,20);
			     $file=substr($file,0,-4);
			     $p=explode("-",$file);
			
			    $this->list_faxes[$index][0]=dbformat_to_dt($dt);
			    $this->list_faxes[$index][1]=$f;
			    $this->list_faxes[$index][2]=$size;
			    $this->list_faxes[$index][4]=$p[0];//src
			    $this->list_faxes[$index][5]=$p[1];//dest

			    $index++;
			  }	
			 
			  $i++; 
		        }
		      
		      }

	       }
		
		$startDay=strtotime("+1 day",$startDay);
		$startH=0;
	    //$i++;	
	    }
	   }else {

	     $endDay=strtotime(substr($this->t_to,0,10)." 23:59");
	     for($c=0; (($startTime<=$endDay) && ($c<=$this->max_interval)); $c++) {
	      $pathD=$faxes_dir."/".date("Y-m-d",$endDay)."/";
	      for (; $endH>=0; $endH--) {
	    	    if($endH<10) $h="0$endH/"; else $h=$endH."/";
	    	    $pathH=$pathD.$h;
		    
		      foreach(array_reverse(@glob($pathH.$pattern)) as $f) {
		        $cf=substr(basename($f),0,16); $cf[10]=" ";
			$cf=strtotime($cf);
			if($cf < $startTime) break 3;
			if($cf <= $endTime) {
		            $file=basename($f);
			    $size=filesize($f);
			  if(($i>=$min) && ($i<$max)) {

			    $this->list_faxes[$index][0]=dbformat_to_dt(substr($file,0,16));
			    $this->list_faxes[$index][1]=$f;
			    $this->list_faxes[$index][2]=$size;
			    $file=substr($file,20);
			    $file=substr($file,0,-4);
			    $p=explode("-",$file);
			    //$dur=0;
			    $this->list_faxes[$index][4]=$p[0];//src
			    $this->list_faxes[$index][5]=$p[1];//dest
			    $index++;
		          }	
			 $i++; 
			}
		      
		      }

	       }
		$endDay=strtotime("-1 day",$endDay);
		$endH=23;
	    //$i++;	
	    }
	   
	   
	   }
	  $this->num=$i; 	    
	}    


	
} // end class 
?>
