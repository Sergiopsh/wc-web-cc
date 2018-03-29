<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    $query = "update autocall.tasks set task_status='stop' where id=$id";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','TasksList');
</script>
