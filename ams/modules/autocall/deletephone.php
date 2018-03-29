<?
    $id = $_POST['id'];
    $name = $_POST['name'];
    if(!$db) $db=DbConnect();
    $query = "DELETE FROM autocall.abon_phone where id_list=$id and phone_number='$name'";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','AbonList');
</script>
