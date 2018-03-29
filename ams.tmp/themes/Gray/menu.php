<?
include("themes/$theme/main_menu.php");
?>
<!-- header END -->
<!-- leftside BEGIN -->
<div id="frame-side-left">
<?
include_once("modules/$menu_module/menu.php");
?>
<br>
<?
if($_SESSION['acl'][$menu_module]['Access'])
    include_once("themes/$theme/module_menu.php");
?>
<br><br>
<?
//include_once("modules/$menu_module/extra_form.php");

?>

</div>