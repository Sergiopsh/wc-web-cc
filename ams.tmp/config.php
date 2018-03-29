<?php

/************************************************/
$db_host="localhost";
$db_port="3306";
$db_user="root";
$db_pass="";
$db_name="ams";
$db_type="mysql";

/***********************************************/

$www_dir="/ams";
$tmp_dir="/tmp";
$config_dir="/etc/asterisk";
$spool_dir="/var/spool/asterisk";
$lib_dir="/var/lib/asterisk";
$monitor_dir="/var/spool/asterisk/monitor";
$faxes_dir="/var/spool/asterisk/faxes";
$voicemail_dir="/var/spool/asterisk/voicemail";
$sound_dir="/var/lib/asterisk/sounds";
$log_dir="/var/log/asterisk";
$logfiles_dirs=array($log_dir);


/***********************************************/
$ami_ip="127.0.0.1";
$ami_port="5038";
$ami_login="admin";
$ami_psw="djljrfyfk";
$asterisk_http_url="http://10.10.11.34:9080/asterisk";

/**********************************************/

$httpTimeout=100000; //ms 
$defaultFromAddress="AMS@localhost";
$defaultFromName="Asterisk Management System";
$mailer="smtp";
$mail_host="localhost";
//module to start with
$default_module="Home";
//action to start with
$default_action="";
$default_lang="en_us"; // this language will be in login box
$default_theme="Original";
$default_currency="USD";
$default_dateformat="21-12-2006";
$languages=array('en_us'=>'US English',
		 'ru_ru'=>'Russian');

$dateformats=array("21-12-2006","12-21-2006","2006-12-21","21/12/2006","21.12.2006");
$limit_display=20;
$client_charset="windows-1251";
$configfiles_dirs=array(array($config_dir,"--Asterisk Config--",array("*.conf","*.ael")));

$filemanager_dirs=array('Asterisk Libs'=>'/var/lib/asterisk',
			'Asterisk Sounds'=>'/var/lib/asterisk/sounds',
			'Asterisk Spool'=>'/var/spool/asterisk');

$audio_exts=array('wav','gsm','ulaw','ul','al','mu','alaw','g729','g722','ogg','pcm','sln','g723','vox','au','wav49');
$max_file_size=2000000;

/***********************************/

include_once("lib/DB-modules/phplib_".$db_type.".php");

$show_action_time=true;
$err_reporting=0;
$sql_protect=0;
$sql_injections=array("select","union","update","delete");

?>