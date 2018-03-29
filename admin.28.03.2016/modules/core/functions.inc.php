<?php

class core_conf {
	var $_sip_general    = array();
	var $_iax_general    = array();
	var $_featuregeneral = array();
	var $_featuremap     = array();
	var $_applicationmap = array();
	// return an array of filenames to write
	function get_filename() {
		global $chan_dahdi;
		
		$files = array(
			'sip_additional.conf',
			'sip_registrations.conf',
			'iax_additional.conf',
			'iax_registrations.conf',
			'sip_general_additional.conf',
			'iax_general_additional.conf',
			'features_general_additional.conf',
			'features_applicationmap_additional.conf',
			'features_featuremap_additional.conf',
		);

		if ($chan_dahdi) {
			$files[] = 	'chan_dahdi_additional.conf';
		} else {
			$files[] = 	'zapata_additional.conf';
		}

		return $files;
	}
	
	// return the output that goes in each of the files
	function generateConf($file) {
		global $version;

		switch ($file) {
			case 'sip_general_additional.conf':
				return $this->generate_sip_general_additional($version);
				break;
			case 'sip_additional.conf':
				return $this->generate_sip_additional($version);
				break;
			case 'sip_registrations.conf':
				return $this->generate_sip_registrations($version);
				break;
			case 'iax_general_additional.conf':
				return $this->generate_iax_general_additional($version);
				break;
			case 'iax_additional.conf':
				return $this->generate_iax_additional($version);
				break;
			case 'iax_registrations.conf':
				return $this->generate_iax_registrations($version);
				break;
			case 'chan_dahdi_additional.conf':
				return $this->generate_zapata_additional($version);
				break;
			case 'zapata_additional.conf':
				return $this->generate_zapata_additional($version);
				break;
			case 'features_general_additional.conf':
				return $this->generate_featuregeneral_additional($version);
				break;
			case 'features_applicationmap_additional.conf':
				return $this->generate_applicationmap_additional($version);
				break;
			case 'features_featuremap_additional.conf':
				return $this->generate_featuremap_additional($version);
				break;
		}
	}

	function addSipGeneral($key, $value) {
		$this->_sip_general[] = array('key' => $key, 'value' => $value);
	}

	function generate_sip_general_additional($ast_version) {
		$output = '';

		if (isset($this->_sip_general) && is_array($this->_sip_general)) {
			foreach ($this->_sip_general as $values) {
				$output .= $values['key']."=".$values['value']."\n";
			}
		}
		return $output;
	}

	function addIaxGeneral($key, $value) {
		$this->_iax_general[] = array('key' => $key, 'value' => $value);
	}

	function generate_iax_general_additional($ast_version) {
		$output = '';

		if (isset($this->_iax_general) && is_array($this->_iax_general)) {
			foreach ($this->_iax_general as $values) {
				$output .= $values['key']."=".$values['value']."\n";
			}
		}
		return $output;
	}

	function addFeatureGeneral($key, $value) {
		$this->_featuregeneral[] = array('key' => $key, 'value' => $value);
	}

	function generate_featuregeneral_additional($ast_version) {
		$output = '';

		if (isset($this->_featuregeneral) && is_array($this->_featuregeneral)) {
			foreach ($this->_featuregeneral as $values) {
				$output .= $values['key']."=".$values['value']."\n";
			}
		}
		return $output;
	}

	function addFeatureMap($key, $value) {
		$this->_featuremap[] = array('key' => $key, 'value' => $value);
	}

	function generate_featuremap_additional($ast_version) {
		$output = '';

		if (isset($this->_featuremap) && is_array($this->_featuremap)) {
			foreach ($this->_featuremap as $values) {
				$output .= $values['key']."=".$values['value']."\n";
			}
		}
		return $output;
	}

	function addApplicationMap($key, $value) {
		$this->_applicationmap[] = array('key' => $key, 'value' => $value);
	}

	function generate_applicationmap_additional($ast_version) {
		$output = '';

		if (isset($this->_applicationmap) && is_array($this->_applicationmap)) {
			foreach ($this->_applicationmap as $values) {
				$output .= $values['key']."=".$values['value']."\n";
			}
		}
		return $output;
	}

	function generate_sip_additional($ast_version) {
		global $db;

		$table_name = "sip";
		$additional = "";
		$output = "";

		// Asterisk 1.4 requires call-limit be set for hints to work properly
		//
		if (version_compare($ast_version, "1.4", "ge")) { 
			$call_limit = "call-limit=50\n";
			$ver12 = false;
		} else {
			$call_limit = "";
			$ver12 = true;
		}

		$sql = "SELECT keyword,data from $table_name where id=-1 and keyword <> 'account' and flags <> 1";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}
		foreach ($results as $result) {
			if ($ver12) {
				$additional .= $result['keyword']."=".$result['data']."\n";
			} else {
				$option = $result['data'];
				switch (strtolower($result['keyword'])) {
					case 'insecure':
						if ($option == 'very')
							$additional .= "insecure=port,invite\n";
						else if ($option == 'yes')
							$additional .= "insecure=port\n";
						else
							$additional .= $result['keyword']."=$option\n";
						break;
					case 'allow':
					case 'disallow':
						if ($option != '')
							$additional .= $result['keyword']."=$option\n";
						break;
					default:
						$additional .= $result['keyword']."=$option\n";
				}
			}
		}

		$sql = "SELECT data,id from $table_name where keyword='account' and flags <> 1 group by data";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}

		foreach ($results as $result) {
			$account = $result['data'];
			$id = $result['id'];
			$output .= "[$account]\n";
	
			$sql = "SELECT keyword,data from $table_name where id='$id' and keyword <> 'account' and flags <> 1 order by flags, keyword DESC";
			$results2_pre = $db->getAll($sql, DB_FETCHMODE_ASSOC);
			if(DB::IsError($results2_pre)) {
   			die($results2->getMessage());
			}

			// Move all 'disallow=all' and 'deny' to the top to avoid errors
			//
			$results2 = array();
			foreach ($results2_pre as $element) {
				if (strtolower(trim($element['keyword'])) != 'secret') {
					$options = explode("&", $element['data']);
					foreach ($options as $option) {
						if (($element['keyword'] == 'disallow' && $option == 'all') | ($element['keyword'] == 'deny')) {
							array_unshift($results2,array('keyword'=>$element['keyword'],'data'=>$option));
						} else {
							$results2[] = array('keyword'=>$element['keyword'],'data'=>$option);
						}
					}
				} else {
					$results2[] = array('keyword'=>$element['keyword'],'data'=>$element['data']);
				}
			}
			unset($results2_pre);

			$context='';
			foreach ($results2 as $result2) {
				$option = strtolower($result2['data']);
				if ($ver12) {
					switch (strtolower($result2['keyword'])) {
						case 'context':
							$context = $option;
							//fall-through
						default:
							$output .= $result2['keyword']."=".$result2['data']."\n";
					}
				} else {
					switch (strtolower($result2['keyword'])) {
						case 'insecure':
							if ($option == 'very')
								$output .= "insecure=port,invite\n";
							else if ($option == 'yes')
								$output .= "insecure=port\n";
							else
								$output .= $result2['keyword']."=".$result2['data']."\n";
							break;
						case 'allow':
						case 'disallow':
							if ($option != '')
								$output .= $result2['keyword']."=".$result2['data']."\n";
							break;
						case 'record_in':
						case 'record_out':
							break;
						case 'context':
							$context = $result2['data'];
							//fall-through
						default:
							$output .= $result2['keyword']."=".$result2['data']."\n";
					}
				}
			}
			if ($call_limit && (substr($id,0,4) != "9999" | $id < 99990)) {

				$output .= $call_limit;
			}
			$output .= $additional."\n";
		}
		return $output;
	}

	function generate_sip_registrations($ast_version) {
		global $db;

		$table_name = "sip";
		$output = "";

		// items with id like 9999999% get put in registrations file
		//
		$sql = "SELECT keyword,data from $table_name where id LIKE '9999999%' and keyword <> 'account' and flags <> 1";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}

		foreach ($results as $result) {
			$output .= $result['keyword']."=".$result['data']."\n";
		}

		return $output;
	}

	function generate_iax_additional($ast_version) {
		global $db;

		$table_name = "iax";
		$additional = "";
		$output = "";

		$ver12 = version_compare($ast_version, '1.4', 'lt');

		$sql = "SELECT keyword,data from $table_name where id=-1 and keyword <> 'account' and flags <> 1";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}
		foreach ($results as $result) {
			if ($ver12) {
				$additional .= $result['keyword']."=".$result['data']."\n";
			} else {
				$option = $result['data'];
				switch ($result['keyword']) {
					case 'notransfer':
						if (strtolower($option) == 'yes') {
							$additional .= "transfer=no\n";
						} else if (strtolower($option) == 'no') {
							$additional .= "transfer=yes\n";
						} else if (strtolower($option) == 'mediaonly') {
							$additional .= "transfer=mediaonly\n";
						} else {
							$additional .= $result['keyword']."=$option\n";
						}
						break;
					case 'allow':
					case 'disallow':
						if ($option != '')
							$additional .= $result['keyword']."=$option\n";
						break;
					default:
						$additional .= $result['keyword']."=$option\n";
				}
			}
		}

		$sql = "SELECT data,id from $table_name where keyword='account' and flags <> 1 group by data";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}
		
		foreach ($results as $result) {
			$account = $result['data'];
			$id = $result['id'];
			$output .= "[$account]\n";
	
			$sql = "SELECT keyword,data from $table_name where id='$id' and keyword <> 'account' and flags <> 1 order by flags, keyword DESC";
			$results2_pre = $db->getAll($sql, DB_FETCHMODE_ASSOC);
			if(DB::IsError($results2)) {
   			die($results2_pre->getMessage());
			}	

			// Move all 'disallow=all' and deny to the top to avoid errors
			//
			$results2 = array();
			foreach ($results2_pre as $element) {
				if (strtolower(trim($element['keyword'])) != 'secret') {
					$options = explode("&", $element['data']);
					foreach ($options as $option) {
						if (($element['keyword'] == 'disallow' && $option == 'all') | ($element['keyword'] == 'deny')) {
							array_unshift($results2,array('keyword'=>$element['keyword'],'data'=>$option));
						} else {
							$results2[] = array('keyword'=>$element['keyword'],'data'=>$option);
						}
					}
				} else {
					$results2[] = array('keyword'=>$element['keyword'],'data'=>$element['data']);
				}
			}
			unset($results2_pre);

			$context='';
			foreach ($results2 as $result2) {
				$option = strtolower($result2['data']);
				if ($ver12) {
					switch (strtolower($result2['keyword'])) {
						case 'context':
							$context = $result2['data'];
							//fall-through
						default:
							$output .= $result2['keyword']."=".$result2['data']."\n";
					}
				} else {
					switch ($result2['keyword']) {
						case 'notransfer':
							if (strtolower($option) == 'yes') {
								$output .= "transfer=no\n";
							} else if (strtolower($option) == 'no') {
								$output .= "transfer=yes\n";
							} else if (strtolower($option) == 'mediaonly') {
								$output .= "transfer=mediaonly\n";
							} else {
								$output .= $result2['keyword']."=".$result2['data']."\n";
							}
							break;
						case 'allow':
						case 'disallow':
							if ($option != '')
								$output .= $result2['keyword']."=".$result2['data']."\n";
							break;
						case 'record_in':
						case 'record_out':
							break;
						case 'context':
							$context = $option;
							//fall-through
						default:
							$output .= $result2['keyword']."=".$result2['data']."\n";
					}
				}
			}
			switch (substr($id,0,8)) {
				case 'tr-peer-':
					if ($context == '') {
						$output .= "context=from-trunk-iax2-$account\n";
					}
					break;
				case 'tr-user-':
					if ($context == '') {
						$tn = substr($id, 8);
						// this is a 'user' trunk, we need to get the name of the corresponding 'peer'
						// trunk so we can set the context appropriately for the group count
						//
						$td = core_trunks_getDetails($tn);
						if (isset($td['channelid'])) {
							$output .= "context=from-trunk-iax2-".$td['channelid']."\n";
						}
					}
					break;
				default:
					if ($call_limit) {
						$output .= $call_limit;
					}
			}
			$output .= $additional."\n";
		}
		return $output;
	}

	function generate_iax_registrations($ast_version) {
		global $db;

		$table_name = "iax";
		$output = "";

		// items with id like 9999999% get put in the registration file
		//
		$sql = "SELECT keyword,data from $table_name where id LIKE '9999999%' and keyword <> 'account' and flags <> 1";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}

		foreach ($results as $result) {
			$output .= $result['keyword']."=".$result['data']."\n";
		}

		return $output;
	}

	function generate_zapata_additional($ast_version) {
		global $db;

		$table_name = "zap";

		$additional = "";
		$output = '';

		$sql = "SELECT keyword,data from $table_name where id=-1 and keyword <> 'account' and flags <> 1";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}
		foreach ($results as $result) {
			$additional .= $result['keyword']."=".$result['data']."\n";
		}

		$sql = "SELECT data,id from $table_name where keyword='account' and flags <> 1 group by data";
		$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		if(DB::IsError($results)) {
   		die($results->getMessage());
		}

		foreach ($results as $result) {
			$account = $result['data'];
			$id = $result['id'];
			$output .= ";;;;;;[$account]\n";
	
			$sql = "SELECT keyword,data from $table_name where id=$id and keyword <> 'account' and flags <> 1 order by keyword DESC";
			$results2 = $db->getAll($sql, DB_FETCHMODE_ASSOC);
			if(DB::IsError($results2)) {
   			die($results2->getMessage());
			}
			$zapchannel="";
			foreach ($results2 as $result2) {
				switch ($result2['keyword']) {
					case 'channel':
						$zapchannel = $result2['data'];
						break;

					// These are not zapata.conf variables so keep out of file
					case 'record_out':
					case 'record_in':
					case 'dial':
						break;
					default:
						$output .= $result2['keyword']."=".$result2['data']."\n";
				}
			}
			$output .= "channel=>$zapchannel\n";
			$output .= $additional."\n";
		}
		return $output;
	}
}

// The destinations this module provides
// returns a associative arrays with keys 'destination' and 'description'
function core_destinations() {
	//static destinations
	$extens = array();
	$category = _("Terminate Call");
	$extens[] = array('destination' => 'app-blackhole,hangup,1', 'description' => _("Hangup"), 'category' => $category);
	$extens[] = array('destination' => 'app-blackhole,congestion,1', 'description' => _("Congestion"), 'category' => $category);
	$extens[] = array('destination' => 'app-blackhole,busy,1', 'description' => _("Busy"), 'category' => $category);
	$extens[] = array('destination' => 'app-blackhole,zapateller,1', 'description' => _("Play SIT Tone (Zapateller)"), 'category' => $category);
	$extens[] = array('destination' => 'app-blackhole,musiconhold,1', 'description' => _("Put caller on hold forever"), 'category' => $category);
	$extens[] = array('destination' => 'app-blackhole,ring,1', 'description' => _("Play ringtones to caller until they hangup"), 'category' => $category);
	
	//get the list of meetmes
	$results = core_users_list();
	
	if (isset($results) && function_exists('voicemail_getVoicemail')) {
		//get voicemail
		$uservm = voicemail_getVoicemail();
		$vmcontexts = array_keys($uservm);
		foreach ($results as $thisext) {
			$extnum = $thisext[0];
			// search vm contexts for this extensions mailbox
			foreach ($vmcontexts as $vmcontext) {
				if(isset($uservm[$vmcontext][$extnum])){
					//$vmname = $uservm[$vmcontext][$extnum]['name'];
					//$vmboxes[$extnum] = array($extnum, '"' . $vmname . '" <' . $extnum . '>');
					$vmboxes[$extnum] = true;
				}
			}
		}
	}
	
	// return an associative array with destination and description
	// core provides both users and voicemail boxes as destinations
	if (isset($results)) {
		foreach($results as $result) {
			$extens[] = array('destination' => 'from-did-direct,'.$result['0'].',1', 'description' => ' <'.$result['0'].'> '.$result['1'], 'category' => 'Extensions');
			if(isset($vmboxes[$result['0']])) {
				$extens[] = array('destination' => 'ext-local,vmb'.$result['0'].',1', 'description' => '<'.$result[0].'> '.$result[1].' (busy)', 'category' => 'Voicemail');
				$extens[] = array('destination' => 'ext-local,vmu'.$result['0'].',1', 'description' => '<'.$result[0].'> '.$result[1].' (unavail)', 'category' => 'Voicemail');
				$extens[] = array('destination' => 'ext-local,vms'.$result['0'].',1', 'description' => '<'.$result[0].'> '.$result[1].' (no-msg)', 'category' => 'Voicemail');
			}
		}
	}
	
	if (isset($extens))
		return $extens;
	else
		return null;
}

function core_getdest($exten) {
	$dests[] = 'from-did-direct,'.$exten.',1';
	if (!function_exists('voicemail_mailbox_get')) {
		return $dests;
	}
	$box = voicemail_mailbox_get($exten);
	if ($box == null) {
		return $dests;
	}
	$dests[] = 'ext-local,vmb'.$exten.',1';
	$dests[] = 'ext-local,vmu'.$exten.',1';
	$dests[] = 'ext-local,vms'.$exten.',1';

	return $dests;
}

