<?

session_start();
$theme=$_SESSION['theme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>		
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<style type="text/css" media="screen">
		@import url("../../themes/<?=$theme?>/style.css");
	</style>
</head>
<body class="module-warning" style="font-size: 11px; color: #555555; font-style: normal;">

<?
include_once("../../config.php");
include_once("../../lib/func.php");
include_once("../../lang/".$_SESSION['lang'].".lang.php");
include_once("lang/".$_SESSION['lang'].".lang.php");
include_once("Rate.php");
include_once("../../modules/CodesDirectory/CodesDirectory.php");


extract(stripslashes_r($_POST));

$import_file=$_FILES['import_file']['tmp_name'];

$rt = new Rate("",$accountcode);
//$rt->db->Debug=1; 
if (!file_exists($import_file)) { showMsg($strErrorUploadFile);  exit(); }    

$f_import=fopen($import_file,"r");
$i=0;
$time_start=getmicrotime();

$rates = array();
$replace_flag = array();
          
while (!feof($f_import)) {
        $str=fgets($f_import);
        if (strlen($str) < 3) continue;	//ignore empty and short strings
        if ($i++ < $first_rows) {
	    echo "$i. $str"; echo "&nbsp;<font color=red><i>$strIgnored</i></font><br>";
	    continue;
	    
        }
        unset($name,$cfr,$cto,$min_len,$max_len,$rate,$is_name_exists,$is_code_exists,$first_time);
	$fields=explode($separator,$str);
	if (!$col_names) {
		$name=trim($fields[0],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
		$cfr=trim($fields[1],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
		$cto=trim($fields[2],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
		$min_len=trim($fields[3],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
		$max_len=trim($fields[4],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
		$rate=trim($fields[5],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94");
	} else {
	      $columns=explode(",",$col_names);
	      
	      for ($j=0; $j < count($columns); $j++) {	
	        switch (trim($columns[$j])) {
		    case "name"	: $name=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		    case "code_from": $cfr=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		    case "code_to" : $cto=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		    case "min_len" : $min_len=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		    case "max_len" : $max_len=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		    case "rate" : $rate=trim($fields[$j],"\x09\x0A\x0D\x20\x22\x82\x84\x91..\x94"); break;
		
		}
	      }
	      
	//end initialize columns
	}

	
	if (!$rate) $rate="0.00000";
	$rate=str_replace(",",".",$rate);//replace , to . in float number
	$rate = (float) $rate;
	$min_len = (int) $min_len;
	$max_len = (int) $max_len;
	if (!$min_len) $min_len=1;
	if (!$max_len) $max_len=20;
	if($max_len < $min_len) $min_len=$max_len;
	echo "<b>$i. $str</b><br>";
	echo "<div nowrap style='font-size: 11px; color: #555555; font-family: arial,sans; margin-left: 20px;'>";
	if (!$name) { //if name doesn't exists end this row
	    echo "<font color=red><i>&nbsp;$strErrNameNotDefined...$strNotImported</i></div>";
	    continue; 
	}
	if((!is_numeric($cfr) && !is_empty($cfr)) || (!is_numeric($cto) && !is_empty($cto))) {
	    echo "<font color=red><i>&nbsp;$strWrongCode...$strNotImported</i></font></div>";
	    continue; 
	}elseif(is_empty($cfr)) {
	    //if codes not defined, try to get it from Codes directory
	    echo "<font color=blue><i>&nbsp;$strCodeNotDefined. $strTryCodesDirectory...</i></font><br>";
		    $cd = new CodesDirectory($name);
		    list($cfr,$cto,$min_len,$max_len,$num_codes)=$cd->getData();
		    if (!$num_codes) {
			echo "&nbsp;<font color=red><i>&nbsp;$strNoDestName...$strNotImported</i></font></div>";
			continue; //if no codes find end this row
		    }
	
	
	
	}else {
	    $cfr=array((int) $cfr); $num_codes=1; 
	    if (is_empty($cto)) $cto=$cfr;
	    else $cto=array((int) $cto);
	}
	for($ind=0; $ind < count($cfr); $ind++) {
	    printf("%s %d-%d %d-%d %.5f",$name,$cfr[$ind],$cto[$ind],$min_len,$max_len,$rate);	
	    echo "<br>";
	}	
	$rt->name=$name;
	$rt->set_fields($cfr,$cto,$min_len,$max_len,$rate,$num_codes);
	if(!$rates[$name]) $first_time=true;
	
	if($first_time) $is_name_exists=$rt->is_name_exists();
	
	if($is_name_exists && !$replace){
	     echo "<font color=red><i>&nbsp;$strDupName...$strNotImported&nbsp;</i></font></div>";    
	     continue; //duplicate name - end this row 		
	}
	
	//check for duplicate codes
	$rt->name_prev=$name;
	$is_code_exists=$rt->is_code_exists();

	if($is_code_exists) {	
		echo "<font color=red><i>&nbsp;$strDupRow $strNotImported&nbsp;</i></div>";
		continue; //duplicate code - end this row
	}
	if ($replace && $first_time && $is_name_exists) {
		       //delete only one time
		         echo "<font color=red><i>&nbsp;$strDeletePrev&nbsp;</i></font>";    
		         $rt->delete_rate($name);
		      
	}
	
	if (!$first_time && $rates[$name]['rate'] != $rate) {
		$rate=$rates[$name]['rate'];//if we have different rates in different rows - it's not right
		$rt->rate=$rate;
		echo "<font color=blue><i>&nbsp;$strOneValue".$rate."&nbsp;</i></font>";
	}

	if($first_time) { //temporary save data for next checking
	    $rates[$name]['rate']=$rate;
	    $rates[$name]['name']=$name;		
	}
	//insert rate
	$rt->insert();
	echo "<font color=green><i>&nbsp;$strImportedRow&nbsp;</i></font></div>";

}//end while (!feof)

echo "<br><br>";
actionTime($time_start);

?>

</body>
</html>
