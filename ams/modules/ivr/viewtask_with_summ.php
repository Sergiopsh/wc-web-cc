<?php

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");


if (empty($current_page)) $current_page=0;
if (empty($limit_display)) $limit_display=0;
if (empty($sort_date)) $sort_date=0;


$id = $_POST['id'];
$id_list = $_POST['id'];


?>
<div id="frame-module-header" nowrap>
Информация о задании
</div>
<br>
<script>
rep = new ObjectD();
</script>

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>"/>
<INPUT TYPE="hidden" NAME="limit_display" id="limit_display" value="<?=$limit_display ?>"/>
<INPUT TYPE="hidden" NAME="id" id="id" value="<?=$id?>"/>
<INPUT TYPE="hidden" NAME="sort_date" id="sort_date" value="<?=$sort_date?> "/>


</FORM>

<br>


<?

if(!$db) $db=DbConnect();
$db->set_sql_custom("and task_id=$id");
$list_tasks = $db->get_cond_list(array("task_id","phone_number","path","date_first_try","try_number","date_end","call_length","call_status","processed_by","call_end-call_start"),"autocall.calls_with_summ","","call_end-call_start,call_status desc",$current_page,$limit_display);

$num=$db->count;
$num_disp=count($list_tasks);
$n_pages = num_pages($num,$limit_display,$current_page);


moduleForm(array("current_page","limit_display"));

$tbl = new ListTbl(); 

if (!$num) { echo "</form>"; $tbl->emptyTbl("Нет информации"); }

//if($_SESSION['acl']['Users']['Access']>1) 
$cn=3;
$tbl->tblHead(array("","5","","","","",""),array("Номер телефона","Результат","Дата/время первой попытки","Номер попытки","Дата/время последней попытки","Продолжительность звонка,сек"),"100%");

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);
	  $tbl->td($list_tasks[$j][1],15,"left","",$list_tasks[$j][1],"","");

	  switch ($list_tasks[$j][7]) {
	    case '0':
		$tbl->td("Попыток не было",15,"left","","Попыток не было","","");
		break;
	    case '1':
		$tbl->td("Идёт попытка дозвона",15,"left","","Идёт попытка дозвона","","");
		break;
	    case '2':
		$tbl->td("Последняя попытка успешна",15,"left","","Последняя попытка успешна","","");
		break;
	    case '3':
		$tbl->td("Последняя попытка провалена",15,"left","","Последняя попытка провалена","","");
		break;
	    case '4':
		$tbl->td("Вызов провален",15,"left","","Вызов провален","","");
		break;
	  };
	  $tbl->td($list_tasks[$j][3],15,"left","",$list_tasks[$j][3],"","");
	  $tbl->td($list_tasks[$j][4],15,"left","",$list_tasks[$j][4],"","");
	  $tbl->td($list_tasks[$j][5],15,"left","",$list_tasks[$j][5],"","");
	  $tbl->td(round($list_tasks[$j][9],15),"left","",round($list_tasks[$j][9]),"","");

    }
$tbl->tblEnd($current_page,$n_pages);



?>


