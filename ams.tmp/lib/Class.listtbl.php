<?php
class ListTbl {
    var $width = "100%";
    var $cols = array();
    var $n_cols;
    var $cs;
    var $acl;
    var $sel_oper = array();

    function ListTbl() {
    
    }

    function pageNav($cp,$np,$rpp=1,$obj="") {
      global $www_dir,$strNext,$strPrev,$strRowsPerPage,$strToStart,$strToEnd,$limit_display,$theme;
      if($obj) $obj.=".";
      $int_p = (int) ($np / 10); 
      if($int_p > 10) $int_p = 10; 
      if($int_p < 3) $int_p = 3;
      $img =$www_dir."/themes/$theme/images";
      echo "<span class='page-toggle'>";
      if($rpp) {
        if(($np > 1) || $limit_display > 20) {
    	    $lp = array(20,30,50); 
    	    echo "$strRowsPerPage&nbsp;&nbsp;";
    	    foreach($lp as $l) {
		if($l == $limit_display) echo "[$l]&nbsp;";
		else echo "<span><a href='javascript:".$obj."setLimitDisplay($l)'>[$l]</a></span>&nbsp;";
    
    	    }
    	    echo "&nbsp;&nbsp;&nbsp;"; 
        }
      }
      if($cp) {
        echo "<a href='javascript:".$obj."setPage(1)'><img title='$strToStart' align='absmiddle' src='$img/prev_off3.gif'";
        echo " onmouseover='this.src=\"$img/prev3.gif\"' onmouseout='this.src=\"$img/prev_off3.gif\"' /></a>";
        if($cp > $int_p) {
          $p=$cp - $int_p + 1;
          echo "<a href='javascript:".$obj."setPage($p)'><img title='-$int_p' align='absmiddle' src='$img/prev_off2.gif'";
          echo " onmouseover='this.src=\"$img/prev2.gif\"' onmouseout='this.src=\"$img/prev_off2.gif\"' /></a>";
        }
        echo "<a href='javascript:".$obj."setPage($cp)'><img title='$strPrev' align='absmiddle' src='$img/prev_off.gif'";
        echo " onmouseover='this.src=\"$img/prev.gif\"' onmouseout='this.src=\"$img/prev_off.gif\"' /></a> - "; 
      }
      echo ($cp+1)." / ".$np;
      if (($cp+1)<$np){
	 $p=$cp+2;
         echo " - <a href='javascript:".$obj."setPage($p)'><img title='$strNext' align='absmiddle' src='$img/next_off.gif'";
         echo " onmouseover='this.src=\"$img/next.gif\"' onmouseout='this.src=\"$img/next_off.gif\"' /></a>";
         if(($np-$cp) > ($int_p + 1)) {
            $p=$cp+$int_p+1;
            echo "<a href='javascript:".$obj."setPage($p)'><img title='+$int_p' align='absmiddle' src='$img/next_off2.gif'";
            echo " onmouseover='this.src=\"$img/next2.gif\"' onmouseout='this.src=\"$img/next_off2.gif\"' /></a>";
         }
         echo "<a href='javascript:".$obj."setPage($np)'><img title='$strToEnd' align='absmiddle' src='$img/next_off3.gif'";
         echo " onmouseover='this.src=\"$img/next3.gif\"' onmouseout='this.src=\"$img/next_off3.gif\"' /></a>";

      echo "</span>";
     }
    }
    function emptyTbl($msg,$width="100%",$exit=1) {
	echo "<table border=\"0\" width=\"$width\" cellspacing=\"1\" cellpadding=\"0\" class=\"list-tbl\">";
	echo "<tr style='height: 1px'><th style='height: 1px;width: 100%;'></th></tr>";
	echo "<tr><th>&nbsp;</th></tr><tr><td align=center class='module-note'>$msg<td></tr>";
	echo "<tr><th></th></tr></table>";
	if($exit) exit();
    }
    function tblTr($i,$style="") {
	$class = "trcls1";
	if($style) $style=" style=\"".$style."\" ";
	$i % 2 ? 0: $class="trcls2";
 	echo "<tr class='$class' $style align='center' onMouseover='this.className=\"trmover\"' onMousedown='this.className=\"trmdown\"' onMouseout='this.className=\"$class\"'>";
	
    }
    function tblEnd($cp=0,$np=1,$nav=1,$rpp=1,$obj="") {
	echo "<tr><th colspan=\"".$this->cs."\" align=\"right\">";
	if($nav) $this->pageNav($cp,$np,$rpp,$obj);
	echo "</th></tr></table>";
    }

