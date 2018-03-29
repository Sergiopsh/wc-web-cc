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
if(!isset($u_id) && !isset($u_name)) die('Not a Valid Entry');


$user = new User($u_id,$u_name);
$cur = new Currency();

$user->get_data();
$u_id=$user->id;
$u_name=$user->user_name;
$u_first_name=$user->first_name;
$u_last_name=$user->last_name;
$is_employee=$user->is_employee;
$is_admin=$user->is_admin;
$u_comment=$user->comment;
$u_address=$user->address;
$u_phone_work=$user->phone_work;
$u_phone_mobile=$user->phone_mobile;
$u_phone_home=$user->phone_home;
$u_phone_office=$user->phone_office;
$u_department=$user->department;
$acl=$user->acl;
$u_currency=$cur->getNameByCode($user->currency);
$u_status=$user->status;
$u_lang=$languages[$user->lang];
$u_dateformat=$user->dateformat;
$u_fax=$user->fax;
$u_email=$user->email;
$u_messenger=$user->messenger;
$u_title=$user->title;
$date_entered=$user->date_entered;
$date_modified=$user->date_modified;
$created_by=$user->created_by;
$modified_by=$user->modified_by;
$u_role=$user->role_name;
if(empty($u_role)) unset($u_role);




?>
<div id="frame-module-header" nowrap>
<?=$strUser?>: <?=$u_name?>
</div>
<br>

<table width="100%" class="input-data-tbl" cellpadding=0 cellspacing=5>

    <tr>
    <td width="10%">&nbsp;<?=$strNameUser?></td>
    <td width="30%" ><INPUT TYPE="text" NAME="u_name" id="u_name" size="30" value="<?=hc($u_name)?>" readonly=1 class="i_readonly">
    </td>

    <td width="15%"><?=$strAdmin?></td>
    <td align="left" ><INPUT TYPE="checkbox" NAME="is_admin" id="is_admin" <?if($is_admin) echo "checked"?> disabled class="checkbox">
    </td>

     <?if ($_SESSION['acl']['Users']['Access']>1) {?>
     <td align=right>
     <input type="button" onclick="loadModule(0,'','Users','EditUser',$H({u_id: <?=$u_id?>}));" value="<?=$strEdit?>" class="sbutton">
     &nbsp;</td></tr>
    <?}?>

    </tr>
    <tr>
    <td nowrap>&nbsp;<?=$strFirstName?></td>
    <td ><INPUT TYPE="text" NAME="u_first_name" id="u_first_name" size="30" value="<?=hc($u_first_name)?>" readonly=1 class="i_readonly">
    </td>
    
    <td><?=$strEmployee?></td>
    <td align="left" ><INPUT TYPE="checkbox" NAME="is_employee" id="is_employee" <?if($is_employee) echo "checked"?> disabled class="checkbox">
    </td>

    </tr>
    <tr>
    
    <td nowrap>&nbsp;<?=$strLastName?></td>
    <td ><INPUT TYPE="text" NAME="u_last_name" id="u_last_name" size="30" value="<?=hc($u_last_name)?>" readonly=1 class="i_readonly">
    </td>
    <td nowrap><?=$strStatus?></td>
    <td ><INPUT TYPE="text" NAME="u_status" id="u_status"
    <?if ($u_status) echo "style='color: green;'"; else echo "style='color: red;'";
    ?>  
    size="15" value='<?if ($u_status) echo $strActive; else echo $strDisabled;?>' readonly=1 class="i_readonly">
    </td>

    </tr>

</table>
<br>

<table width="100%" class="input-data-tbl" cellpadding=0 cellspacing=5>
    <tr>
    <td width="10%" >&nbsp;<?=$strTitle?></td>
    <td width="30%" ><INPUT TYPE="text" NAME="u_title" id="u_title" size="30" value="<?=hc($u_title)?>" readonly=1 class="i_readonly">
    </td>

    <td nowrap width="10%">&nbsp;<?=$strPhoneWork?></td>
    <td><INPUT TYPE="text" NAME="u_phone_work" id="u_phone_work" size="25" value="<?=hc($u_phone_work)?>" readonly=1 class="i_readonly">
    </td>

    <td nowrap>&nbsp;<?=$strPhoneMobile?></td>
    <td ><INPUT TYPE="text" NAME="u_phone_mobile" id="u_phone_mobile" size="25" value="<?=hc($u_phone_mobile)?>" readonly=1 class="i_readonly">
    </td>
    </tr>
    
    <tr>
    <td nowrap>&nbsp;<?=$strDepartment?></td>
    <td nowrap><INPUT TYPE="text" NAME="u_department" id="u_department" size="30" value="<?=hc($u_department)?>" readonly=1 class="i_readonly">
    </td>

    <td nowrap>&nbsp;<?=$strPhoneOffice?></td>
    <td ><INPUT TYPE="text" NAME="u_phone_office" id="u_phone_office" size="25" value="<?=hc($u_phone_office)?>" readonly=1 class="i_readonly">
    </td>

    <td nowrap>&nbsp;<?=$strPhoneHome?></td>
    <td ><INPUT TYPE="text" NAME="u_phone_home" id="u_phone_home" size="25" value="<?=hc($u_phone_home)?>" readonly=1 class="i_readonly">
    </td>
    </tr>

    <tr>
    <td>&nbsp;<?=$strEmail?></td>
    <td ><INPUT TYPE="text" NAME="u_email" id="u_email" size="30" value="<?=hc($u_email)?>" readonly=1 class="i_readonly">
    </td>


    <td>&nbsp;<?=$strMessenger?></td>
    <td ><INPUT TYPE="text" NAME="u_messenger" id="u_messenger" size="25" value="<?=hc($u_messenger)?>" readonly=1 class="i_readonly">
    </td>

    <td>&nbsp;<?=$strFax?></td>
    <td ><INPUT TYPE="text" NAME="u_fax" id="u_fax" size="25" value="<?=hc($u_fax)?>" readonly=1 class="i_readonly">
    </td>
    </tr>
    
    <tr>
    <td valign="top">&nbsp;<?=$strAddress?></td>
    <td ><textarea NAME="u_address" id="u_address" cols="30" rows="5" readonly=1 class="i_readonly"><?=hc($u_address)?></textarea>
    </td>

    <td valign="top">&nbsp;<?=$strComment?></td>
    <td colspan=2><textarea NAME="u_comment" id="u_comment" cols="30" rows="5" readonly=1 class="i_readonly"><?=hc($u_comment)?></textarea>
    </td>


    </tr>
