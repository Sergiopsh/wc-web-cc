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
if(!isset($e_id)) die('Not a Valid Entry');

$emp = new Employee($e_id);

list($is_user,$e_first_name,$e_last_name,$e_comment,$e_address,$e_phone_work,
     $e_phone_mobile,$e_phone_home,$e_phone_office,$e_department,
     $e_fax,$e_email,$e_messenger,$e_title) = $emp->get_data();

$date_entered=$emp->date_entered;
$date_modified=$emp->date_modified;
$created_by=$emp->created_by;
$modified_by=$emp->modified_by;

?>
<div id="frame-module-header" nowrap>
<?=$strEmployee?>: <?echo "$e_first_name $e_last_name";?>
</div>
<br>

<form name="module_form" id="module_form" method="post">
<input type="hidden" name="e_id" id="e_id" value="<?=$e_id?>">
<table width="100%" class="input-data-tbl" cellpadding=0 cellspacing=5>

    <tr>
    <td width="10%">&nbsp;<?=$strFirstName?></td>
    <td width="30%" ><INPUT TYPE="text" NAME="e_first_name" id="e_first_name" size="30" value="<?=hs($e_first_name)?>" readonly=1 class="i_readonly">
    </td>
    <td width="10%" nowrap>&nbsp;<?=$strTitle?></td>
    <td ><INPUT TYPE="text" NAME="e_title" id="e_title" size="30" value="<?=hs($e_title)?>" readonly=1 class="i_readonly">
    </td>


     <?if ($_SESSION['acl']['Employees']['Access']>1) {?>
     <td align=right>
     <input type="button" onclick="loadModule(1,menu_module,'Employees','EditEmployee');" value="<?=$strEdit?>" class="sbutton">
     &nbsp;</td></tr>
    <?}?>

    </tr>
    <tr>
    <td nowrap>&nbsp;<?=$strLastName?></td>
    <td ><INPUT TYPE="text" NAME="e_last_name" id="e_last_name" size="30" value="<?=hs($e_last_name)?>" readonly=1 class="i_readonly">
    </td>
    <td nowrap>&nbsp;<?=$strDepartment?></td>
    <td ><INPUT TYPE="text" NAME="e_department" id="e_department" size="30" value="<?=hs($e_department)?>" readonly=1 class="i_readonly">
    </td>
    </tr>

</table>
<br>

<table width="100%" class="input-data-tbl" cellspacing=5>
    <tr>

    <td width="10%" nowrap>&nbsp;<?=$strPhoneOffice?></td>
    <td width="30%" ><INPUT TYPE="text" size="30" value="<?=hs($e_phone_office)?>" readonly=1 class="i_readonly">
    </td>
    <td nowrap width="10%">&nbsp;<?=$strPhoneWork?></td>
    <td><INPUT TYPE="text" size="30" value="<?=hs($e_phone_work)?>" readonly=1 class="i_readonly">
    </td>
    <tr>
    <td>&nbsp;<?=$strMessenger?></td>
    <td ><INPUT TYPE="text" size="30" value="<?=hs($e_messenger)?>" readonly=1 class="i_readonly">
    </td>
    <td nowrap>&nbsp;<?=$strPhoneMobile?></td>
    <td ><INPUT TYPE="text" size="30" value="<?=hs($e_phone_mobile)?>" readonly=1 class="i_readonly">
    </td>
    </tr>
    <tr>
    <td>&nbsp;<?=$strEmail?></td>
    <td ><INPUT TYPE="text" size="30" value="<?=hs($e_email)?>" readonly=1 class="i_readonly">
    </td>
    <td nowrap>&nbsp;<?=$strPhoneHome?></td>
    <td ><INPUT TYPE="text" size="30" value="<?=hs($e_phone_home)?>" readonly=1 class="i_readonly">
    </td>
    </tr>
    <tr>
    <td valign="top">&nbsp;<?=$strAddress?></td>
    <td ><textarea cols="30" rows="5" readonly=1 class="i_readonly" style="overflow: auto;"><?=hs($e_address)?></textarea>
    </td>
    <td valign="top">&nbsp;<?=$strComment?></td>
    <td colspan=2><textarea cols="30" rows="5" readonly=1 class="i_readonly" style="overflow: auto;"><?=hs($e_comment)?></textarea>
    </td>
</tr>
</table>
<br>

</form>
