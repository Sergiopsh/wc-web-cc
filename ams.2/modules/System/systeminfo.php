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
$lang=$_SESSION['lang'];

include_once("lang/$lang.lang.php");
include_once("../../lib/func.php");
include_once("../../lib/sysfunc.php");

$free_opt=$_POST['free_opt'];
$df_opt=$_POST['df_opt'];


if(!isset($df_opt)) $df_opt="h";

if($free_opt<>"") {
    if(!ereg("[mgot]",$free_opt)) $free_opt="";
}
if($df_opt<>"") {
    if(!ereg("[hali]",$df_opt)) $df_opt="h";
}

$out=Array();
setenvlang();
?>
<table width="100%" border=0 cellspasing=0 cellpadding=0>
    <tr>
    <td width="20">&nbsp;</td>
    <th colspan=2 align="left"><?=$strOS?></th>
    </tr>
    <tr>
    <td>&nbsp;</td><td width="20">&nbsp;</td>
    <td>
      <?
      echo execute("cat /proc/version")."&nbsp;".execute("uname -n")."&nbsp;".execute("uname -m")."&nbsp;".execute("uname -p")."&nbsp;".execute("uname -i")."&nbsp;".execute("uname -o");
      ?>
    </td>
    </tr>
    <tr>
    <td width="20">&nbsp;</td>
    <th colspan=2 align="left"><?=$strUptime?></th>
    </tr>
    <tr>
    <td>&nbsp;</th><th width="20">&nbsp;</td>
    <td><?echo execute("uptime");?> </td>
    </tr>
    <tr><td width="20">&nbsp;</td>
    <th colspan=2 align="left"><?=$strCPUInfo?></th>
    </tr>
    
    <?
    $o=explode("\n",execute("cat /proc/cpuinfo")); 
    $i=0;
    while($i<count($o)) {
	if(strstr($o[$i],"processor")) {?>
	<tr><td>&nbsp;</td><td width='20'>&nbsp;</td>
	<?
	    echo "<td>".$o[$i]."&nbsp;".$o[$i+1]."&nbsp;".$o[$i+2]."&nbsp;".$o[$i+4]."&nbsp;".$o[$i+6]."</td></tr>";
	    $i=$i+4;
	}
	$i++;
    }
    ?>
    
    <tr><td width="20">&nbsp;</td>
    <th colspan=2 align="left">
    <?=$strMemoryUsage?>:&nbsp;&nbsp;
    (free&nbsp;-
    <input style="font-weight: normal;" type="text" size="5" name="free_opt" id="free_opt" value="<?=$free_opt?>">
    )</th>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td width='20'>&nbsp;</td>
    <td align="left">
         <?if($free_opt<>"") $opt="-".$free_opt;
	 $out=explode("\n",execute("free ".$opt));
         $f=str_replace(" ","&nbsp;",$out[0]);
	 echo "<div width='100%' style='background-color: #d5d5d5;'><b>";
	  echo $f."</b></div>";
	 for ($i=1; $i<count($out);$i++) {
           $f=str_replace(" ","&nbsp;",$out[$i]);
	   echo $f."<br>";
         }?>

    </td></tr>

    <tr><td width="20">&nbsp;</td>
    <th colspan=2 align="left">
    <?=$strDiskUsage?>:&nbsp;&nbsp;
    (df&nbsp;-
    <input style="font-weight: normal;" type="text" size="5" name="df_opt" id="df_opt" value="<?=$df_opt?>">
    )</th>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td width='20'>&nbsp;</td>
    <td align="left">
         <? unset($out,$opt);
	 if($df_opt<>"") $opt="-".$df_opt;
	 $out=explode("\n",execute("df ".$opt));
         $f=explode(" ",$out[0]);
         $f=str_replace(" ","&nbsp;",$out[0]);
	 echo "<div width='100%' style='background-color: #d5d5d5;'><b>";
	 echo $f."</b></div>";
	 for ($i=1; $i<count($out);$i++) {
           $f=str_replace(" ","&nbsp;",$out[$i]);
	   echo $f."<br>";
         }?>
    </td></tr>

</table>
    



