<?php

// HELPER FUNCTIONS:

function fw_fop_print_errors($src, $dst, $errors) {
	echo "error copying fw_fop files:<br />'cp -rf' from src: '$src' to dst: '$dst'...details follow<br />";
	foreach ($errors as $error) {
		echo "$error<br />";
	}
}

if (! function_exists('out')) {
	function out($text) {
		echo $text."<br>";
	}
}

if (! function_exists('outn')) {
	function outn($text) {
		echo $text;
	}
}

if (! function_exists('error')) {
	function error($text) {
		echo "[ERROR] ".$text."<br>";
	}
}

if (! function_exists('fatal')) {
	function fatal($text) {
		echo "[FATAL] ".$text."<br>";
		exit(1);
	}
}

if (! function_exists('debug')) {
	function debug($text) {
		global $debug;
		
		if ($debug) echo "[DEBUG-preDB] ".$text."<br>";
	}
}

global $amp_conf;
global $asterisk_conf;

$debug = false;
$dryrun = false;

/** verison_compare that works with freePBX version numbers
 *  included here because there are some older versions of functions.inc.php that do not have
 *  it included as it was added during 2.3.0beta1
 */
if (!function_exists('version_compare_freepbx')) {
	function version_compare_freepbx($version1, $version2, $op = null) {
		$version1 = str_replace("rc","RC", strtolower($version1));
		$version2 = str_replace("rc","RC", strtolower($version2));
		if (!is_null($op)) {
			return version_compare($version1, $version2, $op);
		} else {
			return version_compare($version1, $version2);
		}
	}
}

/*
 * fw_fop install script
 */
	$bin_source = dirname(__FILE__)."/bin/*";
	$bin_dest = $amp_conf['AMPBIN'];

	$htdocs_panel_source = dirname(__FILE__)."/htdocs_panel/*";

	// There was a past bug where FOPWEBROOT was pointing to AMPWEBROOT so if that is the case then hardcode
	// and force to panel here.
	//
	$htdocs_panel_dest = $amp_conf['FOPWEBROOT'];
	if ($htdocs_panel_dest == $amp_conf['AMPWEBROOT']) {
		$htdocs_panel_dest .= "/panel";
	}

	exec("cp -rf $htdocs_panel_source $htdocs_panel_dest 2>&1",$out,$ret);
	if ($ret != 0) {
		fw_fop_print_errors($htdocs_panel_source, $htdocs_panel_dest, $out);
	}

	exec("cp -rf $bin_source $bin_dest 2>&1",$out,$ret);
	if ($ret != 0) {
		fw_fop_print_errors($htdocs_panel_source, $bin_dest, $out);
	}

?>
