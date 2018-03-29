<?php
/*include ("modules/ReportsSuper/loadagent1.php");
print workTime(1317488423,1317574823,'Local/502@from-internal');
exit;

include ("modules/ReportsSuper/loadagent.php");
workTime(1317488423,1317574823,'Local/502@from-internal');
exit;
*/

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");

?>
<div id="frame-module-header" nowrap>
Управление списком заданий
</div>
<?
$iformat=dt_format();
if(!isset($dateperiod)) $dateperiod=0;
?>
<br>
<script>
rep = new ObjectD();

rep.setPeriod=function(z) {
    if(z == '0') {
	var a = new Date();
	var m = a.getMinutes();
	if (m<10) m = "0" + m;
	var h = a.getHours();	
	if (h<10) h = "0" + h;	
	var dt = setPeriod(z);
	$('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
	$('periodto').value=dt[1].print("<?=$iformat?> "+h+":"+m);
	return;
    };
    var dt = setPeriod(z);
    $('periodfrom').value=dt[0].print("<?=$iformat?> %H:00");
    $('periodto').value=dt[1].print("<?=$iformat?> %H:59");
}

</script>

<FORM name="module_form" id="module_form" METHOD="POST">
<INPUT TYPE="hidden" NAME="current_page" id="current_page" value="<?=$current_page?>"/>
<INPUT TYPE="hidden" NAME="limit_display" id="limit_display" value="<?=$limit_display?>"/>
<INPUT TYPE="hidden" NAME="sort_date" id="sort_date" value="<?=$sort_date?>"/>



<?

$currentdate=date("Y-m-d");
$current_time=date(" H:i");

$tbl = new DataTbl("",0,0,4,"margin-bottom: 10px;");

$p=$tbl->addElement("taskname","input","Имя задания",1,"New Task");

$p=$tbl->addElement("startfrom","time","Время начала выполнения",2,dbformat_to_dt("$currentdate$current_time"));
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");
$p=$tbl->addElement("endto","time","Время окончания",2,dbformat_to_dt("$currentdate 18:00")); //$p->colspan=5; $p->width2="15%";
$p->setOptions(18,"$iformat %H:%M","","function (cal) { $('fixperiod').value='0'; }");

$p=$tbl->addElement("try_count","select","Попыток дозвона",3,$dateperiod);
$p->options=array(1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5" );
$p->action="rep.setPeriod(this.value)";

$p=$tbl->addElement("cals_in_min","select","Звонков в минуту",3,$dateperiod);
$p->options=array(1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5" );
$p->action="rep.setPeriod(this.value)";

$p=$tbl->addElement("answer_wait","select","Ждать ответа,сек",4,"5");
$p->options=array(20 => "20", 30 => "30",40 => "40",50 => "50", 60 => "60" );
$p->action="rep.setPeriod(this.value)";

$p=$tbl->addElement("try_delay","select","Пауза между попытками,сек",4,"10");
$p->options=array(5 => "5", 10 => "10", 15 => "15", 20 => "20", 25 => "25" );
$p->action="rep.setPeriod(this.value)";


$p=$tbl->addElement("list_name","select","Имя списка",5,"");
if(!$db) $db=DbConnect();
$QUERY="select id_list,list_name from autocall.abon_list";
$list = $db->get_list($QUERY); 
foreach ($list as $data){
     $p->options[$data[0]]=$data[1];
};

$p=$tbl->addElement("file_name","select","Имя записи",5,"");
if(!$db) $db=DbConnect();
$QUERY="select id_list,list_name from autocall.files_list";
$list = $db->get_list($QUERY); 
foreach ($list as $data){
     $p->options[$data[0]]=$data[1];
};


$p=$tbl->addElement("submit_button","button","",5,"Добавить"); $p->width2="15%";
$p->action="loadModule(1,'autocall','autocall','TasksList',\$H({search: 1}));"; $p->align2="left";
$tbl->show();

?>


</FORM>

<br>

<?
if (isset($submit_button)){
    $QUERY = "
	insert into autocall.tasks(
	    task_name,
	    start_date,
	    end_date,
	    list_id,
	    file_id,
	    calls_in_min,
	    task_status,
	    try_delay,
	    answer_wait,
	    try_count
	) 
	values(
	    '$taskname',
	    STR_TO_DATE('$startfrom','%d-%m-%Y %H:%i'),
	    STR_TO_DATE('$endto','%d-%m-%Y %H:%i'),
	    $list_name,
	    $file_name,
	    $cals_in_min,
	    'before_start',
	    $try_delay,
	    $answer_wait,
	    $try_count
	)";
    mysql_query($QUERY); 
    $res_id=mysql_insert_id();
    if ($res_id == 0){
	echo "Задание не было добавлено, задание с таким именем существует";
    };
};

?>
<script>

task = new ObjectD();

task.deletetask=function (id,name) {
    if (!confirm("Удалить список\n" + name + "?")) return;
    loadModule('','autocall','autocall','deletetask',$H({id: id})   );
}

task.start_immediatly=function (id,name) {
    if (!confirm("Запустить задачу\n" + name + "?")) return;
    //if (!confirm("функция не поддерживается")) return;
    loadModule('','autocall','autocall','TaskWork',$H({id: id})   );
}
task.start_by_time=function (id,name) {
    //if (!confirm("Запустить задачу\n" + name + "?")) return;
    if (!confirm("функция не поддерживается")) return;
    //loadModule('','autocall','autocall','TaskWork',$H({id: id})   );
}

task.stop=function (id,name) {
    if (!confirm("Остановить задачу\n" + name + "?")) return;
    loadModule('','autocall','autocall','TaskStop',$H({id: id})   );
}

task.pause=function (id,name) {
    if (!confirm("Поставить задачу\n" + name + " на паузу?")) return;
    loadModule('','autocall','autocall','TaskPause',$H({id: id})   );
}

task.viewtask=function (id,name) {
    //if (!confirm("Удалить список\n" + name + "?")) return;
    //if (!confirm("функция не поддерживается")) return;
    loadModule('','autocall','autocall','ViewTask',$H({id: id})   );
}


</script>
<?

if (empty($current_page)) $current_page=0;

if(!$db) $db=DbConnect();
$list_tasks = $db->get_cond_list("","autocall.tasks","","id,task_name,start_date,end_date,list_id,file_id,calls_in_min,task_status,try_delay,answer_wait,try_count",$current_page,$limit_display);

$num=$db->count;
$num_disp=count($list_tasks);
$n_pages = num_pages($num,$limit_display,$current_page);



moduleForm(array("current_page","limit_display"));

$tbl = new ListTbl(); 
if (!$num) { echo "</form>"; $tbl->emptyTbl("Нет информации"); }

//if($_SESSION['acl']['Users']['Access']>1) 
$cn=3;
$tbl->tblHead(array("","5","","","","","","","","","","","","","","","",""),array("","","","Статус","Имя задания","Начать","Завершить","Список"," Запись","Звонков в мин.","Пауза,сек"," Ждать ответа,сек","Попыток","Обработанные","Необработанные","Успешные","Неуспешные"),"100%");

    for($j=0; $j < $num_disp; $j++) {
	    $tbl->tblTr($j);

	  switch ($list_tasks[$j][7]) {
	    case 'before_start':
		$tbl->td("",15,"","",array(),"","");	    
		$tbl->td("",15,"","task.start_immediatly",array($list_tasks[$j][0],$list_tasks[$j][1]),"turnon.gif","запустить немедленно");	    
		$tbl->td("",15,"","task.start_by_time",array($list_tasks[$j][0],$list_tasks[$j][1]),"play.png","запустить по времени");		
		$tbl->td("Ожидание подтверждения запуска",15,"left","","Ожидание подтверждения запуска","","");
		break;
	    case 'before_start_by_time':
		$tbl->td("",15,"","",array(),"","");	    
		$tbl->td("",15,"","task.start_immediatly",array($list_tasks[$j][0],$list_tasks[$j][1]),"turnon.gif","запустить немедленно");	    
		$tbl->td("",15,"","task.stop",array($list_tasks[$j][0],$list_tasks[$j][1]),"turnoff.gif","Остановить");		
		$tbl->td("Ожидание запуска по времени",15,"left","","Ожидание запуска по времени","","");
		break;
	    case 'work':
		$tbl->td("",15,"","",array(),"","");	    
		$tbl->td("",15,"","task.pause",array($list_tasks[$j][0],$list_tasks[$j][1]),"down.gif","Пауза");					    
		$tbl->td("",15,"","task.stop",array($list_tasks[$j][0],$list_tasks[$j][1]),"turnoff.gif","Остановить");		
		$tbl->td("Идёт Выполнение задания",15,"left","task.viewtask",$list_tasks[$j][0],"","Просмотреть отчёт о выполнении");
		break;
	    case 'pause':
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("",15,"","task.start_immediatly",array($list_tasks[$j][0],$list_tasks[$j][1]),"turnon.gif","запустить немедленно");
		//$tbl->td("Идёт Выполнение задания",15,"left","task.viewtask",$list_tasks[$j][0],"","Просмотреть отчёт о выполнении");		
		$tbl->td("На паузе",15,"left","task.viewtask",$list_tasks[$j][0],"","На паузе","","");
		break;
	    case 'stop':
		$tbl->td("",15,"","task.deletetask",array($list_tasks[$j][0],$list_tasks[$j][1]),"drop.gif","Удалить задачу");
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("Задание остановлено",15,"left","task.viewtask",$list_tasks[$j][0],"","Задание остановлено","","");
		break;
	    case 'end':
		$tbl->td("",15,"","task.deletetask",array($list_tasks[$j][0],$list_tasks[$j][1]),"drop.gif","Удалить задачу");
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("",15,"","",array(),"","");
		$tbl->td("Выполнение задачи завершено",15,"left","task.viewtask",$list_tasks[$j][0],"","Выполнение задачи завершено","","");
		break;
	  }

	  $tbl->td($list_tasks[$j][1],15,"left","",$list_tasks[$j][1],"","");
	  $tbl->td($list_tasks[$j][2],15,"left","",$list_tasks[$j][2],"","");
	  $tbl->td($list_tasks[$j][3],15,"left","",$list_tasks[$j][3],"","");
	$query = "select list_name from autocall.abon_list where id_list=".$list_tasks[$j][4];
	$res = $db->get_list($query);
	  $tbl->td($res[0][0],15,"left","",$res[0][0],"","");
	$query = "select list_name from autocall.files_list where id_list=".$list_tasks[$j][5];
	$res = $db->get_list($query);
	  $tbl->td($res[0][0],15,"left","",$res[0][0],"","");
	  $tbl->td($list_tasks[$j][6],15,"left","",$list_tasks[$j][6],"","");
	  $tbl->td($list_tasks[$j][8],15,"left","",$list_tasks[$j][8],"","");
	  $tbl->td($list_tasks[$j][9],15,"left","",$list_tasks[$j][9],"","");
	  $tbl->td($list_tasks[$j][10],15,"left","",$list_tasks[$j][10],"","");
	  
	  $QUERY2 = "select sum(processed_by != 0) as proc,count(*)-sum(processed_by != 0) as non_proc,sum(call_status=2) as good,sum(call_status=4) as bad from autocall.calls where task_id=".$list_tasks[$j][0]." limit 10";
	  $h=@mysql_pconnect("localhost", "root", "");
	  $res=mysql_query($QUERY2,$h);
	  $row = mysql_fetch_array($res); 
	  $all = $row["proc"]+$row["non_proc"];
	  $val = $row["proc"]." (".round($row["proc"]/$all*100)."%)";
	  $tbl->td($val,15,"left","","","","");
	  $val = $row["non_proc"]." (".round($row["non_proc"]/$all*100)."%)";
	  $tbl->td($val,15,"left","","","","");

	  $val = $row["good"]." (".round($row["good"]/$row["proc"]*100)."%)";
	  $tbl->td($val,15,"left","","","","");

	  $val = $row["bad"]." (".round($row["bad"]/$row["proc"]*100)."%)";
	  $tbl->td($val,15,"left","","","","");

    }
$tbl->tblEnd($current_page,$n_pages);



?>
