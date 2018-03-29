<?php
/*
 * Asterisk Management System - An open source toolkit for Asterisk PBX.
 * See http://www.asterisk.org for more information about
 * the Asterisk project.
 *
 * Copyright (C) 2006 - 2007, West-Web Limited.
 *
 * Nickolay Shestakov <ns@ampex.ru>
 *
 * This program is free software, distributed under the terms of
 * the GNU General Public License Version 2. See the LICENSE file
 * at the top of the source tree.
 */

if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
if (!isset($_SESSION['current_t_id'])) die('Not A Valid Entry');
else {
    $t_id=$_SESSION['current_t_id'];
    $tplan= new TariffPlan($t_id);
    list($t_name,$accountcode,$t_step,$t_val,$o_name)=$tplan->get_data();
}
include_once("lib/Class.datatbl.php");
$show_action_time=false;
?>
<div id="frame-module-header" nowrap>
<?=$strImportHead?>
</div>
<br>
<div class='module-warning'><?=$strImportHead2?> - <b><?=$t_name?></b>
</div>

<script>

importFile = function() {
    if($('import_file').value=='') return;
    if ($('replace').checked && !confirm("<?=$strWinConfirmImport?>")) return;
    $('div-import-form').hide();
    $('frame-import-file').show();
    $('import_log').innerHTML='<?=$strImportLog?><br><br>';
    $('module_form').submit();
}

</script>

<br>
<div id="import_log" class="module-warning">
</div>
<div style="width: 100%;">
  <iframe name="frame-import-file" id="frame-import-file" width="100%" scrolling="yes" frameborder=0 
   style="height: 500px;border: 0px solid black; display: none;">
  </iframe>
</div>
<div id="div-import-form">
<form name="module_form" id="module_form" method="post" enctype="multipart/form-data" action="modules/Rates/import.php" target="frame-import-file">
<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="<?=$max_file_size?>">
<input type="hidden" name="accountcode" id="accountcode" value="<?=$accountcode?>">

<?
$tbl = new DataTbl("70%");
$p=$tbl->addElement("import_file","text",$strImportFromFile,1,$import_file);
$p->setOptions(40,1); $p->type="file";
$p=$tbl->addElement("","button","",1,$strImport); $p->align2="right"; $p->action="importFile()";
$tbl->show();
$col_names="name, code_from, code_to, min_len, max_len, rate";
?>
<br>
<table border=0 class="input-data-tbl" width="70%" cellpadding=0 cellspacing=5>
    <tr><td width="30%">&nbsp;&nbsp;<?=$strOptions?>: </td>
    <?/*
    <td nowrap width="1%">
    <input style="vertical-align: top;" type="checkbox" class="checkbox" name="checking" id="checking" value="checking" <?if ($checking) echo "checked"?> >
    </td><td nowrap>
    <?=$strShowLog?>
    </td> </tr>
    <tr><td>&nbsp</td>
    */?>
    <td width="1%">
    <input type="checkbox" class="checkbox" name="replace" id="replace" value="replace" <?if ($replace) echo "checked"?> >
    </td><td nowrap>
    <?=$strReplaceData?>
    </td> </tr>
    <tr><td>&nbsp</td>

    <td nowrap colspan=2>
	<table border=0 cellpadding=0 cellspacing=2 style="font-size: 12px;">
	<tr><td nowrap>
	&nbsp;<?=$strIgnoreFirst?>&nbsp;</td><td>
	<input type="text" size="5" maxlength="2" name="first_rows" value="<?=$first_rows?>" >
	<?=$strRows?></td>
	</tr><tr>
	<td nowrap >&nbsp;<?=$strSeparator?>&nbsp;</td><td>
	<input type="text" size="5" maxlength="1" name="separator" id="separator" value='<?if ($separator) echo $separator; else echo ";";?>' >
	</td></tr>
        <tr><td nowrap>&nbsp;<?=$strColNames?>&nbsp;</td><td>
	<input type="text" size="50" name="col_names" id="col_names" value="<?=$col_names?>">
	</td></tr>
        </table>
    </td></tr>
</table>

</form>
</div>