function core_getdestinfo($dest) {
  global $amp_conf;
	global $active_modules;

	// Check for Extension Number Destinations
	//
	if (substr(trim($dest),0,16) == 'from-did-direct,') {
		$exten = explode(',',$dest);
		$exten = $exten[1];
		$thisexten = core_users_get($exten);
		if (empty($thisexten)) {
			return array();
		} else {
			//$type = isset($active_modules['announcement']['type'])?$active_modules['announcement']['type']:'setup';
			$display = ($amp_conf['AMPEXTENSIONS'] == "deviceanduser")?'users':'extensions';
			return array('description' => sprintf(_("User Extension %s: %s"),$exten,$thisexten['name']),
			             'edit_url' => "config.php?type=setup&display=$display&extdisplay=".urlencode($exten)."&skip=0",
								  );
		}

	// Check for voicemail box destinations
	//
	} else if (substr(trim($dest),0,12) == 'ext-local,vm') {
		$exten = explode(',',$dest);
		$exten = substr($exten[1],3);
		if (!function_exists('voicemail_mailbox_get')) {
			return array();
		}
		$thisexten = core_users_get($exten);
		if (empty($thisexten)) {
			return array();
		}
		$box = voicemail_mailbox_get($exten);
		if ($box == null) {
			return array();
		}
		$display = ($amp_conf['AMPEXTENSIONS'] == "deviceanduser")?'users':'extensions';
		return array('description' => 'User Extension '.$exten.': '.$thisexten['name'],
		             'edit_url' => "config.php?type=setup&display=$display&extdisplay=".urlencode($exten)."&skip=0",
							  );

	// Check for blackhole Termination Destinations
	//
	} else if (substr(trim($dest),0,14) == 'app-blackhole,') {
		$exten = explode(',',$dest);
		$exten = $exten[1];

		switch ($exten) {
			case 'hangup': 
				$description = 'Hangup';
				break;
			case 'congestion': 
				$description = 'Congestion';
				break;
			case 'busy': 
				$description = 'Busy';
				break;
			case 'zapateller': 
				$description = 'Play SIT Tone (Zapateller)';
				break;
			case 'musiconhold': 
				$description = 'Put caller on hold forever';
				break;
			case 'ring': 
				$description = 'Play ringtones to caller';
				break;
			default:
				$description = false;
		}
		if ($description) {
			return array('description' => 'Core: '.$description,
		             	 'edit_url' => false,
							  	 );
		} else {
			return array();
		}

	// None of the above, so not one of ours
	//
	} else {
		return false;
	}
}
/* 	Generates dialplan for "core" components (extensions & inbound routing)
	We call this with retrieve_conf
*/
function core_get_config($engine) {
	global $ext;  // is this the best way to pass this?
	global $version;  // this is not the best way to pass this, this should be passetd together with $engine
	global $engineinfo;
	global $amp_conf;
	global $core_conf;
	global $chan_dahdi;
	global $astman;;

	$modulename = "core";
	
	switch($engine) {
		case "asterisk":

			// Now add to sip_general_addtional.conf
			//
			if (isset($core_conf) && is_a($core_conf, "core_conf")) {
				$core_conf->addSipGeneral('disallow','all');
				$core_conf->addSipGeneral('allow','ulaw');
				$core_conf->addSipGeneral('allow','alaw');
				$core_conf->addSipGeneral('context','from-sip-external');
				$core_conf->addSipGeneral('callerid','Unknown');
				$core_conf->addSipGeneral('notifyringing','yes');
				if (version_compare($version, '1.4', 'ge')) { 
					$core_conf->addSipGeneral('notifyhold','yes');
					$core_conf->addSipGeneral('limitonpeers','yes');
					$core_conf->addSipGeneral('tos_sip','cs3');    // Recommended setting from doc/ip-tos.txt
					$core_conf->addSipGeneral('tos_audio','ef');   // Recommended setting from doc/ip-tos.txt
					$core_conf->addSipGeneral('tos_video','af41'); // Recommended setting from doc/ip-tos.txt
					$core_conf->addSipGeneral('alwaysauthreject','yes');
				} else {
					$core_conf->addSipGeneral('tos','0x68'); // This really doesn't do anything with astersk not running as root
				}
				$core_conf->addIaxGeneral('disallow','all');
				$core_conf->addIaxGeneral('allow','ulaw');
				$core_conf->addIaxGeneral('allow','alaw');
				$core_conf->addIaxGeneral('allow','gsm');
				$core_conf->addIaxGeneral('mailboxdetail','yes');
				if (version_compare($version, '1.4', 'ge')) {
					$core_conf->addIaxGeneral('tos','ef'); // Recommended setting from doc/ip-tos.txt
				}

				$fcc = new featurecode($modulename, 'blindxfer');
				$code = $fcc->getCodeActive();
				unset($fcc);
				if ($code != '') {
					$core_conf->addFeatureMap('blindxfer',$code);
				}

				$fcc = new featurecode($modulename, 'atxfer');
				$code = $fcc->getCodeActive();
				unset($fcc);
				if ($code != '') {
					$core_conf->addFeatureMap('atxfer',$code);
				}

				$fcc = new featurecode($modulename, 'automon');
				$code = $fcc->getCodeActive();
				unset($fcc);
				if ($code != '') {
					$core_conf->addFeatureMap('automon',$code);
				}

				$fcc = new featurecode($modulename, 'disconnect');
				$code = $fcc->getCodeActive();
				unset($fcc);
				if ($code != '') {
					$core_conf->addFeatureMap('disconnect',$code);
				}

				$fcc = new featurecode($modulename, 'pickupexten');
				$code = $fcc->getCodeActive();
				unset($fcc);
				if ($code != '') {
					$core_conf->addFeatureGeneral('pickupexten',$code);
				}
			}

			// FeatureCodes
			$fcc = new featurecode($modulename, 'userlogon');
			$fc_userlogon = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'userlogoff');
			$fc_userlogoff = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'zapbarge');
			$fc_zapbarge = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'chanspy');
			$fc_chanspy = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'simu_pstn');
			$fc_simu_pstn = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'simu_fax');
			$fc_simu_fax = $fcc->getCodeActive();
			unset($fcc);

			$fcc = new featurecode($modulename, 'pickup');
			$fc_pickup = $fcc->getCodeActive();
			unset($fcc);

			// Log on / off -- all in one context
			if ($fc_userlogoff != '' || $fc_userlogon != '') {
				$ext->addInclude('from-internal-additional', 'app-userlogonoff'); // Add the include from from-internal
				
				if ($fc_userlogoff != '') {
					$ext->add('app-userlogonoff', $fc_userlogoff, '', new ext_macro('user-logoff'));
					$ext->add('app-userlogonoff', $fc_userlogoff, '', new ext_hangup(''));
				}
	
				if ($fc_userlogon != '') {
					$ext->add('app-userlogonoff', $fc_userlogon, '', new ext_macro('user-logon'));
					$ext->add('app-userlogonoff', $fc_userlogon, '', new ext_hangup(''));
					
					$clen = strlen($fc_userlogon);
					$fc_userlogon = "_$fc_userlogon.";
					$ext->add('app-userlogonoff', $fc_userlogon, '', new ext_macro('user-logon,${EXTEN:'.$clen.'}'));
					$ext->add('app-userlogonoff', $fc_userlogon, '', new ext_hangup(''));
				}
			}

			// Call pickup using app_pickup - Note that '**xtn' is hard-coded into the GXPs and SNOMs as a number to dial
			// when a user pushes a flashing BLF. 
			//
			// We need to add ringgoups to this so that if an extension is part of a ringgroup, we can try to pickup that
			// extension by trying the ringgoup which is what the pickup application is going to respond to.
			//
			// NOTICE: this may be confusing, we check if this is a BRI build of Asterisk and use dpickup instead of pickup
			//         if it is. So we simply assign the varaible $ext_pickup which one it is, and use that variable when
			//         creating all the extnesions below. So those are "$ext_pickup" on purpose!
			//
			if ($fc_pickup != '') {
				$ext->addInclude('from-internal-additional', 'app-pickup');
				$fclen = strlen($fc_pickup);
				$ext_pickup = (strstr($engineinfo['raw'], 'BRI')) ? 'ext_dpickup' : 'ext_pickup';

				$fcc = new featurecode('paging', 'intercom-prefix');
				$intercom_code = $fcc->getCodeActive();
				unset($fcc);


				$ext->add('app-pickup', "_$fc_pickup.", '', new ext_NoOp('Attempt to Pickup ${EXTEN:'.$fclen.'} by ${CALLERID(num)}'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('${EXTEN:'.$fclen.'}'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('${EXTEN:'.$fclen.'}@ext-local'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('${EXTEN:'.$fclen.'}@from-internal'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('${EXTEN:'.$fclen.'}@from-internal-xfer'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('${EXTEN:'.$fclen.'}@from-did-direct'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('FMPR-${EXTEN:'.$fclen.'}'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('LC-${EXTEN:'.$fclen.'}@from-internal'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('LC-${EXTEN:'.$fclen.'}@from-internal-xfer'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('FMPR-${EXTEN:'.$fclen.'}@from-internal'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('FMPR-${EXTEN:'.$fclen.'}@from-internal-xfer'));
				$ext->add('app-pickup', "_$fc_pickup.", '', new $ext_pickup('FMPR-${EXTEN:'.$fclen.'}@from-did-direct'));
				if ($intercom_code != '') {
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('${EXTEN:'.strlen($fc_pickup.$intercom_code).'}'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-internal'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-internal-xfer'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-did-direct'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('FMPR-${EXTEN:'.strlen($fc_pickup.$intercom_code).'}'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('FMPR-${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-internal'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('FMPR-${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-internal-xfer'));
					$ext->add('app-pickup', "_{$fc_pickup}{$intercom_code}.", '', new $ext_pickup('FMPR-${EXTEN:'.strlen($fc_pickup.$intercom_code).'}@from-did-direct'));
				}
				$ext->add('app-pickup', "_$fc_pickup.", '', new ext_hangup(''));
				// In order to do call pickup in ringgroups, we will need to try the ringgoup number
				// when doing call pickup for that ringgoup so we must see who is a member of what ringgroup
				// and then generate the dialplan
				//
				$rg_members = array();
				if (function_exists('ringgroups_list')) {
					$rg_list = ringgroups_list(true);
					foreach ($rg_list as $item) {
						$thisgrp = ringgroups_get($item['grpnum']);
						$grpliststr = $thisgrp['grplist'];
						$grplist = explode("-", $grpliststr);
						foreach ($grplist as $exten) {
							if (strpos($exten,"#") === false) {
								$rg_members[$exten][] = $item['grpnum'];
							}
						}
					}
				}
				// Now we have a hash of extensions and what ringgoups they are members of
				// so we need to generate the callpickup dialplan for these specific extensions
				// to try the ringgoup.
				foreach ($rg_members as $exten => $grps) {
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($exten));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($exten.'@ext-local'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($exten.'@from-internal'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($exten.'@from-internal-xfer'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($exten.'@from-did-direct'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('LC-'.$exten.'@from-internal'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('LC-'.$exten.'@from-internal-xfer'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('FMPR-'.$exten));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('FMPR-'.$exten.'@from-internal'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('FMPR-'.$exten.'@from-internal-xfer'));
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup('FMPR-'.$exten.'@from-did-direct'));
					foreach ($grps as $grp) {
						$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($grp.'@from-internal'));
						$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($grp.'@from-internal-xfer'));
						$ext->add('app-pickup', "$fc_pickup".$exten, '', new $ext_pickup($grp.'@ext-group'));
					}
					$ext->add('app-pickup', "$fc_pickup".$exten, '', new ext_hangup(''));
				}
			}
			
			
			// zap barge
			if ($fc_zapbarge != '') {
				$ext->addInclude('from-internal-additional', 'app-zapbarge'); // Add the include from from-internal
				
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_macro('user-callerid'));
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_setvar('GROUP()','${CALLERID(number)}'));
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_answer(''));
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_wait(1));
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_zapbarge(''));
				$ext->add('app-zapbarge', $fc_zapbarge, '', new ext_hangup(''));
			}

			// chan spy
			if ($fc_chanspy != '') {
				$ext->addInclude('from-internal-additional', 'app-chanspy'); // Add the include from from-internal
				$ext->add('app-chanspy', $fc_chanspy, '', new ext_macro('user-callerid'));
				$ext->add('app-chanspy', $fc_chanspy, '', new ext_answer(''));
				$ext->add('app-chanspy', $fc_chanspy, '', new ext_wait(1));
				$ext->add('app-chanspy', $fc_chanspy, '', new ext_chanspy(''));
				$ext->add('app-chanspy', $fc_chanspy, '', new ext_hangup(''));
			}
			
			// Simulate options (ext-test)
			if ($fc_simu_pstn != '' || $fc_simu_fax != '') {
				$ext->addInclude('from-internal-additional', 'ext-test'); // Add the include from from-internal
				
				if ($fc_simu_pstn != '') {
					if (ctype_digit($fc_simu_pstn)) {
						$ext->add('ext-test', $fc_simu_pstn, '', new ext_goto('1', '${EXTEN}', 'from-pstn'));
					} else {
						$ext->add('ext-test', $fc_simu_pstn, '', new ext_goto('1', 's', 'from-pstn'));
					}
				}

				if ($fc_simu_fax != '') {
					$ext->add('ext-test', $fc_simu_fax, '', new ext_goto('1', 'in_fax', 'ext-fax'));
				}

				$ext->add('ext-test', 'h', '', new ext_macro('hangupcall'));
			}
			
			/* Always have Fax detection in ext-did, no matter what */

			$ext->addInclude('ext-did', 'ext-did-0001'); // Add the include from from-internal
			$ext->addInclude('ext-did', 'ext-did-0002'); // Add the include from from-internal
			$ext->add('ext-did-0001', 'fax', '', new ext_goto('1','in_fax','ext-fax'));
			$ext->add('ext-did-0002', 'fax', '', new ext_goto('1','in_fax','ext-fax'));
			$ext->add('ext-did', 'fax', '', new ext_goto('1','in_fax','ext-fax'));

			/* inbound routing extensions */
			$didlist = core_did_list();
			if(is_array($didlist)){
				$catchall = false;
				$catchall_context='ext-did-catchall';
				foreach($didlist as $item) {
					if (trim($item['destination']) == '') {
						continue;
					}
					$exten = trim($item['extension']);
					$cidnum = trim($item['cidnum']);

					// If the user put in just a cid number for routing, we add _. pattern to catch
					// all DIDs with that CID number. Asterisk will complain about _. being dangerous
					// but we don't want to limit this to just numberic as someone may be trying to
					// route a non-numeric did
					//
					$cidroute = false;
					if ($cidnum != '' && $exten == '') {
						$exten = '_.';
						$pricid = ($item['pricid']) ? true:false;
						$cidroute = true;
					} else if (($cidnum != '' && $exten != '') || ($cidnum == '' && $exten == '')) {
						$pricid = true;
					} else {
						$pricid = false;
					}
					$context = ($pricid) ? "ext-did-0001":"ext-did-0002";

					$exten = (($exten == "")?"s":$exten);
					$exten = $exten.(($cidnum == "")?"":"/".$cidnum); //if a CID num is defined, add it

					if ($cidroute) {
						$ext->add($context, $exten, '', new ext_setvar('__FROM_DID','${EXTEN}'));
						$ext->add($context, $exten, '', new ext_goto('1','s'));
						$exten = "s/$cidnum";
						$ext->add($context, $exten, '', new ext_execif('$["${FROM_DID}" = ""]','Set','__FROM_DID=${EXTEN}'));
					} else {
						$ext->add($context, $exten, '', new ext_setvar('__FROM_DID','${EXTEN}'));
					}
					// always set callerID name
					$ext->add($context, $exten, '', new ext_execif('$[ "${CALLERID(name)}" = "" ] ','Set','CALLERID(name)=${CALLERID(num)}'));

					if (!empty($item['mohclass']) && trim($item['mohclass']) != 'default') {
						$ext->add($context, $exten, '', new ext_setmusiconhold($item['mohclass']));
						$ext->add($context, $exten, '', new ext_setvar('__MOHCLASS',$item['mohclass']));
					}

					// If we require RINGING, signal it as soon as we enter.
					if ($item['ringing'] === "CHECKED") {
						$ext->add($context, $exten, '', new ext_ringing(''));
					}
					if ($item['delay_answer']) {
						$ext->add($context, $exten, '', new ext_wait($item['delay_answer']));
					}

					if ($exten == "s") {  
						//if the exten is s, then also make a catchall for undefined DIDs
						$catchaccount = "_.".(empty($cidnum)?"":"/".$cidnum);
						if ($catchaccount =="_." && ! $catchall) {
							$catchall = true;
							$ext->add($catchall_context, $catchaccount, '', new ext_NoOp('Catch-All DID Match - Found ${EXTEN} - You probably want a DID for this.'));
							$ext->add($catchall_context, $catchaccount, '', new ext_goto('1','s','ext-did'));
						}
					}
					
					if ($item['faxexten'] != "default") {
						$ext->add($context, $exten, '', new ext_setvar('FAX_RX',$item['faxexten']));
					}
					if (!empty($item['faxemail'])) {
						$ext->add($context, $exten, '', new ext_setvar('FAX_RX_EMAIL',$item['faxemail']));
					}
					if ($item['answer'] == "1") {
						$ext->add($context, $exten, '', new ext_answer(''));
						$ext->add($context, $exten, '', new ext_wait($item['wait']));
					}
					if ($item['answer'] == "2") { // NVFaxDetect
						$ext->add($context, $exten, '', new ext_answer(''));
						$ext->add($context, $exten, '', new ext_playtones('ring'));
						$ext->add($context, $exten, '', new ext_nvfaxdetect($item['wait']."|t"));
					}
					if ($item['privacyman'] == "1") {
						$ext->add($context, $exten, '', new ext_macro('privacy-mgr'));
					} else {
						// if privacymanager is used, this is not necessary as it will not let blocked/anonymous calls through
						// otherwise, we need to save the caller presence to set it properly if we forward the call back out the pbx
						// note - the indirect table could go away as of 1.4.20 where it is fixed so that SetCallerPres can take
						// the raw format.
						//
						if (version_compare($version, "1.6", "lt")) { 
							$ext->add($context, $exten, '', new ext_setvar('__CALLINGPRES_SV','${CALLINGPRES_${CALLINGPRES}}'));
						} else {
							$ext->add($context, $exten, '', new ext_setvar('__CALLINGPRES_SV','${CALLERPRES()}'));
						}
						$ext->add($context, $exten, '', new ext_setcallerpres('allowed_not_screened'));
					}
					if (!empty($item['alertinfo'])) {
						$ext->add($context, $exten, '', new ext_setvar("__ALERT_INFO", str_replace(';', '\;', $item['alertinfo'])));
					}
					// Add CID prefix, no need to do checks for existing pre-pends, this is an incoming did so this should
					// be the first time the CID is manipulated. We set _RGPREFIX which is the same used throughout the different
					// modules.
					//
					// TODO: If/When RGPREFIX is added to trunks, then see code in ringgroups to strip prefix if added here.
					//
					// TODO: core FreePBX documentation about this standard. (and probably rename from RGPREFIX to CIDPREFIX)
					//
					if (!empty($item['grppre'])) {
						$ext->add($context, $exten, '', new ext_setvar('_RGPREFIX', $item['grppre']));
						$ext->add($context, $exten, '', new ext_setvar('CALLERID(name)','${RGPREFIX}${CALLERID(name)}'));
					}
					
					//the goto destination
					// destination field in 'incoming' database is backwards from what ext_goto expects
					$goto_context = strtok($item['destination'],',');
					$goto_exten = strtok(',');
					$goto_pri = strtok(',');
					$ext->add($context, $exten, '', new ext_goto($goto_pri,$goto_exten,$goto_context));
					
				}
				// If there's not a catchall, make one with an error message
				if (!$catchall) {
					$ext->add($catchall_context, 's', '', new ext_noop("No DID or CID Match"));
					$ext->add($catchall_context, 's', 'a2', new ext_answer(''));
					$ext->add($catchall_context, 's', '', new ext_wait('2'));
					$ext->add($catchall_context, 's', '', new ext_playback('ss-noservice'));
					$ext->add($catchall_context, 's', '', new ext_sayalpha('${FROM_DID}'));
					$ext->add($catchall_context, 's', '', new ext_hangup(''));
					$ext->add($catchall_context, '_.', '', new ext_setvar('__FROM_DID', '${EXTEN}'));
					$ext->add($catchall_context, '_.', '', new ext_noop('Received an unknown call with DID set to ${EXTEN}'));
					$ext->add($catchall_context, '_.', '', new ext_goto('a2','s'));
					$ext->add($catchall_context, 'h', '', new ext_hangup(''));
				}
					
			}

			// Now create macro-from-zaptel-nnn for each defined channel to route it to the DID routing
			// Send it to from-trunk so it is handled as other dids would be handled.
			//
			foreach (core_zapchandids_list() as $row) {
				$channel = $row['channel'];
				$did     = $row['did'];

				$zap_context = "macro-from-zaptel-{$channel}";
				$ext->add($zap_context, 's', '', new ext_noop('Entering '.$zap_context.' with DID = ${DID} and setting to: '.$did));
				$ext->add($zap_context, 's', '', new ext_setvar('__FROM_DID',$did));
				$ext->add($zap_context, 's', '', new ext_goto('1',$did,'from-trunk'));
			}

			/* user extensions */
			$ext->addInclude('from-internal-additional','ext-local');

			// If running in Dynamic mode, this will insert the hints through an Asterisk #exec call.
			// which require "execincludes=yes" to be set in the [options] section of asterisk.conf
			//

			$fcc = new featurecode('paging', 'intercom-prefix');
			$intercom_code = $fcc->getCodeActive();
			unset($fcc);

			$intercom_code = ($intercom_code == '') ? 'nointercom' : $intercom_code;

			// Pass the code so agi scripts like user_login_logout know to generate hints
			//
			$ext->addGlobal('INTERCOMCODE',$intercom_code);

			if ($amp_conf['DYNAMICHINTS']) {
				if ($amp_conf['USEDEVSTATE'] && function_exists('donotdisturb_get_config')) {
					$add_dnd = ' dnd';
				} else {
					$add_dnd = '';
				}
				$ext->addExec('ext-local',$amp_conf['AMPBIN'].'/generate_hints.php '.$intercom_code.$add_dnd);
			}
			$userlist = core_users_list();
			if (is_array($userlist)) {
				foreach($userlist as $item) {
					$exten = core_users_get($item[0]);
					$vm = ((($exten['voicemail'] == "novm") || ($exten['voicemail'] == "disabled") || ($exten['voicemail'] == "")) ? "novm" : $exten['extension']);

					if (isset($exten['ringtimer']) && $exten['ringtimer'] != 0)
						$ext->add('ext-local', $exten['extension'], '', new ext_setvar('__RINGTIMER',$exten['ringtimer']));
					
					$ext->add('ext-local', $exten['extension'], '', new ext_macro('exten-vm',$vm.",".$exten['extension']));
					
					if($vm != "novm") {
						$ext->add('ext-local', $exten['extension'], '', new ext_goto('1','vmret'));
						$ext->add('ext-local', '${VM_PREFIX}'.$exten['extension'], '', new ext_macro('vm',$vm.',DIRECTDIAL,${IVR_RETVM}'));
						$ext->add('ext-local', '${VM_PREFIX}'.$exten['extension'], '', new ext_goto('1','vmret'));
						$ext->add('ext-local', 'vmb'.$exten['extension'], '', new ext_macro('vm',$vm.',BUSY,${IVR_RETVM}'));
						$ext->add('ext-local', 'vmb'.$exten['extension'], '', new ext_goto('1','vmret'));
						$ext->add('ext-local', 'vmu'.$exten['extension'], '', new ext_macro('vm',$vm.',NOANSWER,${IVR_RETVM}'));
						$ext->add('ext-local', 'vmu'.$exten['extension'], '', new ext_goto('1','vmret'));
						$ext->add('ext-local', 'vms'.$exten['extension'], '', new ext_macro('vm',$vm.',NOMESSAGE,${IVR_RETVM}'));
						$ext->add('ext-local', 'vms'.$exten['extension'], '', new ext_goto('1','vmret'));
					} else {
						// If we return from teh macro, it means we are suppose to return to the IVR
						//
						$ext->add('ext-local', $exten['extension'], '', new ext_goto('1','return','${IVR_CONTEXT}'));
					}
						
					// Create the hints if running in normal mode
					//
					if (!$amp_conf['DYNAMICHINTS']) {
						$hint = core_hint_get($exten['extension']);
						$dnd_string = ($amp_conf['USEDEVSTATE'] && function_exists('donotdisturb_get_config')) ? "&Custom:DND".$exten['extension'] : '';
						if (!empty($hint)) {
							$ext->addHint('ext-local', $exten['extension'], $hint.$dnd_string);
							if ($intercom_code != '') {
								$ext->addHint('ext-local', $intercom_code.$exten['extension'], $hint.$dnd_string);
							}
						} else if ($dnd_string) {
							$ext->addHint('ext-local', $exten['extension'], "&Custom:DND".$exten['extension']);
							if ($intercom_code != '') {
								$ext->addHint('ext-local', $intercom_code.$exten['extension'], "&Custom:DND".$exten['extension']);
							}
						}
					}
					if ($exten['sipname']) {
						$ext->add('ext-local', $exten['sipname'], '', new ext_goto('1',$item[0],'from-internal'));
					}
					// Now make a special context for the IVR inclusions of local extension dialing so that
					// when people use the Queues breakout ability, and break out to someone's extensions, voicemail
					// works.
					//
					$ivr_context = 'from-did-direct-ivr';
					$ext->add($ivr_context, $exten['extension'],'', new ext_execif('$["${BLKVM_OVERRIDE}" != ""]','dbDel','${BLKVM_OVERRIDE}'));
					$ext->add($ivr_context, $exten['extension'],'', new ext_setvar('__NODEST', ''));
					$ext->add($ivr_context, $exten['extension'],'', new ext_goto('1',$exten['extension'],'from-did-direct'));
					if($vm != "novm") {
						$ext->add($ivr_context, '${VM_PREFIX}'.$exten['extension'],'', new ext_execif('$["${BLKVM_OVERRIDE}" != ""]','dbDel','${BLKVM_OVERRIDE}'));
						$ext->add($ivr_context, '${VM_PREFIX}'.$exten['extension'],'', new ext_setvar('__NODEST', ''));
						$ext->add($ivr_context, '${VM_PREFIX}'.$exten['extension'],'', new ext_macro('vm',$vm.',DIRECTDIAL,${IVR_RETVM}'));
						$ext->add($ivr_context, '${VM_PREFIX}'.$exten['extension'],'', new ext_gotoif('$["${IVR_RETVM}" = "RETURN" & "${IVR_CONTEXT}" != ""]','ext-local,vmret,playret'));
					}
				}
				$ext->add('ext-local', 'vmret', '', new ext_gotoif('$["${IVR_RETVM}" = "RETURN" & "${IVR_CONTEXT}" != ""]','playret'));
				$ext->add('ext-local', 'vmret', '', new ext_hangup(''));
				$ext->add('ext-local', 'vmret', 'playret', new ext_playback('exited-vm-will-be-transfered&silence/1'));
				$ext->add('ext-local', 'vmret', '', new ext_goto('1','return','${IVR_CONTEXT}'));
			}


			// create from-trunk context for each trunk that adds counts to channels
			//
			$trunklist = core_trunks_list(true);
			if (is_array($trunklist)) {
				foreach ($trunklist as $trunkprops) {
					if (trim($trunkprops['value']) == 'on') {
						// value of on is disabled and for zap we don't create a context
						continue;
					}
					switch ($trunkprops['tech']) {
						case 'DUNDI':
							$macro_name = 'macro-dundi-'.substr($trunkprops['globalvar'],4);
							$ext->addSwitch($macro_name,'DUNDI/'.$trunkprops['name']);
							$ext->add($macro_name, 's', '', new ext_goto('1','${ARG1}'));
						case 'IAX':
						case 'IAX2':
						case 'SIP':
							$trunkgroup = $trunkprops['globalvar'];
							$trunkcontext  = "from-trunk-".strtolower($trunkprops['tech'])."-".$trunkprops['name'];
							$ext->add($trunkcontext, '_.', '', new ext_setvar('GROUP()',$trunkgroup));
							$ext->add($trunkcontext, '_.', '', new ext_goto('1','${EXTEN}','from-trunk'));
							break;
						default:
					}
				}
			}

			/* dialplan globals */
			// modules should NOT use the globals table to store anything!
			// modules should use $ext->addGlobal("testvar","testval"); in their module_get_config() function instead
			// I'm cheating for core functionality - do as I say, not as I do ;-)		

			// Auto add these globals to give access to agi scripts and other needs, unless defined in the global table.
			//
			$amp_conf_globals = array( 
				"ASTETCDIR", 
				"ASTMODDIR", 
				"ASTVARLIBDIR", 
				"ASTAGIDIR", 
				"ASTSPOOLDIR", 
				"ASTRUNDIR", 
				"ASTLOGDIR",
				"CWINUSEBUSY",
				"AMPMGRUSER",
				"AMPMGRPASS"
			);

			$disable_recording = false;

			$sql = "SELECT * FROM globals";
			$globals = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
			foreach($globals as $global) {
				$value = $global['value'];
        if ($chan_dahdi && substr($value, 0, 4) === 'ZAP/') {
          $value = 'DAHDI/' . substr($value, 4);
        }
				$ext->addGlobal($global['variable'],$value);

				// now if for some reason we have a variable in the global table
				// that is in our $amp_conf_globals list, then remove it so we
				// don't duplicate, the sql table will take precedence
				//
				if (array_key_exists($global['variable'],$amp_conf_globals)) {
					$rm_keys = array_keys($amp_conf_globals,$global['variable']);
					foreach ($rm_keys as $index) {
						unset($amp_conf_globals[$index]);
					}
				}
				if (($global['variable'] == 'RECORDING_STATE') && (strtoupper($global['value']) == 'DISABLED')) {
					$disable_recording = true;
				}
			}
			foreach ($amp_conf_globals as $global) {
				if (isset($amp_conf[$global])) {
					$value = $amp_conf[$global];
					if ($value === true || $value === false) {
						$value = ($value) ? 'true':'false';
					}
					$ext->addGlobal($global, $value);
					out("Added to globals: $global = $value");
				}
			}
			// Put the asterisk version in a global for agi etc.
			$ext->addGlobal('ASTVERSION', $version);
			// Put the use of chan_dahdi in a global for dialparties
			$ext->addGlobal('ASTCHANDAHDI', $chan_dahdi ? '1' : '0');
			
			// Create CallingPresTable to deal with difference that ${CALINGPRES} returns vs. what
			// SetCallerPres() accepts. This is a workaround that gets resolved in 1.6 where
			// function CALLINGPRES() is consistent.
			// This should be fixed in 1.4.20 but for now we keep it in until 1.6
			//
			if (version_compare($version, "1.6", "lt")) { 
				$ext->addGlobal('CALLINGPRES_0', 'allowed_not_screened');
				$ext->addGlobal('CALLINGPRES_1', 'allowed_passed_screen');
				$ext->addGlobal('CALLINGPRES_2', 'allowed_failed_screen');
				$ext->addGlobal('CALLINGPRES_3', 'allowed');
				$ext->addGlobal('CALLINGPRES_32', 'prohib_not_screened');
				$ext->addGlobal('CALLINGPRES_33', 'prohib_passed_screen');
				$ext->addGlobal('CALLINGPRES_34', 'prohib_failed_screen');
				$ext->addGlobal('CALLINGPRES_35', 'prohib');
				$ext->addGlobal('CALLINGPRES_67', 'unavailable');
			}

			// This checks if we have func_extstate loaded, if so we set the global which dialparties
			// can use (and any other AGI script) to determine that this function exists and not connect
			// to the manager to get the information
			//
			if (version_compare($version, '1.4', 'ge') && version_compare($version, '1.6', 'lt')) {
				$response = $astman->send_request('Command', array('Command' => 'module show like func_extstate'));
				if (preg_match('/1 modules loaded/', $response['data'])) {
					$ext->addGlobal('HAS_EXTENSION_STATE', 'TRUE');
				}
			}

			// Let's create globals for each trunk to determine which one's have fixlocalprefix settings.
			// this allows us to skip calling the agi script if there are no rules to process saving
			// on performance
			//
			$conf = core_trunks_readDialRulesFile();
			if (is_array($conf)) {
				foreach ($conf as $trunknum => $entries) {
					$trunkname = substr($trunknum,6);
					$ext->addGlobal("PREFIX_TRUNK_$trunkname",count($entries));
				}
			}

			// Generate macro-record-enable, if recording is disabled then we just make it a stub
			// Otherwise we make it right
			//
			$context = 'macro-record-enable';
			$exten = 's';
			if ($disable_recording) {
				$ext->add($context, $exten, '', new ext_macroexit());
			} else {
				$ext->add($context, $exten, '', new ext_gotoif('$["${BLINDTRANSFER}" = ""]', 'check'));
				$ext->add($context, $exten, '', new ext_resetcdr('w'));

				if (version_compare($version, '1.4', 'ge')) {
					$ext->add($context, $exten, '', new ext_stopmixmonitor());
				} else {
					$ext->add($context, $exten, '', new ext_stopmonitor());
				}

				$ext->add($context, $exten, 'check', new ext_agi('recordingcheck,${STRFTIME(${EPOCH},,%Y%m%d-%H%M%S)},${UNIQUEID}'));
				$ext->add($context, $exten, '', new ext_macroexit());
				// keep this 999 in case people have issues updating their recording script
				$ext->add($context, $exten, 'record', new ext_mixmonitor('${MIXMON_DIR}${CALLFILENAME}.${MIXMON_FORMAT}','','${MIXMON_POST}'),'1',998);
			}

			/* outbound routes */
			// modules should use their own table for storage (and module_get_config() to add dialplan)
			// modules should NOT use the extension table to store anything!
			$sql = "SELECT application FROM extensions where context = 'outbound-allroutes' ORDER BY application";
			$outrts = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
			$ext->addInclude('from-internal-additional','outbound-allroutes');
			$ext->add('outbound-allroutes', 'foo', '', new ext_noop('bar'));
			foreach($outrts as $outrt) {
				$ext->addInclude('outbound-allroutes',$outrt['application']);
				$sql = "SELECT * FROM extensions where context = '".$outrt['application']."' ORDER BY extension, CAST(priority AS UNSIGNED) ASC";
				$thisrt = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
				$lastexten = false;
				foreach($thisrt as $exten) {
					//if emergencyroute, then set channel var
					if(strpos($exten['args'],"EMERGENCYROUTE") !== false)
						$ext->add($outrt['application'], $exten['extension'], '', new ext_setvar("EMERGENCYROUTE",substr($exten['args'],15)));
					if(strpos($exten['args'],"INTRACOMPANYROUTE") !== false)
						$ext->add($outrt['application'], $exten['extension'], '', new ext_setvar("INTRACOMPANYROUTE",substr($exten['args'],18)));
					// Don't set MOHCLASS if already set, threre may be a feature code that overrode it
					if(strpos($exten['args'],"MOHCLASS") !== false)
						$ext->add($outrt['application'], $exten['extension'], '', new ext_setvar("MOHCLASS", '${IF($["x${MOHCLASS}"="x"]?'.substr($exten['args'],9).':${MOHCLASS})}' ));
					if(strpos($exten['args'],"dialout-trunk") !== false || strpos($exten['args'],"dialout-enum") !== false || strpos($exten['args'],"dialout-dundi") !== false) {
						if ($exten['extension'] !== $lastexten) {

							// If NODEST is set, clear it. No point in remembering since dialout-trunk will just end in the
							// bit bucket. But if answered by an outside line with transfer capability, we want NODEST to be
							// clear so a subsequent transfer to an internal extension works and goes to voicmail or other
							// destinations.
							//
							// Then do one call to user-callerid and record-enable instead of each time as in the past
							//
							$ext->add($outrt['application'], $exten['extension'], '', new ext_macro('user-callerid,SKIPTTL'));
							$ext->add($outrt['application'], $exten['extension'], '', new ext_setvar("_NODEST",""));
							$ext->add($outrt['application'], $exten['extension'], '', new ext_macro('record-enable,${AMPUSER},OUT'));
							$lastexten = $exten['extension'];
						}
						$ext->add($outrt['application'], $exten['extension'], '', new ext_macro($exten['args']));
					}
					if(strpos($exten['args'],"outisbusy") !== false)
						$ext->add($outrt['application'], $exten['extension'], '', new ext_macro("outisbusy"));
				}
			}

			general_generate_indications();

			// "blackhole" destinations
			$ext->add('app-blackhole', 'hangup', '', new ext_noop('Blackhole Dest: Hangup'));
			$ext->add('app-blackhole', 'hangup', '', new ext_hangup());

			$ext->add('app-blackhole', 'zapateller', '', new ext_noop('Blackhole Dest: Play SIT Tone'));
			$ext->add('app-blackhole', 'zapateller', '', new ext_answer());
			$ext->add('app-blackhole', 'zapateller', '', new ext_zapateller());
			// Should hangup ?
			// $ext->add('app-blackhole', 'zapateller', '', new ext_hangup());
					
			$ext->add('app-blackhole', 'musiconhold', '', new ext_noop('Blackhole Dest: Put caller on hold forever'));
			$ext->add('app-blackhole', 'musiconhold', '', new ext_answer());
			$ext->add('app-blackhole', 'musiconhold', '', new ext_musiconhold());

			$ext->add('app-blackhole', 'congestion', '', new ext_noop('Blackhole Dest: Congestion'));
			$ext->add('app-blackhole', 'congestion', '', new ext_answer());
			$ext->add('app-blackhole', 'congestion', '', new ext_playtones('congestion'));
			$ext->add('app-blackhole', 'congestion', '', new ext_congestion());
			$ext->add('app-blackhole', 'congestion', '', new ext_hangup());

			$ext->add('app-blackhole', 'busy', '', new ext_noop('Blackhole Dest: Busy'));
			$ext->add('app-blackhole', 'busy', '', new ext_answer());
			$ext->add('app-blackhole', 'busy', '', new ext_playtones('busy'));
			$ext->add('app-blackhole', 'busy', '', new ext_busy());
			$ext->add('app-blackhole', 'busy', '', new ext_hangup());

			$ext->add('app-blackhole', 'ring', '', new ext_noop('Blackhole Dest: Ring'));
			$ext->add('app-blackhole', 'ring', '', new ext_answer());
			$ext->add('app-blackhole', 'ring', '', new ext_playtones('ring'));
			$ext->add('app-blackhole', 'ring', '', new ext_wait(300));
			$ext->add('app-blackhole', 'ring', '', new ext_hangup());

			if ($amp_conf['AMPBADNUMBER'] !== false) {
				$context = 'bad-number';
				$exten = '_X.';
				$ext->add($context, $exten, '', new extension('ResetCDR()'));
				$ext->add($context, $exten, '', new extension('NoCDR()'));
				$ext->add($context, $exten, '', new ext_wait('1'));
				$ext->add($context, $exten, '', new ext_playback('silence/1&cannot-complete-as-dialed&check-number-dial-again,noanswer'));
				$ext->add($context, $exten, '', new ext_wait('1'));
				$ext->add($context, $exten, '', new ext_congestion('20'));
				$ext->add($context, $exten, '', new ext_hangup());
			}

			/*
			;------------------------------------------------------------------------
			; [macro-confirm]
			;------------------------------------------------------------------------
			; CONTEXT:      macro-confirm                                                                                                              
			; PURPOSE:      added default message if none supplied
			;
			; Follom-Me and Ringgroups provide an option to supply a message to be
			; played as part of the confirmation. These changes have added a default
			; message if none is supplied.
			;
			;------------------------------------------------------------------------
			*/
			$context = 'macro-confirm';
			$exten = 's';

			$ext->add($context, $exten, '', new ext_setvar('LOOPCOUNT','0'));
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT','ABORT'));
			$ext->add($context, $exten, '', new ext_setvar('MSG1','${IF($["foo${ARG1}" != "foo"]?${ARG1}:"incoming-call-1-accept-2-decline")}'));
			if (version_compare($version, "1.4", "ge")) { 
				$ext->add($context, $exten, 'start', new ext_background('${MSG1},m,${CHANNEL(language)},macro-confirm'));
			} else {
				$ext->add($context, $exten, 'start', new ext_background('${MSG1},m,${LANGUAGE},macro-confirm'));
			}
			$ext->add($context, $exten, '', new ext_read('INPUT', '', 1, '', '', 4));
			$ext->add($context, $exten, '', new ext_gotoif('$[${LEN(${INPUT})} > 0]', '${INPUT},1', 't,1'));

			$exten = '1';
			$ext->add($context, $exten, '', new ext_gotoif('$["${DB_EXISTS(RG/${ARG3}/${UNIQCHAN})}" = "0"]', 'toolate,1'));
			$ext->add($context, $exten, '', new ext_dbdel('RG/${ARG3}/${UNIQCHAN}'));
			$ext->add($context, $exten, '', new ext_dbdel('${BLKVM_OVERRIDE}'));
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT',''));
			$ext->add($context, $exten, 'exitopt1', new ext_macroexit());

			$exten = '2';
			$ext->add($context, $exten, '', new ext_goto(1, 'noanswer'));

			$exten = '3';
			$ext->add($context, $exten, '', new ext_saydigits('${CALLCONFIRMCID}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${DB_EXISTS(RG/${ARG3}/${UNIQCHAN})}" = "0"]', 'toolate,1','s,start'));

			$exten = 't';
			$ext->add($context, $exten, '', new ext_gotoif('$["${DB_EXISTS(RG/${ARG3}/${UNIQCHAN})}" = "0"]', 'toolate,1'));
			$ext->add($context, $exten, '', new ext_setvar('LOOPCOUNT','$[ ${LOOPCOUNT} + 1 ]'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${LOOPCOUNT} < 5 ]', 's,start','noanswer,1'));

			$exten = '_X';
			if (version_compare($version, "1.4", "ge")) { 
				$ext->add($context, $exten, '', new ext_background('invalid,m,${CHANNEL(language)},macro-confirm'));
			} else {
				$ext->add($context, $exten, '', new ext_background('invalid,m,${LANGUAGE},macro-confirm'));
			}
			$ext->add($context, $exten, '', new ext_gotoif('$["${DB_EXISTS(RG/${ARG3}/${UNIQCHAN})}" = "0"]', 'toolate,1'));
			$ext->add($context, $exten, '', new ext_setvar('LOOPCOUNT','$[ ${LOOPCOUNT} + 1 ]'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${LOOPCOUNT} < 5 ]', 's,start','noanswer,1'));

			$exten = 'noanswer';
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT','ABORT'));
			$ext->add($context, $exten, 'exitnoanswer', new ext_macroexit());

			$exten = 'toolate';
			$ext->add($context, $exten, '', new ext_setvar('MSG2','${IF($["foo${ARG2}" != "foo"]?${ARG2}:"incoming-call-no-longer-avail")}'));
			$ext->add($context, $exten, '', new ext_playback('${MSG2}'));
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT','ABORT'));
			$ext->add($context, $exten, 'exittoolate', new ext_macroexit());

			$exten = 'h';
			$ext->add($context, $exten, '', new ext_macro('hangupcall'));

			/*
			;------------------------------------------------------------------------
			; [macro-auto-confirm]
			;------------------------------------------------------------------------
			; This macro is called from ext-local-confirm to auto-confirm a call so that other extensions
			; are aware that the call has been answered.
			;
			;------------------------------------------------------------------------
			*/
			$context = 'macro-auto-confirm';
			$exten = 's';
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT',''));
			$ext->add($context, $exten, '', new ext_dbdel('${BLKVM_OVERRIDE}'));
			$ext->add($context, $exten, '', new ext_dbdel('RG/${ARG1}/${UNIQCHAN}'));


			/*
			;------------------------------------------------------------------------
			; [macro-auto-blkvm]
			;------------------------------------------------------------------------
			; This macro is called for any extension dialed form a queue, ringgroup
			; or followme, so that the answering extension can clear the voicemail block
			; override allow subsequent transfers to properly operate.
			;
			;------------------------------------------------------------------------
			*/
			$context = 'macro-auto-blkvm';
			$exten = 's';
			$ext->add($context, $exten, '', new ext_setvar('__MACRO_RESULT',''));
			$ext->add($context, $exten, '', new ext_dbdel('${BLKVM_OVERRIDE}'));

			/*
			;------------------------------------------------------------------------
			; [sub-pincheck]
			;------------------------------------------------------------------------
			; This subroutine checks the pincode and then resets the CDR from that point
			; if the pincode passes. This way the billsec and duration fields are set
			; properly for pin dialing.
			;
			; ${ARG3} is the pincode if this was called, used by dialout-trunk, dialout-enum
			; and dialout-dundi
			;
			;------------------------------------------------------------------------
			*/
			$context = 'sub-pincheck';
			$exten = 's';
			$ext->add($context, $exten, '', new ext_authenticate('${ARG3}'));
			$ext->add($context, $exten, '', new ext_resetcdr(''));
			$ext->add($context, $exten, '', new ext_return(''));

			
			/*
			 * dialout using a trunk, using pattern matching (don't strip any prefix)
			 * arg1 = trunk number, arg2 = number, arg3 = route password
			 *
			 * MODIFIED (PL)
			 *
			 * Modified both Dial() commands to include the new TRUNK_OPTIONS from the general
			 * screen of AMP
			 */
			 
			$context = 'macro-dialout-trunk';
			$exten = 's';

			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK', '${ARG1}'));
			$ext->add($context, $exten, '', new ext_gosubif('$[$["${ARG3}" != ""] & $["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]]','sub-pincheck,s,1'));
			$ext->add($context, $exten, '', new ext_gotoif('$["x${OUTDISABLE_${DIAL_TRUNK}}" = "xon"]', 'disabletrunk,1'));
			$ext->add($context, $exten, '', new ext_set('DIAL_NUMBER', '${ARG2}')); // fixlocalprefix depends on this
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK_OPTIONS', '${DIAL_OPTIONS}')); // will be reset to TRUNK_OPTIONS if not intra-company
			$ext->add($context, $exten, '', new ext_set('OUTBOUND_GROUP', 'OUT_${DIAL_TRUNK}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${OUTMAXCHANS_${DIAL_TRUNK}}foo" = "foo"]', 'nomax'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${GROUP_COUNT(OUT_${DIAL_TRUNK})} >= ${OUTMAXCHANS_${DIAL_TRUNK}} ]', 'chanfull'));
			$ext->add($context, $exten, 'nomax', new ext_gotoif('$["${INTRACOMPANYROUTE}" = "YES"]', 'skipoutcid'));  // Set to YES if treated like internal
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK_OPTIONS', '${TRUNK_OPTIONS}'));
			$ext->add($context, $exten, '', new ext_macro('outbound-callerid', '${DIAL_TRUNK}'));
			$ext->add($context, $exten, 'skipoutcid', new ext_execif('$["${PREFIX_TRUNK_${DIAL_TRUNK}}" != ""]','AGI','fixlocalprefix'));  // this sets DIAL_NUMBER to the proper dial string for this trunk
			$ext->add($context, $exten, '', new ext_set('OUTNUM', '${OUTPREFIX_${DIAL_TRUNK}}${DIAL_NUMBER}'));  // OUTNUM is the final dial number
			$ext->add($context, $exten, '', new ext_set('custom', '${CUT(OUT_${DIAL_TRUNK},:,1)}'));  // Custom trunks are prefixed with "AMP:"
		
			// Back to normal processing, whether intracompany or not.
			// But add the macro-setmusic if we don't want music on this outbound call
			$ext->add($context, $exten, '', new ext_execif('$[$["${MOHCLASS}" != "default"] & $["${MOHCLASS}" != ""]]', 'Set', 'DIAL_TRUNK_OPTIONS=M(setmusic^${MOHCLASS})${DIAL_TRUNK_OPTIONS}'));
		
			// This macro call will always be blank and is provided as a hook for customization required prior to making a call
			// such as adding SIP header information or other requirements. All the channel variables from above are present
			
			$ext->add($context, $exten, 'gocall', new ext_macro('dialout-trunk-predial-hook'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${PREDIAL_HOOK_RET}" = "BYPASS"]', 'bypass,1'));
		
			$ext->add($context, $exten, '', new ext_gotoif('$["${custom}" = "AMP"]', 'customtrunk'));
			$ext->add($context, $exten, '', new ext_dial('${OUT_${DIAL_TRUNK}}/${OUTNUM}', '300,${DIAL_TRUNK_OPTIONS}'));  // Regular Trunk Dial
			$ext->add($context, $exten, '', new ext_goto(1, 's-${DIALSTATUS}'));
			
			$ext->add($context, $exten, 'customtrunk', new ext_set('pre_num', '${CUT(OUT_${DIAL_TRUNK},$,1)}'));
			$ext->add($context, $exten, '', new ext_set('the_num', '${CUT(OUT_${DIAL_TRUNK},$,2)}'));  // this is where we expect to find string OUTNUM
			$ext->add($context, $exten, '', new ext_set('post_num', '${CUT(OUT_${DIAL_TRUNK},$,3)}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${the_num}" = "OUTNUM"]', 'outnum', 'skipoutnum'));  // if we didn't find "OUTNUM", then skip to Dial
			$ext->add($context, $exten, 'outnum', new ext_set('the_num', '${OUTNUM}'));  // replace "OUTNUM" with the actual number to dial
			$ext->add($context, $exten, 'skipoutnum', new ext_dial('${pre_num:4}${the_num}${post_num}', '300,${DIAL_TRUNK_OPTIONS}'));
			$ext->add($context, $exten, '', new ext_goto(1, 's-${DIALSTATUS}'));
			
			$ext->add($context, $exten, 'chanfull', new ext_noop('max channels used up'));
		
			$exten = 's-BUSY';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting BUSY - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('busy'));
			$ext->add($context, $exten, '', new ext_busy(20));
		
			$exten = 's-NOANSWER';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting NOANSWER - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('congestion'));
			$ext->add($context, $exten, '', new ext_congestion(20));
		
			$exten = 's-CANCEL';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting CANCEL - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('congestion'));
			$ext->add($context, $exten, '', new ext_congestion(20));

			$exten = 's-CHANUNAVAIL';
			$ext->add($context, $exten, '', new ext_gotoif('$["x${OUTFAIL_${ARG1}}" = "x"]', 'noreport'));
			$ext->add($context, $exten, '', new ext_agi('${OUTFAIL_${ARG1}}'));
			$ext->add($context, $exten, 'noreport', new ext_noop('TRUNK Dial failed due to ${DIALSTATUS} (hangupcause: ${HANGUPCAUSE}) - failing through to other trunks'));
		
			$exten = '_s-.';
			$ext->add($context, $exten, '', new ext_gotoif('$["x${OUTFAIL_${ARG1}}" = "x"]', 'noreport'));
			$ext->add($context, $exten, '', new ext_agi('${OUTFAIL_${ARG1}}'));
			$ext->add($context, $exten, 'noreport', new ext_noop('TRUNK Dial failed due to ${DIALSTATUS} - failing through to other trunks'));
			
			$ext->add($context, 'disabletrunk', '', new ext_noop('TRUNK: ${OUT_${DIAL_TRUNK}} DISABLED - falling through to next trunk'));
			$ext->add($context, 'bypass', '', new ext_noop('TRUNK: ${OUT_${DIAL_TRUNK}} BYPASSING because dialout-trunk-predial-hook'));
		
			$ext->add($context, 'h', '', new ext_macro('hangupcall'));




			$context = 'macro-dialout-dundi';
			$exten = 's';
			
			/*
			 * Dialout Dundi Trunk
			 */
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK', '${ARG1}'));
			$ext->add($context, $exten, '', new ext_gosubif('$[$["${ARG3}" != ""] & $["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]]','sub-pincheck,s,1'));
			$ext->add($context, $exten, '', new ext_gotoif('$["x${OUTDISABLE_${DIAL_TRUNK}}" = "xon"]', 'disabletrunk,1'));
			$ext->add($context, $exten, '', new ext_set('DIAL_NUMBER', '${ARG2}')); // fixlocalprefix depends on this
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK_OPTIONS', '${DIAL_OPTIONS}')); // will be reset to TRUNK_OPTIONS if not intra-company
			$ext->add($context, $exten, '', new ext_set('OUTBOUND_GROUP', 'OUT_${DIAL_TRUNK}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${OUTMAXCHANS_${DIAL_TRUNK}}foo" = "foo"]', 'nomax'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${GROUP_COUNT(OUT_${DIAL_TRUNK})} >= ${OUTMAXCHANS_${DIAL_TRUNK}} ]', 'chanfull'));
			$ext->add($context, $exten, 'nomax', new ext_gotoif('$["${INTRACOMPANYROUTE}" = "YES"]', 'skipoutcid'));  // Set to YES if treated like internal
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK_OPTIONS', '${TRUNK_OPTIONS}'));
			$ext->add($context, $exten, '', new ext_macro('outbound-callerid', '${DIAL_TRUNK}'));
			$ext->add($context, $exten, 'skipoutcid', new ext_execif('$["${PREFIX_TRUNK_${DIAL_TRUNK}}" != ""]','AGI','fixlocalprefix'));  // this sets DIAL_NUMBER to the proper dial string for this trunk
			$ext->add($context, $exten, '', new ext_set('OUTNUM', '${OUTPREFIX_${DIAL_TRUNK}}${DIAL_NUMBER}'));  // OUTNUM is the final dial number

			// Back to normal processing, whether intracompany or not.
			// But add the macro-setmusic if we don't want music on this outbound call
			$ext->add($context, $exten, '', new ext_execif('$[$["${MOHCLASS}" != "default"] & $["${MOHCLASS}" != ""]]', 'Set', 'DIAL_TRUNK_OPTIONS=M(setmusic^${MOHCLASS})${DIAL_TRUNK_OPTIONS}'));
		
			// This macro call will always be blank and is provided as a hook for customization required prior to making a call
			// such as adding SIP header information or other requirements. All the channel variables from above are present
			
			$ext->add($context, $exten, 'gocall', new ext_macro('dialout-dundi-predial-hook'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${PREDIAL_HOOK_RET}" = "BYPASS"]', 'bypass,1'));
		
			$ext->add($context, $exten, '', new ext_gotoif('$["${custom}" = "AMP"]', 'customtrunk'));

			$ext->add($context, $exten, '', new ext_macro('dundi-${DIAL_TRUNK}','${OUTNUM}'));
			$ext->add($context, $exten, '', new ext_goto(1, 's-${DIALSTATUS}'));
			
			$ext->add($context, $exten, 'chanfull', new ext_noop('max channels used up'));
		
			$exten = 's-BUSY';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting BUSY - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('busy'));
			$ext->add($context, $exten, '', new ext_busy(20));
		
			$exten = 's-NOANSWER';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting NOANSWER - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('congestion'));
			$ext->add($context, $exten, '', new ext_congestion(20));
		
			$exten = 's-CANCEL';
			$ext->add($context, $exten, '', new ext_noop('Dial failed due to trunk reporting CANCEL - giving up'));
			$ext->add($context, $exten, '', new ext_playtones('congestion'));
			$ext->add($context, $exten, '', new ext_congestion(20));
		
			$exten = '_s-.';
			$ext->add($context, $exten, '', new ext_gotoif('$["x${OUTFAIL_${ARG1}}" = "x"]', 'noreport'));
			$ext->add($context, $exten, '', new ext_agi('${OUTFAIL_${ARG1}}'));
			$ext->add($context, $exten, 'noreport', new ext_noop('TRUNK Dial failed due to ${DIALSTATUS} - failing through to other trunks'));
			
			$ext->add($context, 'disabletrunk', '', new ext_noop('TRUNK: ${OUT_${DIAL_TRUNK}} DISABLED - falling through to next trunk'));
			$ext->add($context, 'bypass', '', new ext_noop('TRUNK: ${OUT_${DIAL_TRUNK}} BYPASSING because dialout-dundi-predial-hook'));
		
			$ext->add($context, 'h', '', new ext_macro('hangupcall'));



			/*
			 * sets the callerid of the device to that of the logged in user
			 *
			 * ${AMPUSER} is set upon return to the real user despite any aliasing that may
			 * have been set as a result of the AMPUSER/<nnn>/cidnum field. This is used by
			 * features like DND, CF, etc. to set the proper structure on aliased instructions 
			 */
			$context = 'macro-user-callerid';
			$exten = 's';
			
			//$ext->add($context, $exten, '', new ext_noop('user-callerid: ${CALLERID(name)} ${CALLERID(number)}'));
							
			// make sure AMPUSER is set if it doesn't get set below			
			$ext->add($context, $exten, '', new ext_set('AMPUSER', '${IF($["foo${AMPUSER}" = "foo"]?${CALLERID(number)}:${AMPUSER})}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${CHANNEL:0:5}" = "Local"]', 'report'));
			$ext->add($context, $exten, '', new ext_execif('$["${REALCALLERIDNUM:1:2}" = ""]', 'Set', 'REALCALLERIDNUM=${CALLERID(number)}'));
			//$ext->add($context, $exten, 'start', new ext_noop('REALCALLERIDNUM is ${REALCALLERIDNUM}'));
			$ext->add($context, $exten, '', new ext_set('AMPUSER', '${DB(DEVICE/${REALCALLERIDNUM}/user)}'));
			$ext->add($context, $exten, '', new ext_set('AMPUSERCIDNAME', '${DB(AMPUSER/${AMPUSER}/cidname)}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["x${AMPUSERCIDNAME:1:2}" = "x"]', 'report'));

			// user may masquerade as a different user internally, so set the internal cid as indicated
			// but keep the REALCALLERID which is used to determine their true identify and lookup info
			// during outbound calls.
			$ext->add($context, $exten, '', new ext_set('AMPUSERCID', '${IF($["${DB_EXISTS(AMPUSER/${AMPUSER}/cidnum)}" = "1"]?${DB_RESULT}:${AMPUSER})}'));
			$ext->add($context, $exten, '', new ext_set('CALLERID(all)', '"${AMPUSERCIDNAME}" <${AMPUSERCID}>'));
			/*
			 * This is where to splice in things like setting the language based on a user's astdb setting,
			 * or where you might set the CID account code based on a user instead of the device settings.
			 */
			$ext->add($context, $exten, 'report', new ext_gotoif('$[ "${ARG1}" = "SKIPTTL" ]', 'continue'));
			$ext->add($context, $exten, 'report2', new ext_set('__TTL', '${IF($["foo${TTL}" = "foo"]?64:$[ ${TTL} - 1 ])}'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${TTL} > 0 ]', 'continue'));
			$ext->add($context, $exten, '', new ext_wait('${RINGTIMER}'));  // wait for a while, to give it a chance to be picked up by voicemail
			$ext->add($context, $exten, '', new ext_answer());
			$ext->add($context, $exten, '', new ext_wait('2'));
			$ext->add($context, $exten, '', new ext_playback('im-sorry&an-error-has-occured&with&call-forwarding'));
			$ext->add($context, $exten, '', new ext_macro('hangupcall'));
			$ext->add($context, $exten, '', new ext_congestion(20));
			$ext->add($context, $exten, 'continue', new ext_noop('Using CallerID ${CALLERID(all)}'));
			$ext->add($context, 'h', '', new ext_macro('hangupcall'));
			
			/*
			 * arg1 = trunk number, arg2 = number
			 * 
			 * Re-written to use enumlookup.agi
			 */
	
			$context = 'macro-dialout-enum';
			$exten = 's';
	
			$ext->add($context, $exten, '', new ext_gosubif('$[$["${ARG3}" != ""] & $["${DB(AMPUSER/${AMPUSER}/pinless)}" != "NOPASSWD"]]','sub-pincheck,s,1'));
			$ext->add($context, $exten, '', new ext_macro('outbound-callerid', '${ARG1}'));
			$ext->add($context, $exten, '', new ext_set('OUTBOUND_GROUP', 'OUT_${ARG1}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${OUTMAXCHANS_${ARG1}}foo" = "foo"]', 'nomax'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ ${GROUP_COUNT(OUT_${ARG1})} >= ${OUTMAXCHANS_${ARG1}} ]', 'nochans'));
			$ext->add($context, $exten, 'nomax', new ext_set('DIAL_NUMBER', '${ARG2}'));
			$ext->add($context, $exten, '', new ext_set('DIAL_TRUNK', '${ARG1}'));
			$ext->add($context, $exten, '', new ext_execif('$["${PREFIX_TRUNK_${DIAL_TRUNK}}" != ""]','AGI','fixlocalprefix'));  // this sets DIAL_NUMBER to the proper dial string for this trunk
			//  Replacement for asterisk's ENUMLOOKUP function
			$ext->add($context, $exten, '', new ext_agi('enumlookup.agi'));
			// Now we have the variable DIALARR set to a list of URI's that can be called, in order of priority
			// Loop through them trying them in order.
			$ext->add($context, $exten, 'dialloop', new ext_gotoif('$["foo${DIALARR}"="foo"]', 'end'));
			$ext->add($context, $exten, '', new ext_set('TRYDIAL', '${CUT(DIALARR,%,1)}'));
			$ext->add($context, $exten, '', new ext_set('DIALARR', '${CUT(DIALARR,%,2-)}'));
			$ext->add($context, $exten, '', new ext_dial('${TRYDIAL}', ''));
			$ext->add($context, $exten, '', new ext_noop('Dial exited in macro-enum-dialout with ${DIALSTATUS}'));
			// Now, if we're still here, that means the Dial failed for some reason. 
			// If it's CONGESTION or CHANUNAVAIL we want to try again on a different
			// different channel. If there's no more left, the dialloop tag will exit.
			$ext->add($context, $exten, '', new ext_gotoif('$[ $[ "${DIALSTATUS}" = "CHANUNAVAIL" ] | $[ "${DIALSTATUS}" = "CONGESTION" ] ]', 'dialloop'));
			// If we're here, then it's BUSY or NOANSWER or something and well, deal with it.
			$ext->add($context, $exten, 'dialfailed', new ext_goto(1, 's-${DIALSTATUS}'));
			// Here are the exit points for the macro.
			$ext->add($context, $exten, 'nochans', new ext_noop('max channels used up'));
			$ext->add($context, $exten, 'end', new ext_noop('Exiting macro-dialout-enum'));
			$ext->add($context, 's-BUSY', '', new ext_noop('Trunk is reporting BUSY'));
			$ext->add($context, 's-BUSY', '', new ext_busy(20));
			$ext->add($context, '_s-.', '', new ext_noop('Dial failed due to ${DIALSTATUS}'));			
			
			/*
			 * overrides callerid out trunks
			 * arg1 is trunk
			 * macro-user-callerid should be called _before_ using this macro
			 */

			$context = 'macro-outbound-callerid';
			$exten = 's';
			
			// If we modified the caller presence, set it back. This allows anonymous calls to be internally prepended but keep
			// their status if forwarded back out. Not doing this can result in the trunk CID being displayed vs. 'blocked call'
			//
			if (version_compare($version, "1.6", "lt")) { 
				$ext->add($context, $exten, '', new ext_execif('$["${CALLINGPRES_SV}" != ""]', 'SetCallerPres', '${CALLINGPRES_SV}'));
			} else {
				$ext->add($context, $exten, '', new ext_execif('$["${CALLINGPRES_SV}" != ""]', 'Set', 'CALLERPRES()=${CALLINGPRES_SV}'));
			}

			// Keep the original CallerID number, for failover to the next trunk.

			$ext->add($context, $exten, '', new ext_execif('$["${REALCALLERIDNUM:1:2}" = ""]', 'Set', 'REALCALLERIDNUM=${CALLERID(number)}'));
			// If this came through a ringgroup or CF, then we want to retain original CID unless
			// OUTKEEPCID_${trunknum} is set.
			// Save then CIDNAME while it is still intact in case we end up sending out this same CID
			$ext->add($context, $exten, 'start', new ext_gotoif('$[ $["${REALCALLERIDNUM}" = ""] | $["${KEEPCID}" != "TRUE"] | $["${OUTKEEPCID_${ARG1}}" = "on"] ]', 'normcid'));  // Set to TRUE if coming from ringgroups, CF, etc.
			$ext->add($context, $exten, '', new ext_set('USEROUTCID', '${REALCALLERIDNUM}'));
			//$ext->add($context, $exten, '', new ext_set('REALCALLERIDNAME', '${CALLERID(name)}'));

			// We now have to make sure the CID is valid. If we find an AMPUSER with the same CID, we assume it is an internal 
			// call (would be quite a conincidence if not) and go through the normal processing to get that CID. If a device 
			// is set for this CID, then it must be internal 
			// If we end up using USEROUTCID at the end, it may still be the REALCALLERIDNUM we saved above. That is determined
			// if the two are equal, AND there is no CALLERID(name) present since it has been removed by the CALLERID(all)=${USEROUTCID}
			// setting. If this is the case, then we put the orignal name back in to send out. Although the CNAME is not honored by most
			// carriers, there are cases where it is so this preserves that information to be used by those carriers who do honor it.
			$ext->add($context, $exten, '', new ext_gotoif('$["foo${DB(AMPUSER/${REALCALLERIDNUM}/device)}" = "foo"]', 'bypass'));

			$ext->add($context, $exten, 'normcid', new ext_set('USEROUTCID', '${DB(AMPUSER/${AMPUSER}/outboundcid)}'));
			$ext->add($context, $exten, 'bypass', new ext_set('EMERGENCYCID', '${DB(DEVICE/${REALCALLERIDNUM}/emergency_cid)}'));
			$ext->add($context, $exten, '', new ext_set('TRUNKOUTCID', '${OUTCID_${ARG1}}'));
			$ext->add($context, $exten, '', new ext_gotoif('$[ $["${EMERGENCYROUTE:1:2}" = ""] | $["${EMERGENCYCID:1:2}" = ""] ]', 'trunkcid'));  // check EMERGENCY ROUTE
			$ext->add($context, $exten, '', new ext_set('CALLERID(all)', '${EMERGENCYCID}'));  // emergency cid for device
			$ext->add($context, $exten, 'exit', new ext_macroexit());


			$ext->add($context, $exten, 'trunkcid', new ext_execif('$["${TRUNKOUTCID:1:2}" != ""]', 'Set', 'CALLERID(all)=${TRUNKOUTCID}'));

			$ext->add($context, $exten, 'usercid', new ext_execif('$["${USEROUTCID:1:2}" != ""]', 'Set', 'CALLERID(all)=${USEROUTCID}'));  // check CID override for extension
			if (version_compare($version, "1.6", "lt")) { 
				$ext->add($context, $exten, 'hidecid', new ext_execif('$["${CALLERID(name)}"="hidden"]', 'SetCallerPres', 'prohib_passed_screen'));
			} else {
				$ext->add($context, $exten, 'hidecid', new ext_execif('$["${CALLERID(name)}"="hidden"]', 'Set', 'CALLERPRES()=prohib_passed_screen'));
			}
			//$ext->add($context, $exten, 'checkname', new ext_execif('$[ $[ "${CALLERID(number)}" = "${REALCALLERIDNUM}" ] & $[ "${CALLERID(name)}" = "" ] ]', 'Set', 'CALLERID(name)=${REALCALLERIDNAME}'));

			
			/*
			 * Adds a dynamic agent/member to a Queue
			 * Prompts for call-back number - in not entered, uses CIDNum
			 */

			$context = 'macro-agent-add';
			$exten = 's';
			
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_macro('user-callerid', 'SKIPTTL'));
			$ext->add($context, $exten, 'a3', new ext_read('CALLBACKNUM', 'agent-login'));  // get callback number from user
			$ext->add($context, $exten, '', new ext_gotoif('$["${CALLBACKNUM}" != ""]', 'a7'));  // if user just pressed # or timed out, use cidnum
			$ext->add($context, $exten, 'a5', new ext_set('CALLBACKNUM', '${AMPUSER}'));
			$ext->add($context, $exten, '', new ext_execif('$["${CALLBACKNUM}" = ""]', 'Set', 'CALLBACKNUM=${CALLERID(number)}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${CALLBACKNUM}" = ""]', 'a3'));  // if still no number, start over
			$ext->add($context, $exten, 'a7', new ext_gotoif('$["${CALLBACKNUM}" = "${ARG1}"]', 'invalid'));  // Error, they put in the queue number
			$ext->add($context, $exten, '', new ext_execif('$["${QREGEX}" != ""]', 'GotoIf', '$["${REGEX("${QREGEX}" ${CALLBACKNUM})}" = "0"]?invalid'));
			$ext->add($context, $exten, '', new ext_execif('$["${ARG2}" != ""]', 'Authenticate', '${ARG2}'));
			$ext->add($context, $exten, 'a9', new ext_addqueuemember('${ARG1}', 'Local/${CALLBACKNUM}@from-internal/n'));  // using chan_local allows us to have agents over trunks
			$ext->add($context, $exten, '', new ext_userevent('Agentlogin', 'Agent: ${CALLBACKNUM}'));
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_playback('agent-loginok&with&extension'));
			$ext->add($context, $exten, '', new ext_saydigits('${CALLBACKNUM}'));
			$ext->add($context, $exten, '', new ext_hangup());
			$ext->add($context, $exten, '', new ext_macroexit());
			$ext->add($context, $exten, 'invalid', new ext_playback('pbx-invalid'));
			$ext->add($context, $exten, '', new ext_goto('a3'));

			/*
			 * Removes a dynamic agent/member from a Queue
			 * Prompts for call-back number - in not entered, uses CIDNum 
			 */

			$context = 'macro-agent-del';
			
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_macro('user-callerid', 'SKIPTTL'));
			$ext->add($context, $exten, 'a3', new ext_read('CALLBACKNUM', 'agent-logoff'));  // get callback number from user
			$ext->add($context, $exten, '', new ext_gotoif('$["${CALLBACKNUM}" = ""]', 'a5', 'a7'));  // if user just pressed # or timed out, use cidnum
			$ext->add($context, $exten, 'a5', new ext_set('CALLBACKNUM', '${AMPUSER}'));
			$ext->add($context, $exten, '', new ext_execif('$["${CALLBACKNUM}" = ""]', 'Set', 'CALLBACKNUM=${CALLERID(number)}'));
			$ext->add($context, $exten, '', new ext_gotoif('$["${CALLBACKNUM}" = ""]', 'a3'));  // if still no number, start over
			$ext->add($context, $exten, 'a7', new ext_removequeuemember('${ARG1}', 'Local/${CALLBACKNUM}@from-internal/n'));
			$ext->add($context, $exten, '', new ext_userevent('RefreshQueue'));
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_playback('agent-loggedoff'));
			$ext->add($context, $exten, '', new ext_hangup());

			$context = 'macro-systemrecording';
			
			//exten => s,1,Set(RECFILE=${IF($["${ARG2}" = ""]?/tmp/${AMPUSER}-ivrrecording:${ARG2})})
			$ext->add($context, 's', '', new ext_setvar('RECFILE','${IF($["${ARG2}" = ""]?/tmp/${AMPUSER}-ivrrecording:${ARG2})}'));
			$ext->add($context, 's', '', new ext_execif('$["${ARG3}" != ""]','Authenticate','${ARG3}'));
			$ext->add($context, 's', '', new ext_goto(1, '${ARG1}'));
			
			$exten = 'dorecord';
			
			// Delete all versions of the current sound file (does not consider languages though
			// otherwise you might have some versions that are not re-recorded
			//
			$ext->add($context, $exten, '', new ext_system('rm ${ASTVARLIBDIR}/sounds/${RECFILE}.*'));
			$ext->add($context, $exten, '', new ext_record('${RECFILE}:wav'));
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_goto(1, 'confmenu'));

			$exten = 'docheck';
			
			$ext->add($context, $exten, '', new ext_playback('beep'));
			if (version_compare($version, "1.4", "ge")) { 
				$ext->add($context, $exten, 'dc_start', new ext_background('${RECFILE},m,${CHANNEL(language)},macro-systemrecording'));
			} else {
				$ext->add($context, $exten, 'dc_start', new ext_background('${RECFILE},m,${LANGUAGE},macro-systemrecording'));
			}
			$ext->add($context, $exten, '', new ext_wait(1));
			$ext->add($context, $exten, '', new ext_goto(1, 'confmenu'));

			$exten = 'confmenu';
			if (version_compare($version, "1.4", "ge")) { 
				$ext->add($context, $exten, '', new ext_background('to-listen-to-it&press-1&to-rerecord-it&press-star&astcc-followed-by-pound,m,${CHANNEL(language)},macro-systemrecording'));
			} else {
				$ext->add($context, $exten, '', new ext_background('to-listen-to-it&press-1&to-rerecord-it&press-star&astcc-followed-by-pound,m,${LANGUAGE},macro-systemrecording'));
			}
			$ext->add($context, $exten, '', new ext_read('RECRESULT', '', 1, '', '', 4));
			$ext->add($context, $exten, '', new ext_gotoif('$["x${RECRESULT}"="x*"]', 'dorecord,1'));
			$ext->add($context, $exten, '', new ext_gotoif('$["x${RECRESULT}"="x1"]', 'docheck,2'));
			$ext->add($context, $exten, '', new ext_goto(1));
			
			$ext->add($context, '1', '', new ext_goto('dc_start', 'docheck'));
			$ext->add($context, '*', '', new ext_goto(1, 'dorecord'));
			
			$ext->add($context, 't', '', new ext_playback('goodbye'));
			$ext->add($context, 't', '', new ext_hangup());
			
			$ext->add($context, 'i', '', new ext_playback('pm-invalid-option'));
			$ext->add($context, 'i', '', new ext_goto(1, 'confmenu'));

			$ext->add($context, 'h', '', new ext_hangup());

			
			$context = 'from-zaptel';
			$exten = '_X.';
			
			$ext->add($context, $exten, '', new ext_set('DID', '${EXTEN}'));
			$ext->add($context, $exten, '', new ext_goto(1, 's'));

			$exten = 's';
			$ext->add($context, $exten, '', new ext_noop('Entering from-zaptel with DID == ${DID}'));
			// Some trunks _require_ a RINGING be sent before an Answer. 
			$ext->add($context, $exten, '', new ext_ringing());
			// If ($did == "") { $did = "s"; }
			$ext->add($context, $exten, '', new ext_set('DID', '${IF($["${DID}"= ""]?s:${DID})}'));
			$ext->add($context, $exten, '', new ext_noop('DID is now ${DID}'));
			if ($chan_dahdi) {
				$ext->add($context, $exten, '', new ext_gotoif('$["${CHANNEL:0:5}"="DAHDI"]', 'zapok', 'notzap'));
			} else { 
				$ext->add($context, $exten, '', new ext_gotoif('$["${CHANNEL:0:3}"="Zap"]', 'zapok', 'notzap'));
			}
			$ext->add($context, $exten, 'notzap', new ext_goto('1', '${DID}', 'from-pstn'));
			// If there's no ext-did,s,1, that means there's not a no did/no cid route. Hangup.
			$ext->add($context, $exten, '', new ext_macro('Hangupcall', 'dummy'));
			$ext->add($context, $exten, 'zapok', new ext_noop('Is a Zaptel Channel'));
			if ($chan_dahdi) {
				$ext->add($context, $exten, '', new ext_set('CHAN', '${CHANNEL:6}'));
			} else { 
				$ext->add($context, $exten, '', new ext_set('CHAN', '${CHANNEL:4}'));
			}				
			$ext->add($context, $exten, '', new ext_set('CHAN', '${CUT(CHAN,-,1)}'));
			$ext->add($context, $exten, '', new ext_macro('from-zaptel-${CHAN}', '${DID},1'));
			// If nothing there, then treat it as a DID
			$ext->add($context, $exten, '', new ext_noop('Returned from Macro from-zaptel-${CHAN}'));
			$ext->add($context, $exten, '', new ext_goto(1, '${DID}', 'from-pstn'));
			$ext->add($context, 'fax', '', new ext_goto(1, 'in_fax', 'ext-fax'));

			/*
			* vm-callme context plays voicemail over telephone for web click-to-call
			* MSG and MBOX are channel variables that must be set when originating the call
			*/

			$context = 'vm-callme';

			$ext->add($context, 's', '', new ext_answer());
			$ext->add($context, 's', '', new ext_wait(1));
			$ext->add($context, 's', 'repeat', new ext_background('${MSG}&silence/2&vm-repeat&vm-starmain'));
			$ext->add($context, 's', '', new ext_waitexten(15));

			$ext->add($context, '5', '', new ext_goto('repeat', 's'));

			$ext->add($context, '#', '', new ext_playback('vm-goodbye'));
			$ext->add($context, '#', '', new ext_hangup());

			$ext->add($context, '*', '', new ext_macro('get-vmcontext', '${MBOX}'));
			$ext->add($context, '*', '', new ext_vmmain('${MBOX}@${VMCONTEXT},s'));

			$ext->add($context, 'i', '', new ext_playback('pm-invalid-option'));
			$ext->add($context, 'i', '', new ext_goto('repeat', 's'));

			$ext->add($context, 't', '', new ext_playback('vm-goodbye'));
			$ext->add($context, 't', '', new ext_hangup());

			$ext->add($context, 'h', '', new ext_hangup());

			/* end vm-callme context  */

			/*
			* macro-vm 
			*/
			
			/*
                        ;------------------------------------------------------------------------
                        ; [macro-vm]
                        ;------------------------------------------------------------------------
                        ; CONTEXT:      macro-vm
                        ; PURPOSE:      call voicemail system and extend with personal ivr
                        ;
                        ; Under normal use, this macro will call the voicemail system with the extension and
                        ; desired greeting mode of busy, unavailable or as specified with direct voicemail
                        ; calls (usually unavailable) when entered from destinations.
                        ;
                        ; The voicemail system's two greetings have been 'hijacked' as follows to extend the
                        ; system by giving the option of a private 'ivr' for each voicemail user. The following
                        ; applies to both the busy and unavailable modes of voicemail and can be applied to one
                        ; or both, and differently.
                        ;
                        ; Global Defaults:
                        ;
                        ; The following are default values, used in both busy and unavail modes if no specific
                        ; values are specified.
                        ;
                        ; VMX_REPEAT
                        ;                                       The number of times to repeat the users message if no option is pressed.
                        ; VMX_TIMEOUT
                        ;                                       The timeout to wait after playing message before repeating or giving up.
                        ; VMX_LOOPS
                        ;                                       The number of times it should replay the message and check for an option when
                        ;                                       an invalid option is pressed.
                        ;
                        ; VMX_OPTS_DOVM
                        ;                                       Default voicemail option to use if vm is chosen as an option. No options will
                        ;                                       cause Allison's generic message, 's' will go straight to beep.
                        ; VMX_OPTS_TIMEOUT
                        ;                                       Default voicemail option to use if it times out with no options. No options will
                        ;                                       cause Allison's generic message, 's' will go straight to beep.
                        ;                                       IF THE USER PRESSES # - it will look like a timeout as well since no option will
                        ;                                       be presented. If the user wishes to enable a mode where a caller can press #
                        ;                                       during their message and it goes straight to voicemail with only a 'beep' then
                        ;                                       this should be set to 's'.
                        ; VMX_OPTS_LOOPS
                        ;                                       Default voicemail option to use if to many wrong options occur. No options will
                        ;                                       cause Allison's generic message, 's' will go straight to beep.
                        ;
                        ; VMX_CONTEXT
                        ;                                       Default context for user destinations if not supplied in the user's settings
                        ; VMX_PRI
                        ;                                       Default priority for user destinations if not supplied in the user's settings
                        ;
                        ; VMX_TIMEDEST_CONTEXT
                        ;                                       Default context for timeout destination if not supplied in the user's settings
                        ; VMX_TIMEDEST_EXT
                        ;                                       Default extension for timeout destination if not supplied in the user's settings
                        ; VMX_TIMEDEST_PRI
                        ;                                       Default priority for timeout destination if not supplied in the user's settings
                        ;
                        ; VMX_LOOPDEST_CONTEXT
                        ;                                       Default context for loops  destination if not supplied in the user's settings
                        ; VMX_LOOPDEST_EXT
                        ;                                       Default extension for loops  destination if not supplied in the user's settings
                        ; VMX_LOOPDEST_PRI
                        ;                                       Default priority for loops  destination if not supplied in the user's settings
                        ;
                        ;
                        ; The AMPUSER database variable has been extended with a 'vmx' tree (vm-extension). A
                        ; duplicate set is included for both unavail and busy. You could choose for to have an
                        ; ivr when unavail is taken, but not with busy - or a different once with busy.
                        ; The full list is below, each specific entry is futher described:
                        ;
                        ; state:                Whether teh current mode is enabled or disabled. Anything but 'enabled' is
                        ;                                               treated as disabled.
                        ; repeat:               This is the number of times that the users message should be played after the
                        ;                                               timeout if the user has not entered anything. It is just a variable to the
                        ;                                               Read() function which will do the repeating.
                        ; timeout:      This is how long to wait after the message has been read for a response from
                        ;                                               the user. A caller can enter a digit any time during the playback.
                        ; loops:                This is the number of loops that the system will allow a caller to retry if
                        ;                                               they enter a bad menu choice, before going to the loop failover destination
                        ; vmxopts:      This is the vm options to send to the voicemail command used when a specific
                        ;                                               voicemail destination is chosen (inidcated by 'dovm' in the ext field). This is
                        ;                                               typically either set to 's' or left blank. When set to 's' there will be no
                        ;                                               message played when entering the voicemail, just a beep. When blank, you will
                        ;                                               have Allison's generic message played. It is not typical to play the greetings
                        ;                                               since they have been 'hijacked' for these IVR's and from a caller's perspecitive
                        ;                                               this system appears interconnected with the voicemail so instructions can be
                        ;                                               left there.
                        ; timedest: The three variables: ext, context and pri are the goto destination if the caller
                        ;                                               enters no options and it timesout. None have to be set and a system default
                        ;                                               will be used. If just ext is set, then defaults will be used for context and
                        ;                                               pri, etc.
                        ; loopdest:     This is identical to timedest but used if the caller exceeds the maximum invalid
                        ;                                               menu choices.
                        ; [0-9*]:               The user can specify up to 11 ivr options, all as single digits from 0-9 or *. The
                        ;                                               # key can not be used since it is used as a terminator key for the Read command
                        ;                                               and will never be returned. A minimum of the ext must be specified for each valid
                        ;                                               option and as above, the context and priority can also be specified if the default
                        ;                                               is not to be used.
                        ;                                               Option '0' takes on a special meaning. Since a user is able to break out of the
                        ;                                               voicemail command once entering it with a 0, if specified, the 0 destination will
                        ;                                               be used.
                        ;                                               Option '*' can also be used to breakout. It is undecided at this point whether
                        ;                                               providing that option will be used as well. (probably should).
                        ;
                        ;
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/state:                                                              enabled|disabled
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/repeat:                                                             n (times to repeat message)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/timeout:                                                    n (timeout to wait for digit)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/loops:                                                              n (loop returies for invalid entries)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/vmxopts/dovm:                                       vmoptions (if ext is dovm)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/vmxopts/timeout:                    vmoptions (if timeout)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/vmxopts/loops:                              vmoptions (if loops)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/timedest/ext:                                       extension (if timeout)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/timedest/context:                   context (if timeout)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/timedest/pri:                                       priority (if timeout)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/loopdest/ext:                                       extension (if too many failures)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/loopdest/context:                   context (if too many failures)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/loopdest/pri:                                       priority (if too many failures)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/[0-9*]/ext:                                         extension (dovm for vm access)
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/[0-9*]/context:                             context
                        ; /AMPUSER/<ext>/vmx/[busy|unavail]/[0-9*]/pri:                                         priority
                        ;------------------------------------------------------------------------

			*/
			// ARG1 - extension
			// ARG2 - DIRECTDIAL/BUSY
			// ARG3 - RETURN makes macro return, otherwise hangup
			//exten => s,1,Macro(user-callerid,SKIPTTL)
			$ext->add('macro-vm', 's', '', new ext_macro('user-callerid', 'SKIPTTL'));
			//exten => s,n,Set(VMGAIN=${IF($["foo${VM_GAIN}"!="foo"]?"g(${VM_GAIN})":"")})
			$ext->add('macro-vm','s', '', new ext_setvar("VMGAIN", '${IF($["foo${VM_GAIN}"!="foo"]?"g(${VM_GAIN})":"")}'));
			// If BLKVM_OVERRIDE is set, then someone told us to block calls from going to
			// voicemail. This variable is reset by the answering channel so subsequent
			// transfers will properly function.
			
			//exten => s,n,GotoIf($["foo${DB(${BLKVM_OVERRIDE})}" != "fooTRUE"]?vmx,1)
			$ext->add('macro-vm','s', '', new ext_gotoif('$["foo${DB(${BLKVM_OVERRIDE})}" != "fooTRUE"]','vmx,1'));

			// we didn't branch so block this from voicemail

			//exten => s,n,Noop(CAME FROM: ${NODEST} - Blocking VM cause of key: ${DB(BLKVM_OVERRIDE)})
			$ext->add('macro-vm','s', '', new ext_NoOp('CAME FROM: ${NODEST} - Blocking VM cause of key: ${DB(BLKVM_OVERRIDE)}')); 
			//exten => s,n,Hangup
			$ext->add('macro-vm','s', '', new ext_hangup(''));
			// If vmx not enabled for the current mode,then jump to normal voicemail behavior
			// also - if not message (no-msg) is requested, straight to voicemail

			//exten => vmx,1,GotoIf($["${ARG2}"="NOMESSAGE"]?s-${ARG2},1)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${ARG2}"="NOMESSAGE"]','s-${ARG2},1'));
			//exten => vmx,n,Set(MODE=${IF($["${ARG2}"="BUSY"]?busy:unavail)})
			$ext->add('macro-vm','vmx', '', new ext_setvar("MODE", '${IF($["${ARG2}"="BUSY"]?busy:unavail)}'));
			//exten => vmx,n,GotoIf($["${ARG2}" != "DIRECTDIAL"]?notdirect)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${ARG2}" != "DIRECTDIAL"]','notdirect'));
			//exten => vmx,n,Set(MODE=${IF($["${REGEX("[b]" ${VM_DDTYPE})}" = "1"]?busy:${MODE})})
			$ext->add('macro-vm','vmx', '', new ext_setvar("MODE", '${IF($["${REGEX("[b]" ${VM_DDTYPE})}" = "1"]?busy:${MODE})}'));
			//exten => vmx,n(notdirect),Noop(Checking if ext ${ARG1} is enabled: ${DB(AMPUSER/${ARG1}/vmx/${MODE}/state)})
			$ext->add('macro-vm','vmx', 'notdirect', new ext_NoOp('Checking if ext ${ARG1} is enabled: ${DB(AMPUSER/${ARG1}/vmx/${MODE}/state)}'));
			//exten => vmx,n,GotoIf($["${DB(AMPUSER/${ARG1}/vmx/${MODE}/state)}" != "enabled"]?s-${ARG2},1)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB(AMPUSER/${ARG1}/vmx/${MODE}/state)}" != "enabled"]','s-${ARG2},1'));
			// If the required voicemail file does not exist, then abort and go to normal voicemail behavior
			//
			// If 1.4 or above, use the STAT function to check for the file. Prior to 1.4, use the AGI script since the System() command tried
			// in the past had errors.
			//
			//exten => vmx,n,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm', 'vmx', '', new ext_macro('get-vmcontext', '${ARG1}'));			
			//;exten => vmx,n,TrySystem(/bin/ls ${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}.[wW][aA][vV])
			//$ext->add('macro-vm', 'vmx', '', new ext_trysystem('/bin/ls ${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}.[wW][aA][vV]'));
			if (version_compare($version, "1.4", "ge")) { 
				$ext->add('macro-vm','vmx', '', new ext_gotoif('$[(${STAT(f,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/temp.wav)} = 1) || (${STAT(f,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/temp.WAV)} = 1)]','tmpgreet'));
				$ext->add('macro-vm','vmx', '', new ext_gotoif('$[(${STAT(f,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}.wav)} = 0) && (${STAT(f,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}.WAV)} = 0)]','nofile'));
			} else {
				$ext->add('macro-vm', 'vmx', '',new ext_agi('checksound.agi,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/temp'));
				$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${SYSTEMSTATUS}" = "SUCCESS"]','tmpgreet'));
				$ext->add('macro-vm', 'vmx', '',new ext_agi('checksound.agi,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}'));
				$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${SYSTEMSTATUS}" != "SUCCESS"]','nofile'));
			}
			// Get the repeat, timeout and loop times to use if they are overriden form the global settings

			//exten => vmx,n,Set(LOOPCOUNT=0)
			$ext->add('macro-vm','vmx', '', new ext_setvar("LOOPCOUNT", '0'));			
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/repeat)}" = "0"]?vmxtime)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/repeat)}" = "0"]','vmxtime'));
			//exten => vmx,n,Set(VMX_REPEAT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_REPEAT", '${DB_RESULT}'));
			//exten => vmx,n(vmxtime),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timeout)}" = "0"]?vmxloops)
			$ext->add('macro-vm','vmx', 'vmxtime', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timeout)}" = "0"]','vmxloops'));
			//exten => vmx,n,Set(VMX_TIMEOUT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_TIMEOUT", '${DB_RESULT}'));
			//exten => vmx,n(vmxloops),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loops)}" = "0"]?vmxanswer)
			$ext->add('macro-vm','vmx', 'vmxloops', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loops)}" = "0"]','vmxanswer'));
			//exten => vmx,n,Set(VMX_LOOPS=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_LOOPS", '${DB_RESULT}'));
			//exten => vmx,n(vmxanswer),Answer()
			$ext->add('macro-vm','vmx','vmxanswer',new ext_answer(''));

			// Now play the users voicemail recording as the basis for their ivr, the Read command will repeat as needed and if it timesout
			// then we go to the timeout. Otherwise handle invalid options by looping until the limit until a valid option is played.

			//exten => vmx,n(loopstart),Read(ACTION,${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE},1,skip,${VMX_REPEAT},${VMX_TIMEOUT})
			$ext->add('macro-vm','vmx','loopstart',new ext_read('ACTION', '${ASTSPOOLDIR}/voicemail/${VMCONTEXT}/${ARG1}/${MODE}', 1, 'skip', '${VMX_REPEAT}', '${VMX_TIMEOUT}'));
			//exten => vmx,n,GotoIf($["${EXISTS(${ACTION})}" = "1"]?checkopt)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${EXISTS(${ACTION})}" = "1"]','checkopt'));
			// If we are here we timed out, go to the required destination

			//exten => vmx,n(noopt),Noop(Timeout: going to timeout dest)
			$ext->add('macro-vm','vmx', 'noopt', new ext_NoOp('Timeout: going to timeout dest'));			
			//exten => vmx,n,Set(VMX_OPTS=${VMX_OPTS_TIMEOUT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_OPTS", '${VMX_OPTS_TIMEOUT}'));
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/timeout)}" = "0"]?chktime)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/timeout)}" = "0"]','chktime'));
			//exten => vmx,n,Set(VMX_OPTS=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_OPTS", '${DB_RESULT}'));
			//exten => vmx,n(chktime),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/ext)}" = "0"]?dotime)
			$ext->add('macro-vm','vmx', 'chktime', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/ext)}" = "0"]','dotime'));
			//exten => vmx,n,Set(VMX_TIMEDEST_EXT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_TIMEDEST_EXT",'${DB_RESULT}'));	
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/context)}" = "0"]?timepri)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/context)}" = "0"]','timepri'));
			//exten => vmx,n,Set(VMX_TIMEDEST_CONTEXT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_TIMEDEST_CONTEXT",'${DB_RESULT}'));
			//exten => vmx,n(timepri),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/pri)}" = "0"]?dotime)
			$ext->add('macro-vm','vmx', 'timepri', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/timedest/pri)}" = "0"]','dotime'));
			//exten => vmx,n,Set(VMX_TIMEDEST_PRI=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_TIMEDEST_PRI",'${DB_RESULT}'));
			//exten => vmx,n(dotime),Goto(${VMX_TIMEDEST_CONTEXT},${VMX_TIMEDEST_EXT},${VMX_TIMEDEST_PRI})
			$ext->add('macro-vm','vmx','dotime',new ext_goto('${VMX_TIMEDEST_PRI}', '${VMX_TIMEDEST_EXT}', '${VMX_TIMEDEST_CONTEXT}'));
			// We got an option, check if the option is defined, or one of the system defaults

			//exten => vmx,n(checkopt),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/ext)}" = "1"]?doopt)
			$ext->add('macro-vm','vmx', 'checkopt', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/ext)}" = "1"]','doopt'));
			//exten => vmx,n,GotoIf($["${ACTION}" = "0"]?o,1)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${ACTION}" = "0"]','o,1'));
			//exten => vmx,n,GotoIf($["${ACTION}" = "*"]?adef,1)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${ACTION}" = "*"]','adef,1'));
			// Got invalid option loop until the max

			//exten => vmx,n,Set(LOOPCOUNT=$[${LOOPCOUNT} + 1])
			$ext->add('macro-vm','vmx', '', new ext_setvar("LOOPCOUNT",'$[${LOOPCOUNT} + 1]'));
			//exten => vmx,n,GotoIf($[${LOOPCOUNT} > ${VMX_LOOPS}]?toomany)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$[${LOOPCOUNT} > ${VMX_LOOPS}]','toomany'));
			//exten => vmx,n,Playback(pm-invalid-option&please-try-again)
			$ext->add('macro-vm','vmx','',new ext_playback('pm-invalid-option&please-try-again'));			
			//exten => vmx,n,Goto(loopstart)
			$ext->add('macro-vm','vmx','',new ext_goto('loopstart'));
			// tomany: to many invalid options, go to the specified destination

			//exten => vmx,n(toomany),Noop(Too Many invalid entries, got to invalid dest)
			$ext->add('macro-vm','vmx', 'toomany', new ext_NoOp('Too Many invalid entries, got to invalid dest'));			
			//exten => vmx,n,Set(VMX_OPTS=${VMX_OPTS_LOOPS})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_OPTS",'${VMX_OPTS_LOOPS}'));
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/loops)}" = "0"]?chkloop)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/loops)}" = "0"]','chkloop'));
			//exten => vmx,n,Set(VMX_OPTS=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_OPTS",'${DB_RESULT}'));
			//exten => vmx,n(chkloop),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/ext)}" = "0"]?doloop)
			$ext->add('macro-vm','vmx', 'chkloop', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/ext)}" = "0"]','doloop'));
			//exten => vmx,n,Set(VMX_LOOPDEST_EXT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_LOOPDEST_EXT",'${DB_RESULT}'));
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/context)}" = "0"]?looppri)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/context)}" = "0"]','looppri'));
			//exten => vmx,n,Set(VMX_LOOPDEST_CONTEXT=${DB_RESULT}) ;TODO make configurable per above
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_LOOPDEST_CONTEXT",'${DB_RESULT}'));
			//exten => vmx,n(looppri),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/pri)}" = "0"]?doloop)
			$ext->add('macro-vm','vmx', 'looppri', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/loopdest/pri)}" = "0"]','doloop'));
			//exten => vmx,n,Set(VMX_LOOPDEST_PRI=${DB_RESULT}) ;TODO make configurable per above
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_LOOPDEST_PRI",'${DB_RESULT}'));
			//exten => vmx,n(doloop),Goto(${VMX_LOOPDEST_CONTEXT},${VMX_LOOPDEST_EXT},${VMX_LOOPDEST_PRI})
			$ext->add('macro-vm','vmx','doloop',new ext_goto('${VMX_LOOPDEST_PRI}','${VMX_LOOPDEST_EXT}','${VMX_LOOPDEST_CONTEXT}'));
			// doopt: execute the valid option that was chosen

			//exten => vmx,n(doopt),Noop(Got a valid option: ${DB_RESULT})
			$ext->add('macro-vm','vmx', 'doopt', new ext_NoOp('Got a valid option: ${DB_RESULT}'));			
			//exten => vmx,n,Set(VMX_EXT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_EXT",'${DB_RESULT}'));

			// Special case, if this option was to go to voicemail, set options and go

			//exten => vmx,n,GotoIf($["${VMX_EXT}" != "dovm"]?getdest)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${VMX_EXT}" != "dovm"]','getdest'));
			//exten => vmx,n(vmxopts),Set(VMX_OPTS=${VMX_OPTS_DOVM})
			$ext->add('macro-vm','vmx', 'vmxopts', new ext_setvar("VMX_OPTS",'${VMX_OPTS_DOVM}'));
			//exten => vmx,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/dovm)}" = "0"]?vmxdovm)
			$ext->add('macro-vm','vmx', '', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/vmxopts/dovm)}" = "0"]','vmxdovm'));
			//exten => vmx,n(vmxopts),Set(VMX_OPTS=${DB_RESULT})
			$ext->add('macro-vm','vmx', 'vmxopts', new ext_setvar("VMX_OPTS",'${DB_RESULT}'));
			//exten => vmx,n(vmxdovm),goto(dovm,1)
			$ext->add('macro-vm','vmx','vmxdovm',new ext_goto('1','dovm'));			

			// General case, setup the goto destination and go there (no error checking, its up to the GUI's to assure
			// reasonable values

			//exten => vmx,n(getdest),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/context)}" = "0"]?vmxpri)
			$ext->add('macro-vm','vmx', 'getdest', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/context)}" = "0"]','vmxpri'));
			//exten => vmx,n,Set(VMX_CONTEXT=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_CONTEXT",'${DB_RESULT}'));
			//exten => vmx,n(vmxpri),GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/pri)}" = "0"]?vmxgoto)
			$ext->add('macro-vm','vmx', 'vmxpri', new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/${ACTION}/pri)}" = "0"]','vmxgoto'));
			//exten => vmx,n,Set(VMX_PRI=${DB_RESULT})
			$ext->add('macro-vm','vmx', '', new ext_setvar("VMX_PRI",'${DB_RESULT}'));
			//exten => vmx,n(vmxgoto),Goto(${VMX_CONTEXT},${VMX_EXT},${VMX_PRI})
			$ext->add('macro-vm','vmx','vmxgoto',new ext_goto('${VMX_PRI}','${VMX_EXT}','${VMX_CONTEXT}'));
			// If the required voicemail file is not present, then revert to normal voicemail
			// behavior treating as if it was not set

			//exten => vmx,n(nofile),Noop(File for mode: ${MODE} does not exist, SYSTEMSTATUS: ${SYSTEMSTATUS}, going to normal voicemail)
			$ext->add('macro-vm','vmx', 'nofile', new ext_NoOp('File for mode: ${MODE} does not exist, SYSTEMSTATUS: ${SYSTEMSTATUS}, going to normal voicemail'));
			//exten => vmx,n,Goto(s-${ARG2},1)
			$ext->add('macro-vm','vmx','',new ext_goto('1','s-${ARG2}'));
			//exten => vmx,n(tmpgreet),Noop(Temporary Greeting Detected, going to normal voicemail)
			$ext->add('macro-vm','vmx', 'tmpgreet', new ext_NoOp('Temporary Greeting Detected, going to normal voicemail'));
			//exten => vmx,n,Goto(s-${ARG2},1)
			$ext->add('macro-vm','vmx','',new ext_goto('1','s-${ARG2}'));

			// Drop into voicemail either as a direct destination (in which case VMX_OPTS might be set to something) or
			// if the user timed out or broke out of the loop then VMX_OPTS is always cleared such that an Allison
			// message is played and the caller know's what is going on.

			//exten => dovm,1,Noop(VMX Timeout - go to voicemail)
			$ext->add('macro-vm','dovm', '', new ext_NoOp('VMX Timeout - go to voicemail'));
			//exten => dovm,n,Voicemail(${ARG1}@${VMCONTEXT},${VMX_OPTS}${VMGAIN}) ; no flags, so allison plays please leave ...
			$ext->add('macro-vm','dovm', '',new ext_vm('${ARG1}@${VMCONTEXT},${VMX_OPTS}${VMGAIN}')); 
			//exten => dovm,n,Goto(exit-${VMSTATUS},1)
			$ext->add('macro-vm','dovm', '',new ext_goto('1','exit-${VMSTATUS}'));

			//exten => s-BUSY,1,NoOp(BUSY voicemail)
			$ext->add('macro-vm','s-BUSY','',new ext_NoOp('BUSY voicemail'));
			//exten => s-BUSY,n,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm','s-BUSY','',new ext_macro('get-vmcontext','${ARG1}'));
			//exten => s-BUSY,n,Voicemail(${ARG1}@${VMCONTEXT},${VM_OPTS}b${VMGAIN})   ; Voicemail Busy message
			$ext->add('macro-vm','s-BUSY', '',new ext_vm('${ARG1}@${VMCONTEXT},${VM_OPTS}b${VMGAIN}'));
			//exten => s-BUSY,n,Goto(exit-${VMSTATUS},1)
			 $ext->add('macro-vm','s-BUSY', '',new ext_goto('1','exit-${VMSTATUS}'));

			//exten => s-NOMESSAGE,1,NoOp(NOMESSAGE (beeb only) voicemail)
			$ext->add('macro-vm','s-NOMESSAGE','',new ext_NoOp('NOMESSAGE (beeb only) voicemail'));
			//exten => s-NOMESSAGE,n,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm','s-NOMESSAGE','',new ext_macro('get-vmcontext','${ARG1}'));
			//exten => s-NOMESSAGE,n,Voicemail(${ARG1}@${VMCONTEXT},s${VM_OPTS}${VMGAIN})
			$ext->add('macro-vm','s-NOMESSAGE','',new ext_vm('${ARG1}@${VMCONTEXT},s${VM_OPTS}${VMGAIN}'));
			//exten => s-NOMESSAGE,n,Goto(exit-${VMSTATUS},1)
			$ext->add('macro-vm','s-NOMESSAGE','',new ext_goto('1','exit-${VMSTATUS}'));

			//exten => s-DIRECTDIAL,1,NoOp(DIRECTDIAL voicemail)
			$ext->add('macro-vm','s-DIRECTDIAL','',new ext_NoOp('DIRECTDIAL voicemail'));
			//exten => s-DIRECTDIAL,n,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm','s-DIRECTDIAL','',new ext_macro('get-vmcontext','${ARG1}'));
			//exten => s-DIRECTDIAL,n,Voicemail(${ARG1}@${VMCONTEXT},${VM_OPTS}${VM_DDTYPE}${VMGAIN})
			$ext->add('macro-vm','s-DIRECTDIAL','',new ext_vm('${ARG1}@${VMCONTEXT},${VM_OPTS}${VM_DDTYPE}${VMGAIN}'));
			//exten => s-DIRECTDIAL,n,Goto(exit-${VMSTATUS},1)
			$ext->add('macro-vm','s-DIRECTDIAL','',new ext_goto('1','exit-${VMSTATUS}'));

			//exten => _s-.,1,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm','_s-.','',new ext_macro('get-vmcontext','${ARG1}'));
			//exten => _s-.,n,Voicemail(${ARG1}@${VMCONTEXT},${VM_OPTS}u${VMGAIN})     ; Voicemail Unavailable message
			$ext->add('macro-vm','_s-.','',new ext_vm('${ARG1}@${VMCONTEXT},${VM_OPTS}u${VMGAIN}'));
			//exten => _s-.,n,Goto(exit-${VMSTATUS},1)
			$ext->add('macro-vm','_s-.','',new ext_goto('1','exit-${VMSTATUS}'));
			// If the user has a 0 option defined, use that for operator zero-out from within voicemail
			// as well to keep it consistant with the menu structure

			//exten => o,1,Background(one-moment-please)      ; 0 during vm message will hangup
			$ext->add('macro-vm','o','',new ext_playback('one-moment-please'));
			//exten => o,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/ext)}" = "0"]?doopdef)
			$ext->add('macro-vm','o','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/ext)}" = "0"]','doopdef'));
			//exten => o,n,Set(VMX_OPDEST_EXT=${DB_RESULT})
			$ext->add('macro-vm','o','',new ext_setvar("VMX_OPDEST_EXT",'${DB_RESULT}'));
			//exten => o,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/context)}" = "1"]?opcontext)
			$ext->add('macro-vm','o','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/context)}" = "1"]','opcontext'));
			//exten => o,n,Set(DB_RESULT=${VMX_CONTEXT})
			$ext->add('macro-vm','o','',new ext_setvar("DB_RESULT",'${VMX_CONTEXT}'));
			//exten => o,n(opcontext),Set(VMX_OPDEST_CONTEXT=${DB_RESULT})
			$ext->add('macro-vm','o','opcontext',new ext_setvar("VMX_OPDEST_CONTEXT",'${DB_RESULT}'));
			//exten => o,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/pri)}" = "1"]?oppri)
			$ext->add('macro-vm','o','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/0/pri)}" = "1"]','oppri'));
			//exten => o,n,Set(DB_RESULT=${VMX_PRI})
			$ext->add('macro-vm','o','',new ext_setvar("DB_RESULT",'${VMX_PRI}'));
			//exten => o,n(oppri),Set(VMX_OPDEST_PRI=${DB_RESULT})
			$ext->add('macro-vm','o','oppri',new ext_setvar("VMX_OPDEST_PRI",'${DB_RESULT}'));
			//exten => o,n,Goto(${VMX_OPDEST_CONTEXT},${VMX_OPDEST_EXT},${VMX_OPDEST_PRI})
			$ext->add('macro-vm','o','',new ext_goto('${VMX_OPDEST_PRI}','${VMX_OPDEST_EXT}','${VMX_OPDEST_CONTEXT}'));
			//exten => o,n(doopdef),GotoIf($["x${OPERATOR_XTN}"="x"]?nooper:from-internal,${OPERATOR_XTN},1)
			$ext->add('macro-vm','o','doopdef',new ext_gotoif('$["x${OPERATOR_XTN}"="x"]','nooper','from-internal,${OPERATOR_XTN},1'));
			//exten => o,n(nooper),GotoIf($["x${FROM_DID}"="x"]?nodid)
			$ext->add('macro-vm','o','nooper',new ext_gotoif('$["x${FROM_DID}"="x"]','nodid'));
			//exten => o,n,Dial(Local/${FROM_DID}@from-pstn)
			$ext->add('macro-vm','o','',new ext_dial('Local/${FROM_DID}@from-pstn',''));
			//exten => o,n,Macro(hangup)
			$ext->add('macro-vm','o','',new ext_macro('hangup'));
			//exten => o,n(nodid),Dial(Local/s@from-pstn)
			$ext->add('macro-vm','o','nodid',new ext_dial('Local/s@from-pstn',''));
			//exten => o,n,Macro(hangup)
			$ext->add('macro-vm','o','',new ext_macro('hangup'));

			// If the user has a * option defined, use that for the * out from within voicemail
			// as well to keep it consistant with the menu structure

			//exten => a,1,Macro(get-vmcontext,${ARG1})
			$ext->add('macro-vm','a','',new ext_macro('get-vmcontext','${ARG1}'));
			//exten => a,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/ext)}" = "0"]?adef,1)
			$ext->add('macro-vm','a','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/ext)}" = "0"]','adef,1'));
			/**/
			//exten => a,n,Set(VMX_ADEST_EXT=${DB_RESULT})
			$ext->add('macro-vm','a','',new ext_setvar("VMX_ADEST_EXT",'${DB_RESULT}'));
			//exten => a,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/context)}" = "1"]?acontext)
			$ext->add('macro-vm','a','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/context)}" = "1"]','acontext'));
			/**/
			//exten => a,n,Set(DB_RESULT=${VMX_CONTEXT})
			$ext->add('macro-vm','a','',new ext_setvar("DB_RESULT",'${VMX_CONTEXT}'));
			//exten => a,n(acontext),Set(VMX_ADEST_CONTEXT=${DB_RESULT})
			$ext->add('macro-vm','a','acontext',new ext_setvar("VMX_ADEST_CONTEXT",'${DB_RESULT}'));

			//exten => a,n,GotoIf($["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/pri)}" = "1"]?apri)
			$ext->add('macro-vm','a','',new ext_gotoif('$["${DB_EXISTS(AMPUSER/${ARG1}/vmx/${MODE}/*/pri)}" = "1"]','apri'));
			/**/
			//exten => a,n,Set(DB_RESULT=${VMX_PRI})
			$ext->add('macro-vm','a','',new ext_setvar("DB_RESULT",'${VMX_PRI}'));
			//exten => a,n(apri),Set(VMX_ADEST_PRI=${DB_RESULT})
			$ext->add('macro-vm','a','apri',new ext_setvar("VMX_ADEST_PRI",'${DB_RESULT}'));
			//exten => a,n,Goto(${VMX_ADEST_CONTEXT},${VMX_ADEST_EXT},${VMX_ADEST_PRI})
			$ext->add('macro-vm','a','',new ext_goto('${VMX_ADEST_PRI}','${VMX_ADEST_EXT}','${VMX_ADEST_CONTEXT}'));
			//exten => adef,1,VoiceMailMain(${ARG1}@${VMCONTEXT})
			$ext->add('macro-vm','adef','',new ext_vmmain('${ARG1}@${VMCONTEXT}'));
			//exten => adef,n,GotoIf($["${ARG3}" = "RETURN"]?exit-RETURN,1)
			$ext->add('macro-vm','adef','',new ext_gotoif('$["${ARG3}" = "RETURN"]','exit-RETURN,1'));
			//exten => adef,n,Hangup
			$ext->add('macro-vm','adef','',new ext_hangup(''));

			//exten => exit-FAILED,1,Playback(im-sorry&an-error-has-occured)
			$ext->add('macro-vm','exit-FAILED','',new ext_playback('im-sorry&an-error-has-occured'));
			//exten => exit-FAILED,n,GotoIf($["${ARG3}" = "RETURN"]?exit-RETURN,1)
			$ext->add('macro-vm','exit-FAILED','',new ext_gotoif('$["${ARG3}" = "RETURN"]','exit-RETURN,1'));
			//exten => exit-FAILED,n,Hangup()
			$ext->add('macro-vm','exit-FAILED','',new ext_hangup(''));

			//exten => exit-SUCCESS,1,GotoIf($["${ARG3}" = "RETURN"]?exit-RETURN,1)
			$ext->add('macro-vm','exit-SUCCESS','',new ext_gotoif('$["${ARG3}" = "RETURN"]','exit-RETURN,1'));
			//exten => exit-SUCCESS,n,Playback(goodbye)
			$ext->add('macro-vm','exit-SUCCESS','',new ext_playback('goodbye'));
			//exten => exit-SUCCESS,n,Hangup()
			$ext->add('macro-vm','exit-SUCCESS','',new ext_hangup(''));

			//exten => exit-USEREXIT,1,GotoIf($["${ARG3}" = "RETURN"]?exit-RETURN,1)
			$ext->add('macro-vm','exit-USEREXIT','',new ext_gotoif('$["${ARG3}" = "RETURN"]','exit-RETURN,1'));
			//exten => exit-USEREXIT,n,Playback(goodbye)
			$ext->add('macro-vm','exit-USEREXIT','',new ext_playback('goodbye'));
			//exten => exit-USEREXIT,n,Hangup()
			$ext->add('macro-vm','exit-USEREXIT','',new ext_hangup(''));

			//exten => exit-RETURN,1,Noop(Returning From Voicemail because macro)
			$ext->add('macro-vm','exit-RETURN','',new ext_noop('Returning From Voicemail because macro'));
			//exten => t,1,Hangup()
			$ext->add('macro-vm','t','',new ext_hangup(''));

			/* end macro-vm  */
									
		break;
	}
}

