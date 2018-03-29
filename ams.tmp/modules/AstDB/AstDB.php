<?


class AstDB extends AmsAMI {

    var $num;
    var $list_db;
    var $db_fam;
    var $db_key;
    var $db_val;
    var $f_fam;
    var $f_fam_type=0;
    var $f_key;
    var $f_key_type=0;
    var $f_val;
    var $f_val_type=0;
    var $res;
    
    function AstDB () {

    }
    
    function setFilters($f,$k,$v) {

	if($f=="") $this->f_fam_type=0;
	elseif($f[0]=='*') {
	    if($f[strlen($f)-1]=='*') $this->f_fam_type=4; 
	    else $this->f_fam_type=3;
	}
	elseif($f[strlen($f)-1]=='*') $this->f_fam_type=2;
	else $this->f_fam_type=1;
	if($k=="") $this->f_key_type=0;
	elseif($k[0]=='*') {
	    if($k[strlen($k)-1]=='*') $this->f_key_type=4; 
	    else $this->f_key_type=3;
	}
	elseif($k[strlen($k)-1]=='*') $this->f_key_type=2;
	else $this->f_key_type=1;
	if($v=="") $this->f_val_type=0;
	elseif($v[0]=='*') {
	    if($v[strlen($v)-1]=='*') $this->f_val_type=4;
	    else $this->f_val_type=3;
	}
	elseif($v[strlen($v)-1]=='*') $this->f_val_type=2;
	else $this->f_val_type=1;
	$f=trim($f," *");
	$k=trim($k," *");
	$v=trim($v," *");
	if($f=="") $this->f_fam_type=0;
	if($k=="") $this->f_key_type=0;
	if($v=="") $this->f_val_type=0;
	$this->f_fam=$f;
	$this->f_key=$k;
	$this->f_val=$v;
    }
    
    function deleteRecord() {
	global $strDeleteEntryFailed, $strDeleteEntrySuccess;
	$action="database del $this->db_fam $this->db_key";
	$id=$this->cmd($action);
	if(stristr($this->response[$id][0],"Database entry removed"))
	    //$this->res=$strDeleteEntrySuccess;
	    $this->res="";
	else $this->res=$strDeleteEntryFailed;
    }

    function deleteSelected($list) {
	global $strDeleteEntryFailed, $strDeleteSelectedSuccess;
	$i=0;
	foreach($list as $l) {
	    $t=explode("__%%%__",$l);
	    $action[$i]="database del $t[0] $t[1]";
	    $i++;
	}
	
	$id=$this->cmd($action);
	if(strstr($this->response[$id][0],"Database entry removed"))
	    //$this->res=$strDeleteSelectedSuccess;
	    $this->res="";
	else $this->res=$strDeleteEntryFailed;
    }

    function insertRecord() {
    	global $strInsertEntryFailed, $strInsertEntrySuccess;
	$action="database put $this->db_fam $this->db_key $this->db_val";
	$id=$this->cmd($action);
	if(stristr($this->response[$id][0],"Updated database success"))
	    //$this->res=$strInsertEntrySuccess;
	    $this->res="";
	else $this->res=$strInsertEntryFailed;
    }
    

    function getList($page,$limit) {
    	$command="database show";
	$fam=$this->f_fam;
	$key=$this->f_key;
	$val=$this->f_val;
	$flr_f=$this->f_fam_type;
	$flr_k=$this->f_key_type;
	$flr_v=$this->f_val_type;
	if($fam && $flr_f==1) {
	    $command="database show $fam";
	    $flr_f=0;
	}
	if($key && $flr_k==1) {
	    $command="database showkey $key";
	    $flr_k=0; $flr_f=$this->f_fam_type;
	}
	$this->num=0;
	$id=$this->cmd($command); 
	$list=$this->response[$id];
	if(!$list) return;
	    $min=$page*$limit;
	    $max=$min+$limit;
	    $i=$index=0;
	    unset($this->list_db);
	    foreach($list as $l) {
	    	$a=explode(":",$l);
		$n=strpos($a[0],'/',1);
		if($n) $tmp_f=trim(substr($a[0],1,$n-1));
		if($tmp_f=="") continue;
		$tmp_k=trim(substr($a[0],$n+1));
		
		//$tmp_v=trim($a[1]);
		$tmp_v=trim(join(":",array_slice($a,1)));
		
		if($flr_f) {
		    if(($flr_f==1) && !($tmp_f==$fam)) continue;
		    elseif(($flr_f==2) && !(strpos($tmp_f,$fam)===0)) continue;
		    elseif(($flr_f==3) && !(strpos($tmp_f,$fam)===(strlen($tmp_f)-strlen($fam)))) continue;
		    elseif(strpos($tmp_f,$fam)===false) continue;
		}
		if($flr_k) {
		    if(($flr_k==1) && !($tmp_k==$key)) continue;
		    elseif(($flr_k==2) && !(strpos($tmp_k,$key)===0)) continue;
		    elseif(($flr_k==3) && !(strpos($tmp_k,$key)===(strlen($tmp_k)-strlen($key)))) continue;
		    elseif(strpos($tmp_k,$key)===false) continue;
		}
		if($flr_v) {
		    if(($flr_v==1) && !($tmp_v==$val)) continue;
		    elseif(($flr_v==2) && !(strpos($tmp_v,$val)===0)) continue;
		    elseif(($flr_v==3) && !(strpos($tmp_v,$val)===(strlen($tmp_v)-strlen($val)))) continue;
		    elseif(strpos($tmp_v,$val)===false) continue;
		}
		if(($i>=$min) && ($i<$max)) {
			$this->list_db[$index][0]=$tmp_f;
			$this->list_db[$index][1]=$tmp_k;
			$this->list_db[$index][2]=$tmp_v;
			$index++;
		}
	        $i++;
	    }
	    $this->num=$i;

    }

//end class
}

?>


