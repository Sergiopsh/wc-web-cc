<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    $query = "DELETE FROM autocall.abon_phone_with_summ where id_list=$id";
    $list_two = $db->get_list($query);
    $query = "DELETE FROM autocall.abon_list_with_summ where id_list=$id";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','AbonListWithSumm');
</script>