/* begin page.ampusers.php functions */

function core_ampusers_add($username, $password, $extension_low, $extension_high, $deptname, $sections) {
	$sql = "INSERT INTO ampusers (username, password, extension_low, extension_high, deptname, sections) VALUES (";
	$sql .= "'".$username."',";
	$sql .= "'".$password."',";
	$sql .= "'".$extension_low."',";
	$sql .= "'".$extension_high."',";
	$sql .= "'".$deptname."',";
	$sql .= "'".implode(";",$sections)."');";
	
	sql($sql,"query");
}

function core_ampusers_del($username) {
	$sql = "DELETE FROM ampusers WHERE username = '".$username."'";
	sql($sql,"query");
}

function core_ampusers_list() {
	$sql = "SELECT username FROM ampusers ORDER BY username";
	return sql($sql,"getAll");
}

/* end page.ampusers.php functions */









/* begin page.did.php functions */

function core_did_list($order='extension'){
	switch ($order) {
		case 'description':
			$sql = "SELECT * FROM incoming ORDER BY description,extension,cidnum";
			break;
		case 'extension':
		default:
			$sql = "SELECT * FROM incoming ORDER BY extension,cidnum";
	}
	return sql($sql,"getAll",DB_FETCHMODE_ASSOC);
}

function core_did_get($extension="",$cidnum=""){
	global $db;
	$cidnum = $db->escapeSimple($cidnum);
	$sql = "SELECT * FROM incoming WHERE cidnum = \"$cidnum\" AND extension = \"$extension\"";
	return sql($sql,"getRow",DB_FETCHMODE_ASSOC);
}

