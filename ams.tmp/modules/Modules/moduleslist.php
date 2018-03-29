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
?>
<div id="frame-module-header" nowrap>
<?=$strModulesList?>
</div>

<?
$mod = new Module($m_name);
//$limit_display=30;

if (empty($current_page)) $current_page=0;
if($make_config) {
    $mod->makeConfig();
    ?>
    <div class="module-warning"><?=$strConfigWrited?></div>
    <?
}
if($menu_id_list) {
    $mod->sortMenu($menu_id_list);
}
if ($m_del_id) {
	$mod->id=$m_del_id;
	$mod->deleteModule();
}
if($m_save) {
    //echo "save= $m_id <br>";
    $mod->id=$m_id;
    $mod->name=$m_name;
    $mod->admin_only=$m_admin;
    $mod->hidden=$m_hidden;
    $mod->main=$m_main;
    $mod->icon=$m_icon;
    $mod->action=$m_action;
    $mod->acl=$acl;
    if($m_id) $mod->update();
    else $mod->insert();
    unset($m_id,$m_name,$m_admin,$m_main,$m_hidden,$m_icon,$m_action,$acl);
}



$mod->getList(array($f_name),$current_page,$limit_display);
$list_mods=$mod->list_mods;
$num=$mod->num_rows;
$num_disp=count($list_mods);

$n_pages = num_pages($num,$limit_display,$current_page);

?>



<script>

mod = new ObjectD();



mod.deleteModule = function (id,name) {
 if (confirm("<?=$strWinConfirmDeleteModule?>\n" + name + "?")) {
    $('module_form').m_del_id.value=id;
    loadModule(1,'Modules','Modules','ModulesList');
 }
}
mod.viewModule = function (id) {
    $('module_form').m_id.value=id;
    loadModule(1,'Modules','Modules','ModulesList');
    
}
mod.saveModule = function () {
    if($('module_form').m_name.value=='') return;
    //$('module_form').m_id.value=id;
    $('module_form').m_save.value=1;
    loadModule(1,'Modules','Modules','ModulesList');
    
}
mod.clearFields= function () {
    $('module_form').m_id.value='';
    $('button_save').value='<?=$strAddModule?>';
    $('module_form').m_name.value='';
    $('module_form').m_action.value='';
    $('module_form').m_icon.value='';
    $('module_form').m_admin.checked=false;
    $('module_form').m_hidden.checked=false;
    $('module_form').m_main.checked=false;
    $('frame-module-header2').innerHTML='<?=$strModule?>:';
    //loadModule(1,'','Modules','ModulesList');
    for(var i=0; i<4; i++) 
           for(var j=0; j<4; j++) $('acl['+i+']['+j+']').value='';
    
}
</script>

<br>
<FORM NAME="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page"  value="<?=$current_page?>">
<INPUT TYPE="hidden" NAME="limit_display" id="limit_display"  value="<?=$limit_display?>">
<INPUT TYPE="hidden" NAME="m_id" id="m_id" value="<?=$m_id?>">
<INPUT TYPE="hidden" NAME="m_save" id="m_save" value="">
<INPUT TYPE="hidden" NAME="m_del_id" id="m_del_id"  value="">
<INPUT TYPE="hidden" NAME="make_config" id="make_config"  value="">

<table class="input-data-tbl" width="100%" cellspacing=5 cellpadding=0>
     <tr>
     <td> <?=$strModuleName?></td>
     <td><input type="text" name="f_name" id="f_name"  size="25" value="<?=hc($f_name)?>" >
     </td>
     <td colspan=2 align="right" >&nbsp;&nbsp;
     <input type="button" onclick="searchModule();" value="<?=$strSearch?>" class="sbutton">
     </td></tr>
     <tr>
     <td colspan=4 align="right" >&nbsp;&nbsp;
     <input type="button" onclick="loadModule(0,0,'Modules','ModulesList',$H({make_config: 1}));" value="<?=$strMakeConfig?>" class="sbutton">
     </td>
     </tr>
