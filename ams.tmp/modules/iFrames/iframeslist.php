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
<?=$striFrames?>
</div>

<?
$iframe = new iFrame();
if ($iframe_del) {

    $iframe->id=$iframe_id;
    $iframe->delete_iframe();
    unset($iframe_id,$iframe_link,$iframe_name);
}
if ($iframe_save && $iframe_link) {
    if($iframe_id) {//update
    $iframe->id=$iframe_id;
    $iframe->link=$iframe_link;
    $iframe->name=$iframe_name;
    $iframe->update();
    unset($iframe_id,$iframe_link,$iframe_name);
    }
    else {//insert new

	$iframe->link=$iframe_link;
	$iframe->name=$iframe_name;
	$iframe->insert();
	unset($iframe_id,$iframe_link,$iframe_name);
    }
    
}	
$iframe->get_list();
$list_frames=$iframe->list_frames;
$num=$iframe->num_rows;
$num_disp=count($list_frames);
?>

<script>

ifr = GlobaliFrames;
ams.deleteObj.push(ifr);


if(!ifr.history) {
    ifr.history = new Array;
}
iFHistory=ifr.history;

ifr.deleteFrame=function (id) {
 if (confirm("<?=$strWinConfirmDeleteFrame?>")) {
    $('module_form').iframe_del.value=1;
    $('module_form').iframe_id.value=id;
    loadModule(1,'iFrames','iFrames','iFramesList');
 }
}

ifr.editFrame=function (id,j) {

    $('iframe_id').value=id;
    $('iframe_link').value=$('list_link' + j).value;
    $('iframe_name').value=$('list_name' + j).value;
    $('button_save').value='<?=$strUpdate?>';
    $('iframe_header').innerHTML='<?=$strEditLink?>';

}

ifr.clearFields=function () {
    $('iframe_id').value='';
    $('iframe_link').value='';
    $('iframe_name').value='';
    $('button_save').value='<?=$strAddNew?>';
    $('iframe_header').innerHTML='<?=$strAddLink?>';
}
ifr.saveFrame=function() {
    var s=$('iframe_link').value;  
    if(s == '') return;
    var a = s.toArray();
    if(a[0] != '/') {
	if(s.indexOf('://',0)== -1)
	   s='http://'+s;
    }
    $('iframe_link').value=s;
    $('module_form').iframe_save.value=1;
    loadModule(1,'iFrames','iFrames','iFramesList');

}
ifr.showFrame=function(j) {
    var l=$('list_link' + j).value;
    loadModule(1,'iFrames','iFrames','iFrameShow',$H({iframe_link: l}));

}
</script>

<br>
<FORM NAME="module_form" id="module_form" METHOD=POST>
<INPUT TYPE="hidden" NAME="iframe_id" id="iframe_id" value="<?=$iframe_id?>">
<INPUT TYPE="hidden" NAME="iframe_del" id="iframe_del" value="">
<INPUT TYPE="hidden" NAME="iframe_save" id="iframe_save" value="">

<? 
$tbl = new ListTbl();
 if (!$num) $tbl->emptyTbl($strNoFrames,"100%",0);
 else {
$tbl->tblHead(array(2,"45","","10"),array($strLinkFrame,$strNameFrame,$strCreatedBy));

	for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);
	?>
	<td width=10><a title="<?=$strDeleteFrame?>"  href="javascript:ifr.deleteFrame(<?=$list_frames[$j][0]?>)"><img src="images/drop.gif"></a></td>
	<td width=10><a title="<?=$strEditFrame?>"  href="javascript:ifr.editFrame(<?=$list_frames[$j][0]?>,<?=$j?>)"><img src="images/edit.gif"></a></td>
	<td align="left" nowrap>&nbsp;
	<a title="<?=$strShowFrame?>" href="javascript:ifr.showFrame(<?=$j?>)">
	<?=hc($list_frames[$j][1])?></a>
	<input type="hidden" name="list_link<?=$j?>" id="list_link<?=$j?>" value="<?=hc($list_frames[$j][1])?>">
	</td>
	<td nowrap><?=hc($list_frames[$j][2])?>
	<input type="hidden" name="list_name<?=$j?>" id="list_name<?=$j?>" value="<?=hc($list_frames[$j][2])?>">	
	</td><td nowrap><?=$list_frames[$j][5]?></td>
	</tr>
	
        <?	

	}
 
$tbl->tblEnd();
}
?>

<br>
<div id="frame-module-header2">
 <div id="iFrame_header">
    <?=$strAddLink?>
 </div>
</div>
<br>
<?
$tbl = new DataTbl("75%");
$p=$tbl->addElement("iframe_link","text",$strLinkFrame,1,$iframe_link);
$p->setOptions(75,1);
$p=$tbl->addElement("button_save","button","",1,$strAddNew);
$p->action="ifr.saveFrame()";
$p=$tbl->addElement("iframe_name","text",$strNameFrame,2,$iframe_name);
$p->setOptions(75);
$tbl->show();
?>
</form>

