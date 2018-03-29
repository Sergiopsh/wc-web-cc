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
include_once("lang/".$_SESSION['lang'].".lang.php");
include_once("../../config.php");
include_once("../../lib/func.php");

extract(stripslashes_r($_POST));
$file=$par[0];
$ext=strtolower(substr(strrchr($file,'.'),1));
$progs=Array();
if(is_file("/usr/bin/lame")) {
    $lame="/usr/bin/lame"; array_push($progs,$lame);
    $lame_formats_in=Array('wav');
    $lame_formats_out=Array('mp3');
}
if(is_file("/usr/bin/ffmpeg")) {
    $ffmpeg="/usr/bin/ffmpeg"; array_push($progs,$ffmpeg);
    $ffmpeg_formats_in=$ffmpeg_formats_out=Array('aiff','au','gsm','mp3','ogg','vox','wav','raw','alaw','ulaw');
}
if(is_file("/usr/bin/sox")) {
    $sox="/usr/bin/sox"; array_push($progs,$sox);
    $sox_formats_in=$sox_formats_out=Array('aiff','au','gsm','ogg','vox','wav','raw','alaw','ulaw');
}
if(is_file("/usr/bin/mpg123")) {
    $mpg="/usr/bin/mpg123"; 
}
if(!$lame && !$ffmpeg && !$sox) {
    echo "Нет ни одной программы для обработки...";
    exit();//
}
$formats_out=array_merge($lame_formats_out,$ffmpeg_formats_out,$sox_formats_out);
$formats_in=array_merge($lame_formats_in,$ffmpeg_formats_in,$sox_formats_in);

if(!in_array($ext,$formats_in)) {
    echo "Нет ни одной программы для обработки данного формата...";
    exit();//
}


switch ($ext) {

    case 'wav': if($lame) { $default_prog=$lame; $default_format_out='mp3';}
		elseif($ffmpeg) { $default_prog=$ffmpeg; $default_format_out='mp3';}
		else  { $default_prog=$sox; $default_format_out='gsm';}
		break;
    case 'mp3': if($ffmpeg) { $default_prog=$ffmpeg; $default_format_out='wav';}
		elseif($ffmpeg) { $default_prog=$ffmpeg; $default_format_out='mp3';}
		else  { $default_prog=$sox; $default_format_out='gsm';}
		break;
    default:    if($sox) { $default_prog=$sox; $default_format_out='wav';}
		else $default_prog=$ffmpeg;

}
?>



<table border=0>
 <tr style="height: 1px;"><td></td></tr>
 <tr>
 <td nowrap>&nbsp;
 <? echo "$strConvertFile&nbsp;<b>".basename($file)."</b>&nbsp;$strToFormat";?>
 </td>
 <td>
 <select name="_audio_format_" id="_audio_format_">
 <? foreach($formats_out as $f) {?>
    <option value="<?=$f?>" <?if($f == $default_format_out) echo "selected";?>><?=$f?></option>
<?}?>
 </select>&nbsp;
 <?=$strWithProgramm?>&nbsp;
 <select name="_convert_programm_" id="_convert_programm_">
 <? foreach($progs as $p) {?>
    <option value="<?=$p?>" <?if($p == $default_prog) echo "selected";?>><?=basename($p)?></option>
<?}?>
 </select>
 </td>
 
 
 <td>
 <a href="#" onclick="$('upload_file_form').submit();">
 <img src="images/run.gif" align="absmiddle">
 </a>&nbsp;
 <? echo "$strToDir&nbsp;<b>$dir</b>";?>
 </td>
 </tr>
 <tr><td>
  <?=$strWriteFileFormat?>
  </td><td>
 <select name="audio_ext" id="audio_ext">
 <?foreach($audio_exts as $ext) {?>
    <option value="<?=$ext?>"><?=$ext?>
<?}?>
 </select>
 </td>
 <td align="left" nowrap colspan="3">

 </td>
 </tr>
 <tr style="height: 1px;"><td></td></tr>
</table>
<?
?>

