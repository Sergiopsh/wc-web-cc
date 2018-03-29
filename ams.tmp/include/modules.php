<?php
$registered_modules=array("Home","Employees","Operators","TariffPlans","Reports","Recording","Administration","VirtualFax","iFrames","Departments","AstDB","MySettings","System","ConfigFiles","CodesDirectory","FileManager","Rates","Roles","Modules","Currencies","Users","AstMonitor");
$admin_only_modules=array("Administration","AstDB","System","ConfigFiles","FileManager","Roles","Modules","Currencies","Users");
$aux_modules=array("AstMonitor","Departments","AstDB","MySettings","System","ConfigFiles","CodesDirectory","FileManager","Rates","Roles","Modules","Currencies","Users","Operators");


$hidden_modules=array("Home","Administration","Departments","AstDB","MySettings","Modules","Operators","AstMonitor");
$root_modules=array("Modules");
$main_menu=array(array(array("Home","images/home.gif"),$strHome,"javascript:loadModule(0,'Home','Home','');"),
		 array(array("Employees","images/employees.gif"),$strEmployees,"javascript:loadModule(0,'Employees','Employees','EmployeesList')"),
		 array(array("TariffPlans","images/tplans.gif"),$strTariffPlans,"javascript:loadModule(0,'TariffPlans','TariffPlans','TariffPlansList');"),
		 array(array("Reports","images/reports.gif"),$strReports,"javascript:loadModule(0,'Reports','Reports','ReportsCDR');"),
		 array(array("Recording","images/rec.gif"),$strRecording,"javascript:loadModule(0,'Recording','Recording','RecordingList');"),
		 array(array("VirtualFax","images/fax.gif"),$strVirtualFax,"javascript:loadModule(0,'VirtualFax','VirtualFax','FaxesList')"),
		 array(array("iFrames","images/iframes.gif"),$striFrames,"javascript:loadModule(0,'iFrames','iFrames','iFramesList')"));



?>