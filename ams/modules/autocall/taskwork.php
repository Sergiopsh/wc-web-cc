<?
    $id = $_POST['id'];
    if(!$db) $db=DbConnect();
    //проверка было ли состояние before_start
    $query = "select task_status from autocall.tasks where id=$id";
    $list_two = $db->get_list($query);
    if (strstr($list_two[0][0],"before_start")){
	$query = "
	    INSERT
	    INTO autocall.calls
	      (
	        task_id,
	        phone_number,
	        path,
	        date_first_try,
	        try_number,
	        date_end,
	        call_length,
	        call_status,
	        processed_by,
	        processed_status
	      )
	    SELECT t.id,
	      ap.phone_number,
	      fl.path,
	      NULL,
	      0,
	      NULL,
	      NULL,
	      0,
	      0,
	      0
	    FROM autocall.tasks t
	    INNER JOIN autocall.files_list fl
	    ON t.file_id=fl.id_list
	    INNER JOIN autocall.abon_phone ap
	    ON t.list_id=ap.id_list
	    where t.id=$id
	";
	$list_two = $db->get_list($query);
    };
    $query = "update autocall.tasks set task_status='work' where id=$id";
    $list_two = $db->get_list($query);
?>
<script>
    loadModule('','autocall','autocall','TasksList');
</script>