    function tblHead($cols,$names,$width="100%",$style="",$cellpadding=1,$cellspacing=1) {
        $this->cs=$cs=($c = count($cols)) + $cols[0];
	if($style) $style=" style=\"".$style."\" ";
        echo "<table $style border=\"0\" width=\"$width\" cellpadding=\"$cellpadding\" cellspacing=\"$cellspacing\" class=\"list-tbl\">";
        echo "<tr style=\"height: 1px;\"><th colspan=\"$cs\" style=\"padding: 0px;height: 1px;\"></th></tr>";
        echo "<tr align=\"center\" style='font-size: 11px;'>";
	if($cols[0]) echo "<th colspan=\"".$cols[0]."\"></th>";
        for ($i=1; $i < $c; $i++) { 
	    echo "<th width=\"".$cols[$i]."%\" nowrap=\"nowrap\">".$names[$i-1]."</th>";
	}
	echo "</tr>";
    }
    function checkbox($i,$val,$width=15) {
	echo "<td width=\"$width\" >";
	echo "<input type=\"checkbox\" class=\"checkbox\" id=\"list_mark[$i]\" name=\"list_mark[$i]\" value=\"".htmlspecialchars($val)."\" /></td>";
    }
    function getArg($val) {
	if(is_integer($val) || is_float($val)) return $val;
	if($val == "event" || $val == "this") return $val;
	return "'".addslashes(htmlspecialchars($val))."'";
	//return "'".addslashes($val)."'";
	
    
    }
    function td($val,$width="",$align="",$action="",$arg="",$img="",$title="",$h_id="",$h_val="",$act_type="",$nowrap=1) {
	if($width) $w="width=\"$width\"";
	if($align) $al="align=\"$align\""; 
	$nowrap = $nowrap ? "nowrap=\"nowrap\"" : ""; 
	echo "<td $w $al $nowrap>";
	if($action) {
	    if($title) $t="title=\"$title\"";
	    //if(!is_array($arg)) $arg=array($arg);
	    if(!$act_type) echo "<a $t href=\"javascript:".$action."(";
	    else echo "<a href=\"javascript:void(0)\" ".$act_type."=\"".$action."(";
	    if(!is_array($arg)) echo $this->getArg($arg).")\">";
	    else {
		foreach($arg as $a) $s.=$this->getArg($a).",";
		$s[strlen($s)-1]=")"; echo $s."\">";
	    }
	    if($img) echo "<img $t src=\"images/$img\" />";
	    echo htmlspecialchars($val)."</a>";
	    
	}
	else echo htmlspecialchars($val);
	if($h_id) echo "<input type=\"hidden\" name=\"".$h_id."\" id=\"".$h_id."\" value=\"".htmlspecialchars($h_val)."\"/>";
	echo "</td>";
    }
    
    function tblTds($tds) {
	foreach($tds as $t) {
	    echo "<td nowrap=\"nowrap\">".htmlspecialchars($t)."</td>";
	}
    }
    function exportLink($title,$act,$width="100%") {
	global $strExportCSV;
	echo "<div id=\"module-export-link\" align=\"right\" style=\"width: $width;\">";
	echo "<a href=\"javascript:void(0)\" onclick=\"".$act."\" title=\"$title\">";
	echo "<img align=\"absmiddle\" src=\"images/xls_export.gif\" />$strExportCSV</a></div>";
    }
    function selOper() {
      global $strSelectAll,$strUnselectAll,$strWithMarked;
	if(empty($this->sel_oper)) return;
	echo "<table border=\"0\"><tr><td align=\"left\" class=\"module-note\">";
	echo "&nbsp;<img src=\"images/arrow3.png\" />";
	echo "<a href=\"javascript:selectAll()\" title=\"$strSelectAll\">$strSelectAll</a>";
	echo "/<a href=\"javascript:unselectAll()\" title=\"$strUnselectAll\">$strUnselectAll</a>";
	echo "&nbsp;$strWithMarked</td>";
	foreach ($this->sel_oper as $op) {
	    echo "<td><a href=\"javascript:".$op[1]."\" title=\"".$op[0]."\">";
	    echo "<img align=\"absmiddle\" src=\"images/".$op[2]."\" /></a></td>";
	}
	echo "</tr></table>";
    
    }    
    function addSelOper($title,$act,$img) {
	array_push($this->sel_oper,array($title,$act,$img));
    }

}
?>

