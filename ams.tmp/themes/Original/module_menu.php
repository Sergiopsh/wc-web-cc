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
?>
<script src="themes/Original/rico.js"></script>

<div id="menu-turnon" style="display: none;">
 <div id="accordionDiv">
<?
    $i=0;$max_items=0;
    
    $n=count($main_menu)-1;
    for(;$n > 0; $n--) {
	$last = $main_menu[$n][0][0];
	if($_SESSION['acl'][$last]['Access']) break;
    }
    foreach($main_menu as $menuitem) {
    	$mname=$menuitem[0][0];
    if(!$_SESSION['acl'][$mname]['Access']) continue; ?>
    <div id="_panelTab<?=$mname?>" onclick="">
	<div class="panelTabHeader" id="_panelTabHeader<?=$mname?>" 
	    <? if(!$_SESSION['is_admin'] && ($mname == $last)) {?>
	    style="border-bottom-color: #63699c;"

	    <?}?> >
	<script type="text/javascript">
	Rico.__setTab('<?=$mname?>','<?=$i?>','<?=addslashes($menuitem[2])?>');
	</script>

	<table border="0" cellspacing="0" cellpadding="0" style="margin-left: 3px; margin-top: 2px;">
	    <tr><td width="15">
	    <?if($menuitem[0][1]) {?>
	    <img src="<?=$menuitem[0][1]?>" align="top" alt="menuitem" />
	    <?}else echo "&nbsp;";
	    ?>
	    </td>
	    <td style="padding-left: 15px;" valign="middle"><?=$menuitem[1]?></td>
	    </tr>
	</table>
	</div>
	<div id="_panelTabContent<?=$menuitem[0][0]?>" class="panelTabContent">
	    <table width="100%" border="0" cellspacing="0" cellpadding="1" class="panelTabContentTbl">
	<?
	    include_once("modules/".$menuitem[0][0]."/menu.php");
	    $m_items=0;
	    foreach($module_menu[1] as $mm) {
	     if($mm[1]) {
	        $m_items++;
	     ?>
	    <tr><td width="3">&nbsp;</td>
	    <td style="padding: 2px;"><a class="menulink" href="<?=$mm[1]?>"><?=$mm[0]?></a>
	    </td>
	    <td align="right"><a class="menulink" href="<?=$mm[1]?>">
	    <img src="themes/<?=$theme?>/images/menuarrow.gif" alt="item" 
	    style="border: 0px; margin: 0px; padding: 0px;" 
	     onmouseover="this.src='themes/<?=$theme?>/images/menuarrow_h.gif'" onmouseout="this.src='themes/<?=$theme?>/images/menuarrow.gif'" /></a>
	    </td>
	    <td width="3">&nbsp;</td>
	    </tr>
	    <?} else echo "<tr><td></td></tr>"; 
	   }?>
	   </table>
	</div>
    </div>

<? $i++; if($max_items<$m_items) $max_items=$m_items;
       
}
$j=0;
foreach($aux_modules as $hm) {
 if(!$_SESSION['acl'][$hm]['Access']) continue;
  include_once("modules/$hm/menu.php");
?>
    <div id="_panelTab<?=$hm?>" style="display: none;">
	<div class="panelTabHeader" id="_panelTabHeader<?=$hm?>">
	<script type="text/javascript">
	  Rico.__setTabH('<?=$hm?>','<?=$i?>');    
	</script>
	<table border="0" cellspacing="0" cellpadding="0" style="margin-left: 3px; margin-top: 2px;">
	    <tr>
	    <td width="15">
	    <img src="<?=$module_menu[0][1]?>" alt="menuitem" />
	    </td>
	    <td width="15">&nbsp;</td>
	    <td><?=$module_menu[0][0]?></td>
	    </tr>
	</table>
	</div>
	<div id="_panelTabContent<?=$hm?>" class="panelTabContent">
	    <table width="100%" border="0" cellspacing="0" cellpadding="1" class="panelTabContentTbl">
	 <?$m_items=0;
	 foreach($module_menu[1] as $mm) {
	  if($mm[1]) {
	    $m_items++;
	    ?>
	    <tr><td width="3">&nbsp;</td>
	    <td style="padding: 2px;">
	    <a class="menulink" href="<?=$mm[1]?>"><?=$mm[0]?></a>
	    </td>
	    <td align="right"><a class="menulink" href="<?=$mm[1]?>">
	    <img src="themes/<?=$theme?>/images/menuarrow.gif" style="border: 0px; margin: 0px; padding: 0px;"
	     onmouseover="this.src='themes/<?=$theme?>/images/menuarrow_h.gif'" onmouseout="this.src='themes/<?=$theme?>/images/menuarrow.gif'" alt="" /></a>
	    </td>
	    <td width="3">&nbsp;</td>
	    </tr>
	    <?} else echo "<tr><td></td></tr>"; 
	 }?>
	  </table>
	</div>
    </div>
<?$i++;$j++; if($max_items<$m_items) $max_items=$m_items;
 
}
if($_SESSION['is_admin']) {
    include_once("modules/Administration/menu.php");
    ?>
    <div id="_panelTabAdministration">
	<div class="panelTabHeader" id="_panelTabHeaderAdministration" style="border-bottom-color: #63699c;">
	<script type="text/javascript">
	    Rico.__setTab('Administration','<?=$i?>',"javascript:loadModule('','Administration','Administration','UsersList')");
	</script>
	<table border="0" cellspacing="0" cellpadding="0" style="margin-left: 3px; margin-top: 2px;">
	    <tr><td width="15">
	    <img src="images/admin.gif" alt="" />
	    </td><td width="15">&nbsp;</td>
	    <td><?=$strAdministration?></td>
	    </tr>
	</table>
	</div>
	<div id="_panelTabContentAdministration" class="panelTabContent">
	    <table width="100%" border="0" cellspacing="0" cellpadding="1" class="panelTabContentTbl">
	 <?$m_items=0;
	 foreach($module_menu[1] as $mm) {
	    if($mm[1]) {
		$m_items++;
	    ?>
	    <tr>
	    <td width="3">&nbsp;</td>
	    <td style="padding: 2px;" nowrap="nowrap"><a class="menulink" href="<?=$mm[1]?>"><?=$mm[0]?></a>
	    </td>
	    <td align="right"><a class="menulink" href="<?=$mm[1]?>">
	    <img src="themes/<?=$theme?>/images/menuarrow.gif" style="border: 0px; 
	    margin: 0px; padding: 0px;" onmouseover="this.src='themes/<?=$theme?>/images/menuarrow_h.gif'" onmouseout="this.src='themes/<?=$theme?>/images/menuarrow.gif'" alt="" /></a>
	    </td>
	    <td width="3">&nbsp;</td>
	    </tr>
	    <?}else echo "<tr><td></td></tr>"; 
	 }?>
	   </table>
	</div>
    </div>
<? if($max_items<$m_items) $max_items=$m_items;
}
$p_height=$max_items*20;
if($p_height<120) $p_height=120;
?>
 </div>
</div>
<script type="text/javascript">

    _accordionMenu = new Rico.Accordion($('accordionDiv'),{panelHeight: <?=$p_height?>,
		onLoadShowTab: 0,
		onShowTab: function (tab) { Rico.__onShowTab(tab);    }
		});
	
    $('menu-turnon').style.display='inline';

</script>