</table>

<br>
<?
$tbl = new ListTbl(); 
if (!$num) $tbl->emptyTbl($strNoModules,"100%",0);
else {
$tbl->tblHead(array("","2","22","15","","13","20"),array("&nbsp;",$strModuleName,$strNoACL,$strMainMenu,$strAdminOnly,$strIcon));

	for($j=0; $j < $num_disp; $j++) {

	  $tbl->tblTr($j);    
	  if($_SESSION['acl']['Modules']['access']=1) {	?>
	    <td align="left"  nowrap="nowrap"><a title="<?=$strDeleteModule?>"  href="javascript:mod.deleteModule(<?=$list_mods[$j][0]?>,'<?=$list_mods[$j][1]?>')"><img src="images/drop.png"></a></td>
	    <td align="left"  nowrap="nowrap"><a href="javascript:mod.viewModule(<?=$list_mods[$j][0]?>)" title="<?=$strEditModule?>"><?=$list_mods[$j][1];?></a></td>
	  <?}else {?>
	    <td align="left"  nowrap="nowrap"><a href="javascript:mod.viewModule(<?=$list_mods[$j][0]?>)" title="<?=$strViewModule?>"><?=$list_mods[$j][1];?></a></td>
	   <?}?>
	    <td align="center" nowrap><?
	    if($list_mods[$j][2]) echo "<img src='images/check.gif'>";
	    ?></td>
	    <td align="left" nowrap="nowrap"><?
	    $str=${"str".$list_mods[$j][1]} ? ${"str".$list_mods[$j][1]} : $list_mods[$j][1];
	    if($list_mods[$j][3]) echo "&nbsp;<img src='images/check.gif'>&nbsp;".$str;
	    ?></td>
	    <td align="center" nowrap="nowrap"><?
	    if($list_mods[$j][4]) echo "<img src='images/check.gif'>";
	    ?></td>
	    <td align="left" nowrap="nowrap">
	    <?
	    if($list_mods[$j][5] && (file_exists("images/".$list_mods[$j][5]))) {  ?>
		&nbsp;<img align="middle" src="images/<?=$list_mods[$j][5]?>">
	    <?echo $list_mods[$j][5]." ";
	    }
	    ?>
	    </td></tr>
        <?	
	}
 
$tbl->tblEnd($current_page,$n_pages);
}
//echo "id = $m_id <br>";
if($m_id) {
    $mod->id=$m_id;
    $mod->getData();
    $m_name=$mod->name;
    $m_main=$mod->main;
    $m_icon=$mod->icon;
    $m_admin=$mod->admin_only;
    $acl=$mod->acl;
    $m_hidden=$mod->hidden;
    $m_action=$mod->action;
}
?>
<br>
<div id="frame-module-header2">
<?=$strModule?>: <?=$m_name?>
</div>
<br>
<table class="input-data-tbl" width="100%" cellpadding=0 cellspacing=5>

     <tr>
     <td width="6%"> <?=$strModuleName?></td>
     <td width="15%">
     <input type="text" name="m_name" id="m_name"  size=20 value="<?=hc($m_name)?>" >
     </td>
     <td nowrap width="17%" align="right"> <?=$strNoACL?></td>
     <td width="3%"><input type="checkbox" class="checkbox" name="m_hidden" id="m_hidden" <?if($m_hidden) echo "checked";?> value="1">
     </td>
     <td width="10%" align="right" nowrap> <?=$strMainMenu?></td>
     <td width="3%"><input type="checkbox" class="checkbox" name="m_main" id="m_main" value="1" <?if($m_main) echo "checked";?> >
     </td>
     <td width="10%" align="right"> <?=$strAdminOnly?></td>
     <td width="3%" align="left"><input type="checkbox" class="checkbox" name="m_admin" id="m_admin" <?if($m_admin) echo "checked";?> value="1">
     </td>
     <td align="right" >&nbsp;&nbsp;
     <input type="button" onclick="mod.saveModule();" name="button_save" id="button_save" value="<?if($m_id) echo $strUpdate; else echo $strAddModule; ?>" class="sbutton">
     </td>
     </tr>
     <tr>
     <td> <?=$strIcon?></td>
     <td><input type="text" name="m_icon" id="m_icon"  size=20 value="<?=hc($m_icon)?>" >
     </td>
     <td align="right"> <?=$strAction?></td>
     <td colspan=5><input type="text" name="m_action" id="m_iaction"  size="60" value="<?=hc($m_action)?>" >
     </td>
     <td align="right" colspan=3>&nbsp;&nbsp;
     <input type="button" onclick="mod.clearFields();" value="<?=$strClear?>" class="sbutton">
     </td>
     </tr>

