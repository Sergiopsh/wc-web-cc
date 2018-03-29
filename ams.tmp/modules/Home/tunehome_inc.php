<?php
?>
<div id="tune-home-message" class="module-note" style="display: none;">
</div>


<script>


hm = new ObjectD();

hm.menu_all = new Array();
hm.home = new Array();

hm.saveHomeMenu = function() {
    if(!hm.home.length) return;
    var url='modules/Home/savemenu.php';
    var pb="id=1";
    for (var i=0; i < hm.home.length; i++) {
	pb+="&home["+i+"][0]=" + encodeURIComponent(hm.home[i][0]) + "&home["+i+"][1]=" + encodeURIComponent(hm.home[i][1]);
    }
    
    new Ajax.Request(url,
	{postBody: pb,
	 onComplete: function(t) {
	   if(t.responseText)
	      ams.showMessage('&nbsp;&nbsp;&nbsp;' + t.responseText,'tune-home-message');
	 }
	}); 
}

hm.insertItem = function(rm,i) {

    var a = new Array(hm.menu_all[rm].items[i],hm.menu_all[rm].actions[i]);
    hm.home.push(a);
    hm.showHomeMenu();
}

hm.deleteItem = function(i) {
    hm.home[i]=null;
    hm.showHomeMenu();
}
hm.onMouseOver = function(id) {
 id.style.backgroundColor='#92b8d8';

}
hm.onMouseOut = function(id) {
 id.style.backgroundColor=hm.tblBgColor;

}

hm.showModuleMenu = function(rm) {
    var s="<ul style='width: 99%;cursor: hand; list-style-type: none; margin: 0px; padding: 0px;'>";
    var item='';
    for (var i=0; i < hm.menu_all[rm].items.length; i++) {
	item=hm.menu_all[rm].items[i];
	if(item != '')
	    s+="<li height='18' onmouseover='hm.onMouseOver(this)' onmouseout='hm.onMouseOut(this)' >";
	    s+="<a style='color: black;' title='<?=$strInsertItem?>' href='javascript:hm.insertItem(\"" + rm + "\","+i+")'>" + item + "</a></li>";
    }
    s+="</ul>";
    $('menu_items').innerHTML=s;

}
hm.showHomeMenu = function() {
    var s="<ul style='width: 99%;cursor: hand; list-style-type: none; margin: 0px; padding: 0px;'>";    
    var item='';
    hm.home=hm.home.compact();
    for (var i=0; i < hm.home.length; i++) {
	item=hm.home[i][0];
	if(item != '')
	    s+="<li height='18' onmouseover='hm.onMouseOver(this)' onmouseout='hm.onMouseOut(this)' >";
	    s+="<a style='color: black;' title='<?=$strDeleteItem?>' href='javascript:hm.deleteItem("+i+")'>" + item + "</a><br>";
    }
    s+="</ul>";
    $('home_menu').innerHTML=s;
}

</script>



<table border=0 width="80%" >
    <tr height="10"><td></td></tr>
    <tr>
    <td width="2%"></td>
    <td align=left style="font-family: verdana,sans; font-size: 11px; font-weight: 600; color: #333333;">
    &nbsp;&nbsp;<?=$strModule?></td>
    <td width="2%"></td>
    <td align=left style="font-family: verdana,sans; font-size: 11px; font-weight: 600; color: #333333;">
    &nbsp;&nbsp;<?=$strModuleMenu?></td>
    <td width="2%"></td>
    <td align=left style="font-family: verdana,sans; font-size: 11px; font-weight: 600; color: #333333;">
    &nbsp;&nbsp;<?=$strHomeMenu?></td>
    </tr>
    <tr>
	<td width="2%"></td>
	<td id="rm-table" width="25%" valign="top" class="input-data-tbl">
	<table width="100%" border=0>
        <tr><td>

	<ul style="width: 100%; cursor: hand; list-style-type: none; margin: 0px; padding: 0px;">
    <?

foreach($registered_modules as $rm) {
  if(!$_SESSION['acl'][$rm]['Access']) continue;
  if(!$_SESSION['root'] && in_array($rm,$root_modules)) continue;
  unset($module_menu);
  include_once("modules/$rm/menu.php");
    if($module_menu && ($rm<>"Home")) {
	    if(!isset($first_module)) $first_module=$rm;
	    $str=${"str".$rm} ? ${"str".$rm} : $rm;
	    ?>
	    <li height='18' onmouseover="hm.onMouseOver(this);" 
	       onmouseout="hm.onMouseOut(this);" >
	    <a style="color: black;"  title="<?=$strShowModuleMenu?>" href="javascript:hm.showModuleMenu('<?=$rm?>')">
	    <?=$str?>
	    </a>
	    </li>
	    <script>
	    	hm.menu_all['<?=$rm?>'] = new Object();
		hm.menu_all['<?=$rm?>'].items = new Array();
		hm.menu_all['<?=$rm?>'].actions = new Array();
	    </script>
	    <?$i=0;
    	    foreach($module_menu[1] as $m) {?>
	    <script>
	    	hm.menu_all['<?=$rm?>'].items[<?=$i?>]='<?=$m[0]?>';
		hm.menu_all['<?=$rm?>'].actions[<?=$i?>]="<?=$m[1]?>";
	    </script>	
	    <? $i++;
	    }
    }
}

?>
    </ul>
    </td></tr>
    </table>
    </td>
    <td valign="top">
    <table border=0>
	<tr><td>
	<img src="images/bluearrow.gif" align="bottom">
	</td></tr>
    </table>
    </td>
    <td width="25%" valign="top" class="input-data-tbl td">

    <table width="100%" border=0>
    <tr><td>
    <div id="menu_items">
    &nbsp;

    </div>
    </td></tr>
    </table>
    </td>
    <td valign="top">
    <table border=0>
    <tr><td>
    <img src="images/bluearrow.gif" align="absmiddle">
    </td></tr>
    </table>

    </td>

    <td width="25%" valign="top" class="input-data-tbl td">
    <table width="100%" height="100%" border=0 >
    <tr><td>



    <div id="home_menu">
    <?
    if($_SESSION['home']['menu']) {
        $i=0;
	foreach($_SESSION['home']['menu'] as $hm) {
	    //$s=${"str".$hm[0]}; 
	    $s=$hm[0]; 
	    ?>
	    <script>
		hm.home[<?=$i?>] = new Array();
		hm.home[<?=$i?>][0]='<?=$s?>';	
		hm.home[<?=$i?>][1]="<?=$hm[1]?>";	
	    </script>
	<?$i++;
	}
    
    
    }

?>
    </div>
    </td></tr>
    </table>

    </td>
    <td align="left" valign="top">

    <a href="javascript:hm.saveHomeMenu()" title="<?=$strSaveMenu?>">
    <img src="images/save.gif" align="absmiddle">
    </a>
    </td>
    </tr>
<tr height="10"><td></td></tr>
</table>

<script>
  hm.tblBgColor=Element.getStyle('rm-table','backgroundColor');
  hm.showModuleMenu('<?=$first_module?>');
  hm.showHomeMenu();
</script>

