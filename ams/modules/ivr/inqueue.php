<?php

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');

//Получим номер агента из $_SESSION['user_name']
$agent = explode("-",$_SESSION['user_name']);
$agent = $agent[1];
//получение информации по очередям
if ($agent==540){
    shell_exec("asterisk -rx \"queue add member Local/$agent@from-internal/n to ".$_POST['queue']."\"");
}else{
    shell_exec("asterisk -rx \"queue add member Local/$agent@from-internal to ".$_POST['queue']."\"");
};
//print "asterisk -rx \"queue add member Local/$agent@from-internal to ".$_POST['queue']."\"";
?>
<script>
<!--
loadModule('','operator_queue','operator_queue','Status');
//-->
</script>