function core_did_del($extension,$cidnum){
	global $db;
	$cidnum = $db->escapeSimple($cidnum);
	$sql="DELETE FROM incoming WHERE cidnum = \"$cidnum\" AND extension = \"$extension\"";
	sql($sql);
}

function core_did_edit($old_extension,$old_cidnum, $incoming){
	global $db;

	$old_extension = $db->escapeSimple(trim($old_extension));
	$old_cidnum = $db->escapeSimple(trim($old_cidnum));

	$incoming['extension'] = trim($incoming['extension']);
	$incoming['cidnum'] = trim($incoming['cidnum']);

	$extension = $db->escapeSimple($incoming['extension']);
	$cidnum = $db->escapeSimple($incoming['cidnum']);

	// if did or cid changed, then check to make sure that this pair is not already being used.
	//
	if (($extension != $old_extension) || ($cidnum != $old_cidnum)) {
		$existing=core_did_get($extension,$cidnum);
	}

	if (empty($existing)) {
		core_did_del($old_extension,$old_cidnum);
		core_did_add($incoming);
		return true;
	} else {
		echo "<script>javascript:alert('"._("A route for this DID/CID already exists!")." => ".$existing['extension']."/".$existing['cidnum']."')</script>";
	}
	return false;
}

function core_did_add($incoming,$target=false){
	global $db;
	foreach ($incoming as $key => $val) { ${$key} = $db->escapeSimple($val); } // create variables from request

	// Check to make sure the did is not being used elsewhere
	//
	$existing=core_did_get($extension,$cidnum);

	if (empty($existing)) {
		$destination= ($target) ? $target : ${$goto0.'0'};
		$sql="INSERT INTO incoming (cidnum,extension,destination,faxexten,faxemail,answer,wait,privacyman,alertinfo, ringing, mohclass, description, grppre, delay_answer, pricid) values ('$cidnum','$extension','$destination','$faxexten','$faxemail','$answer','$wait','$privacyman','$alertinfo', '$ringing', '$mohclass', '$description', '$grppre', '$delay_answer', '$pricid')";
		sql($sql);
		return true;
	} else {
		echo "<script>javascript:alert('"._("A route for this DID/CID already exists!")." => ".$existing['extension']."/".$existing['cidnum']."')</script>";
		return false;
	}
}

