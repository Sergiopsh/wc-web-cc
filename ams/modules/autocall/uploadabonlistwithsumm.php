<?php
if($_FILES['upload_file']['size']>0) {

    $handle = fopen($_FILES['upload_file']['tmp_name'], "r");
    $badstr=0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$d .= $data[0]."+";
	if (!is_numeric($data[0])) $badstr=1;
	if (!is_numeric( $data[1])) $badstr=1;
	if (!isset($data[1])) $badstr=1;
	if (strlen($data[0])!=12) $badstr=1;
	if ($data[1]>999999.99) $badstr=1;
	$arr = explode('.',$data[1]);
	//$out=$arr[0];
	if (strlen($arr[1])!=2)  $badstr=1;

    };
    if ($badstr==1){
	echo'
    	    <script type="text/javascript">
    	    var elm=parent.window.document.getElementById("result");
    	    elm.innerHTML=elm.innerHTML+"Получен файл, содержащий текстовые данные или номер телефона задан неверно'.$out.'";
    	    </script>
	';
    }else{
	$list_name=substr($_FILES['upload_file']['name'],0,strlen($_FILES['upload_file']['name'])-4);
	$h=@mysql_pconnect("localhost", "root", "");
	mysql_query("insert into autocall.abon_list_with_summ(list_name) values('".$list_name."')",$h); 
	$res_id=mysql_insert_id($h);
	if ($res_id == 0){
	    echo'
    		<script type="text/javascript">
    		var elm=parent.window.document.getElementById("result");
    		elm.innerHTML=elm.innerHTML+"Полученый файл не удалось загрузить,вероятнее,такой файл был загружен ранее";
    		</script>
	    ';
	}else{
	    $handle = fopen($_FILES['upload_file']['tmp_name'], "r");
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$d .= $data[0]."+";
		mysql_query("insert into autocall.abon_phone_with_summ(id_list,phone_number,summ) values($res_id,'$data[0]',$data[1])",$h); 
	    };
	    echo'
    		<script type="text/javascript">
    		    var elm=parent.window.document.getElementById("result");
    		    elm.innerHTML=elm.innerHTML+"Список Успешно загружен.Обновите список для отображения";
		</script>
	    ';	
	};
    }
}
?>
