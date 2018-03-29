<?php


if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("lib/Class.datatbl.php");
include_once("lib/Class.listtbl.php");
include_once("modules/operator_queue/asterisk.php");
?>
<div nowrap id="frame-module-header">
<?=$strStatus?>: Очереди
</div>
<br>
<?

//Получим номер агента из $_SESSION['user_name']
$agent = explode("-",$_SESSION['user_name']);
$agent = $agent[1];
if (!is_numeric($agent)) exit;
//получение информации по очередям
$queue_info_arr = explode("\n",shell_exec("asterisk -rx \"show queues\""));

$agent_queues = get_agent_queue($queue_info_arr,$agent);
$queues = get_queue_list($queue_info_arr);

?>
<table border="0" cellspacing="0" cellpadding="0" width="50%" class="report-tbl">
 <tr><td>
 	<table border="0" cellspacing="1" cellpadding="1" width="100%">

	<tr id="head">
	<td align="center">Наименование Очереди</td>
	<td align="center">Статус</td>
	<td align="center">Действие: Войти в очередь</td>
	<td align="center">Действие: Выйти из очереди</td>
	</tr>

<?
foreach($queues as $queue){
    ?>
    <tr>
	<td align="center"><?=$queue?></td>
	<?
	if (agent_in_queue($agent_queues,$queue)){ 
	    echo "<td bgcolor=\"lightgreen\" align=\"center\">В очереде";
	    echo "<td align=\"center\">Нет действия";
	    echo "<td align=\"center\"> <input class=\"sbutton\" type=\"button\" value=\"Выход\" onclick=\"loadModule('','operator_queue','operator_queue','OutQUEUE',\$H({queue: '$queue'}));\"> </td>";	    
	}else{ 
	    echo "<td align=\"center\">Не вошёл";
	    echo "<td align=\"center\"> <input class=\"sbutton\" type=\"button\" value=\"Вход\" onclick=\"loadModule('','operator_queue','operator_queue','InQUEUE',\$H({queue: '$queue'}));\"> </td>";
	    echo "<td align=\"center\">Нет действия";
	}
	   ?>
	</td>
    </tr>
    <?
};
exit;
?>
<script>
<!--
alert("wait");
//-->
</script>

