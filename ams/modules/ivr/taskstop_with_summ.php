<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    $query = "update autocall.tasks_with_summ set task_status='stop' where id=$id";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','TasksListWithSumm');
</script>
