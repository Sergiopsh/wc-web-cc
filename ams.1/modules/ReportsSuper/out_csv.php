<?
$data = file_get_contents("/var/www/html/ams/modules/ReportsSuper/csv/".$_GET['file']);
$data = iconv('utf-8', 'windows-1251', $data);
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$_GET['file']);
echo $data;
?>
