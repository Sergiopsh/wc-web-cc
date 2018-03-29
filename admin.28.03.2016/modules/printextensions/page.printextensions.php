<?php /* $Id: page.printextensions.php 1197 2006-04-26 21:12 KerryG $ */
//Copyright (C) 2006 Kerry Garrison (kgarrison at servicepointe dot net)
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of the GNU General Public License
//as published by the Free Software Foundation; either version 2
//of the License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

$dispnum = 'printextensions'; //used for switch on config.php

//isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action='';


?>

<div class="content">
<?php
if (!$quietmode) {
	echo "<br /><a href=\"config.php?type=tool&display=printextensions&quietmode=on\" target=\"_blank\"><b>"._("Printer Friendly Page")."</b></a>\n";
}
if (!$extdisplay) {
	echo '<br><h2>'._("PBX Extension Layout").'</h2><table border="0" width="95%">';
	echo "<tr width=90%><td align=left><b>"._("Name")."</b></td><td width=\"10%\" align=\"right\"><b>"._("Extension")."</b></td></tr>";
}

global $active_modules;
$full_list = framework_check_extension_usage(true);
// get all featurecodes
$featurecodes = featurecodes_getAllFeaturesDetailed();
foreach ($full_list as $key => $value) {
	$txtdom = $active_modules[$key]['rawname'];
	if ($txtdom == 'core')
	{
		$txtdom = 'amp';
		$active_modules[$key]['name'] = 'Extensions';
	}

	if ($active_modules[$key]['rawname'] != 'featurecodeadmin')
	{
		echo "<tr colspan=\"2\" width='100%'><td><br /><strong>".sprintf("%s",dgettext($txtdom,$active_modules[$key]['name']))."</strong></td></tr>";
	}

	foreach ($value as $exten => $item) {
		$description = explode(":",$item['description'],2);
		// if from featurecodeadmin then skip as we deal with those later
		if ($active_modules[$key]['rawname'] != 'featurecodeadmin')
		    {
		    echo "<tr width=\"90%\"><td>".(trim($description[1])==''?$exten:$description[1])."</td><td width=\"10%\" align=\"right\">".$exten."</td></tr>";
		    }
		}
    }

echo "<tr colspan=\"2\" width='100%'><td><br /><strong>".sprintf("%s",dgettext($txtdom,$active_modules['featurecodeadmin']['name']))."</strong></td></tr>";
// Now, get all featurecodes. Code gracefully 'borrowed' from featurecodeadmin
foreach($featurecodes as $item) {
 $bind_domains = array();
  if (isset($bind_domains[$item['modulename']]) || (extension_loaded('gettext') && is_dir("modules/".$item['modulename']."/i18n"))) {
   if (!isset($bind_domains[$item['modulename']])) {
    $bind_domains[$item['modulename']] = true;
    bindtextdomain($item['modulename'],"modules/".$item['modulename']."/i18n");
    bind_textdomain_codeset($item['modulename'], 'utf8');
   }
}
    $moduleena = ($item['moduleenabled'] == 1 ? true : false);
    $featureena = ($item['featureenabled'] == 1 ? true : false);
    $featurecodedefault = (isset($item['defaultcode']) ? $item['defaultcode'] : '');
    $featurecodecustom = (isset($item['customcode']) ? $item['customcode'] : '');
    $thiscode = ($featurecodecustom != '') ? $featurecodecustom : $featurecodedefault;
    $thismodena = ($moduleena != '') ? $featurecodecustom : $featurecodedefault;
    $txtdom = $item['modulename'];
    // if core then get translations from amp
    if ($txtdom == 'core') $txtdom = 'amp';
    textdomain($txtdom);
    if ($featureena == true && $moduleena == true) 
	 echo "<tr width=\"90%\"><td>".sprintf(dgettext($txtdom,$item['featuredescription']))."</td><td width=\"10%\" align=\"right\">".$thiscode."</td></tr>";
};
?>
</table>
</div>
