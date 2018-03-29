<?php
$registered_modules=array("Home","Employees","Operators","TariffPlans","Reports","Recording","Administration","VirtualFax","ivr","autocall","iFrames","Departments","AstDB","MySettings","System","ConfigFiles","CodesDirectory","FileManager","Rates","Roles","Modules","Currencies","Users","AstMonitor","ReportsOper","ReportsSuper","ReportsSuper2","operator_queue","operator_reports","supervisor");
$admin_only_modules=array("Administration","AstDB","System","ConfigFiles","FileManager","Roles","Modules","Currencies","Users");
$aux_modules=array("AstMonitor","Departments","AstDB","MySettings","System","ConfigFiles","CodesDirectory","FileManager","Rates","Roles","Modules","Currencies","Users","Operators");


$hidden_modules=array("Home","Administration","Departments","AstDB","MySettings","Modules","Operators","AstMonitor");
$root_modules=array("Modules");
$main_menu=array(
		 array(array("Home","images/home.gif"),$strHome,"javascript:loadModule(0,'Home','Home','');"),
		 array(array("supervisor","images/reports.gif"),$strsupervisor,"javascript:loadModule(0,'supervisor','supervisor','Queue');"),
		 array(array("Employees","images/employees.gif"),$strEmployees,"javascript:loadModule(0,'Employees','Employees','EmployeesList')"),
		 array(array("TariffPlans","images/tplans.gif"),$strTariffPlans,"javascript:loadModule(0,'TariffPlans','TariffPlans','TariffPlansList');"),
 		 array(array("ReportsSuper","images/reports.gif"),$strReportsSuper,"javascript:loadModule(0,'ReportsSuper','ReportsSuper','CommonReports');"),
		 array(array("Recording","images/rec.gif"),$strRecording,"javascript:loadModule(0,'Recording','Recording','RecordingList');"),
		 array(array("VirtualFax","images/fax.gif"),$strVirtualFax,"javascript:loadModule(0,'VirtualFax','VirtualFax','FaxesList')"),
		 array(array("autocall","images/fax.gif"),$strautocall,"javascript:loadModule(0,'autocall','autocall','autocall')"),
		 array(array("ivr","images/fax.gif"),$strivr,"javascript:loadModule(0,'ivr','ivr','ivr')"),
		 array(array("operator_reports","images/reports.gif"),$stroperator_reports,"javascript:loadModule(0,'operator_reports','operator_reports','Common');"),
 		 array(array("operator_queue","images/reports.gif"),$stroperator_queue,"javascript:loadModule(0,'operator_queue','operator_queue','Status');"),
 		 
		 array(array("iFrames","images/iframes.gif"),$striFrames,"javascript:loadModule(0,'iFrames','iFrames','iFramesList')")
		 );



?>
