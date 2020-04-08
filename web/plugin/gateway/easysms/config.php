<?php
defined('_SECURE_') or die('Forbidden');

$data = registry_search(0, 'gateway', 'easysms');
$plugin_config['easysms'] = $data['gateway']['easysms'];
$plugin_config['easysms']['name'] = 'EasySMS';
$plugin_config['easysms']['url'] = 'http://easysms.gr/api/sms/send';

// smsc configuration
$plugin_config['easysms']['_smsc_config_'] = array(
	'APIKey' => _('API Key'),
	'module_sender' => _('Module sender ID'),
	'datetime_timezone' => _('Module timezone')
);
