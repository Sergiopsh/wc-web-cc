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

$id = $_POST['id'];
$id_list = $_POST['id'];

include_once("modules/Users/lang/".$lang.".lang.php");


if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");

if (empty($current_page)) $current_page=0;

if(!$db) $db=DbConnect();
$db->set_sql_custom("and id_list=$id");
$list_users = $db->get_cond_list("","autocall.abon_phone","","phone_number",$current_page,$limit_display);

$num=$db->count;
$num_disp=count($list_users);
$n_pages = num_pages($num,$limit_display,$current_page);


?>

<div id="frame-module-header" nowrap>
<?="Список телефонных номеров для выбранного списка:"?>
</div>

<script>

phone = new ObjectD();

phone.deletephone=function (id,name) {
    if (!confirm("Удалить список\n" + name + "?")) return;
    //if (!confirm("функция не поддерживается")) return;
    loadModule('','autocall','autocall','deletephone',$H({id: id,name: name})   );
}

phone.editphone=function (id) {
    if (!confirm("функция не поддерживается")) return;
    //loadModule(1,'autocall','autocall','Editlist',$H({u_id: id}));
}
</script>
<br>
<?

moduleForm(array("current_page","limit_display"));

$tbl = new ListTbl(); 
if (!$num) { echo "</form>"; $tbl->emptyTbl("Нет информации"); }

//if($_SESSION['acl']['Users']['Access']>1) 
$cn=3;
$tbl->tblHead(array($cn,"100%"),array("Номер телефона"));

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  $tbl->checkbox($j,$list_users[$j][0]);
	  $tbl->td("",15,"","phone.deletephone",array($list_users[$j][0],$list_users[$j][1]),"drop.gif","Удалить телефон из списка");
	  //$tbl->td("",15,"","user.editlist",$list_users[$j][0],"edit.gif",$strEditUser);
	  $tbl->td("",15,"","phone.editphone",$list_users[$j][0],"edit.gif","редактировать телефон");
	  $tbl->td($list_users[$j][1],15,"left","",$list_users[$j][0],"","");
	
	?>
<td></td>
</tr>
<?	

    }
$tbl->tblEnd($current_page,$n_pages);
?>
<INPUT TYPE="hidden" NAME="id" id="id" value="<?=$id?>"/>

</form>
</iframe>
