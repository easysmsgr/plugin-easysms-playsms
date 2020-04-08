<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

if (!auth_isadmin()) {
	auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/easysms/config.php";

$callback_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/plugin/gateway/easysms/callback.php";
$callback_url = str_replace("//", "/", $callback_url);
$callback_url = "http://" . $callback_url;

switch (_OP_) {
	case "manage":
		$tpl = array(
			'name' => 'easysms',
			'vars' => array(
				'DIALOG_DISPLAY' => _dialog(),
				'Manage easysms' => _('Manage easysms'),
				'Gateway name' => _('Gateway name'),
				'APIKey' => _('API Key'),
				'Module sender ID' => _('Module sender ID'),
				'Module timezone' => _('Module timezone'),
				'Save' => _('Save'),
				'Notes' => _('Notes'),
				// 'CALLBACK_URL' => _HTTP_PATH_PLUG_ . '/gateway/easysms/callback.php',
				'HINT_API_KEY' => _hint(_('Sign up with easysms.gr to get your API Key')),
				'HINT_MODULE_SENDER' => _hint(_('Max. 16 numeric or 11 alphanumeric char. empty to disable')),
				'HINT_TIMEZONE' => _hint(_('Eg: +0700 for Jakarta/Bangkok timezone')),
				'EASYSMS_SIGN_UP' => _('To get your API Key sign up with'),
				'CALLBACK_URL_IS' => _('Your callback URL is'),
				'CALLBACK_URL_ACCESSIBLE' => _('Your callback URL should be accessible from EasySMS'),
				'EASYSMS_PUSH_DLR' => _('EasySMS will push DLR in real time to your callback URL'),
				'EASYSMS_IS_BULK' => _('EasySMS is a high quality SMS provider'),
				'EASYSMS_FREE_CREDIT' => _('free credits are available for testing purposes'),
				'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
				'status_active' => $status_active,
				'easysms_param_apikey' => $plugin_config['easysms']['APIKey'],
				'easysms_param_module_sender' => $plugin_config['easysms']['module_sender'],
				'easysms_param_datetime_timezone' => $plugin_config['easysms']['datetime_timezone'],
				'callback_url' => $callback_url
			)
		);
		_p(tpl_apply($tpl));
		break;

	case "manage_save":
		//$up_url = ($_REQUEST['up_url'] ? $_REQUEST['up_url'] : $plugin_config['easysms']['default_url']);
		$up_url = $plugin_config['easysms']['url'];
		$up_apikey = $_REQUEST['up_apikey'];
		$up_module_sender = $_REQUEST['up_module_sender'];
		$up_datetime_timezone = $_REQUEST['up_datetime_timezone'];
		if ($up_url) {
			$items = array(
				'url' => $up_url,
				'APIKey' => $up_apikey,
				'module_sender' => $up_module_sender,
				'datetime_timezone' => $up_datetime_timezone
			);
			if (registry_update(0, 'gateway', 'easysms', $items)) {
				$_SESSION['dialog']['info'][] = _('Gateway module configurations has been saved');
			} else {
				$_SESSION['dialog']['danger'][] = _('Fail to save gateway module configurations');
			}
		} else {
			$_SESSION['dialog']['danger'][] = _('All mandatory fields must be filled');
		}
		header("Location: " . _u('index.php?app=main&inc=gateway_easysms&op=manage'));
		exit();
		break;
}
