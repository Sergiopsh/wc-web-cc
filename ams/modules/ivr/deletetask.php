<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    $query = "select count(*) from autocall.tasks where id=$id and task_status in ('end','stop')";
    $list_two = $db->get_list($query);

    if ($list_two[0][0]==1){
	$query = "DELETE FROM autocall.calls where task_id=$id";
	$list_two = $db->get_list($query);
	$query = "DELETE FROM autocall.tasks where id=$id";
	$list_two = $db->get_list($query);
    }else{
	echo "Состояние задания не позволяет его удалить";
	exit;
    };

?>
<script>
    loadModule('','autocall','autocall','TasksList');
</script>