/* end page.did.php functions */


/* begin page.devices.php functions */

//get the existing devices
function core_devices_list($tech="all",$detail=false,$get_all=false) {
	if (strtolower($detail) == 'full') {
		$sql = "SELECT * FROM devices";
	} else {
		$sql = "SELECT id,description FROM devices";
	}
	switch (strtoupper($tech)) {
		case "IAX":
			$sql .= " WHERE tech = 'iax2'";
			break;
		case "IAX2":
		case "SIP":
		case "ZAP":
			$sql .= " WHERE tech = '".strtolower($tech)."'";
			break;
		case "ALL":
		default:
	}
	$sql .= ' ORDER BY id';
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	$extens = null;
	foreach($results as $result){
		if ($get_all || checkRange($result['id'])){

			$record = array();
			$record[0] = $result['id'];  // for backwards compatibility
			$record[1] = $result['description'];  // for backwards compatibility
			foreach ($result as $key => $value) {
				$record[$key] = $value;
			}
			$extens[] = $record;
			/*
			$extens[] = array(
				0=>$result[0],  // for backwards compatibility
				1=>$result[1],
				'id'=>$result[0], // FETCHMODE_ASSOC emulation
				'description'=>$result[1],
			);
			*/
		}
	}
	return $extens;
}


function core_devices_add($id,$tech,$dial,$devicetype,$user,$description,$emergency_cid=null,$editmode=false){
	global $amp_conf;
	global $currentFile;
	global $astman;
	global $db;
	
	$display = isset($_REQUEST['display'])?$_REQUEST['display']:'';

	if (trim($id) == '' ) {
		if ($display != 'extensions') {
			echo "<script>javascript:alert('"._("You must put in a device id")."');</script>";
		}
		return false;
	}
	
	//ensure this id is not already in use
	$devices = core_devices_list();
	if (is_array($devices)) {
		foreach($devices as $device) {
			if ($device[0] === $id) {
				if ($display <> 'extensions') echo "<script>javascript:alert('"._("This device id is already in use")."');</script>";
				return false;
			}
		}
	}
	//unless defined, $dial is TECH/id
	if ( $dial == '' ) {
		//zap is an exception
		if ( strtolower($tech) == "zap" ) {
			$zapchan = $_REQUEST['devinfo_channel'] != '' ? $_REQUEST['devinfo_channel'] : $_REQUEST['channel'];
			$dial = 'ZAP/'.$zapchan;
		} else {
			$dial = strtoupper($tech)."/".$id;
		}
	}
	
	//check to see if we are requesting a new user
	if ($user == "new") {
		$user = $id;
		$jump = true;
	}
	
	if(!get_magic_quotes_gpc()) {
		if(!empty($emergency_cid))
			$emergency_cid = $db->escapeSimple($emergency_cid);
		if(!empty($description))
			$description = $db->escapeSimple($description);
	}
	
	//insert into devices table
	$sql="INSERT INTO devices (id,tech,dial,devicetype,user,description,emergency_cid) values (\"$id\",\"$tech\",\"$dial\",\"$devicetype\",\"$user\",\"$description\",\"$emergency_cid\")";
	sql($sql);
	
	//add details to astdb
	if ($astman) {
		// if adding or editting a fixed device, user property should always be set
		if ($devicetype == 'fixed' || !$editmode) {
			$astman->database_put("DEVICE",$id."/user",$user);
		}
		// If changing from a fixed to an adhoc, the user property should be intialized
		// to the new default, not remain as the previous fixed user
		if ($editmode) {
			$previous_type = $astman->database_get("DEVICE",$id."/type");
			if ($previous_type == 'fixed' && $devicetype == 'adhoc') {
				$astman->database_put("DEVICE",$id."/user",$user);
			}
		}
		$astman->database_put("DEVICE",$id."/dial",$dial);
		$astman->database_put("DEVICE",$id."/type",$devicetype);
		$astman->database_put("DEVICE",$id."/default_user",$user);
		if($emergency_cid != '') {
			$astman->database_put("DEVICE",$id."/emergency_cid","\"".$emergency_cid."\"");
		}

		if ($user != "none") {
			$existingdevices = $astman->database_get("AMPUSER",$user."/device");
			if (empty($existingdevices)) {
				$astman->database_put("AMPUSER",$user."/device",$id);
			} else {
				$existingdevices_array = explode('&',$existingdevices);
				if (!in_array($id, $existingdevices_array)) {
					$existingdevices_array[]=$id;
					$existingdevices = implode('&',$existingdevices_array);
					$astman->database_put("AMPUSER",$user."/device",$existingdevices);
				}
			}
		}

	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
	
	// create a voicemail symlink if needed
	$thisUser = core_users_get($user);
	if(isset($thisUser['voicemail']) && ($thisUser['voicemail'] != "novm")) {
		if(empty($thisUser['voicemail'])) {
			$vmcontext = "default";
		} else { 
			$vmcontext = $thisUser['voicemail'];
		}
		
		//voicemail symlink
		exec("rm -f /var/spool/asterisk/voicemail/device/".$id);
		exec("/bin/ln -s /var/spool/asterisk/voicemail/".$vmcontext."/".$user."/ /var/spool/asterisk/voicemail/device/".$id);
	}
		
	//take care of sip/iax/zap config
	$funct = "core_devices_add".strtolower($tech);
	if(function_exists($funct)){
		$funct($id);
	}
	
/*	if($user != "none") {
		core_hint_add($user);
	}*/
	
	//if we are requesting a new user, let's jump to users.php
	if (isset($jump)) {
		echo("<script language=\"JavaScript\">window.location=\"config.php?display=users&extdisplay={$id}&name={$description}\";</script>");
	}
	return true;
}

function core_devices_del($account,$editmode=false){
	global $amp_conf;
	global $currentFile;
	global $astman;
	
	//get all info about device
	$devinfo = core_devices_get($account);
	
	//delete details to astdb
	if ($astman) {
		// If a user was selected, remove this device from the user
		$deviceuser = $astman->database_get("DEVICE",$account."/user");
		if (isset($deviceuser) && $deviceuser != "none") {
			// Remove the device record from the user's device list
			$userdevices = $astman->database_get("AMPUSER",$deviceuser."/device");

			// We need to remove just this user and leave the rest alone
			$userdevicesarr = explode("&", $userdevices);
			$userdevicesarr_hash = array_flip($userdevicesarr);
			unset($userdevicesarr_hash[$account]);
			$userdevicesarr = array_flip($userdevicesarr_hash);
			$userdevices = implode("&", $userdevicesarr);
			
			if (empty($userdevices)) {
					$astman->database_del("AMPUSER",$deviceuser."/device");
			} else {
					$astman->database_put("AMPUSER",$deviceuser."/device",$userdevices);
			}
		}
		if (! $editmode) {
			$astman->database_del("DEVICE",$account."/dial");
			$astman->database_del("DEVICE",$account."/type");
			$astman->database_del("DEVICE",$account."/user");
			$astman->database_del("DEVICE",$account."/default_user");
			$astman->database_del("DEVICE",$account."/emergency_cid");
		}

		//delete from devices table
		$sql="DELETE FROM devices WHERE id = \"$account\"";
		sql($sql);

		//voicemail symlink
		exec("rm -f /var/spool/asterisk/voicemail/device/".$account);
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
	
	//take care of sip/iax/zap config
	$funct = "core_devices_del".strtolower($devinfo['tech']);
	if(function_exists($funct)){
		$funct($account);
	}
}

function core_devices_get($account){
	//get all the variables for the meetme
	$sql = "SELECT * FROM devices WHERE id = '$account'";
	$results = sql($sql,"getRow",DB_FETCHMODE_ASSOC);
	
	//take care of sip/iax/zap config
	$funct = "core_devices_get".strtolower($results['tech']);
	if (!empty($results['tech']) && function_exists($funct)) {
		$devtech = $funct($account);
		if (is_array($devtech)){
			$results = array_merge($results,$devtech);
		}
	}
	
	return $results;
}

// this function rebuilds the astdb based on device table contents
// used on devices.php if action=resetall
function core_devices2astdb(){
	global $astman;
	global $amp_conf;

	$sql = "SELECT * FROM devices";
	$devresults = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	//add details to astdb
	if ($astman) {
		$astman->database_deltree("DEVICE");
		foreach ($devresults as $dev) {
			extract($dev);	
			$astman->database_put("DEVICE",$id."/dial",$dial);
			$astman->database_put("DEVICE",$id."/type",$devicetype);
			$astman->database_put("DEVICE",$id."/user",$user);		
			$astman->database_put("DEVICE",$id."/default_user",$user);
			if(trim($emergency_cid) != '') {
				$astman->database_put("DEVICE",$id."/emergency_cid","\"".$emergency_cid."\"");
			}
			// If a user is selected, add this device to the user
			if ($user != "none") {
					$existingdevices = $astman->database_get("AMPUSER",$user."/device");
					if (!empty($existingdevices)) {
							$existingdevices .= "&";
					}
					$astman->database_put("AMPUSER",$user."/device",$existingdevices.$id);
			}
			
			// create a voicemail symlink if needed
			$thisUser = core_users_get($user);
			if(isset($thisUser['voicemail']) && ($thisUser['voicemail'] != "novm")) {
				if(empty($thisUser['voicemail']))
					$vmcontext = "default";
				else 
					$vmcontext = $thisUser['voicemail'];
				//voicemail symlink
				exec("rm -f /var/spool/asterisk/voicemail/device/".$id);
				exec("/bin/ln -s /var/spool/asterisk/voicemail/".$vmcontext."/".$user."/ /var/spool/asterisk/voicemail/device/".$id);
			}
		}
		return true;
	} else {
		return false;
	}
}

// this function rebuilds the astdb based on users table contents
// used on devices.php if action=resetall
function core_users2astdb(){
	global $amp_conf;
	global $astman;
	global $db;

	$sql = "SELECT * FROM users";
	$userresults = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
	
	//add details to astdb
	if ($astman) {
		foreach($userresults as $usr) {
			extract($usr);
			$astman->database_put("AMPUSER",$extension."/password",$password);
			$astman->database_put("AMPUSER",$extension."/ringtimer",$ringtimer);
			$astman->database_put("AMPUSER",$extension."/noanswer",$noanswer);
			$astman->database_put("AMPUSER",$extension."/recording",$recording);
			$astman->database_put("AMPUSER",$extension."/outboundcid","\"".$db->escapeSimple($outboundcid)."\"");
			$astman->database_put("AMPUSER",$extension."/cidname","\"".$db->escapeSimple($name)."\"");
			$astman->database_put("AMPUSER",$extension."/voicemail","\"".$voicemail."\"");
		}	
		return true;
	} else {
		return false;
	}

//	TODO: this was...	
// 	return $astman->disconnect();
//	is "true" the correct value...?
}

//add to sip table
function core_devices_addsip($account) {
	global $db;
	global $currentFile;

	foreach ($_REQUEST as $req=>$data) {
		if ( substr($req, 0, 8) == 'devinfo_' ) {
			$keyword = substr($req, 8);
			if ( $keyword == 'dial' && $data == '' ) {
				$sipfields[] = array($account, $keyword, 'SIP/'.$account);
			} elseif ($keyword == 'mailbox' && $data == '') {
				$sipfields[] = array($account,'mailbox',$account.'@device');
			} else {
				$sipfields[] = array($account, $keyword, $data);
			}
		}
	}
	
	if ( !is_array($sipfields) ) { // left for compatibilty....lord knows why !
		$sipfields = array(
			//array($account,'account',$account),
			array($account,'accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
			array($account,'secret',(isset($_REQUEST['secret']))?$_REQUEST['secret']:''),
			array($account,'canreinvite',(isset($_REQUEST['canreinvite']))?$_REQUEST['canreinvite']:'no'),
			array($account,'context',(isset($_REQUEST['context']))?$_REQUEST['context']:'from-internal'),
			array($account,'dtmfmode',(isset($_REQUEST['dtmfmode']))?$_REQUEST['dtmfmode']:''),
			array($account,'host',(isset($_REQUEST['host']))?$_REQUEST['host']:'dynamic'),
			array($account,'type',(isset($_REQUEST['type']))?$_REQUEST['type']:'friend'),
			array($account,'mailbox',(isset($_REQUEST['mailbox']) && !empty($_REQUEST['mailbox']))?$_REQUEST['mailbox']:$account.'@device'),
			array($account,'username',(isset($_REQUEST['username']))?$_REQUEST['username']:$account),
			array($account,'nat',(isset($_REQUEST['nat']))?$_REQUEST['nat']:'yes'),
			array($account,'port',(isset($_REQUEST['port']))?$_REQUEST['port']:'5060'),
			array($account,'qualify',(isset($_REQUEST['qualify']))?$_REQUEST['qualify']:'yes'),
			array($account,'callgroup',(isset($_REQUEST['callgroup']))?$_REQUEST['callgroup']:''),
			array($account,'pickupgroup',(isset($_REQUEST['pickupgroup']))?$_REQUEST['pickupgroup']:''),
			array($account,'deny',(isset($_REQUEST['deny']))?$_REQUEST['deny']:''),
			array($account,'permit',(isset($_REQUEST['permit']))?$_REQUEST['permit']:''),			
			array($account,'disallow',(isset($_REQUEST['disallow']))?$_REQUEST['disallow']:''),
			array($account,'allow',(isset($_REQUEST['allow']))?$_REQUEST['allow']:'')
			//array($account,'record_in',(isset($_REQUEST['record_in']))?$_REQUEST['record_in']:'On-Demand'),
			//array($account,'record_out',(isset($_REQUEST['record_out']))?$_REQUEST['record_out']:'On-Demand'),
			//array($account,'callerid',(isset($_REQUEST['description']))?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>')
		);
	}

	// Very bad
	$sipfields[] = array($account,'account',$account);	
	$sipfields[] = array($account,'callerid',(isset($_REQUEST['description']) && $_REQUEST['description'])?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>');
	
	// Where is this in the interface ??????
	$sipfields[] = array($account,'record_in',($_REQUEST['record_in'])?$_REQUEST['record_in']:'On-Demand');
	$sipfields[] = array($account,'record_out',($_REQUEST['record_out'])?$_REQUEST['record_out']:'On-Demand');

	$compiled = $db->prepare('INSERT INTO sip (id, keyword, data) values (?,?,?)');
	$result = $db->executeMultiple($compiled,$sipfields);
	if(DB::IsError($result)) {
		die_freepbx($result->getDebugInfo()."<br><br>".'error adding to SIP table');	
	}
}

function core_devices_delsip($account) {
	global $db;
	global $currentFile;
	
	$sql = "DELETE FROM sip WHERE id = '$account'";
	$result = $db->query($sql);
	
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
}

function core_devices_getsip($account) {
	global $db;
	$sql = "SELECT keyword,data FROM sip WHERE id = '$account'";
	$results = $db->getAssoc($sql);
	if(DB::IsError($results)) {
		$results = null;
	}
	
	return $results;
}

//add to iax table
function core_devices_addiax2($account) {
	global $db;
	global $currentFile;
	
	foreach ($_REQUEST as $req=>$data) {
		if ( substr($req, 0, 8) == 'devinfo_' ) {
			$keyword = substr($req, 8);
			if ( $keyword == 'dial' && $data == '' ) {
				$iaxfields[] = array($account, $keyword, 'IAX2/'.$account);
			} elseif ($keyword == 'mailbox' && $data == '') {
				$iaxfields[] = array($account,'mailbox',$account.'@device');
			} else {
				$iaxfields[] = array($account, $keyword, $data);
			}
		}
	}
	
	if ( !is_array($iaxfields) ) { // left for compatibilty....lord knows why !
		$iaxfields = array(
			//array($account,'account',$account),
			array($account,'secret',($_REQUEST['secret'])?$_REQUEST['secret']:''),
			array($account,'notransfer',($_REQUEST['notransfer'])?$_REQUEST['notransfer']:'yes'),
			array($account,'context',($_REQUEST['context'])?$_REQUEST['context']:'from-internal'),
			array($account,'host',($_REQUEST['host'])?$_REQUEST['host']:'dynamic'),
			array($account,'type',($_REQUEST['type'])?$_REQUEST['type']:'friend'),
			array($account,'mailbox',($_REQUEST['mailbox'])?$_REQUEST['mailbox']:$account.'@device'),
			array($account,'username',($_REQUEST['username'])?$_REQUEST['username']:$account),
			array($account,'port',($_REQUEST['port'])?$_REQUEST['port']:'4569'),
			array($account,'qualify',($_REQUEST['qualify'])?$_REQUEST['qualify']:'yes'),
			array($account,'deny',(isset($_REQUEST['deny']))?$_REQUEST['deny']:''),
			array($account,'permit',(isset($_REQUEST['permit']))?$_REQUEST['permit']:''),			
			array($account,'disallow',($_REQUEST['disallow'])?$_REQUEST['disallow']:''),
			array($account,'allow',($_REQUEST['allow'])?$_REQUEST['allow']:''),
			array($account,'accountcode',($_REQUEST['accountcode'])?$_REQUEST['accountcode']:'')
			//array($account,'record_in',($_REQUEST['record_in'])?$_REQUEST['record_in']:'On-Demand'),
			//array($account,'record_out',($_REQUEST['record_out'])?$_REQUEST['record_out']:'On-Demand'),
			//array($account,'callerid',($_REQUEST['description'])?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>')
		);
	}

	// Very bad
	$iaxfields[] = array($account,'account',$account);	
	$iaxfields[] = array($account,'callerid',(isset($_REQUEST['description']) && $_REQUEST['description'] != '')?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>');
	// Asterisk treats no caller ID from an IAX device as 'hide callerid', and ignores the caller ID
	// set in iax.conf. As we rely on this for pretty much everything, we need to specify the 
	// callerid as a variable which gets picked up in macro-callerid.
	// Ref - http://bugs.digium.com/view.php?id=456
	$iaxfields[] = array($account,'setvar',"REALCALLERIDNUM=$account");
	
	// Where is this in the interface ??????
	$iaxfields[] = array($account,'record_in',($_REQUEST['record_in'])?$_REQUEST['record_in']:'On-Demand');
	$iaxfields[] = array($account,'record_out',($_REQUEST['record_out'])?$_REQUEST['record_out']:'On-Demand');
	
	$compiled = $db->prepare('INSERT INTO iax (id, keyword, data) values (?,?,?)');
	$result = $db->executeMultiple($compiled,$iaxfields);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage()."<br><br>error adding to IAX table");	
	}
}

function core_devices_deliax2($account) {
	global $db;
	global $currentFile;
	
	$sql = "DELETE FROM iax WHERE id = '$account'";
	$result = $db->query($sql);
	
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
}

function core_devices_getiax2($account) {
	global $db;
	$sql = "SELECT keyword,data FROM iax WHERE id = '$account'";
	$results = $db->getAssoc($sql);
	if(DB::IsError($results)) {
		$results = null;
	}
	
	return $results;
}

function core_devices_addzap($account) {
	global $db;
	global $currentFile;
		
	foreach ($_REQUEST as $req=>$data) {
		if ( substr($req, 0, 8) == 'devinfo_' ) {
			$keyword = substr($req, 8);
			if ( $keyword == 'dial' && $data == '' ) {
				$zapchan = $_REQUEST['devinfo_channel'] != '' ? $_REQUEST['devinfo_channel'] : $_REQUEST['channel'];
				$zapfields[] = array($account, $keyword, 'ZAP/'.$zapchan);
			} elseif ($keyword == 'mailbox' && $data == '') {
				$zapfields[] = array($account,'mailbox',$account.'@device');
			} else {
				$zapfields[] = array($account, $keyword, $data);
			}
		}
	}
	
	if ( !is_array($zapfields) ) { // left for compatibilty....lord knows why !
		$zapfields = array(
			//array($account,'account',$account),
			array($account,'context',($_REQUEST['context'])?$_REQUEST['context']:'from-internal'),
			array($account,'mailbox',($_REQUEST['mailbox'])?$_REQUEST['mailbox']:$account.'@device'),
			//array($account,'callerid',($_REQUEST['description'])?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>'),
			array($account,'immediate',($_REQUEST['immediate'])?$_REQUEST['immediate']:'no'),
			array($account,'signalling',($_REQUEST['signalling'])?$_REQUEST['signalling']:'fxo_ks'),
			array($account,'echocancel',($_REQUEST['echocancel'])?$_REQUEST['echocancel']:'yes'),
			array($account,'echocancelwhenbridged',($_REQUEST['echocancelwhenbridged'])?$_REQUEST['echocancelwhenbridged']:'no'),
			array($account,'immediate',($_REQUEST['immediate'])?$_REQUEST['immediate']:'no'),	
			array($account,'echotraining',($_REQUEST['echotraining'])?$_REQUEST['echotraining']:'800'),
			array($account,'busydetect',($_REQUEST['busydetect'])?$_REQUEST['busydetect']:'no'),
			array($account,'busycount',($_REQUEST['busycount'])?$_REQUEST['busycount']:'7'),
			array($account,'callprogress',($_REQUEST['callprogress'])?$_REQUEST['callprogress']:'no'),
			//array($account,'record_in',($_REQUEST['record_in'])?$_REQUEST['record_in']:'On-Demand'),	
			//array($account,'record_out',($_REQUEST['record_out'])?$_REQUEST['record_out']:'On-Demand'),
			array($account,'accountcode',(isset($_REQUEST['accountcode']))?$_REQUEST['accountcode']:''),
			array($account,'callgroup',(isset($_REQUEST['callgroup']))?$_REQUEST['callgroup']:''),
			array($account,'pickupgroup',(isset($_REQUEST['pickupgroup']))?$_REQUEST['pickupgroup']:''),
			array($account,'channel',($_REQUEST['channel'])?$_REQUEST['channel']:'')
		);
	}

	// Very bad
	$zapfields[] = array($account,'account',$account);	
	$zapfields[] = array($account,'callerid',($_REQUEST['description'])?$_REQUEST['description']." <".$account.'>':'device'." <".$account.'>');
	
	// Where is this in the interface ??????
	$zapfields[] = array($account,'record_in',($_REQUEST['record_in'])?$_REQUEST['record_in']:'On-Demand');
	$zapfields[] = array($account,'record_out',($_REQUEST['record_out'])?$_REQUEST['record_out']:'On-Demand');

	$compiled = $db->prepare('INSERT INTO zap (id, keyword, data) values (?,?,?)');
	$result = $db->executeMultiple($compiled,$zapfields);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage()."<br><br>error adding to ZAP table");	
	}
}

function core_devices_delzap($account) {
	global $db;
	global $currentFile;
	
	$sql = "DELETE FROM zap WHERE id = '$account'";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
}

function core_devices_getzap($account) {
	global $db;
	$sql = "SELECT keyword,data FROM zap WHERE id = '$account'";
	$results = $db->getAssoc($sql);
	if(DB::IsError($results)) {
		$results = null;
	}
	return $results;
}
/* end page.devices.php functions */




function core_hint_get($account){
	global $astman;

	$chan_dahdi = ast_with_dahdi();
	// We should always check the AMPUSER in case they logged into a device
	// but we will fall back to the old methond if $astman not open although
	// I'm pretty sure everything else will puke anyhow if not running
	//
	if ($astman) {
		$device=$astman->database_get("AMPUSER",$account."/device");
		$device_arr = explode('&',$device);
		$sql = "SELECT dial from devices where id in ('".implode("','",$device_arr)."')";
	} else {
		$sql = "SELECT dial from devices where user = '{$account}'";
	}
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);
	
	//create an array of strings
	if (is_array($results)){
		foreach ($results as $result) {
			if ($chan_dahdi) {
				$dial[] = str_replace('ZAP', 'DAHDI', $result['dial']);
			} else {
				$dial[] = $result['dial'];
			}
		}
	}
	
	//create a string with & delimiter
	if (isset($dial) && is_array($dial)){
		$hint = implode($dial,"&");
	} else {
		if (isset($results[0]['dial'])) {
			$hint = $results[0]['dial'];
		} else {
			$hint = null;
		}
	}
	
	return $hint;
}



/* begin page.users.php functions */

// get the existing extensions
// the returned arrays contain [0]:extension [1]:name
function core_users_list() {
	$results = sql("SELECT extension,name,voicemail FROM users ORDER BY extension","getAll");

	//only allow extensions that are within administrator's allowed range
	foreach($results as $result){
		if (checkRange($result[0])){
			$extens[] = array($result[0],$result[1],$result[2]);
		}
	}
	
	if (isset($extens)) {
		sort($extens);
		return $extens;
	} else {
		return null;
	}
}

function core_check_extensions($exten=true) {
	global $amp_conf;

	$extenlist = array();
	if (is_array($exten) && empty($exten)) {
		return $extenlist;
	}
	$sql = "SELECT extension, name FROM users ";
	if (is_array($exten)) {
		$sql .= "WHERE extension in ('".implode("','",$exten)."')";
	}
	$sql .= " ORDER BY extension";
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	foreach ($results as $result) {
		$thisexten = $result['extension'];
		$extenlist[$thisexten]['description'] = _("User Extension: ").$result['name'];
		$extenlist[$thisexten]['status'] = 'INUSE';
		$display = ($amp_conf['AMPEXTENSIONS'] == "deviceanduser")?'users':'extensions';
		$extenlist[$thisexten]['edit_url'] = "config.php?type=setup&display=$display&extdisplay=".urlencode($thisexten)."&skip=0";
	}
	return $extenlist;
}

function core_check_destinations($dest=true) {
	global $active_modules;

	$destlist = array();
	if (is_array($dest) && empty($dest)) {
		return $destlist;
	}
	$sql = "SELECT extension, cidnum, description, destination FROM incoming ";
	if ($dest !== true) {
		$sql .= "WHERE destination in ('".implode("','",$dest)."')";
	}
	$sql .= "ORDER BY extension, cidnum";
	$results = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	//$type = isset($active_modules['announcement']['type'])?$active_modules['announcement']['type']:'setup';

	foreach ($results as $result) {
		$thisdest = $result['destination'];
		$thisid   = $result['extension'].'/'.$result['cidnum'];
		$destlist[] = array(
			'dest' => $thisdest,
			'description' => sprintf(_("Inbound Route: %s (%s)"),$result['description'],$thisid),
			'edit_url' => 'config.php?display=did&extdisplay='.urlencode($thisid),
		);
	}
	return $destlist;
}

function core_sipname_check($sipname, $extension) {
	global $db;
	if (!isset($sipname) || trim($sipname)=='')
		return true;

	$sql = "SELECT sipname FROM users WHERE sipname = '$sipname' AND extension != '$extension'";
	$results = $db->getRow($sql,DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
        die_freepbx($results->getMessage().$sql);
	}
	
	if (isset($results['sipname']) && trim($results['sipname']) == $sipname) 
		return false;
	else
		return true;
}

function core_users_add($vars, $editmode=false) {
	extract($vars);
	
	global $db;
	global $amp_conf;
	global $astman;

	$thisexten = isset($thisexten) ? $thisexten : '';

	if (trim($extension) == '' ) {
		echo "<script>javascript:alert('"._("You must put in an extension (or user) number")."');</script>";
		return false;
	}

	//ensure this id is not already in use
	$extens = core_users_list();
	if(is_array($extens)) {
		foreach($extens as $exten) {
			if ($exten[0]===$extension) {
				echo "<script>javascript:alert('".sprintf(_("This user/extension %s is already in use"),$extension)."');</script>";
				return false;
			}
		}
	}

	$newdid_name = isset($newdid_name) ? $db->escapeSimple($newdid_name) : '';
	$newdid = isset($newdid) ? $newdid : '';
	$newdid = preg_replace("/[^0-9._XxNnZz\[\]\-\+]/" ,"", trim($newdid));

	$newdidcid = isset($newdidcid) ? trim($newdidcid) : '';
	if (!preg_match('/^priv|^block|^unknown|^restrict|^unavail|^anonym/',strtolower($newdidcid))) {
		$newdidcid = preg_replace("/[^0-9._XxNnZz\[\]\-\+]/" ,"", $newdidcid);
	}

	// Well more ugliness since the javascripts are already in here
	if ($newdid != '' || $newdidcid != '') {
		$existing = core_did_get($newdid, $newdidcid);
		if (! empty($existing)) {
			echo "<script>javascript:alert('".sprintf(_("A route with this DID/CID: %s/%s already exists"),$existing['extension'],$existing['cidnum'])."')</script>";
			return false;
		}
	}

	$sipname = preg_replace("/\s/" ,"", trim($sipname));
	if (! core_sipname_check($sipname, $extension)) {
		echo "<script>javascript:alert('"._("This sipname: {$sipname} is already in use")."');</script>";
		return false;
	}
	
	//build the recording variable
	$recording = "out=".$record_out."|in=".$record_in;
	
	//escape quotes and any other bad chars:
	if(!get_magic_quotes_gpc()) {
		$outboundcid = $db->escapeSimple($outboundcid);
		$name = $db->escapeSimple($name);
	}

	//if voicemail is enabled, set the box@context to use
	//havn't checked but why is voicemail needed on users anyway?  Doesn't exactly make it modular !
	if ( function_exists('voicemail_mailbox_get') ) {
		$vmbox = voicemail_mailbox_get($extension);
		if ( $vmbox == null ) {
			$voicemail = "novm";
		} else {
			$voicemail = $vmbox['vmcontext'];
		}
	}

	// Clean replace any <> with () in display name - should have javascript stopping this but ...
	//
	$name = preg_replace(array('/</','/>/'), array('(',')'), trim($name));
	
	//insert into users table
	$sql="INSERT INTO users (extension,password,name,voicemail,ringtimer,noanswer,recording,outboundcid,sipname) values (\"";
	$sql.= "$extension\", \"";
	$sql.= isset($password)?$password:'';
	$sql.= "\", \"";
	$sql.= isset($name)?$name:'';
	$sql.= "\", \"";
	$sql.= isset($voicemail)?$voicemail:'default';
	$sql.= "\", \"";
	$sql.= isset($ringtimer)?$ringtimer:'';
	$sql.= "\", \"";
	$sql.= isset($noanswer)?$noanswer:'';
	$sql.= "\", \"";
	$sql.= isset($recording)?$recording:'';
	$sql.= "\", \"";
	$sql.= isset($outboundcid)?$outboundcid:'';
	$sql.= "\", \"";
	$sql.= isset($sipname)?$sipname:'';
	$sql.= "\")";
	sql($sql);

	//write to astdb
	if ($astman) {
		$cid_masquerade = (isset($cid_masquerade) && trim($cid_masquerade) != "")?trim($cid_masquerade):$extension;
		$astman->database_put("AMPUSER",$extension."/password",isset($password)?$password:'');
		$astman->database_put("AMPUSER",$extension."/ringtimer",isset($ringtimer)?$ringtimer:'');
		$astman->database_put("AMPUSER",$extension."/noanswer",isset($noanswer)?$noanswer:'');
		$astman->database_put("AMPUSER",$extension."/recording",isset($recording)?$recording:'');
		$astman->database_put("AMPUSER",$extension."/outboundcid",isset($outboundcid)?"\"".$outboundcid."\"":'');
		$astman->database_put("AMPUSER",$extension."/cidname",isset($name)?"\"".$name."\"":'');
		$astman->database_put("AMPUSER",$extension."/cidnum",$cid_masquerade);
		$astman->database_put("AMPUSER",$extension."/voicemail","\"".isset($voicemail)?$voicemail:''."\"");
		switch ($call_screen) {
			case '0':
				$astman->database_del("AMPUSER",$extension."/screen");
				break;
			case 'nomemory':
				$astman->database_put("AMPUSER",$extension."/screen","\"nomemory\"");
				break;
			case 'memory':
				$astman->database_put("AMPUSER",$extension."/screen","\"memory\"");
				break;
			default:
		}

		if (!$editmode) {
			$astman->database_put("AMPUSER",$extension."/device","\"".((isset($device))?$device:'')."\"");
		}

		if (trim($callwaiting) == 'enabled') {
			$astman->database_put("CW",$extension,"\"ENABLED\"");
		} else if (trim($callwaiting) == 'disabled') {
			$astman->database_del("CW",$extension);
		} else {
			echo "ERROR: this state should not exist<br>";
		}

		// Moved VmX setup to voicemail module since it is part of voicemail
		//
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}

	// OK - got this far, if they entered a new inbound DID/CID let's deal with it now
	// remember - in the nice and ugly world of this old code, $vars has been extracted
	// newdid and newdidcid

	// Now if $newdid is set we need to add the DID to the routes
	//
	if ($newdid != '' || $newdidcid != '') {
		$did_dest                = 'from-did-direct,'.$extension.',1';
		$did_vars['extension']   = $newdid;
		$did_vars['cidnum']      = $newdidcid;
		$did_vars['faxexten']    = '';
		$did_vars['faxemail']    = '';
		$did_vars['answer']      = '0';
		$did_vars['wait']        = '0';
		$did_vars['privacyman']  = '';
		$did_vars['alertinfo']   = '';
		$did_vars['ringing']     = '';
		$did_vars['mohclass']    = 'default';
		$did_vars['description'] = $newdid_name;
		$did_vars['grppre']      = '';
		$did_vars['delay_answer']= '0';
		$did_vars['pricid']= '';
		core_did_add($did_vars, $did_dest);
	}

	return true;
}

