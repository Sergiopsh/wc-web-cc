<?php
// auto-generated by sfDefineEnvironmentConfigHandler
// date: 2010/07/22 11:21:02
sfConfig::add(array(
  'sf_logging_enabled' => true,
  'sf_logging_level' => 'err',
  'sf_logging_rotate' => true,
  'sf_logging_period' => 7,
  'sf_logging_history' => 10,
  'sf_logging_purge' => true,
));

$logger = sfLogger::getInstance();
$logger->setLogLevel(constant('SF_LOG_'.strtoupper(sfConfig::get('sf_logging_level'))));

$log = new sfFileLogger();
$log->initialize(array (
  'file' => '/var/www/html/sell/log/asterisell_prod.log',
));
$logger->registerLogger($log);
