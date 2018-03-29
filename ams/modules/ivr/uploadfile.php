<?php
if($_FILES['upload_file']['size']>0) {
    $out = exec("/usr/bin/file ".$_FILES['upload_file']['tmp_name']." | /bin/grep -c \"PCM, 16 bit, mono 22050\"");
    if ($out=='1'){
	if(!preg_match('/^[a-zA-Z0-9\.]+$/',$_FILES['upload_file']['name'])){
    	    echo'
    		<script type="text/javascript">
    		    var elm=parent.window.document.getElementById("result_file");
    		    elm.innerHTML=elm.innerHTML+"В имени файла содержатся недопустимые символы(могут быть только буквы и цифры)";
		</script>

	    ';	
	    exit;
	}; 
	$file_name = substr($_FILES['upload_file']['name'],0,strlen($_FILES['upload_file']['name'])-4);
	$h=@mysql_pconnect("localhost", "root", "");
	mysql_query("insert into autocall.files_list(path,list_name) values('/var/lib/asterisk/sounds/autocall/".$file_name.".gsm','".$file_name."')",$h); 
	$res_id=mysql_insert_id($h);
	if ($res_id == 0){
	    echo'
    		<script type="text/javascript">
    		var elm=parent.window.document.getElementById("result_file");
    		elm.innerHTML=elm.innerHTML+"Полученый файл не удалось загрузить,вероятнее,такой файл был загружен ранее";
    		</script>
	    ';
	}else{
	    exec("/usr/bin/sox ".$_FILES['upload_file']['tmp_name']." -r 8000 \"/var/lib/asterisk/sounds/autocall/".$file_name.".gsm\"");
	    echo'
	    
    		<script type="text/javascript">
    		    var elm=parent.window.document.getElementById("result_file");
    		    elm.innerHTML=elm.innerHTML+"Файл Успешно загружен.Обновите список для отображения";
		</script>
	    ';	

	};
    }else{
    	    echo'
    		<script type="text/javascript">
    		    var elm=parent.window.document.getElementById("result_file");
    		    elm.innerHTML=elm.innerHTML+"Формат загружаемого файла не соответствует:PCM, 16 bit, mono 22050.Необходимо загружать файлы только указанного формата.";
		</script>

	    ';	
    }
}
?>
