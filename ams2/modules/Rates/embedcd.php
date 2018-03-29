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
session_start();
if(!$_SESSION['ams_entry']) die('Not a Valid Entry');
include_once("../../config.php");
$lang=$_SESSION['lang'];
$theme=$_SESSION['theme'];
include_once("../../lib/func.php");
include_once("../../lib/Class.listtbl.php");
include_once("lang/".$lang.".lang.php");
include_once("../../modules/CodesDirectory/CodesDirectory.php");

extract(stripslashes_r($_POST));

if(empty($cd_dest)) $cd_dest="*"; else $cd_dest.='*';
if(empty($cd_code)) $cd_code="*"; else $cd_code.='*';

if (!isset($current_page)) $current_page=0;
$limit_display=15;

$cd = new CodesDirectory();

$list = $cd->getList(array($cd_dest,$cd_code),$current_page,$limit_display);

$num=$cd->num_rows;
$n_pages=num_pages($num,$limit_display);
$num_disp=count($list);

$tbl = new ListTbl();
 if (!$num) {echo "</form>"; $tbl->emptyTbl($strNoCodes); }
 $tbl->tblHead(array("","80",""),array($strNameDest,$strCodes),"100%","font-size: 11px;",0,1);
	 
    for($j=0; $j < $num_disp; $j++) {
	$tbl->tblTr($j);
	$tbl->td($list[$j][1],"","left","rate.insertDest",$list[$j][1],"",$strInsertFromCD);
	?>
	<td align="left"><a  href="javascript:void(0)" 
	onmouseover="rate.onMouseOver(this,'<?=ah($list[$j][1])?>')"
	onmouseout="clearTimeout(rate.tsc);" ><?=$list[$j][2]?>...</a></td>
	</tr>
		
	
        <?	
   }
$tbl->tblEnd($current_page,$n_pages,1,0,"rate");

?>
