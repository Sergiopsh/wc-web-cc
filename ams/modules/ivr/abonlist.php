



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


include_once("modules/Users/lang/".$lang.".lang.php");
include_once("modules/autocall/User.php");


if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");
$user = new User($u_id);

if (empty($current_page)) $current_page=0;


$list_users=$user->getList(array($f_name),$f_admin,$f_status,$current_page,$limit_display);
$num=$user->num_rows;
$num_disp=count($list_users);
$n_pages = num_pages($num,$limit_display,$current_page);


?>

<div id="frame-module-header" nowrap>
<?="Список телефонных номеров"?>
</div>
<script>

list = new ObjectD();

list.deletelist=function (id,name) {
 if (!confirm("Удалить список\n" + name + "?")) return;
    loadModule('','autocall','autocall','deletelist',$H({id: id})   );
}

list.editlist=function (id) {
    if (!confirm("функция реализована внутри просмотра списка")) return;
    //loadModule(1,'autocall','autocall','Editlist',$H({u_id: id}));
}
list.viewlist=function (id) {
    loadModule('','autocall','autocall','viewlist',$H({id: id})   );
}
list.SendFile=function() {
    $$('result','');
    //отправка файла на сервер
    $$f({
        formid:'test_form',//id формы
        url:'modules/autocall/uploadabonlist.php',//адрес на серверный скрипт который будет принимать файл
        onstart:function () {//действие при начале загрузки файла
            //$$('result','начинаю отправку файла');//в элемент с id="result" выводим результат
        },
        onsend:function () {//действие по окончании загрузки файла
            $$('result',$$('result').innerHTML);//в элемент с id="result" выводим результат
        }
    });
}


</script>

<form id="test_form" method="post" enctype="multipart/form-data" onSubmit="">
    Загрузка списка абонентов из файла<br>
    <input class="file_form" type="file" name="upload_file" />
    <div onclick="list.SendFile();"><button>Загрузить</button></div>
</form><br />
    <div id="result"></div><br />

<?

moduleForm(array("current_page","limit_display"));

$tbl = new ListTbl(); 
if (!$num) { echo "</form>"; $tbl->emptyTbl("Нет информации"); }

//if($_SESSION['acl']['Users']['Access']>1) 
$cn=3;
$tbl->tblHead(array($cn,"100%"),array("Имя списка"));

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  $tbl->checkbox($j,$list_users[$j][0]);
	  $tbl->td("",15,"","list.deletelist",array($list_users[$j][0],$list_users[$j][1]),"drop.gif","Удалить запись");
	  //$tbl->td("",15,"","user.editlist",$list_users[$j][0],"edit.gif",$strEditUser);
	  $tbl->td("",15,"","list.editlist",$list_users[$j][0],"edit.gif","редактировать список");
	  $tbl->td($list_users[$j][1],15,"left","list.viewlist",$list_users[$j][0],"","Просмотр и редактирование списка");
	
	?>
<td></td>
</tr>
<?	

    }
$tbl->tblEnd($current_page,$n_pages);
?>

</iframe>