function core_users_get($extension){
	global $db;
	global $amp_conf;
	global $astman;
	//get all the variables for the meetme
	$sql = "SELECT * FROM users WHERE extension = '$extension'";
	$results = $db->getRow($sql,DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage().$sql);
	}
	if (empty($results)) {
		return array();
	}
	
	//explode recording vars
	$recording = explode("|",$results['recording']);
	if (isset($recording[1])) {
		$recout = substr($recording[0],4);
		$recin = substr($recording[1],3);
		$results['record_in']=$recin;
		$results['record_out']=$recout;
	} else {
		$results['record_in']='Adhoc';
		$results['record_out']='Adhoc';
	}
	if ($astman) {
		$cw = $astman->database_get("CW",$extension);
		$results['callwaiting'] = (trim($cw) == 'ENABLED') ? 'enabled' : 'disabled';
		$cid_masquerade=$astman->database_get("AMPUSER",$extension."/cidnum");
		$results['cid_masquerade'] = (trim($cid_masquerade) != "")?$cid_masquerade:$extension;

		$call_screen=$astman->database_get("AMPUSER",$extension."/screen");
		$results['call_screen'] = (trim($call_screen) != "")?$call_screen:'0';
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}

	return $results;
}

function core_users_del($extension, $editmode=false){
	global $db;
	global $amp_conf;
	global $astman;
	
	//delete from devices table
	$sql="DELETE FROM users WHERE extension = \"$extension\"";
	$results = $db->query($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage().$sql);
	}

	//delete details to astdb
	if($astman)  {
		$astman->database_del("AMPUSER",$extension."/screen");
	}
	if ($astman && !$editmode) {
		// TODO just change this to delete everything
		$astman->database_deltree("AMPUSER/".$extension);
	}
}

function core_users_directdid_get($directdid=""){
	return array();
}

function core_users_cleanastdb($extension) {
	// This is called to remove any ASTDB traces of the user after a deletion. Otherwise,
	// call forwarding, call waiting settings could hang around and bite someone if they
	// recycle an extension. Is called from page.xtns and page.users.
	global $amp_conf;
	global $astman;

	if ($astman) {
		$astman->database_del("CW",$extension);
		$astman->database_del("CF",$extension);
		$astman->database_del("CFB",$extension);
		$astman->database_del("CFU",$extension);

	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
}

function core_users_edit($extension,$vars){
	global $db;
	global $amp_conf;
	global $astman;
	
	//I we are editing, we need to remember existing user<->device mapping, so we can delete and re-add
	if ($astman) {
		$ud = $astman->database_get("AMPUSER",$extension."/device");
		$current_vmcontext = $astman->database_get("AMPUSER",$extension."/voicemail");
		$new_vmcontext = isset($vars['vmcontext']) ? $vars['vmcontext'] : 'novm';
		$vars['device'] = $ud;
	} else {
		fatal("Cannot connect to Asterisk Manager with ".$amp_conf["AMPMGRUSER"]."/".$amp_conf["AMPMGRPASS"]);
	}
	
	// clean and check the did to make sure it is not being used by another extension or in did routing
	//
	$newdid_name = isset($newdid_name) ? $db->escapeSimple($newdid_name) : '';
	$newdid = isset($vars['newdid']) ? $vars['newdid'] : '';
	$newdid = preg_replace("/[^0-9._XxNnZz\[\]\-\+]/" ,"", trim($newdid));

	$newdidcid = isset($vars['newdidcid']) ? trim($vars['newdidcid']) : '';
	if (!preg_match('/^priv|^block|^unknown|^restrict|^unavail|^anonym/',strtolower($newdidcid))) {
		$newdidcid = preg_replace("/[^0-9._XxNnZz\[\]\-\+]/" ,"", $newdidcid);
	}

	// Well more ugliness since the javascripts are already in here
	if ($newdid != '' || $newdidcid != '') {
		$existing = core_did_get($newdid, $newdidcid);
		if (! empty($existing)) {
			echo "<script>javascript:alert('".sprintf(_("A route with this DID/CID: %s/%s already exists"),$existing['extension'],$existing['cidnum'])."')</script>";
			return false;
		}
	}

	//delete and re-add
	if (core_sipname_check($vars['sipname'],$extension)) {
		core_users_del($extension, true);
		core_users_add($vars, true);

		// If the vmcontext has changed, we need to change all the links. In extension mode, the link
		// to the current fixed device will get changed, but none others will
		//
		if ($current_vmcontext != $new_vmcontext) {
			$user_devices = explode('&',$ud);
			foreach ($user_devices as $user_device) {
				exec("rm -f /var/spool/asterisk/voicemail/device/".$user_device);
				if ($new_context != 'novm') {
					exec("/bin/ln -s /var/spool/asterisk/voicemail/".$new_vmcontext."/".$extension."/ /var/spool/asterisk/voicemail/device/".$user_device);
				}
			}
		}
	}
	return true;
}

function core_directdid_list(){
	return array();
}

function core_zapchandids_add($description, $channel, $did) {
	global $db;


	if (!ctype_digit(trim($channel)) || trim($channel) == '') {
		echo "<script>javascript:alert('"._("Invalid Channel Number, must be numeric and not blank")."')</script>";
		return false;
	}
	if (trim($did) == '') {
		echo "<script>javascript:alert('"._("Invalid DID, must be a non-blank DID")."')</script>";
		return false;
	}

	$description = q($description);
	$channel     = q($channel);
	$did         = q($did);

	$sql = "INSERT INTO zapchandids (channel, description, did) VALUES ($channel, $description, $did)";
	$results = $db->query($sql);
	if (DB::IsError($results)) {
		if ($results->getCode() == DB_ERROR_ALREADY_EXISTS) {
			echo "<script>javascript:alert('"._("Error Duplicate Channel Entry")."')</script>";
			return false;
		} else {
			die_freepbx($results->getMessage()."<br><br>".$sql);
		}
	}
	return true;
}

function core_zapchandids_edit($description, $channel, $did) {
	global $db;

	$description = q($description);
	$channel     = q($channel);
	$did         = q($did);

	$sql = "UPDATE zapchandids SET description = $description, did = $did WHERE channel = $channel";
	$results = $db->query($sql);
	if (DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>".$sql);
	}
	return true;
}

function core_zapchandids_delete($channel) {
	global $db;

	$channel     = q($channel);

	$sql = "DELETE FROM zapchandids WHERE channel = $channel";
	$results = $db->query($sql);
	if (DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>".$sql);
	}
	return true;
}

function core_zapchandids_list() {
	global $db;

	$sql = "SELECT * FROM zapchandids ORDER BY channel";
	return sql($sql,"getAll",DB_FETCHMODE_ASSOC);
}

function core_zapchandids_get($channel) {
	global $db;

	$channel     = q($channel);

	$sql = "SELECT * FROM zapchandids WHERE channel = $channel";
	return sql($sql,"getRow",DB_FETCHMODE_ASSOC);
}

/* end page.users.php functions */





/* begin page.trunks.php functions */

// we're adding ,don't require a $trunknum
function core_trunks_add($tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $keepcid, $failtrunk, $disabletrunk) {
	global $db;
	
	// find the next available ID
	$trunknum = 1;

	// This is pretty ugle, will fix when we redo trunks and routes with proper uniqueids.
	// get the list, sort them, then look for a hole and use it, or overflow to the end if
	// not and use that
	//
	$trunk_hash = array();
	foreach(core_trunks_list() as $trunk) {
		$trunknum = ltrim($trunk[0],"OUT_");
		$trunk_hash[] = $trunknum;
	}
	sort($trunk_hash);
	$trunknum = 1;
	foreach ($trunk_hash as $trunk_id) {
		if ($trunk_id != $trunknum) {
			break;
		}
		$trunknum++;
	}
	
	core_trunks_backendAdd($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $keepcid, $failtrunk, $disabletrunk);
	
	return $trunknum;
}

function core_trunks_del($trunknum, $tech = null) {
	global $db;
	
	if ($tech === null) { // in EditTrunk, we get this info anyways
		$tech = core_trunks_getTrunkTech($trunknum);
	}

	//delete from globals table
	sql("DELETE FROM globals WHERE variable IN ('OUT_$trunknum','OUTCID_$trunknum','OUTMAXCHANS_$trunknum','OUTPREFIX_$trunknum','OUTKEEPCID_$trunknum','OUTFAIL_$trunknum','OUTDISABLE_$trunknum')");
	
	// conditionally, delete from iax or sip
	switch (strtolower($tech)) {
		case "iax2":
      $tech = "iax";
      // fall through
		case "iax":
		case "iax2":
			sql("DELETE FROM iax WHERE id = '9999$trunknum' OR id = '99999$trunknum' OR id = '9999999$trunknum'");
		break;
		case "sip": 
			sql("DELETE FROM sip WHERE id = '9999$trunknum' OR id = '99999$trunknum' OR id = '9999999$trunknum'");
		break;
	}
}

function core_trunks_edit($trunknum, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $keepcid, $failtrunk, $disabletrunk) {
	//echo "editTrunk($trunknum, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register)";
	$tech = core_trunks_getTrunkTech($trunknum);
  if ($tech == "") {
    return false;
  }
	core_trunks_del($trunknum, $tech);
	core_trunks_backendAdd($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $keepcid, $failtrunk, $disabletrunk);
}

// just used internally by addTrunk() and editTrunk()
//obsolete
function core_trunks_backendAdd($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register, $keepcid, $failtrunk, $disabletrunk) {
	global $db;
	
	if  (is_null($dialoutprefix)) $dialoutprefix = ""; // can't be NULL
	
	//echo  "backendAddTrunk($trunknum, $tech, $channelid, $dialoutprefix, $maxchans, $outcid, $peerdetails, $usercontext, $userconfig, $register)";
	
	// change iax to "iax2" (only spot we actually store iax2, since its used by Dial()..)
	$techtemp = ((strtolower($tech) == "iax") ? "iax2" : $tech);
	$outval = (($techtemp == "custom") ? "AMP:".$channelid : strtoupper($techtemp).'/'.$channelid);
	
	
	$glofields = array(
			array('OUT_'.$trunknum, $outval),
			array('OUTPREFIX_'.$trunknum, $dialoutprefix),
			array('OUTMAXCHANS_'.$trunknum, $maxchans),
			array('OUTCID_'.$trunknum, $outcid),
			array('OUTKEEPCID_'.$trunknum, $keepcid),
			array('OUTFAIL_'.$trunknum, $failtrunk),
			array('OUTDISABLE_'.$trunknum, $disabletrunk),
			);
			
	unset($techtemp); 
	
	$compiled = $db->prepare('INSERT INTO globals (variable, value) values (?,?)');
	$result = $db->executeMultiple($compiled,$glofields);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage()."<br><br>".$sql);
	}
	
	$disable_flag = ($disabletrunk == "on")?1:0;
	
	switch (strtolower($tech)) {
		case "iax":
		case "iax2":
			core_trunks_addSipOrIax($peerdetails,'iax',$channelid,$trunknum,$disable_flag);
			if ($usercontext != ""){
				core_trunks_addSipOrIax($userconfig,'iax',$usercontext,'9'.$trunknum,$disable_flag);
			}
			if ($register != ""){
				core_trunks_addRegister($trunknum,'iax',$register,$disable_flag);
			}
		break;
		case "sip":
			core_trunks_addSipOrIax($peerdetails,'sip',$channelid,$trunknum,$disable_flag);
			if ($usercontext != ""){
				core_trunks_addSipOrIax($userconfig,'sip',$usercontext,'9'.$trunknum,$disable_flag);
			}
			if ($register != ""){
				core_trunks_addRegister($trunknum,'sip',$register,$disable_flag);
			}
		break;
	}	
}

function core_trunks_getTrunkTech($trunknum) {

	$results = sql("SELECT value FROM globals WHERE variable = 'OUT_".$trunknum."'","getAll");
	if (!$results) {
		return false;
	}
	if(strpos($results[0][0],"AMP:") === 0) {  //custom trunks begin with AMP:
		$tech = "custom";
	} else {
		$tech = strtolower( strtok($results[0][0],'/') ); // the technology.  ie: ZAP/g0 is ZAP
		
		if ($tech == "iax2") $tech = "iax"; // same thing, here
	}
	return $tech;
}

//add trunk info to sip or iax table
function core_trunks_addSipOrIax($config,$table,$channelid,$trunknum,$disable_flag=0) {
	global $db;
	
	$confitem['account'] = $channelid;
	$gimmieabreak = nl2br($config);
	$lines = split('<br />',$gimmieabreak);
	foreach ($lines as $line) {
		$line = trim($line);
		if (count(split('=',$line)) > 1) {
			$tmp = split('=',$line,2);
			$key=trim($tmp[0]);
			$value=trim($tmp[1]);
			if (isset($confitem[$key]) && !empty($confitem[$key]))
				$confitem[$key].="&".$value;
			else
				$confitem[$key]=$value;
		}
	}
	// rember 1=disabled so we start at 2 (1 + the first 1)
	$seq = 1;
	foreach($confitem as $k=>$v) {
		$seq = ($disable_flag == 1) ? 1 : $seq+1;
		$dbconfitem[]=array($k,$v,$seq);
	}
	$compiled = $db->prepare("INSERT INTO $table (id, keyword, data, flags) values ('9999$trunknum',?,?,?)");
	$result = $db->executeMultiple($compiled,$dbconfitem);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage()."<br><br>INSERT INTO $table (id, keyword, data, flags) values ('9999$trunknum',?,?,'$disable_flag')");	
	}
}

//Sort trunks for sqlite
function sort_trunks($a,$b)  {
        global $unique_trunks;
        ereg("OUT_([0-9]+)",$unique_trunks[$a][0],$trunk_num1);
        ereg("OUT_([0-9]+)",$unique_trunks[$b][0],$trunk_num2);
        return ($trunk_num1[1] >= $trunk_num2[1]? 1:-1);
}

//get unique trunks
function core_trunks_list($assoc = false) {
	// TODO: $assoc default to true, eventually..

	global $db;
	global $amp_conf;
	
        // sqlite doesn't support the syntax required for the SQL so we have to do it the hard way
        if ($amp_conf["AMPDBENGINE"] == "sqlite3")
        {
		$sqlstr = "SELECT variable, value FROM globals WHERE variable LIKE 'OUT\_%' ESCAPE '\'";
                $my_unique_trunks = sql($sqlstr,"getAll",DB_FETCHMODE_ASSOC);

		$sqlstr = "SELECT variable, value FROM globals WHERE variable LIKE 'OUTDISABLE\_%' ESCAPE '\'";
                $disable_states = sql($sqlstr,"getAll",DB_FETCHMODE_ASSOC);

                foreach($disable_states as $arr)  {
                        $disable_states_assoc[$arr['variable']] = $arr['value'];
                }
                global $unique_trunks;
                $unique_trunks = array();

                foreach ($my_unique_trunks as $this_trunk) {

                        $trunk_num = substr($this_trunk['variable'],4);
			$this_state = (isset($disable_states_assoc['OUTDISABLE_'.$trunk_num]) ? $disable_states_assoc['OUTDISABLE_'.$trunk_num] : 'off');
                        $unique_trunks[] = array($this_trunk['variable'], $this_trunk['value'], $this_state);
                }
                // sort this array using a custom function sort_trunks(), defined above
                uksort($unique_trunks,"sort_trunks");
                // re-index the newly sorted array
                foreach($unique_trunks as $arr) {
                        $unique_trunks_t[] = array($arr[0],$arr[1],$arr[2]);
                }
                $unique_trunks = $unique_trunks_t;

        }
        else
        {
                $sqlstr  = "SELECT t.variable, t.value, d.value state FROM `globals` t ";
                $sqlstr .= "JOIN (SELECT x.variable, x.value FROM globals x WHERE x.variable LIKE 'OUTDISABLE\_%') d ";
                $sqlstr .= "ON substring(t.variable,5) = substring(d.variable,12) WHERE t.variable LIKE 'OUT\_%' ";
                $sqlstr .= "UNION ALL ";
                $sqlstr .= "SELECT v.variable, v.value, concat(substring(v.value,1,0),'off') state  FROM `globals` v ";
                $sqlstr .= "WHERE v.variable LIKE 'OUT\_%' AND concat('OUTDISABLE_',substring(v.variable,5)) NOT IN ";
                $sqlstr .= " ( SELECT variable from globals WHERE variable LIKE 'OUTDISABLE\_%' ) ";
                $sqlstr .= "ORDER BY variable";
                //$unique_trunks = sql("SELECT * FROM globals WHERE variable LIKE 'OUT_%' ORDER BY variable","getAll");
                $unique_trunks = sql($sqlstr,"getAll");
        }

	//if no trunks have ever been defined, then create the proper variables with the default zap trunk
	// TODO: this looks dumb, updated to remove deprecated values but why can't there be no trunks?
	//
	if (count($unique_trunks) == 0) 
	{
		sql("INSERT INTO globals (variable, value) values ('OUT_1','ZAP/g0')");
		$unique_trunks[] = array('OUT_1','ZAP/g0');
	}
	// asort($unique_trunks);

	if ($assoc) {
		$trunkinfo = array();

		foreach ($unique_trunks as $trunk) {
			list($tech,$name) = explode('/',$trunk[1]);
			$trunkinfo[$trunk[1]] = array(
				'name' => $name,
				'tech' => $tech,
				'globalvar' => $trunk[0], // ick
				'value' => $trunk[2], // ??  no idea what this is.
			);	
		}
		return $trunkinfo;
	} else {
		return $unique_trunks;
	}
}

function core_trunks_addRegister($trunknum,$tech,$reg,$disable_flag=0) {
	sql("INSERT INTO $tech (id, keyword, data, flags) values ('9999999$trunknum','register','$reg','$disable_flag')");
}


function core_trunks_addDialRules($trunknum, $dialrules) {
	$values = array();
	$i = 1;
	foreach ($dialrules as $rule) {
		$values["rule".$i++] = $rule;
	}
	
	$conf = core_trunks_readDialRulesFile();
	
	// rewrite for this trunk
	$conf["trunk-".$trunknum] = $values;
	
	core_trunks_writeDialRulesFile($conf);
}

function core_trunks_readDialRulesFile() {
	global $amp_conf;
	$localPrefixFile = $amp_conf['ASTETCDIR']."/localprefixes.conf";
	
	core_trunks_parse_conf($localPrefixFile, $conf, $section);
	
	return $conf;
}

function core_trunks_writeDialRulesFile($conf) {
	global $amp_conf;
	$localPrefixFile = $amp_conf['ASTETCDIR']."/localprefixes.conf";
	
	$fd = fopen($localPrefixFile,"w");
	foreach ($conf as $section=>$values) {
		fwrite($fd, "[".$section."]\n");
		foreach ($values as $key=>$value) {
			fwrite($fd, $key."=".$value."\n");
		}
		fwrite($fd, "\n");
	}
	fclose($fd);
}

function core_trunks_parse_conf($filename, &$conf, &$section) {
	if (is_null($conf)) {
		$conf = array();
	}
	if (is_null($section)) {
		$section = "general";
	}
	
	if (file_exists($filename)) {
		$fd = fopen($filename, "r");
		while ($line = fgets($fd, 1024)) {
			if (preg_match("/^\s*([a-zA-Z0-9-_]+)\s*=\s*(.*?)\s*([;#].*)?$/",$line,$matches)) {
				// name = value
				// option line
				$conf[$section][ $matches[1] ] = $matches[2];
			} else if (preg_match("/^\s*\[(.+)\]/",$line,$matches)) {
				// section name
				$section = strtolower($matches[1]);
			} else if (preg_match("/^\s*#include\s+(.*)\s*([;#].*)?/",$line,$matches)) {
				// include another file
				
				if ($matches[1][0] == "/") {
					// absolute path
					$filename = $matches[1];
				} else {
					// relative path
					$filename =  dirname($filename)."/".$matches[1];
				}
				
				core_trunks_parse_conf($filename, $conf, $section);
			}
		}
	}
}

function core_trunks_getTrunkTrunkName($trunknum) {
	$results = sql("SELECT value FROM globals WHERE variable = 'OUT_".$trunknum."'","getAll");
	if (!$results) {
		return false;
	}
	
	if(strpos($results[0][0],"AMP:") === 0) {  //custom trunks begin with AMP:
		$tname = substr($results[0][0],4);
	} else {
	strtok($results[0][0],'/');
		$tname = strtok('/'); // the text _after_ technology.  ie: ZAP/g0 is g0
	}
	return $tname;
}

//get and print peer details (prefixed with 4 9's)
function core_trunks_getTrunkPeerDetails($trunknum) {
	global $db;
	
	$tech = core_trunks_getTrunkTech($trunknum);
	
	if ($tech == "zap" || $tech == "") return ""; // zap has no details
	
	$results = sql("SELECT keyword,data FROM $tech WHERE id = '9999$trunknum' ORDER BY flags, keyword DESC","getAll");
	
	foreach ($results as $result) {
		if ($result[0] != 'account') {
			if (isset($confdetail))
				$confdetail .= $result[0] .'='. $result[1] . "\n";
			else
				$confdetail = $result[0] .'='. $result[1] . "\n";
		}
	}
	return $confdetail;
}

//get trunk user context (prefixed with 5 9's)
function core_trunks_getTrunkUserContext($trunknum) {
	$tech = core_trunks_getTrunkTech($trunknum);
	if ($tech == "zap") return ""; // zap has no account
	
	$results = sql("SELECT keyword,data FROM $tech WHERE id = '99999$trunknum'","getAll");

	foreach ($results as $result) {
		if ($result[0] == 'account') {
			$account = $result[1];
		}
	}
	return isset($account)?$account:null;
}

//get and print user config (prefixed with 5 9's)
function core_trunks_getTrunkUserConfig($trunknum) {
	global $db;
	
	$tech = core_trunks_getTrunkTech($trunknum);
	
	if ($tech == "zap" || $tech == "") return ""; // zap has no details
	
	$results = sql("SELECT keyword,data FROM $tech WHERE id = '99999$trunknum' ORDER BY flags, keyword DESC","getAll");

	foreach ($results as $result) {
		if ($result[0] != 'account') {
			if (isset($confdetail))
				$confdetail .= $result[0] .'='. $result[1] . "\n";
			else
				$confdetail = $result[0] .'='. $result[1] . "\n";
		}
	}
	return isset($confdetail)?$confdetail:null;
}

//get trunk account register string
function core_trunks_getTrunkRegister($trunknum) {
	$tech = core_trunks_getTrunkTech($trunknum);
	
	if ($tech == "zap" || $tech == "") return ""; // zap has no register
	
	$results = sql("SELECT keyword,data FROM $tech WHERE id = '9999999$trunknum'","getAll");

	foreach ($results as $result) {
			$register = $result[1];
	}
	return isset($register)?$register:null;
}

function core_trunks_getDialRules($trunknum) {
	$conf = core_trunks_readDialRulesFile();
	if (isset($conf["trunk-".$trunknum])) {
		return $conf["trunk-".$trunknum];
	}
	return false;
}

//get outbound routes for a given trunk
function core_trunks_gettrunkroutes($trunknum) {
  $sql_code = "SELECT DISTINCT SUBSTRING(context,7) route ,args trunk FROM extensions WHERE context LIKE 'outrt-%' AND 
    (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-dundi,%') ORDER BY context,priority";
  $results = sql( $sql_code, "getAll" ,DB_FETCHMODE_ASSOC);
  $routeseq = array();
  foreach ($results as $entry) {
    $pos1 = strpos($entry['trunk'],',')+1;
    $routeseq[$entry['route']][] = substr($entry['trunk'],$pos1,strpos($entry['trunk'],',',$pos1)-$pos1);
  }
  $routes = array();
  foreach ($routeseq as $key => $value) {
    $pos = array_search($trunknum, array_values(array_unique($value)));
    if ($pos !== false) {
      $routes[$key] = $pos+1; // start at 1, not 0
    }
  }
  return $routes;
}

function core_trunks_deleteDialRules($trunknum) {
	$conf = core_trunks_readDialRulesFile();
	
	// remove rules for this trunk
	unset($conf["trunk-".$trunknum]);
	
	core_trunks_writeDialRulesFile($conf);
}

/* end page.trunks.php functions */


/* begin page.routing.php functions */

//get unique outbound route names
function core_routing_getroutenames() 
{
	global $amp_conf;
	
	if ($amp_conf["AMPDBENGINE"] == "sqlite3") 
	{
		// SUBSTRING is not supported under sqlite3, we need to filter
		// this in php. I am not sure why "6" and not "7"
		// but I don't really care -> it works :)
		$results = sql("SELECT DISTINCT context FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ","getAll");
		foreach( array_keys($results) as $idx )
		{
			 $results[$idx][0] = substr( $results[$idx][0], 6);
		}
	}
	else
	{
		// we SUBSTRING() to remove "outrt-"
		$results = sql("SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ","getAll");
	}


	//TODO: This needs to be yanked, should be in the upgrade script somewhere not here
	//
	if (count($results) == 0) {
		// see if they're still using the old dialprefix method
		if ($amp_conf["AMPDBENGINE"] == "sqlite3")  {
			$sql ="SELECT variable,value FROM globals WHERE variable LIKE 'DIAL\_OUT\_%' ESCAPE '\'";
		}
		else  {
			$sql ="SELECT variable,value FROM globals WHERE variable LIKE 'DIAL\\\_OUT\\\_%'";
		}
		$results = sql($sql,"getAll");
		// we SUBSTRING() to remove "outrt-"
		
		if (count($results) > 0) {
			// yes, they are using old method, let's update
			
			// get the default trunk
			$results_def = sql("SELECT value FROM globals WHERE variable = 'OUT'","getAll");
			
			if (preg_match("/{OUT_(\d+)}/", $results_def[0][0], $matches)) {
				$def_trunk = $matches[1];
			} else {
				$def_trunk = "";
			}
			
			$default_patterns = array(	// default patterns that used to be in extensions.conf
						"NXXXXXX",
						"NXXNXXXXXX",
						"1800NXXXXXX",
						"1888NXXXXXX",
						"1877NXXXXXX",
						"1866NXXXXXX",
						"1NXXNXXXXXX",
						"011.",
						"911",
						"411",
						"311",
						);
			
			foreach ($results as $temp) {
				// temp[0] is "DIAL_OUT_1"
				// temp[1] is the dial prefix
				
				$trunknum = substr($temp[0],9);
				
				$name = "route".$trunknum;
				
				$trunks = array(1=>"OUT_".$trunknum); // only one trunk to use
				
				$patterns = array();
				foreach ($default_patterns as $pattern) {
					$patterns[] = $temp[1]."|".$pattern;
				}
				
				if ($trunknum == $def_trunk) {
					// this is the default trunk, add the patterns with no prefix
					$patterns = array_merge($patterns, $default_patterns);
				}
				
				// add this as a new route
				core_routing_add($name, $patterns, $trunks,"new");
			}
			
			
			// delete old values
			if ($amp_conf["AMPDBENGINE"] == "sqlite3")  {
				$sql = "DELETE FROM globals WHERE (variable LIKE 'DIAL\_OUT\_%') ESCAPE '\' OR (variable = 'OUT') ";
			}
			else  {
				$sql = "DELETE FROM globals WHERE (variable LIKE 'DIAL\\\_OUT\\\_%') OR (variable = 'OUT') ";
			}
			sql($sql);

			// we need to re-generate extensions_additional.conf
			// i'm not sure how to do this from here
			
			// re-run our query
			$results = sql("SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ","getAll");
			// we SUBSTRING() to remove "outrt-"
		}
		
	} // else, it just means they have no routes.
	
	return $results;
}

function core_routing_setroutepriority($routepriority, $reporoutedirection, $reporoutekey)
{
	global $db, $amp_conf;
	$counter=-1;
	foreach ($routepriority as $tresult) 
	{
		$counter++;
		if (($counter==($reporoutekey-1)) && ($reporoutedirection=="up")) {
			// swap this one with the one before (move up)
			$temproute = $routepriority[$counter];
			$routepriority[ $counter ] = $routepriority[ $counter+1 ];
			$routepriority[ $counter+1 ] = $temproute;
			
		} else if (($counter==($reporoutekey)) && ($reporoutedirection=="down")) {
			// swap this one with the one after (move down)
			$temproute = $routepriority[ $counter+1 ];
			$routepriority[ $counter+1 ] = $routepriority[ $counter ];
			$routepriority[ $counter ] = $temproute;
		}
	}
	unset($temproute);
	$routepriority = array_values($routepriority); // resequence our numbers
	$counter=0;
	foreach ($routepriority as $tresult) {
		$order=core_routing_setroutepriorityvalue($counter++);
		$sql = sprintf("Update extensions set context='outrt-%s-%s' WHERE context='outrt-%s'",$order,substr($tresult[0],4), $tresult[0]);
		$result = $db->query($sql); 
		if(DB::IsError($result)) {     
			die_freepbx($result->getMessage()); 
		}
	}
	
	// Delete and readd the outbound-allroutes entries
	$sql = "delete from  extensions WHERE context='outbound-allroutes'";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
        	die_freepbx($result->getMessage().$sql);
	}
	
	$sql = "SELECT DISTINCT context FROM extensions WHERE context like 'outrt-%' ORDER BY context";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}

	$priority_loops=1;	
	foreach ($results as $row) {
		$sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ";
		$sql .= "('outbound-allroutes', ";
		$sql .= "'include', ";
		$sql .= "'".$priority_loops++."', ";
		$sql .= "'".$row[0]."', ";
		$sql .= "'', ";
		$sql .= "'', ";
		$sql .= "'2')";
	
		//$sql = sprintf("Update extensions set application='outrt-%s-%s' WHERE context='outbound-allroutes' and  application='outrt-%s'",$order,substr($tresult[0],4), $tresult[0]);
		$result = $db->query($sql); 
		if(DB::IsError($result)) {     
			die_freepbx($result->getMessage(). $sql); 
 		}
	}
	
	if ( $amp_conf["AMPDBENGINE"] == "sqlite3")
		$sql = "SELECT DISTINCT context FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ";
	else
		$sql = "SELECT DISTINCT SUBSTRING(context,7) FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context ";

        // we SUBSTRING() to remove "outrt-"
        $routepriority = $db->getAll($sql);
        if(DB::IsError($routepriority)) {
                die_freepbx($routepriority->getMessage());
        }

	// TODO: strip the context on the sqlite3 backend
	// not sure where does it effects, since this is working on my setup...
	// welcome to funky town
        return ($routepriority);
}

