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
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
?>
<div id="frame-module-header" nowrap>
<?=$strRecordingRules?>
</div>

<?
if (!isset($module_action)) $module_action="add";
if ($rule_id == "") unset($rule_id);
if(!isset($current_page)) $current_page=0;
$rec = new Recording();
//$rec->db->Debug=1;

switch ($module_action) {
    case "delete": {
		$rec->rule_id=$rule_id;
		$rec->deleteRule();
		unset($rule_id,$rule_name,$rule_dir,$invert,$rule_status);
		$module_action="add";
		break;
    }
    case "insert": 
    case "update": 
	{
        if(!($rule_is_exists=$rec->isRuleExists($rule_name,$rule_id))) {
      	    $rec->rule_name=$rule_name;
	    $rec->rule_status=$rule_status;
	    $rec->rule_dir=$rule_dir;
	    $rec->list_rules_dst=$rule_dst;
	    $rec->list_rules_src=$rule_src;
	    $rec->rule_id=$rule_id;
	    $rec->invert=$invert;
	    if($module_action=="update") $rec->updateRule();
	    else $rec->insertRule();
	    unset($rule_id,$rule_dst,$rule_src,$rule_name,$rule_dir,$invert,$rule_status);
	    $module_action="add";
	}else {
	    if($module_action=="update") $module_action="edit";
	    else $module_action="add";
	}
	    
    }
    
}	
$rec->setRulesFilters($f_name,$f_src,$f_dest,$f_dir);
$rec->getRulesList($current_page,$limit_display);

$list_rules=$rec->list_rules;
$list_rules_dst=$rec->list_rules_dst;
$list_rules_src=$rec->list_rules_src;
$num=$rec->num;
$num_disp=count($list_rules);

$n_pages=num_pages($num,$limit_display);

?>

<script>
ams.showReloadLink();

rec = new ObjectD();

action="RecordsRules";

rec.deleteRule=function (id) {
 if (!confirm("<?=$strWinConfirmDeleteRule?>")) return;
    $('module_form').module_action.value="delete";
    $('module_form').rule_id.value=id;
    loadModule(1,'Recording','Recording','RecordsRules');
}

rec.editRule=function (id,j) {
    $('rule_id').value=id;
    $('module_action').value='edit';
    loadModule(1,'Recording','Recording','RecordsRules');
}

rec.clearFields=function () {
    $('rule_id').value='';
    $('rule_name').value='';
    $('rule_status').value=1;
    $('rule_dir').value='a';
    $('rule_dst').value='';
    $('rule_src').value='';
    $('invert').value=0;
    $('module_form').module_action.value="add";
    $('button_save').value='<?=$strAddNew?>';    
    $('rule_header').innerHTML='<?=$strAddRule?>';    
}
rec.saveRules=function () {
    if($('rule_name').value=='') return;
    if($('module_form').module_action.value == "edit")
	$('module_form').module_action.value="update";
    else $('module_form').module_action.value="insert";
    loadModule(1,'Recording','Recording','RecordsRules');
	
}

</script>


