<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * modified by Ben Drushell http://www.technobreeze.com/
 *
 * $Id: db_mysql.inc,v 1.2 2000/07/12 18:22:34 kk Exp $
 *
 */ 

class DB_Sql {
  
  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";

  /* public: configuration parameters */
  var $Auto_Free     = 1;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "report"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.2";
  
  /* sql conditions */
  
  var $sql_like = "";
  var $sql_equal = "";
  var $sql_time = "";
  var $sql_interval = "";
  var $sql_custom = "";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $count = 0;

  /* public: constructor */
  function DB_Sql() {
      //$this->query($query);
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
  }

  /* public: connection management */
  function connect() {
      $Database = $this->Database;
      $Host     = $this->Host;
      $User     = $this->User;
      $Password = $this->Password;
      
    /* establish connection, select database */
    if (!$this->Link_ID ) {
    
      $this->Link_ID=@mysql_pconnect($Host, $User, $Password);
      if (!$this->Link_ID) {
        $this->halt("Error connecting to database...");
        return 0;
      }

      if (!@mysql_select_db($Database,$this->Link_ID)) {
        $this->halt("cannot use database ".$this->Database);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }

  /* public: perform a query */
  function query($query) {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($query == "")  return 0;

    if (!$this->connect()) return 0; 
    
    /* New query, discard previous result.*/
    if ($this->Query_ID) $this->free();
    if ($this->Debug)
      printf("Debug: query = %s<br>\n", $query);
    
    
    
    $this->Query_ID = @mysql_query($query,$this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) $this->halt("Invalid SQL: ".$query);
    return $this->Query_ID;
  }

  function get_limit_list($q,$s,$num) {
     $this->count = 0;
     if($q=="") return;
     if (!$this->connect()) return; 
     if ($this->Query_ID) $this->free();
    $qc="SELECT count(*) ".stristr($q,'from');
    if($this->Debug) printf("Debug: get_limit_list - SQL: %s<br>\n",$qc);
    if(!($r = @mysql_query($qc,$this->Link_ID))) {
     	$this->Errno = mysql_errno();
        $this->Error = mysql_error();
	$this->halt("Invalid SQL: ".$qc);
    }
    if(stristr($qc,"group by")) {
	$this->count=@mysql_num_rows($r);
    }else {
	$n=@mysql_fetch_row($r);	
	$this->count=$n[0];
    }
    @mysql_free_result($r);
    if(!$this->count) return;
    $q.=" LIMIT $s,$num";
    if($this->Debug) printf("Debug: get_limit_list - SQL: %s<br>\n",$q);
    if((!$r = @mysql_query($q,$this->Link_ID))) {
     	$this->Errno = mysql_errno();
        $this->Error = mysql_error();
	$this->halt("Invalid SQL: ".$q);
    }
    $i=0;
    while($row=@mysql_fetch_row($r)) {$l[$i++]=$row;}
    @mysql_free_result($r);
    return $l;
  }
  function get_list($q) {
     $this->count = 0;
     if($q=="") return;
     if (!$this->connect()) return; 
     if ($this->Query_ID) $this->free();
     if($this->Debug) printf("Debug: get_list - SQL: %s<br>\n",$q);
     if(!($r=@mysql_query($q,$this->Link_ID))) {
     	$this->Errno = mysql_errno();
        $this->Error = mysql_error();
	$this->halt("Invalid SQL: ".$q);

     }
     if(!($this->count=@mysql_num_rows($r))) return;
     $i=0;
     while($row=@mysql_fetch_row($r)) {$l[$i++]=$row;}
     @mysql_free_result($r);
     return $l;
  }
  function next_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = @mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }

  function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }

  function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }


  /* private: error handling */
  function halt($msg) {
    $this->Error = @mysql_error($this->Link_ID);
    $this->Errno = @mysql_errno($this->Link_ID);
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session terminated.");
  }

  function haltmsg($msg) {
    printf("<b>Database error:</b><i> %s</i><br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
  }


  function set_sql_like($c,$f) {
	    $this->sql_like="";
	    $num = count($c);
	    for ($i=0; $i < $num; $i++) {	    
		if(isset($f[$i]) && $f[$i] != "") $this->sql_like .= " AND ".$c[$i]." LIKE ".quote(str_replace('*','%',$f[$i]))." ";
	    }

	}
	
  function set_sql_interval($field,$int) {
	    $this->sql_interval="";
	    if(!is_array($field)) {
	        if(is_numeric($int[0])) $this->sql_interval=" AND $field >= ".quote($int[0])." ";
		if(is_numeric($int[1])) $this->sql_interval.=" AND $field <= ".quote($int[1])." ";
		return;
	    }
	    $i=0;
	    foreach($field as $f) {
	        if(is_numeric($int[$i][0])) $this->sql_interval.=" AND $f >= ".quote($int[$i][0])." ";
		if(is_numeric($int[$i][1])) $this->sql_interval.=" AND $f <= ".quote($int[$i][1])." ";
		$i++;
	    }
  }	
  function set_sql_equals($c,$f) {
	    $this->sql_equal="";
	    $num = count($c);
	    for ($i=0; $i < $num; $i++) {	    
		if(isset($f[$i]) && $f[$i] != "") $this->sql_equal .= " AND ".$c[$i]." = ".quote($f[$i])." ";
	    }

	}
  function set_sql_equal($c,$f) {
		
	    if(isset($f) && $f != "") $this->sql_equal .= " AND ".$c." = ".quote($f)." ";
	
	}
  function set_sql_custom($sql) {
		
	    $this->sql_custom = $sql;
	
	}
  function set_sql_time($field,$from,$to) {
	    $this->sql_time = " AND UNIX_TIMESTAMP($field) >= UNIX_TIMESTAMP('".$from."') AND UNIX_TIMESTAMP($field) <= UNIX_TIMESTAMP('".$to."') ";
	
  }
  function get_cond_list($cols,$table,$group_by="",$order_by="",$page=0,$limit=0) {
	
	    $query = "SELECT ";
	    $n = count($cols)-1;
	    for($i=0; $i <= $n; $i++) {
		$query .= $cols[$i];
		if($i < $n) $query .= ",";

	    }
	    if(empty($cols)) $query.="* ";
	    $query .= " FROM $table WHERE 1 ".$this->sql_like.$this->sql_equal.$this->sql_time.$this->sql_interval.$this->sql_custom;
	    if($group_by) $query .= " GROUP BY ".$group_by;
	    if($order_by) $query .= " ORDER BY ".$order_by;
	    $this->reset_sql();
	    if($limit) return $this->get_limit_list($query,$page*$limit,$limit);
	    else return $this->get_list($query);
   }
   function set_sql_export($cols,$table,$group_by="",$order_by="") {
	
	    $query = "SELECT ";
	    $n = count($cols)-1;
	    for($i=0; $i <= $n; $i++) {
		$query .= $cols[$i];
		if($i < $n) $query .= ",";

	    }
	    if(empty($cols)) $query.="* ";
	    $query .= " FROM $table WHERE 1 ".$this->sql_like.$this->sql_equal.$this->sql_time.$this->sql_interval.$this->sql_custom;
	    if($group_by) $query .= " GROUP BY ".$group_by;
	    if($order_by) $query .= " ORDER BY ".$order_by;
	    $_SESSION['sql_export']=$query;
	    if($this->Debug) printf("Debug: Export SQL = %s<br>\n",$query);
	    
   }
   function delete_values($table) {
	$query = "DELETE FROM $table WHERE 1 ".$this->sql_like.$this->sql_equal.$this->sql_time.$this->sql_interval.$this->sql_custom;
	$this->query($query);
	$this->reset_sql();
   }
   function delete_value($table,$col,$val) {
	$query = "DELETE FROM $table WHERE $col = ".quote($val);
	$this->query($query);
   }
   function delete_array($table,$col,$arr) {
	if(empty($arr)) return;
	foreach ($arr as $a) {
	    $query = "DELETE FROM $table WHERE $col = ".quote($a)."";
	    $this->query($query); 
	}  
	
   }
   function reset_sql() {
	$this->sql_like=$this->sql_equal=$this->sql_interval=$this->sql_time=$this->sql_custom="";
   }
   function is_exists($table,$cols,$cond,$excond="") {
	$query="SELECT * FROM $table WHERE 1 AND (";
	$n = count($cols)-1;
	for($i=0; $i <= $n; $i++) {
	    $query.= $cols[$i]."=".quote($cond[$i]);
	    if($i < $n) $query.=" OR "; else $query.=")";   
	}
	if(!empty($excond)) $query.=" AND ".$excond[0]."<>".quote($excond[1])." ";
	$query.=" LIMIT 1";
	$this->query($query);
        if($this->num_rows()) return true;
	return false;
   }
   function insert($table,$cols,$vals) {
    
	$q = "INSERT INTO $table (";
	foreach($cols as $c) {  $q .=$c.","; }
	$q[strlen($q)-1]=")";
	$q .= " VALUES (";
	foreach($vals as $v) {  $q .=quote($v).","; }
   	$q{strlen($q)-1}=")";
	$this->query($q);
	return @mysql_insert_id($this->Link_ID);
   }
   function update($table,$cols,$vals) {

	$q = "UPDATE $table SET ";
	for($i=0; $i < count($cols);$i++) { $q .=$cols[$i]."=".quote($vals[$i]).","; }
	$q{strlen($q)-1}=" ";
	$q .= " WHERE 1 ".$this->sql_like.$this->sql_equal.$this->sql_time.$this->sql_interval.$this->sql_custom;
	$this->query($q);
	$this->reset_sql();
   }
}
?>
