<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    $query = "select path FROM autocall.files_list where id_list=$id";
    $list_two = $db->get_list($query);
    exec("rm -rf \"".$list_two[0][0]."\"");
    $query = "DELETE FROM autocall.files_list where id_list=$id";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','FilesList');
</script>