<br>
<?
moduleForm(array("rule_id","module_action","current_page","limit_display"));
$tbl = new DataTbl();
$p=$tbl->addElement("f_name","text",$strNameRule,1,$f_name);
$p->setOptions(18);
$p=$tbl->addElement("f_dest","text",$strDest,1,$f_dest);
$p->setOptions(18);
$p=$tbl->addElement("","button","",1,$strSearch); $p->align2="right";
$p->action="$('current_page').value=0;loadModule(1,'Recording','Recording','RecordsRules');";
$p=$tbl->addElement("f_dir","select",$strDirection,2,$f_dir);
$p->options=array("a"=>$strAll,"i"=>$strIncoming,"o"=>$strOutgoing);
$p=$tbl->addElement("f_src","text",$strSource,2,$f_src);
$p->setOptions(18);
$tbl->cols=array("12%","20%","12%");
$tbl->show();
echo "<br>";
$tbl = new ListTbl();
 if (!$num) $tbl->emptyTbl($strNoRules,"100%",0);
 else {
 $tbl->tblHead(array(2,"15","","","","",""),array($strNameRule,$strRule,$strDest,$strSource,$strDirection,$strRuleStatus));
 

	for($j=0; $j < $num_disp; $j++) {

		$r_id=$list_rules[$j][0];	
		$tbl->tblTr($j);
	
	$tbl->td("",20,"","rec.deleteRule",$r_id,"drop.gif",$strDeleteRule);
	$tbl->td("",20,"","rec.editRule",array($r_id,$j),"edit.gif",$strEditRule);
	$tbl->td($list_rules[$j][1],"","","rec.editRule",array($r_id,$j),"",$strEditRule);

	?>
	<td nowrap>
	<?if($list_rules[$j][8]) echo "<font color=blue>$strRuleNotWrite</font>";
	  else echo "<font color=red>$strRuleWrite</font>";
	?>
	</td>
	<td nowrap>
	<a title="<?=$strEditRule?>"  href="javascript:rec.editRule(<?=$r_id?>,<?=$j?>);">
	<?
	    if($list_rules_dst[$r_id][0][2]) {
		$c=count($list_rules_dst[$r_id]);
		if($c > 1) {
		    $i=0;
		    foreach($list_rules_dst[$r_id] as $l) {
			echo $l[2];
			$i++; if($i==$c) break;
			echo ", ";
			if($i > 1) {echo "..."; break;}
		    }
		} else echo hc($list_rules_dst[$r_id][0][2]);
	    
	    }else echo $strAll;
	    ?></a>
	</td>
	<td nowrap>
	<a title="<?=$strEditRule?>"  href="javascript:rec.editRule(<?=$r_id?>,<?=$j?>);">
	<?
	if($list_rules_src[$r_id][0][2]) {
		$c=count($list_rules_src[$r_id]);
		if($c > 1) {
		    $i=0;
		    foreach($list_rules_src[$r_id] as $l) {
			echo $l[2];
			$i++; if($i==$c) break;
			echo ", ";
			if($i > 1) {echo "..."; break;}
		    }
		} else echo $list_rules_src[$r_id][0][2];
	    
	}else echo $strAll;
	
	?>
	</a>
	</td>
	<td nowrap>
	<?
	    switch ($list_rules[$j][6]) {
		case 'i': echo $strIncoming;break;
	    	case 'o': echo $strOutgoing;break;
		default: echo $strAll;
	    }
	
	?></td>
	<td nowrap>
	<?
	    switch ($list_rules[$j][7]) {
		case 0: echo "<font color=red>$strDisable</font>";break;
		default: echo "<font color=green>$strActive</font>";
	    }
	
	?></td>
	</tr>
	
        <?	

	}
$tbl->tblEnd($current_page,$n_pages);
}

if($module_action == "edit" && !$rule_is_exists) {

    $rec->rule_id=$rule_id;
    $rec->getRuleData();
    $rule_data=$rec->rule_data;
    $rule_name=$rule_data[1];
    $invert=$rule_data[8];
    $rule_status=$rule_data[7];
    $rule_dir=$rule_data[6];
    $rec->getDstSrcById($rule_id);
    $list_dst=$rec->list_rules_dst[$rule_id];
    $list_src=$rec->list_rules_src[$rule_id];
    $c=count($list_dst);
    for($i=0,$rule_dst=""; $i <= $c; $i++) {
	$rule_dst .= $list_dst[$i][2];
	if($i < ($c-1)) $rule_dst .= ", ";
    }
    $c=count($list_src);
    for($i=0,$rule_src=""; $i <= $c; $i++) {
	$rule_src .= $list_src[$i][2];
	if($i < ($c-1)) $rule_src .= ", ";
    }
    
}
if(!isset($rule_status)) $rule_status=1;
if(!isset($invert)) $invert=0;
//echo "action=".$module_action."<br>";
?>
<br>
<div id="frame-module-header2">
<div id="rule_header"><?=($module_action == "edit") ? $strEditRule : $strAddRule?></div>
</div>

<div id="validate-fields" class="module-warning" style="display: none; margin-bottom: 10px;">
</div>
<? if($rule_is_exists) showMsg($strRuleExists,'validate-fields',0,'warning.gif');


$tbl = new DataTbl("80%");
$p=$tbl->addElement("rule_name","text",$strNameRule,1,$rule_name);
$p->setOptions(20,1);
$p=$tbl->addElement("invert","select",$strRule,1,$invert);
$p->options=array(0=>$strRuleWrite,1=>$strRuleNotWrite);
$p=$tbl->addElement("button_save","button","",1,($module_action == "edit") ? $strUpdate : $strAddNew); $p->align2="right";
$p->action="rec.saveRules()";
$p=$tbl->addElement("rule_dst","textarea",$strDest,2,$rule_dst);
$p->symbols=array("'","\"","?","<",">");
$p->setOptions(25,5,"symbols"); $p->valign1="top";
$p=$tbl->addElement("rule_src","textarea",$strSource,2,$rule_src);
$p->symbols=array("'","\"","?","<",">");
$p->setOptions(25,5,"symbols"); $p->valign1="top";

$p=$tbl->addElement("","button","",2,$strClear); $p->align2="right";
$p->action="rec.clearFields()"; $p->submit=false; $p->valign2="top";
$p=$tbl->addElement("rule_dir","select",$strDirection,3,$rule_dir);
$p->options=array("a"=>$strAll,"i"=>$strIncoming,"o"=>$strOutgoing);
$p=$tbl->addElement("rule_status","select",$strRuleStatus,3,$rule_status);
$p->options=array(0=>$strDisable,1=>$strActive);
$tbl->show();

echo "</form>";