function core_routing_setroutepriorityvalue($key)
{
	$key=$key+1;
	if ($key<10)
		$prefix = sprintf("00%d",$key);
	else if ((9<$key)&&($key<100))
		$prefix = sprintf("0%d",$key);
	else if ($key>100)
		$prefix = sprintf("%d",$key);
	return ($prefix);
}


function core_routing_add($name, $patterns, $trunks, $method, $pass, $emergency = "", $intracompany = "", $mohsilence = "") {

	global $db;

	$trunktech=array();

	// Make sure only valid characters are there, javascript should enforce this (and more)
	//
	$name = preg_replace("/[^a-zA-Z0-9_\-]/" ,"",$name);

	//Retrieve each trunk tech for later lookup
        if ($amp_conf["AMPDBENGINE"] == "sqlite3")  {
		$sql="select * from globals WHERE variable LIKE 'OUT\_%' ESCAPE '\'";
	}
	else  {
		$sql="select * from globals WHERE variable LIKE 'OUT\\_%'";
	}
        $result = $db->getAll($sql);
        if(DB::IsError($result)) {
		die_freepbx($result->getMessage());
	}
	foreach($result as $tr) {
		$tech = strtok($tr[1], "/");
		$trunktech[$tr[0]]=$tech;
	}
	
 	if ($method=="new") {	
		$sql="select DISTINCT context FROM extensions WHERE context LIKE 'outrt-%' ORDER BY context";
		$routepriority = $db->getAll($sql);
		if(DB::IsError($result)) {
			die_freepbx($result->getMessage());
		}
		$order=core_routing_setroutepriorityvalue(count($routepriority));
		$name = sprintf ("%s-%s",$order,$name);
	}
	$trunks = array_values($trunks); // probably already done, but it's important for our dialplan

	
	foreach ($patterns as $pattern) {
		if (false !== ($pos = strpos($pattern,"|"))) {
			// we have a | meaning to not pass the digits on
			// (ie, 9|NXXXXXX should use the pattern _9NXXXXXX but only pass NXXXXXX, not the leading 9)
			
			$pattern = str_replace("|","",$pattern); // remove all |'s
			$exten = "EXTEN:".$pos; // chop off leading digit
		} else {
			// we pass the full dialed number as-is
			$exten = "EXTEN"; 
		}
		
		if (!preg_match("/^[0-9*]+$/",$pattern)) { 
			// note # is not here, as asterisk doesn't recoginize it as a normal digit, thus it requires _ pattern matching
			
			// it's not strictly digits, so it must have patterns, so prepend a _
			$pattern = "_".$pattern;
		}
		
		// 1st priority is emergency dialing variable (if set)
		if(!empty($emergency)) {
			$startpriority = 1;
			$sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
			$sql .= "('outrt-".$name."', ";
			$sql .= "'".$pattern."', ";
			$sql .= "'".$startpriority."', ";
			$sql .= "'SetVar', ";
			$sql .= "'EMERGENCYROUTE=YES', ";
			$sql .= "'Use Emergency CID for device')";
			$result = $db->query($sql);
			if(DB::IsError($result)) {
				die_freepbx($result->getMessage());
			}
		} else {
			$startpriority = 0;
		}

		// Next Priority (either first or second depending on above)
		if(!empty($intracompany)) {
			   $startpriority += 1;
			   $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
			   $sql .= "('outrt-".$name."', ";
			   $sql .= "'".$pattern."', ";
			   $sql .= "'".$startpriority."', ";
			   $sql .= "'SetVar', ";
			   $sql .= "'INTRACOMPANYROUTE=YES', ";
			   $sql .= "'Preserve Intenal CID Info')";
			   $result = $db->query($sql);
				if(DB::IsError($result)) {
					   die_freepbx($result->getMessage());
				}
		}

		// Next Priority (either first, second or third depending on above)
		if(!empty($mohsilence) && trim($mohsilence) != 'default') {
			   $startpriority += 1;
			   $sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
			   $sql .= "('outrt-".$name."', ";
			   $sql .= "'".$pattern."', ";
			   $sql .= "'".$startpriority."', ";
			   $sql .= "'SetVar', ";
			   $sql .= "'MOHCLASS=".$mohsilence."', ";
			   $sql .= "'Do not play moh on this route')";
			   $result = $db->query($sql);
				if(DB::IsError($result)) {
					   die_freepbx($result->getMessage());
				}
		}

		$first_trunk = 1;
		foreach ($trunks as $priority => $trunk) {
			$priority += $startpriority;
			$priority += 1; // since arrays are 0-based, but we want priorities to start at 1
			
			$sql = "INSERT INTO extensions (context, extension, priority, application, args) VALUES ";
			$sql .= "('outrt-".$name."', ";
			$sql .= "'".$pattern."', ";
			$sql .= "'".$priority."', ";
			$sql .= "'Macro', ";
			if ($first_trunk)
				$pass_str = $pass;
			else
				$pass_str = "";

			if ($trunktech[$trunk] == "ENUM") {
				$sql .= "'dialout-enum,".substr($trunk,4).",\${".$exten."},".$pass_str."'"; // cut off OUT_ from $trunk
			} else if ($trunktech[$trunk] == "DUNDI") {
				$sql .= "'dialout-dundi,".substr($trunk,4).",\${".$exten."},".$pass_str."'"; // cut off OUT_ from $trunk
			} else {
				$sql .= "'dialout-trunk,".substr($trunk,4).",\${".$exten."},".$pass_str."'"; // cut off OUT_ from $trunk
			}
			$sql .= ")";
			
			$result = $db->query($sql);
			if(DB::IsError($result)) {
				die_freepbx($result->getMessage());
			}
			//To identify the first trunk in a pattern
			//so that passwords are in the first trunk in
			//each pattern
			$first_trunk = 0;
		}
		
		$priority += 1;
		$sql = "INSERT INTO extensions (context, extension, priority, application, args, descr) VALUES ";
		$sql .= "('outrt-".$name."', ";
		$sql .= "'".$pattern."', ";
		$sql .= "'".$priority."', ";
		$sql .= "'Macro', ";
		$sql .= "'outisbusy', ";
		$sql .= "'No available circuits')";
		
		$result = $db->query($sql);
		if(DB::IsError($result)) {
			die_freepbx($result->getMessage());
		}
	}

	
	// add an include=>outrt-$name  to [outbound-allroutes]:
	
	// we have to find the first available priority.. priority doesn't really matter for the include, but
	// there is a unique index on (context,extension,priority) so if we don't do this we can't put more than
	// one route in the outbound-allroutes context.
	$sql = "SELECT priority FROM extensions WHERE context = 'outbound-allroutes' AND extension = 'include'";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}
	$priorities = array();
	foreach ($results as $row) {
		$priorities[] = $row[0];
	}
	for ($priority = 1; in_array($priority, $priorities); $priority++);
	
	// $priority should now be the lowest available number
	
	$sql = "INSERT INTO extensions (context, extension, priority, application, args, descr, flags) VALUES ";
	$sql .= "('outbound-allroutes', ";
	$sql .= "'include', ";
	$sql .= "'".$priority."', ";
	$sql .= "'outrt-".$name."', ";
	$sql .= "'', ";
	$sql .= "'', ";
	$sql .= "'2')";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($priority.$result->getMessage());
	}
	
}

function core_routing_edit($name, $patterns, $trunks, $pass, $emergency="", $intracompany = "", $mohsilence="") {
	core_routing_del($name);
	core_routing_add($name, $patterns, $trunks,"edit", $pass, $emergency, $intracompany, $mohsilence);
}

function core_routing_del($name) {
	global $db;
	$sql = "DELETE FROM extensions WHERE context = 'outrt-".$name."'";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage());
	}
	
	$sql = "DELETE FROM extensions WHERE context = 'outbound-allroutes' AND application = 'outrt-".$name."' ";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage());
	}
	
	return $result;
}

/* Delete all occurences of the specified trunk from all routes that may use it
 */
function core_routing_trunk_del($trunknum) {
	global $db;

  $sql = "DELETE FROM `extensions` WHERE `application` = 'Macro' AND `context` LIKE 'outrt-%' AND `args` LIKE 'dialout-%,$trunknum,%'";
	$result = $db->query($sql);
}

function core_routing_rename($oldname, $newname) {
	global $db;

	$route_prefix=substr($oldname,0,4);
	$newname=$route_prefix.$newname;
	$sql = "SELECT context FROM extensions WHERE context = 'outrt-".$newname."'";
	$results = $db->getAll($sql);
	if (count($results) > 0) {
		// there's already a route with this name
		return false;
	}
	
	$sql = "UPDATE extensions SET context = 'outrt-".$newname."' WHERE context = 'outrt-".$oldname."'";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage());
	}
        $mypriority=sprintf("%d",$route_prefix);	
	$sql = "UPDATE extensions SET application = 'outrt-".$newname."', priority = '$mypriority' WHERE context = 'outbound-allroutes' AND application = 'outrt-".$oldname."' ";
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage());
	}
	
	return true;
}

//get unique outbound route patterns for a given context
function core_routing_getroutepatterns($route) {
	global $db;
	$sql = "SELECT extension, args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk%' OR args LIKE 'dialout-enum%' OR args LIKE 'dialout-dundi%') ORDER BY extension ";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}
	
	$patterns = array();
	foreach ($results as $row) {
		if ($row[0][0] == "_") {
			// remove leading _
			$pattern = substr($row[0],1);
		} else {
			$pattern = $row[0];
		}
		
		if (preg_match("/{EXTEN:(\d+)}/", $row[1], $matches)) {
			// this has a digit offset, we need to insert a |
			$pattern = substr($pattern,0,$matches[1])."|".substr($pattern,$matches[1]);
		}
		
		$patterns[] = $pattern;
	}
	return array_unique($patterns);
}

//get unique outbound route trunks for a given context
function core_routing_getroutetrunks($route) {
	global $db;
	$sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-dundi,%') ORDER BY CAST(priority as UNSIGNED) ";
	$results = $db->getAll($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}
	
	$trunks = array();
	foreach ($results as $row) {
		if (preg_match('/^dialout-trunk,(\d+)/', $row[0], $matches)) {
			// check in_array -- even though we did distinct
			// we still might get ${EXTEN} and ${EXTEN:1} if they used | to split a pattern
			if (!in_array("OUT_".$matches[1], $trunks)) {
				$trunks[] = "OUT_".$matches[1];
			}
		} else if (preg_match('/^dialout-enum,(\d+)/', $row[0], $matches)) {
			if (!in_array("OUT_".$matches[1], $trunks)) {
				$trunks[] = "OUT_".$matches[1];
			}
		} else if (preg_match('/^dialout-dundi,(\d+)/', $row[0], $matches)) {
			if (!in_array("OUT_".$matches[1], $trunks)) {
				$trunks[] = "OUT_".$matches[1];
			}
		}
	}
	return $trunks;
}


//get password for this route
function core_routing_getroutepassword($route) {
	global $db;
	$sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'dialout-trunk,%' OR args LIKE 'dialout-enum,%' OR args LIKE 'dialout-dundi,%') ORDER BY CAST(priority as UNSIGNED) ";
	$results = $db->getOne($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}
	if (preg_match('/^.*,.*,.*,(\d+|\/\S+)/', $results, $matches)) {
		$password = $matches[1];
	} else {
		$password = "";
	}
	
	return $password;
}

//get emergency state for this route
function core_routing_getrouteemergency($route) {
	global $db;
	$sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'EMERGENCYROUTE%') ";
	$results = $db->getOne($sql);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage());
	}
	if (preg_match('/^.*=(.*)/', $results, $matches)) {
		$emergency = $matches[1];
	} else {
		$emergency = "";
	}
	
	return $emergency;
}

//get intracompany routing status for this route
function core_routing_getrouteintracompany($route) {

       global $db;
       $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'INTRACOMPANYROUTE%') ";
       $results = $db->getOne($sql);
       if(DB::IsError($results)) {
               die_freepbx($results->getMessage());
       }
       if (preg_match('/^.*=(.*)/', $results, $matches)) {
               $intracompany = $matches[1];
       } else {
               $intracompany = "";
       }
       return $intracompany;
}

//get mohsilence routing status for this route
function core_routing_getroutemohsilence($route) {

       global $db;
       $sql = "SELECT DISTINCT args FROM extensions WHERE context = 'outrt-".$route."' AND (args LIKE 'MOHCLASS%') ";
       $results = $db->getOne($sql);
       if(DB::IsError($results)) {
               die_freepbx($results->getMessage());
       }
       if (preg_match('/^.*=(.*)/', $results, $matches)) {
               $mohsilence = $matches[1];
       } else {
               $mohsilence = "";
       }
       return $mohsilence;
}