</table>

  <br>




<table width="100%" class="input-data-tbl" cellpadding=0 cellspacing=5>

    <tr>
    <td width="10%">&nbsp;<?=$strRole?></td>
    <td nowrap width="30%">
    <INPUT TYPE="text" NAME="u_role_name" id="u_role_name" size="25" value="<?=hc($u_role)?>" readonly=1 class="i_readonly">
    </td>
    <td width="10%"><?=$strCreatedBy?></td>
    <td nowrap >
    <INPUT TYPE="text" NAME="created_by" id="created_by" size="25" value="<?=$created_by?>" readonly=1 class="i_readonly">
    </td>
    </tr>
    <tr>
    <td align="left">&nbsp;<?=$strCurrency?></td>
    <td>
    <INPUT TYPE="text" NAME="u_currency" id="u_currency" size="25" value="<?=hc($u_currency)?>" readonly=1 class="i_readonly">
    </td>
    <td align=left><?=$strDateEntered?></td>
    <td nowrap >
    <INPUT TYPE="text" NAME="date_entered" id="date_entered" size="25" value="<?=dbformat_to_dt($date_entered)?>" readonly=1 class="i_readonly">
    </td>
    </tr><tr>
    <td align=left>&nbsp;<?=$strLang?></td>
    <td>
    <INPUT TYPE="text" NAME="u_lang" id="u_lang" size="25" value="<?=$u_lang?>" readonly=1 class="i_readonly">
    </td>
    <td><?=$strModifiedBy?></td>
    <td nowrap>
    <INPUT TYPE="text" NAME="modified_by" id="modified_by" size="25" value="<?=$modified_by?>" readonly=1 class="i_readonly">
    </td>
    </tr><tr>
    <td nowrap>&nbsp;<?=$strDateFormat?></td>
    <td>
    <INPUT TYPE="text" NAME="u_dateformat" id="u_dateformat" size="25" value="<?=$u_dateformat?>" readonly=1 class="i_readonly">
    </td>
    <td><?=$strDateModified?></td>
    <td nowrap>
    <INPUT TYPE="text" NAME="date_modified" id="date_modified" size="25" value="<?if($date_modified[0]) echo dbformat_to_dt($date_modified);?>" readonly=1 class="i_readonly">
    </td>

    </tr>

</table>

<br>

<?

if(empty($acl)) {
    if(!empty($u_role)) showMsg($strNotValidRole,"","","warning.gif");
    exit();
}
$role = new Role();
list($acl_names,$arr,$width_tbl,$w1,$w_td,$num_td)=$role->prepareACLTable($acl);

?>

<table border=0 width="<?=$width_tbl?>%">
<tr><td>
  <table width="100%" border=0  class="data-tbl2" cellpadding="2" cellspacing="0">
    <tr><th nowrap id="th_1" width="<?=$w1?>%" rowspan=2 align=center><?=$strModule?></th>
    <th colspan=20 id="th_2" align=center><?=$strACL?></th></tr>
    <tr>
    <?

    foreach($acl_names as $a) {
	echo "<th width=\"$w_td%\" align=center>".getStr($a)."</th>";
    }
    echo "</tr>";
    foreach ($arr as $rm) {
	if(!$acl[$rm] || (!$is_admin && in_array($rm,$admin_only_modules))) $acl[$rm]['Access']="0:Disable";
		?>
	<tr>
	<td id="td_1" align="center" <?if(in_array($rm,$admin_only_modules)) echo "style='color: red';";?>>
	<?=getStr($rm)?></td>
	<?foreach($acl_names as $a) {?>
	    <td align=center>
	    <?
	    if(!$acl[$rm][$a]) { echo "&nbsp;</td>"; continue; }
		$al=explode(":",$acl[$rm][$a]);
		if($a=="Access") {
		    switch ($al[0]) {
			case "0"; echo "<font color=red>$strDisable</font>";break;
		    	case "1"; echo "<font color=blue>$strView</font>";break;
			case "2"; echo "<font color=green>$strFull</font>";break;
		    } 
		}else echo "<font color=blue>".getStr($al[1])."</font>";
            	
	     echo "</td>";
	 }
	 echo "</tr>";
   }

  ?>
    
  </table>
 </td></tr>
 </table>