</table>
<br>
<table width="100%" border=0>
<tr><td valign="top" align="left">
  <table width="50%" border=0 class="data-tbl2" cellpadding="2" cellspacing="0">
  <tr>
  <th id="th_1" align="center" width="25%"><?=$strACLName?></th>
  <th align="center" width="25%"><?=$strLevel1?></th>
  <th align="center" width="25%"><?=$strLevel2?></th>
  <th align="center" width="25%"><?=$strLevel3?></th>
  </tr>
  <?if((!$m_hidden) && $m_id) {?>
    <tr>
    <td>
    <input type="text" size="20" value="<?=$strAccess?>" readonly=1 class="i_readonly">
    </td>
    <td>
    <input type="text" style="color: red;" size="20" value="<?=$strDisable?>" readonly=1 class="i_readonly">
    </td>
    <td>
    <input type="text" style="color: blue;" size="20" value="<?=$strView?>" readonly=1 class="i_readonly">
    </td>
    <td>
    <input type="text" style="color: green;" size="20" value="<?=$strFull?>" readonly=1 class="i_readonly">
    </td>
    </tr>
<? }
    for($i=0; $i<4; $i++) {?>
    <tr>
    <td>
    <input type="text" size="20" name="acl[<?=$i?>][0]" id="acl[<?=$i?>][0]" value="<?=$acl[$i][0]?>">
    </td>
    <td>
    <input type="text" name="acl[<?=$i?>][1]" id="acl[<?=$i?>][1]" style="color: blue;" size="20" value="<?=$acl[$i][1]?>">
    </td>
    <td>
    <input type="text" name="acl[<?=$i?>][2]" id="acl[<?=$i?>][2]" style="color: blue;" size="20" value="<?=$acl[$i][2]?>">
    </td>
    <td>
    <input type="text" name="acl[<?=$i?>][3]" id="acl[<?=$i?>][3]" style="color: blue;" size="20" value="<?=$acl[$i][3]?>">
    </td>
    </tr>

<?}
?>
  </table>
</td>
<td width="15%">&nbsp;</td>
<td width="40%" align="left" valign="top">
<div style="width: 70%;" class="sort-div">
 <h3 align="center"> 
    <?=$strMainMenu?></h3>
    <ul id="sort_menu">
<?	


	$mod->getList(array("*"),0,100);
	$list_mods=$mod->list_mods;
if(empty($list_mods)) exit();
	$i=0;
	foreach($list_mods as $lm) {
	    if($lm[3]) {?>
	    <li id="menuitem_<?=$i?>"><center>
		<?$str=${"str".$lm[1]} ? ${"str".$lm[1]} : $lm[1] ;
		echo $str;?>
	    </center>
	    </li>
	<? $i++;
	    }
	}
?>
    </ul>
</div>
<script>
    Sortable.create("sort_menu",
	{onUpdate: function(e) {
	    var p= new String(''); e=$(e); 
	    Sortable.sequence(e,e.id).map(function(item) {p+=item+',';}); 
	    loadModule(0,0,'Modules','ModulesList',$H({menu_id_list: p.substr(0,p.length-1)}));
	}});
</script>
</td>
</tr>
</table>

</form>