function general_get_zonelist() {
	return array(
 array ( "name" => "Austria",  "iso" => "at", "conf" => "ringcadence = 1000,5000\ndial = 420\nbusy = 420/400,0/400\nring = 420/1000,0/5000\ncongestion = 420/200,0/200\ncallwaiting = 420/40,0/1960\ndialrecall = 420\nrecord = 1400/80,0/14920\ninfo = 950/330,1450/330,1850/330,0/1000\nstutter = 380+420\n"),
 array ( "name" => "Australia",  "iso" => "au", "conf" => "ringcadence = 400,200,400,2000\ndial = 413+438\nbusy = 425/375,0/375\nring = 413+438/400,0/200,413+438/400,0/2000\ncongestion = 425/375,0/375,420/375,0/375\ncallwaiting = 425/200,0/200,425/200,0/4400\ndialrecall = 413+438\nrecord = !425/1000,!0/15000,425/360,0/15000\ninfo = 425/2500,0/500\nstd = !525/100,!0/100,!525/100,!0/100,!525/100,!0/100,!525/100,!0/100,!525/100\nfacility = 425\nstutter = 413+438/100,0/40\nringmobile = 400+450/400,0/200,400+450/400,0/2000\n"),
 array ( "name" => "Brazil",  "iso" => "br", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/250,0/250\nring = 425/1000,0/4000\ncongestion = 425/250,0/250,425/750,0/250\ncallwaiting = 425/50,0/1000\ndialrecall = 350+440\nrecord = 425/250,0/250\ninfo = 950/330,1400/330,1800/330\nstutter = 350+440\n"),
 array ( "name" => "Belgium",  "iso" => "be", "conf" => "ringcadence = 1000,3000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/3000\ncongestion = 425/167,0/167\ncallwaiting = 1400/175,0/175,1400/175,0/3500\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = 900/330,1400/330,1800/330,0/1000\nstutter = 425/1000,0/250\n"),
 array ( "name" => "Bulgaria",  "iso" => "bg", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/250,0/250\ncallwaiting = 425/150,0/150,425/150,0/4000\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\nrecord = 1400/425,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = 425/1500,0/100\n"),
 array ( "name" => "Switzerland",  "iso" => "ch", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/200,0/200,425/200,0/4000\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\nrecord = 1400/80,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = 425+340/1100,0/1100\n"),
 array ( "name" => "Chile",  "iso" => "cl", "conf" => "ringcadence = 1000,3000\ndial = 400\nbusy = 400/500,0/500\nring = 400/1000,0/3000\ncongestion = 400/200,0/200\ncallwaiting = 400/250,0/8750\ndialrecall = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400\nrecord = 1400/500,0/15000\ninfo = 950/333,1400/333,1800/333,0/1000\nstutter = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400\n"),
 array ( "name" => "China",  "iso" => "cn", "conf" => "ringcadence = 1000,4000\ndial = 450\nbusy = 450/350,0/350\nring = 450/1000,0/4000\ncongestion = 450/700,0/700\ncallwaiting = 450/400,0/4000\ndialrecall = 450\nrecord = 950/400,0/10000\ninfo = 450/100,0/100,450/100,0/100,450/100,0/100,450/400,0/400\nstutter = 450+425\n"),
 array ( "name" => "Czech Republic",  "iso" => "cz", "conf" => "ringcadence = 1000,4000\ndial = 425/330,0/330,425/660,0/660\nbusy = 425/330,0/330\nring = 425/1000,0/4000\ncongestion = 425/165,0/165\ncallwaiting = 425/330,0/9000\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425/330,0/330,425/660,0/660\nrecord = 1400/500,0/14000\ninfo = 950/330,0/30,1400/330,0/30,1800/330,0/1000\nstutter = 425/450,0/50\n"),
 array ( "name" => "Germany",  "iso" => "de", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/480,0/480\nring = 425/1000,0/4000\ncongestion = 425/240,0/240\ncallwaiting = !425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,!0/5000,!425/200,!0/200,!425/200,0\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\nrecord = 1400/80,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = 425+400\n"),
 array ( "name" => "Denmark",  "iso" => "dk", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = !425/200,!0/600,!425/200,!0/3000,!425/200,!0/200,!425/200,0\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\nrecord = 1400/80,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = 425/450,0/50\n"),
 array ( "name" => "Estonia",  "iso" => "ee", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/300,0/300\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 950/650,0/325,950/325,0/30,1400/1300,0/2600\ndialrecall = 425/650,0/25\nrecord = 1400/500,0/15000\ninfo = 950/650,0/325,950/325,0/30,1400/1300,0/2600\nstutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\n"),
 array ( "name" => "Finland",  "iso" => "fi", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/300,0/300\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/150,0/150,425/150,0/8000\ndialrecall = 425/650,0/25\nrecord = 1400/500,0/15000\ninfo = 950/650,0/325,950/325,0/30,1400/1300,0/2600\nstutter = 425/650,0/25\n"),
 array ( "name" => "France",  "iso" => "fr", "conf" => "ringcadence = 1500,3500\ndial = 440\nbusy = 440/500,0/500\nring = 440/1500,0/3500\ncongestion = 440/250,0/250\ncallwait = 440/300,0/10000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330\nstutter = !440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,!440/100,!0/100,440\n"),
 array ( "name" => "Greece",  "iso" => "gr", "conf" => "ringcadence = 1000,4000\ndial = 425/200,0/300,425/700,0/800\nbusy = 425/300,0/300\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/150,0/150,425/150,0/8000\ndialrecall = 425/650,0/25\nrecord = 1400/400,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0\nstutter = 425/650,0/25\n"),
array ( "name" => "Hong Kong", "iso" => "hk", "conf" => "ringcadence = 400,200,400,3000\ndial = 350+440\nbusy = 480+620/500,0/500\nring = 440+480/400,0/200,440+480/400,0/3000\ncongestion = 480+620/250,0/250\ncallwaiting = 440/300,0/10000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "Hungary",  "iso" => "hu", "conf" => "ringcadence = 1250,3750\ndial = 425\nbusy = 425/300,0/300\nring = 425/1250,0/3750\ncongestion = 425/300,0/300\ncallwaiting = 425/40,0/1960\ndialrecall = 425+450\nrecord = 1400/400,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0\nstutter = 350+375+400\n"),
 array ( "name" => "India",  "iso" => "in", "conf" => "ringcadence = 400,200,400,2000\ndial = 400*25\nbusy = 400/750,0/750\nring = 400*25/400,0/200,400*25/400,0/2000\ncongestion = 400/250,0/250\ncallwaiting = 400/200,0/100,400/200,0/7500\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0/1000\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "Israel",  "iso" => "il", "conf" => "ringcadence = 1000,3000\ndial = 414\nbusy = 414/500,0/500\nring = 414/1000,0/3000\ncongestion = 414/250,0/250\ncallwaiting = 414/100,0/100,414/100,0/100,414/600,0/3000 \ndialrecall = !414/100,!0/100,!414/100,!0/100,!414/100,!0/100,414\nrecord = 1400/500,0/15000\ninfo = 1000/330,1400/330,1800/330,0/1000\nstutter = !414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,!414/160,!0/160,414 \n"),
 array ( "name" => "Italy",  "iso" => "it", "conf" => "ringcadence = 1000,4000\ndial = 425/200,0/200,425/600,0/1000\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/400,0/100,425/250,0/100,425/150,0/14000\ndialrecall = 470/400,425/400\nrecord = 1400/400,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0\nstutter = 470/400,425/400\n"),
 array ( "name" => "Japan",  "iso" => "jp", "conf" => "ringcadence = 1000,2000\ndial = 400\nbusy = 400/500,0/500\nring = 400+15/1000,0/2000\ncongestion = 400/500,0/500\ncallwaiting = 400+16/500,0/8000\ndialrecall = !400/200,!0/200,!400/200,!0/200,!400/200,!0/200,400\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,!400/100,!0/100,400\n"),
 array ( "name" => "Lithuania",  "iso" => "lt", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/350,0/350\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/150,0/150,425/150,0/4000\ndialrecall = 425/500,0/50\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0\nstutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\n"),
 array ( "name" => "Mexico",  "iso" => "mx", "conf" => "ringcadence = 2000,4000\ndial = 425\nbusy = 425/250,0/250\nring = 425/1000,0/4000\ncongestion = 425/250,0/250\ncallwaiting = 425/200,0/600,425/200,0/10000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = 950/330,0/30,1400/330,0/30,1800/330,0/1000\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "Netherlands",  "iso" => "nl", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/250,0/250\ncallwaiting = 425/500,0/9500\ndialrecall = 425/500,0/50\nrecord = 1400/500,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = 425/500,0/50\n"),
 array ( "name" => "Norway",  "iso" => "no", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/200,0/200\ncallwaiting = 425/200,0/600,425/200,0/10000\ndialrecall = 470/400,425/400\nrecord = 1400/400,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,0\nstutter = 470/400,425/400\n"),
 array ( "name" => "New Zealand",  "iso" => "nz", "conf" => "ringcadence = 400,200,400,2000\ndial = 400\nbusy = 400/250,0/250\nring = 400+450/400,0/200,400+450/400,0/2000\ncongestion = 400/375,0/375\ncallwaiting = !400/200,!0/3000,!400/200,!0/3000,!400/200,!0/3000,!400/200\ndialrecall = !400/100!0/100,!400/100,!0/100,!400/100,!0/100,400\nrecord = 1400/425,0/15000\ninfo = 400/750,0/100,400/750,0/100,400/750,0/100,400/750,0/400\nstutter = !400/100!0/100,!400/100,!0/100,!400/100,!0/100,!400/100!0/100,!400/100,!0/100,!400/100,!0/100,400\n"),
 array ( "name" => "Philippines",  "iso" => "phl", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 480+620/500,0/500\nring = 425+480/1000,0/4000\ncongestion = 480+620/250,0/250\ncallwaiting = 440/300,0/10000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "Poland",  "iso" => "pl", "conf" => "ringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/500,0/500\ncallwaiting = 425/150,0/150,425/150,0/4000\ndialrecall = 425/500,0/50\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000,!950/330,!1400/330,!1800/330,!0/1000\nstutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\n"),
 array ( "name" => "Portugal",  "iso" => "pt", "conf" => "ringcadence = 1000,5000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/5000\ncongestion = 425/200,0/200\ncallwaiting = 440/300,0/10000\ndialrecall = 425/1000,0/200\nrecord = 1400/500,0/15000\ninfo = 950/330,1400/330,1800/330,0/1000\nstutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\n"),
 array ( "name" => "Russia / ex Soviet Union",  "iso" => "ru", "conf" => "ringcadence = 800,3200\ndial = 425\nbusy = 425/350,0/350\nring = 425/800,0/3200\ncongestion = 425/350,0/350\ncallwaiting = 425/200,0/5000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\n"),
 array ( "name" => "Singapore",  "iso" => "sg", "conf" => "ringcadence = 400,200,400,2000\ndial = 425\nring = 425*24/400,0/200,425*24/400,0/2000 ; modulation should be 100%, not 90%\nbusy = 425/750,0/750\ncongestion = 425/250,0/250\ncallwaiting = 425*24/300,0/200,425*24/300,0/3200\nstutter = !425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,!425/200,!0/200,!425/600,!0/200,425\ninfo = 950/330,1400/330,1800/330,0/1000 ; not currently in use acc. to reference\ndialrecall = 425*24/500,0/500,425/500,0/2500 ; unspecified in IDA reference, use repeating Holding Tone A,B\nrecord = 1400/500,0/15000 ; unspecified in IDA reference, use 0.5s tone every 15s\nnutone = 425/2500,0/500\nintrusion = 425/250,0/2000\nwarning = 425/624,0/4376 ; end of period tone, warning\nacceptance = 425/125,0/125\nholdinga = !425*24/500,!0/500 ; followed by holdingb\nholdingb = !425/500,!0/2500\n"),
 array ( "name" => "South Africa",  "iso" => "za", "conf" => "ringcadence = 400,200,400,2000\ndial = 400*33\nbusy = 400/500,0/500\nring = 400*33/400,0/200,400*33/400,0/2000\ncongestion = 400/250,0/250\ncallwaiting = 400*33/250,0/250,400*33/250,0/250,400*33/250,0/250,400*33/250,0/250\ndialrecall = 350+440\nrecord = 1400/500,0/10000\ninfo = 950/330,1400/330,1800/330,0/330\nstutter =!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,!400*33/100,!0/100,400*33 \n"),
 array ( "name" => "Spain",  "iso" => "es", "conf" => "ringcadence = 1500,3000\ndial = 425\nbusy = 425/200,0/200\nring = 425/1500,0/3000\ncongestion = 425/200,0/200,425/200,0/200,425/200,0/600\ncallwaiting = 425/175,0/175,425/175,0/3500\ndialrecall = !425/200,!0/200,!425/200,!0/200,!425/200,!0/200,425\nrecord = 1400/500,0/15000\ninfo = 950/330,0/1000\ndialout = 500\n\n"),
 array ( "name" => "Sweden",  "iso" => "se", "conf" => "ringcadence = 1000,5000\ndial = 425\nbusy = 425/250,0/250\nring = 425/1000,0/5000\ncongestion = 425/250,0/750\ncallwaiting = 425/200,0/500,425/200,0/9100\ndialrecall = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\nrecord = 1400/500,0/15000\ninfo = !950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,!0/2024,!950/332,!0/24,!1400/332,!0/24,!1800/332,0\nstutter = !425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,!425/100,!0/100,425\n"),
 array ("name" => "Turkey", "iso" => "tr", "conf" => "ringcadance = 2000,4000\ndial = 450\nbusy = 450/500,0/500\nring = 450/2000,450/4000\ncongestion = 450/200,0/200,450/200,0/200,450/200,0/200,450/600,0/200\ncallwaiting = 450/200,0/600,450/200,0/8000\ndialrecall = 450/1000,0/250\nrecord = 1400/500,0/15000\ninfo = !950/300,!1400/300,!1800/300,!0/1000,!950/300,!1400/300,!1800/300,!0/1000,!950/300,!1400/300,!1800/300,!0/1000,0\n"),
 array ( "name" => "United Kingdom",  "iso" => "uk", "conf" => "ringcadence = 400,200,400,2000\ndial = 350+440\nspecialdial = 350+440/750,440/750\nbusy = 400/375,0/375\ncongestion = 400/400,0/350,400/225,0/525\nspecialcongestion = 400/200,1004/300\nunobtainable = 400\nring = 400+450/400,0/200,400+450/400,0/2000\ncallwaiting = 400/100,0/4000\nspecialcallwaiting = 400/250,0/250,400/250,0/250,400/250,0/5000\ncreditexpired = 400/125,0/125\nconfirm = 1400\nswitching = 400/200,0/400,400/2000,0/400\ninfo = 950/330,0/15,1400/330,0/15,1800/330,0/1000\nrecord = 1400/500,0/60000\nstutter = 350+440/750,440/750\n"),
 array ( "name" => "United States / North America",  "iso" => "us", "conf" => "ringcadence = 2000,4000\ndial = 350+440\nbusy = 480+620/500,0/500\nring = 440+480/2000,0/4000\ncongestion = 480+620/250,0/250\ncallwaiting = 440/300,0/10000\ndialrecall = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "United States Circa 1950/ North America",  "iso" => "us-old", "conf" => "ringcadence = 2000,4000\ndial = 600*120\nbusy = 500*100/500,0/500\nring = 420*40/2000,0/4000\ncongestion = 500*100/250,0/250\ncallwaiting = 440/300,0/10000\ndialrecall = !600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,600*120\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,!600*120/100,!0/100,600*120\n"),
 array ( "name" => "Taiwan",  "iso" => "tw", "conf" => "ringcadence = 1000,4000\ndial = 350+440\nbusy = 480+620/500,0/500\nring = 440+480/1000,0/2000\ncongestion = 480+620/250,0/250\ncallwaiting = 350+440/250,0/250,350+440/250,0/3250\ndialrecall = 300/1500,0/500\nrecord = 1400/500,0/15000\ninfo = !950/330,!1400/330,!1800/330,0\nstutter = !350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,!350+440/100,!0/100,350+440\n"),
 array ( "name" => "Venezuela / South America",  "iso" => "ve", "conf" => "; Tone definition source for ve found on\n; Reference: http://www.itu.int/ITU-T/inr/forms/files/tones-0203.pdf\nringcadence = 1000,4000\ndial = 425\nbusy = 425/500,0/500\nring = 425/1000,0/4000\ncongestion = 425/250,0/250\ncallwaiting = 400+450/300,0/6000\ndialrecall = 425\nrecord =  1400/500,0/15000\ninfo = !950/330,!1440/330,!1800/330,0/1000\n"),);
}

function general_display_zones($curzone) {
	$zonelist = general_get_zonelist();
	echo "<select name='TONEZONE'>\n";
	foreach ($zonelist as $zone) {
		if ($zone['iso'] == $curzone) 
			echo "<option selected value='{$zone['iso']}'>{$zone['name']}</option>\n";
		else	
			echo "<option value='{$zone['iso']}'>{$zone['name']}</option>\n";
	}
	echo "</select>";
	
}

function general_generate_indications() {
	global $db;
	global $asterisk_conf;

	$notify =& notifications::create($db);

	$sql = "SELECT value FROM globals WHERE variable='TONEZONE'";
	$result = $db->getRow($sql,DB_FETCHMODE_ASSOC);

	$filename = isset($asterisk_conf["astetcdir"]) && $asterisk_conf["astetcdir"] != '' ? rtrim($asterisk_conf["astetcdir"],DIRECTORY_SEPARATOR) : "/etc/asterisk";
	$filename .= "/indications.conf";

	if (($fd = fopen($filename, "w")) === false) {
		$desc = sprintf(_("Failed to open %s for writing, aborting attempt to write the country indications. The file may be readonly or the permissions may be incorrect."), $filename);
		$notify->add_error('core','INDICATIONS',_("Failed to write indications.conf"), $desc);
		return;
	}
	$notify->delete('core', 'INDICATIONS');

	fwrite($fd, "[general]\ncountry=".$result['value']."\n\n");

	$zonelist = general_get_zonelist();
	foreach ($zonelist as $zone) {
		fwrite($fd, "[{$zone['iso']}]\n{$zone['conf']}\n\n");
	}
	fclose($fd);
}
/* end page.routing.php functions */



// init registered 'your' config load and config process functions
function core_users_configpageinit($dispnum) {
	global $currentcomponent;
	global $amp_conf;

	if ( $dispnum == 'users' || $dispnum == 'extensions' ) {
		// Setup option list we need
		$currentcomponent->addoptlistitem('recordoptions', 'Adhoc', _("On Demand"));
		$currentcomponent->addoptlistitem('recordoptions', 'Always', _("Always"));
		$currentcomponent->addoptlistitem('recordoptions', 'Never', _("Never"));
		$currentcomponent->setoptlistopts('recordoptions', 'sort', false);

		$currentcomponent->addoptlistitem('callwaiting', 'enabled', _("Enable"));
		$currentcomponent->addoptlistitem('callwaiting', 'disabled', _("Disable"));
		$currentcomponent->setoptlistopts('callwaiting', 'sort', false);

		$currentcomponent->addoptlistitem('call_screen', '0', _("Disable"));
		$currentcomponent->addoptlistitem('call_screen', 'nomemory', _("Screen Caller: No Memory"));
		$currentcomponent->addoptlistitem('call_screen', 'memory', _("Screen Caller: Memory"));
		$currentcomponent->setoptlistopts('call_screen', 'sort', false);

		$currentcomponent->addoptlistitem('ringtime', '0', _("Default"));
		for ($i=1; $i <= 120; $i++) {
			$currentcomponent->addoptlistitem('ringtime', "$i", "$i");
		}
		$currentcomponent->setoptlistopts('ringtime', 'sort', false);

		// Special CID handling to deal with Private, etc.
		//
		$js = 
		'var mycid = thiscid.toLowerCase();
		if (isDialpattern(thiscid) || mycid.substring(0,4) == "priv" || mycid.substring(0,5) == "block" || mycid == "unknown" || mycid.substring(0,8) == "restrict" || mycid.substring(0,7) == "unavail" || mycid.substring(0,6) == "anonym") { return true } else { return false };
		';
		$currentcomponent->addjsfunc('isValidCID(thiscid)', $js);

		// Add the 'proces' functions
		$currentcomponent->addguifunc('core_users_configpageload');
		// Ensure users is called in middle order ($sortorder = 5), this is to allow
		// other modules to call stuff before / after the processing of users if needed
		// e.g. Voicemail module needs to create mailbox BEFORE the users as the mailbox
		// context is needed by the add users function
		$currentcomponent->addprocessfunc('core_users_configprocess', 5);			
	}
}

// Used below in usort
function dev_grp($a, $b) {
	if ($a['devicetype'] == $b['devicetype']) {
		return ($a['id'] < $b['id']) ? -1 : 1;
	} else {
		return ($a['devicetype'] > $b['devicetype']) ? -1 : 1;
	}
}

function core_users_configpageload() {
	global $currentcomponent;
	global $amp_conf;

	// Ensure variables possibly extracted later exist
	$name = $outboundcid = $record_in = $record_out =  $sipname = $cid_masquerade = $newdid_name = $newdid = $newdidcid = null;

	// Init vars from $_REQUEST[]
	$display = isset($_REQUEST['display'])?$_REQUEST['display']:null;;
	$action = isset($_REQUEST['action'])?$_REQUEST['action']:null;
	$extdisplay = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:null;
	$tech_hardware = isset($_REQUEST['tech_hardware'])?$_REQUEST['tech_hardware']:null;

	if ( $action == 'del' ) { // Deleted

		$currentcomponent->addguielem('_top', new gui_subheading('del', $extdisplay.' '._("deleted"), false));

	} elseif ( $display == 'extensions' && ($extdisplay == '' && $tech_hardware == '') ) { // Adding

		// do nothing as you want the Devices to handle this bit

	} else {

		$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=del';
	
		if ( is_string($extdisplay) ) {	

			if (!isset($GLOBALS['abort']) || $GLOBALS['abort'] !== true) {
				$extenInfo=core_users_get($extdisplay);
				extract($extenInfo);
			}
			if (isset($deviceInfo) && is_array($deviceInfo))
				extract($deviceInfo);
	
			if ( $display == 'extensions' ) {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Extension").": $extdisplay", false), 0);
				if (!isset($GLOBALS['abort']) || $GLOBALS['abort'] !== true) {
					$tlabel = sprintf(_("Delete Extension %s"),$extdisplay);
					$label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/user_delete.png"/>&nbsp;'.$tlabel.'</span>';
					$currentcomponent->addguielem('_top', new gui_link('del', $label, $delURL, true, false), 0);

					$usage_list = framework_display_destination_usage(core_getdest($extdisplay));
					if (!empty($usage_list)) {
						$currentcomponent->addguielem('_top', new gui_link_label('dests', $usage_list['text'], $usage_list['tooltip'], true), 0);
					}
				}
			} else {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("User").": $extdisplay", false), 0);
				if (!isset($GLOBALS['abort']) || $GLOBALS['abort'] !== true) {
					$tlabel = sprintf(_("Delete User %s"),$extdisplay);
					$label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/user_delete.png"/>&nbsp;'.$tlabel.'</span>';
					$currentcomponent->addguielem('_top', new gui_link('del', $label, $delURL, true, false), 0);

					$usage_list = framework_display_destination_usage(core_getdest($extdisplay));
					if (!empty($usage_list)) {
						$currentcomponent->addguielem('_top', new gui_link_label('dests', $usage_list['text'], $usage_list['tooltip'], true), 0);
					}
				}
			}

		} elseif ( $display != 'extensions' ) {
			$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Add User/Extension")), 0);
		}
		
		// Setup vars for use in the gui later on							
		$fc_logon = featurecodes_getFeatureCode('core', 'userlogon');
		$fc_logoff = featurecodes_getFeatureCode('core', 'userlogoff');
		
		$msgInvalidExtNum = _("Please enter a valid extension number.");
		$msgInvalidCidNum = _("Please enter a valid CID Num Alias (must be a valid number).");
		$msgInvalidExtPwd = _("Please enter valid User Password using numbers only");
		$msgInvalidDispName = _("Please enter a valid Display Name");
		$msgInvalidOutboundCID = _("Please enter a valid Outbound CID");
		$msgInvalidPause = _("Please enter a valid pause time in seconds, using digits only");
		$msgInvalidDIDNum = _("You have entered a non-standard dialpattern for your DID. You can only enter standard dialpatterns. You must use the inbound routing form to enter non-standard patterns");
		$msgInvalidCIDNum = _("Please enter a valid Caller ID Number or leave it blank for your Assigned DID/CID pair");

		// This is the actual gui stuff
		$currentcomponent->addguielem('_top', new gui_hidden('action', ($extdisplay ? 'edit' : 'add')));
		$currentcomponent->addguielem('_top', new gui_hidden('extdisplay', $extdisplay));
		
		if ( $display == 'extensions' ) {
			$section = ($extdisplay ? _("Edit Extension") : _("Add Extension"));			
		} else {
			$section = ($extdisplay ? _("Edit User") : _("Add User"));
		}
		if ( trim($extdisplay) != '' ) {
			$currentcomponent->addguielem($section, new gui_hidden('extension', $extdisplay), 2);
		} else {
			$currentcomponent->addguielem($section, new gui_textbox('extension', $extdisplay, _("User Extension"), _("The extension number to dial to reach this user."), '!isInteger()', $msgInvalidExtNum, false), 3);
		}
		if ( $display != 'extensions' ) {
			$currentcomponent->addguielem($section, new gui_password('password', $password, _("User Password"), _("A user will enter this password when logging onto a device.").' '.$fc_logon.' '._("logs into a device.").' '.$fc_logoff.' '._("logs out of a device."), '!isInteger() && !isWhitespace()', $msgInvalidExtPwd, true));
			// extra JS function check required for blank password warning -- call last in the onsubmit() function
			$currentcomponent->addjsfunc('onsubmit()', "\treturn checkBlankUserPwd();\n", 9);
		}
		$currentcomponent->addguielem($section, new gui_textbox('name', $name, _("Display Name"), _("The caller id name for calls from this user will be set to this name. Only enter the name, NOT the number."),  '!isAlphanumeric() || isWhitespace()', $msgInvalidDispName, false));
		$cid_masquerade = (trim($cid_masquerade) == $extdisplay)?"":$cid_masquerade;
		$currentcomponent->addguielem($section, new gui_textbox('cid_masquerade', $cid_masquerade, _("CID Num Alias"), _("The CID Number to use for internal calls, if different from the extension number. This is used to masquerade as a different user. A common example is a team of support people who would like their internal callerid to display the general support number (a ringgroup or queue). There will be no effect on external calls."), '!isWhitespace() && !isInteger()', $msgInvalidCidNum, false));
		$currentcomponent->addguielem($section, new gui_textbox('sipname', $sipname, _("SIP Alias"), _("If you want to support direct sip dialing of users internally or through anonymous sip calls, you can supply a friendly name that can be used in addition to the users extension to call them.")));

		// If user mode, list devices associated with this user
		//
		if ($display == 'users' && trim($extdisplay != '')) {
			$section = _("User Devices");
			$device_list = core_devices_list('all','full');

			usort($device_list,'dev_grp');

			$link_count = 0;
			foreach ($device_list as $device_item) {
				if ($device_item['user'] == $extdisplay) {
					$editURL = $_SERVER['PHP_SELF'].'?type=setup&display=devices&skip=0&extdisplay='.$device_item['id'];
					$device_icon = ($device_item['devicetype'] == 'fixed') ? 'images/telephone_key.png' : 'images/telephone_edit.png';
					$device_label  = '&nbsp;';
					$device_label .=  _("Edit:");
					$device_label .= '&nbsp;'.$device_item['id'].'&nbsp;'.$device_item['description'];

					$device_label = '<span>
						<img width="16" height="16" border="0" title="Edit Device" alt="Edit Device" src="'.$device_icon.'"/>'.$device_label.
						'</span> ';
					
					$currentcomponent->addguielem($section, new gui_link('dev'.$link_count++, $device_label, $editURL, true, false), 2);
				}
			}
		}
		
		$section = _("Extension Options");
		$currentcomponent->addguielem($section, new gui_textbox('outboundcid', $outboundcid, _("Outbound CID"), _("Overrides the caller id when dialing out a trunk. Any setting here will override the common outbound caller id set in the Trunks admin.<br><br>Format: <b>\"caller name\" &lt;#######&gt;</b><br><br>Leave this field blank to disable the outbound callerid feature for this user."), '!isCallerID()', $msgInvalidOutboundCID, true),3);
		$ringtimer = (isset($ringtimer) ? $ringtimer : '0');
		$currentcomponent->addguielem($section, new gui_selectbox('ringtimer', $currentcomponent->getoptlist('ringtime'), $ringtimer, _("Ring Time"), _("Number of seconds to ring prior to going to voicemail. Default will use the value set in the General Tab. If no voicemail is configured this will be ignored."), false));
		if (!isset($callwaiting)) {
			if ($amp_conf['ENABLECW']) {
				$callwaiting = 'enabled';
			} else {
				$callwaiting = 'disabled';
			}
		}
		$currentcomponent->addguielem($section, new gui_selectbox('callwaiting', $currentcomponent->getoptlist('callwaiting'), $callwaiting, _("Call Waiting"), _("Set the initial/current Call Waiting state for this user's extension"), false));
		$currentcomponent->addguielem($section, new gui_selectbox('call_screen', $currentcomponent->getoptlist('call_screen'), $call_screen, _("Call Screening"),_("Call Screening requires external callers to say their name, which will be played back to the user and allow the user to accept or reject the call.  Screening with memory only verifies a caller for their caller-id once. Screening without memory always requires a caller to say their name. Either mode will always announce the caller based on the last introduction saved with that callerid. If any user on the system uses the memory option, when that user is called, the caller will be required to re-introduce themselves and all users on the system will have that new introduction associated with the caller's CallerId."), false));

		$section = _("Assigned DID/CID");
		$currentcomponent->addguielem($section, new gui_textbox('newdid_name', $newdid_name, _("DID Description"), _("A description for this DID, such as \"Fax\"")), 4);
		$currentcomponent->addguielem($section, new gui_textbox('newdid', $newdid, _("Add Inbound DID"), _("A direct DID that is associated with this extension. The DID should be in the same format as provided by the provider (e.g. full number, 4 digits for 10x4, etc).<br><br>Format should be: <b>XXXXXXXXXX</b><br><br>.An optional CID can also be associated with this DID by setting the next box"),'!isDialpattern()',$msgInvalidDIDNum,true), 4);
		$currentcomponent->addguielem($section, new gui_textbox('newdidcid', $newdidcid, _("Add Inbound CID"), _("Add a CID for more specific DID + CID routing. A DID must be specified in the above Add DID box. In addition to standard dial sequences, you can also put Private, Blocked, Unknown, Restricted, Anonymous and Unavailable in order to catch these special cases if the Telco transmits them."),"!frm_${display}_isValidCID()",$msgInvalidCIDNum,true), 4);

		$dids = core_did_list('extension');
		$did_count = 0;
		foreach ($dids as $did) {
			$did_dest = split(',',$did['destination']);
			if (isset($did_dest[1]) && $did_dest[1] == $extdisplay) {

				$did_title = ($did['description'] != '') ? $did['description'] : _("DID / CID");

				$addURL = $_SERVER['PHP_SELF'].'?type=setup&display=did&&extdisplay='.$did['extension'].'/'.$did['cidnum'];
				$did_icon = 'images/email_edit.png';
				$did_label = trim($did['extension']) == '' ? ' '._("Any DID") : ' '.$did['extension'];
				if (trim($did['cidnum']) != '') {
					$did_label .= ' / '.$did['cidnum'];
				}
				if (trim($did['description']) != '') {
					$did_label .= ' ('.$did['description'].')';
				}

				$did_label = '&nbsp;<span>
					<img width="16" height="16" border="0" title="'.$did_title.'" alt="" src="'.$did_icon.'"/>'.$did_label.
					'</span> ';

				$currentcomponent->addguielem($section, new gui_link('did_'.$did_count++, $did_label, $addURL, true, false), 4);
			}
		}

		$section = _("Recording Options");
		$currentcomponent->addguielem($section, new gui_selectbox('record_in', $currentcomponent->getoptlist('recordoptions'), $record_in, _("Record Incoming"), _("Record all inbound calls received at this extension."), false));
		$currentcomponent->addguielem($section, new gui_selectbox('record_out', $currentcomponent->getoptlist('recordoptions'), $record_out, _("Record Outgoing"), _("Record all outbound calls received at this extension."), false));
	}
}

function core_users_configprocess() {
	if ( !class_exists('agi_asteriskmanager') )
		include 'common/php-asmanager.php';
	
	//create vars from the request
	extract($_REQUEST);
	
	//make sure we can connect to Asterisk Manager
	if (!checkAstMan()) {
		return false;
	}

	//check if the extension is within range for this user
	if (isset($extension) && !checkRange($extension)){
		echo "<script>javascript:alert('". _("Warning! Extension")." ".$extension." "._("is not allowed for your account").".');</script>";
		$GLOBALS['abort'] = true;
	} else {
		//if submitting form, update database
		if (!isset($action)) $action = null;
		switch ($action) {
			case "add":
				$conflict_url = array();
				$usage_arr = framework_check_extension_usage($_REQUEST['extension']);
				if (!empty($usage_arr)) {
					$GLOBALS['abort'] = true;
					$conflict_url = framework_display_extension_usage_alert($usage_arr,true);
					global $currentcomponent;
					$id=0;
					$currentcomponent->addguielem('_top', new gui_link_label('conflict', _("Conflicting Extensions"), _("The following extension numbers are in conflict, you can click on the item(s) below to edit the conflicting entity."), true));
					foreach ($conflict_url as $edit_link) {
						$currentcomponent->addguielem('_top', new gui_link('conflict'.$i++, $edit_link['label'], $edit_link['url']));
					}
					$msg = ($_REQUEST['display'] == 'users') ? _("Configure user again:") : _("Configure extension again:");
					$currentcomponent->addguielem('_top', new gui_subheading('conflict_end', $msg, false));
					unset($_REQUEST['action']);
					redirect_standard_continue();
				} elseif (core_users_add($_REQUEST)) {
					needreload();
					redirect_standard_continue();
				} else {
					// really bad hack - but if core_users_add fails, want to stop core_devices_add
					// Comment, this does not help everywhere. Other hooks functions can hook before
					// this like voicemail!
					//
					$GLOBALS['abort'] = true;
				}
			break;
			case "del":
				core_users_del($extdisplay);
				core_users_cleanastdb($extdisplay);
				if (function_exists('findmefollow_del')) {
				    findmefollow_del($extdisplay);
				}
				needreload();
				redirect_standard_continue();
			break;
			case "edit":
				if (core_users_edit($extdisplay,$_REQUEST)) {
					needreload();
					redirect_standard_continue('extdisplay');
				} else {
					// really bad hack - but if core_users_edit fails, want to stop core_devices_edit
					$GLOBALS['abort'] = true;
				}
			break;
		}
	}
	return true;
}


function core_devices_configpageinit($dispnum) {
	global $currentcomponent;

	if ( $dispnum == 'devices' || $dispnum == 'extensions' ) {
		// Setup arrays for device types
		$currentcomponent->addgeneralarray('devtechs');
		
		// Some errors for the validation bits
		$msgInvalidDTMFMODE = _("Please enter the dtmfmode for this device");
		$msgInvalidChannel = _("Please enter the channel for this device");
		$msgConfirmSecret = _("You have not entered a Secret for this device, although this is possible it is generally bad practice to not assign a Secret to a device. Are you sure you want to leave the Secret empty?");
		$msgInvalidSecret = _("Please enter a Secret for this device");
		
		// zap
		$tmparr = array();
		$tmparr['channel'] = array('value' => '', 'level' => 0, 'jsvalidation' => 'isEmpty()', 'failvalidationmsg' => $msgInvalidChannel);
		$tmparr['context'] = array('value' => 'from-internal', 'level' => 1);
		$tmparr['immediate'] = array('value' => 'no', 'level' => 1);
		$tmparr['signalling'] = array('value' => 'fxo_ks', 'level' => 1);
		$tmparr['echocancel'] = array('value' => 'yes', 'level' => 1);
		$tmparr['echocancelwhenbridged'] = array('value' => 'no', 'level' => 1);
		$tmparr['echotraining'] = array('value' => '800', 'level' => 1);
		$tmparr['busydetect'] = array('value' => 'no', 'level' => 1);
		$tmparr['busycount'] = array('value' => '7', 'level' => 1);
		$tmparr['callprogress'] = array('value' => 'no', 'level' => 1);
		$tmparr['dial'] = array('value' => '', 'level' => 1);
		$tmparr['accountcode'] = array('value' => '', 'level' => 1);
		$tmparr['callgroup'] = array('value' => '', 'level' => 1);
		$tmparr['pickupgroup'] = array('value' => '', 'level' => 1);
		$tmparr['mailbox'] = array('value' => '', 'level' => 1);
		$currentcomponent->addgeneralarrayitem('devtechs', 'zap', $tmparr);
		unset($tmparr);
		
		// iax2
		$tmparr = array();
		$tmparr['secret'] = array('value' => '', 'level' => 0, 'jsvalidation' => 'isEmpty() && !confirm("'.$msgConfirmSecret.'")', 'failvalidationmsg' => $msgInvalidSecret);
		$tmparr['notransfer'] = array('value' => 'yes', 'level' => 1);
		$tmparr['context'] = array('value' => 'from-internal', 'level' => 1);
		$tmparr['host'] = array('value' => 'dynamic', 'level' => 1);
		$tmparr['type'] = array('value' => 'friend', 'level' => 1);
		$tmparr['port'] = array('value' => '4569', 'level' => 1);
		$tmparr['qualify'] = array('value' => 'yes', 'level' => 1);
		$tmparr['disallow'] = array('value' => '', 'level' => 1);
		$tmparr['allow'] = array('value' => '', 'level' => 1);
		$tmparr['dial'] = array('value' => '', 'level' => 1);
		$tmparr['accountcode'] = array('value' => '', 'level' => 1);
		$tmparr['mailbox'] = array('value' => '', 'level' => 1);
		$tmparr['deny'] = array('value' => '0.0.0.0/0.0.0.0', 'level' => 1);
		$tmparr['permit'] = array('value' => '0.0.0.0/0.0.0.0', 'level' => 1);
		$currentcomponent->addgeneralarrayitem('devtechs', 'iax2', $tmparr);
		unset($tmparr);

		// sip
		$tmparr = array();
		$tmparr['secret'] = array('value' => '', 'level' => 0, 'jsvalidation' => 'isEmpty() && !confirm("'.$msgConfirmSecret.'")', 'failvalidationmsg' => $msgInvalidSecret);
		$tmparr['dtmfmode'] = array('value' => 'rfc2833', 'level' => 0, 'jsvalidation' => 'isEmpty()', 'failvalidationmsg' => $msgInvalidDTMFMODE );
		$tmparr['canreinvite'] = array('value' => 'no', 'level' => 1);
		$tmparr['context'] = array('value' => 'from-internal', 'level' => 1);
		$tmparr['host'] = array('value' => 'dynamic', 'level' => 1);
		$tmparr['type'] = array('value' => 'friend', 'level' => 1);
		$tmparr['nat'] = array('value' => 'yes', 'level' => 1);
		$tmparr['port'] = array('value' => '5060', 'level' => 1);
		$tmparr['qualify'] = array('value' => 'yes', 'level' => 1);
		$tmparr['callgroup'] = array('value' => '', 'level' => 1);
		$tmparr['pickupgroup'] = array('value' => '', 'level' => 1);
		$tmparr['disallow'] = array('value' => '', 'level' => 1);
		$tmparr['allow'] = array('value' => '', 'level' => 1);
		$tmparr['dial'] = array('value' => '', 'level' => 1);
		$tmparr['accountcode'] = array('value' => '', 'level' => 1);
		$tmparr['mailbox'] = array('value' => '', 'level' => 1);
		$tmparr['deny'] = array('value' => '0.0.0.0/0.0.0.0', 'level' => 1);
    		$tmparr['permit'] = array('value' => '0.0.0.0/0.0.0.0', 'level' => 1);
		$currentcomponent->addgeneralarrayitem('devtechs', 'sip', $tmparr);
		unset($tmparr);

		// custom
		$tmparr = array();
		$tmparr['dial'] = array('value' => '', 'level' => 0);
		$currentcomponent->addgeneralarrayitem('devtechs', 'custom', $tmparr);
		unset($tmparr);
		
		// Devices list
		$currentcomponent->addoptlistitem('devicelist', 'sip_generic', _("Generic SIP Device"));
		$currentcomponent->addoptlistitem('devicelist', 'iax2_generic', _("Generic IAX2 Device"));
		$currentcomponent->addoptlistitem('devicelist', 'zap_generic', _("Generic ZAP Device"));
		$currentcomponent->addoptlistitem('devicelist', 'custom_custom', _("Other (Custom) Device"));
		$currentcomponent->setoptlistopts('devicelist', 'sort', false);


		// Option lists used by the gui
		$currentcomponent->addoptlistitem('devicetypelist', 'fixed', _("Fixed"));
		$currentcomponent->addoptlistitem('devicetypelist', 'adhoc', _("Adhoc"));
		$currentcomponent->setoptlistopts('devicetypelist', 'sort', false);
		
		$currentcomponent->addoptlistitem('deviceuserlist', 'none', _("none"));
		$currentcomponent->addoptlistitem('deviceuserlist', 'new', _("New User"));
		$users = core_users_list();
		if (isset($users)) {
			foreach ($users as $auser) {
				$currentcomponent->addoptlistitem('deviceuserlist', $auser[0], $auser[0]);
			}
		}
		$currentcomponent->setoptlistopts('deviceuserlist', 'sort', false);

		// Add the 'proces' functions
		$currentcomponent->addguifunc('core_devices_configpageload');
		$currentcomponent->addprocessfunc('core_devices_configprocess');
	}
}

function core_devices_configpageload() {
	global $currentcomponent;

	// Init vars from $_REQUEST[]
	$display = isset($_REQUEST['display'])?$_REQUEST['display']:null;;
	$action = isset($_REQUEST['action'])?$_REQUEST['action']:null;
	$extdisplay = isset($_REQUEST['extdisplay'])?$_REQUEST['extdisplay']:null;
	$tech_hardware = isset($_REQUEST['tech_hardware'])?$_REQUEST['tech_hardware']:null;
	
	if ( $action == 'del' ) { // Deleted

		if ( $display != 'extensions' )
			$currentcomponent->addguielem('_top', new gui_subheading('del', $extdisplay.' '._("deleted"), false));

	} elseif ( $extdisplay == '' && $tech_hardware == '' ) { // Adding

		if ($_SESSION["AMP_user"]->checkSection('999')) {
			if ( $display != 'extensions') {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Add Device")), 0);
			} else {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Add an Extension")), 0);
			}
			$currentcomponent->addguielem('_top', new gui_label('instructions', _("Please select your Device below then click Submit")));
			$currentcomponent->addguielem('Device', new gui_selectbox('tech_hardware', $currentcomponent->getoptlist('devicelist'), '', _("Device"), '', false));
		} else {
			if ( $display != 'extensions') {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Edit existing Device")), 0);
			} else {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Edit existing Extension")), 0);
			}
		}

	} else {

		$deviceInfo = array();
		if ( $extdisplay ) { // Editing

			$deviceInfo = core_devices_get($extdisplay);

			if ( $display != 'extensions' ) {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Device").": $extdisplay", false), 0);

				$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=del';
				$tlabel = sprintf(_("Delete Device %s"),$extdisplay);
				$label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/telephone_delete.png"/>&nbsp;'.$tlabel.'</span>';
				$currentcomponent->addguielem('_top', new gui_link('del', $label, $delURL, true, false), 0);

				if ($deviceInfo['device_user'] != 'none') {
					$editURL = $_SERVER['PHP_SELF'].'?type=setup&display=users&skip=0&extdisplay='.$deviceInfo['user'];
					$tlabel =  $deviceInfo['devicetype'] == 'adhoc' ? sprintf(_("Edit Default User: %s"),$deviceInfo['user']) : sprintf(_("Edit Fixed User: %s"),$deviceInfo['user']);
					$label = '<span><img width="16" height="16" border="0" title="'.$tlabel.'" alt="" src="images/user_edit.png"/>&nbsp;'.$tlabel.'</span>';
					$currentcomponent->addguielem('_top', new gui_link('edit_user', $label, $editURL, true, false), 0);
				}
			}
		} else {

			$tmparr = explode('_', $tech_hardware);
			$deviceInfo['tech'] = $tmparr[0];
			$deviceInfo['hardware'] = $tmparr[1];
			unset($tmparr);
			
			if ( $display != 'extensions' ) {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Add").' '.strtoupper($deviceInfo['tech']).' '._("Device")), 0);
			} else {
				$currentcomponent->addguielem('_top', new gui_pageheading('title', _("Add").' '.strtoupper($deviceInfo['tech']).' '._("Extension")), 0);
			}
		}

		// Ensure they exist before the extract
		$devinfo_description = $devinfo_emergency_cid = null;
		$devinfo_devicetype = $devinfo_user = $devinfo_hardware = null;
		if ( is_array($deviceInfo) ) {
			extract($deviceInfo, EXTR_PREFIX_ALL, 'devinfo');
		}

		// Setup vars for use in the gui later on							
		$fc_logon = featurecodes_getFeatureCode('core', 'userlogon');
		$fc_logoff = featurecodes_getFeatureCode('core', 'userlogoff');

		$msgInvalidDevID = _("Please enter a device id.");
		$msgInvalidDevDesc = _("Please enter a valid Description for this device");
		$msgInvalidEmergCID = _("Please enter a valid Emergency CID");
		$msgInvalidExtNum = _("Please enter a valid extension number.");
		
		// Actual gui
		$currentcomponent->addguielem('_top', new gui_hidden('action', ($extdisplay ? 'edit' : 'add')));
		$currentcomponent->addguielem('_top', new gui_hidden('extdisplay', $extdisplay));

		if ( $display != 'extensions' ) {
			$section = _("Device Info");
			if ( $extdisplay ) { // Editing
				$currentcomponent->addguielem($section, new gui_hidden('deviceid', $extdisplay));
			} else { // Adding
				$currentcomponent->addguielem($section, new gui_textbox('deviceid', $extdisplay, _("Device ID"), _("Give your device a unique integer ID.  The device will use this ID to authenticate to the system."), '!isInteger()', $msgInvalidDevID, false));
			}
			$currentcomponent->addguielem($section, new gui_textbox('description', $devinfo_description, _("Description"), _("The caller id name for this device will be set to this description until it is logged into."), '!isAlphanumeric() || isWhitespace()', $msgInvalidDevDesc, false));
			$currentcomponent->addguielem($section, new gui_textbox('emergency_cid', $devinfo_emergency_cid, _("Emergency CID"), _("This caller id will always be set when dialing out an Outbound Route flagged as Emergency.  The Emergency CID overrides all other caller id settings."), '!isCallerID()', $msgInvalidEmergCID));
			$currentcomponent->addguielem($section, new gui_selectbox('devicetype', $currentcomponent->getoptlist('devicetypelist'), $devinfo_devicetype, _("Device Type"), _("Devices can be fixed or adhoc. Fixed devices are always associated to the same extension/user. Adhoc devices can be logged into and logged out of by users.").' '.$fc_logon.' '._("logs into a device.").' '.$fc_logoff.' '._("logs out of a device."), false));
			$currentcomponent->addguielem($section, new gui_selectbox('deviceuser', $currentcomponent->getoptlist('deviceuserlist'), $devinfo_user, _("Default User"), _("Fixed devices will always mapped to this user.  Adhoc devices will be mapped to this user by default.<br><br>If selecting 'New User', a new User Extension of the same Device ID will be set as the Default User."), false));
		} else {
			$section = _("Extension Options");
			$currentcomponent->addguielem($section, new gui_textbox('emergency_cid', $devinfo_emergency_cid, _("Emergency CID"), _("This caller id will always be set when dialing out an Outbound Route flagged as Emergency.  The Emergency CID overrides all other caller id settings."), '!isCallerID()', $msgInvalidEmergCID));
		}
		$currentcomponent->addguielem($section, new gui_hidden('tech', $devinfo_tech));
		$currentcomponent->addguielem($section, new gui_hidden('hardware', $devinfo_hardware));

		$section = _("Device Options");

		$device_uses = sprintf(_("This device uses %s technology."),$devinfo_tech).(strtoupper($devinfo_tech) == 'ZAP' && ast_with_dahdi()?" ("._("Via DAHDI compatibility mode").")":"");
		$currentcomponent->addguielem($section, new gui_label('techlabel', $device_uses),4);
		$devopts = $currentcomponent->getgeneralarrayitem('devtechs', $devinfo_tech);
		if (is_array($devopts)) {
			foreach ($devopts as $devopt=>$devoptarr) {
				$devopname = 'devinfo_'.$devopt;
				$devoptcurrent = isset($$devopname) ? $$devopname : $devoptarr['value'];
				$devoptjs = isset($devoptarr['jsvalidation']) ? $devoptarr['jsvalidation'] : '';
				$devoptfailmsg = isset($devoptarr['failvalidationmsg']) ? $devoptarr['failvalidationmsg'] : '';

				if ( $devoptarr['level'] == 0 || ($extdisplay && $devoptarr['level'] == 1) ) { // editing to show advanced as well
					$currentcomponent->addguielem($section, new gui_textbox($devopname, $devoptcurrent, $devopt, '', $devoptjs, $devoptfailmsg), 4);
				} else { // add so only basic
					$currentcomponent->addguielem($section, new gui_hidden($devopname, $devoptcurrent), 4);
				}
			}
		}
	}
}

function core_devices_configprocess() {
	if ( !class_exists('agi_asteriskmanager') )
		include 'common/php-asmanager.php';

	//make sure we can connect to Asterisk Manager
	if (!checkAstMan()) {
		return false;
	}
	
	//create vars from the request
	extract($_REQUEST);

	$extension = isset($extension)?$extension:null;
	$deviceid = isset($deviceid)?$deviceid:null;
	$name = isset($name)?$name:null;
	$action = isset($action)?$action:null;

	// fixed users only in extensions mode
	if ( $display == 'extensions' ) {
		$devicetype = 'fixed';
		$deviceid = $deviceuser = $extension;
        $description = $name;
	}
	
	//if submitting form, update database
	switch ($action) {
		case "add":
		// really bad hack - but if core_users_add fails, want to stop core_devices_add

		if (!isset($GLOBALS['abort']) || $GLOBALS['abort'] !== true || !$_SESSION["AMP_user"]->checkSection('999')) {
			if (core_devices_add($deviceid,$tech,$devinfo_dial,$devicetype,$deviceuser,$description,$emergency_cid)) {
				needreload();
				if ($deviceuser != 'new') {
					redirect_standard_continue();
				}
			}
		} else {
			// This is a bit messy, because by this time, other modules may have added the device but this tries to block
			// the user who does not have add permission from adding a new extension.
			//
			$GLOBALS['abort'] = true;
		}	
		break;
		case "del":
			core_devices_del($extdisplay);
			needreload();
			redirect_standard_continue();
		break;
		case "edit":  //just delete and re-add
			// really bad hack - but if core_users_edit fails, want to stop core_devices_edit
			if (!isset($GLOBALS['abort']) || $GLOBALS['abort'] !== true) {
				core_devices_del($extdisplay,true);
				core_devices_add($deviceid,$tech,$devinfo_dial,$devicetype,$deviceuser,$description,$emergency_cid,true);
				needreload();
				redirect_standard_continue('extdisplay');
			}
			break;
			case "resetall":  //form a url with this option to nuke the AMPUSER & DEVICE trees and start over.
				core_users2astdb();
				core_devices2astdb();
			break;
	}
	return true;
}

?>
