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


if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.listtbl.php");
include_once("lib/Class.datatbl.php");

if (empty($current_page)) $current_page=0;

if(!$db) $db=DbConnect();
$list_users = $db->get_cond_list("","autocall.files_list","","list_name",$current_page,$limit_display);

$num=$db->count;
$num_disp=count($list_users);
$n_pages = num_pages($num,$limit_display,$current_page);


?>

<div id="frame-module-header" nowrap>
<?="Список звуковых файлов:"?>
</div>

<script>

file = new ObjectD();

file.deletefile=function (id,name) {
    if (!confirm("Удалить список\n" + name + "?")) return;
    loadModule('','autocall','autocall','deletefile',$H({id: id})   );
}

file.SendFile=function() {
    $$('result','');
    //отправка файла на сервер
    $$f({
        formid:'file_form',//id формы
        url:'modules/autocall/uploadfile.php',//адрес на серверный скрипт который будет принимать файл
        onstart:function () {//действие при начале загрузки файла
            $$('result_file','');//в элемент с id="result" выводим результат
        },
        onsend:function () {//действие по окончании загрузки файла
            $$('result_file',$$('result_file').innerHTML);//в элемент с id="result" выводим результат
        }
    });
}


</script>

<form id="file_form" method="post" enctype="multipart/form-data" onSubmit="">
    Загрузка звукового файла<br>
    <input class="file_form" type="file" name="upload_file" />
    <div onclick="file.SendFile();"><button>Загрузить</button></div>
</form><br />
    <div id="result_file"></div><br />


<?

moduleForm(array("current_page","limit_display"));

$tbl = new ListTbl(); 
if (!$num) { echo "</form>"; $tbl->emptyTbl("Нет информации"); }

//if($_SESSION['acl']['Users']['Access']>1) 
$cn=3;
$tbl->tblHead(array($cn,"100%"),array("Список Файлов"));

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  $tbl->checkbox($j,$list_users[$j][0]);
	  $tbl->td("",15,"","file.deletefile",array($list_users[$j][0],$list_users[$j][2]),"drop.gif","Удалить файл");
	  //$tbl->td("",15,"","user.editlist",$list_users[$j][0],"edit.gif",$strEditUser);
	  $tbl->td("",15,"","",$list_users[$j][0],"","");
	  $tbl->td($list_users[$j][2],15,"left","",$list_users[$j][0],"","");
	
	?>
<td></td>
</tr>
<?	

    }
$tbl->tblEnd($current_page,$n_pages);
?>

</form>
</iframe>
