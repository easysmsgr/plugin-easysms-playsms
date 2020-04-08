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

// hook_sendsms
// called by main sms sender
// return true for success delivery
// $smsc : smsc
// $sms_sender : sender mobile number
// $sms_footer : sender sms footer or sms sender ID
// $sms_to : destination sms number
// $sms_msg : sms message tobe delivered
// $gpid : group phonebook id (optional)
// $uid : sender User ID
// $smslog_id : sms ID
function easysms_hook_sendsms($smsc, $sms_sender, $sms_footer, $sms_to, $sms_msg, $uid = '', $gpid = 0, $smslog_id = 0, $sms_type = 'text', $unicode = 0) {
	global $plugin_config;

	_log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to, 3, "easysms_hook_sendsms");

	// override plugin gateway configuration by smsc configuration
	$plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);

	$sms_sender = stripslashes($sms_sender);
	if ($plugin_config['easysms']['module_sender']) {
		$sms_sender = $plugin_config['easysms']['module_sender'];
	}

	$sms_footer = stripslashes($sms_footer);
	$sms_msg = stripslashes($sms_msg);
	$ok = false;

	if ($sms_footer) {
		$sms_msg = $sms_msg . $sms_footer;
	}

	// no sender config yet
	if ($sms_to && $sms_msg) {

		$c_sms_flash = ( $sms_type == "flash" ? 1 : 0 );

		$unicode_query_string = '';
		if ($unicode) {
			if (function_exists('mb_convert_encoding')) {
				$sms_msg = mb_convert_encoding($sms_msg, "UTF-8", "auto");
			}
		}

		global $core_config;
		$url = $plugin_config['easysms']['url'] . "?";
		$url .= "key=" . $plugin_config['easysms']['APIKey'];
		$url .= "&from=" . urlencode($sms_sender);
		$url .= "&to=" . urlencode($sms_to);
		$url .= "&text=" . urlencode($sms_msg);
		$url .= ($c_sms_flash == 1 ? '&flash=true' : '');
		$url .= ($unicode ? '&ucs=true' : '');
		$url .= '&callback=' . urlencode($core_config['main']['main_website_url'].'/plugin/gateway/easysms/callback.php?smslog_id='.$smslog_id);
		$url = trim($url);

		_log("send url:[" . $url . "]", 3, "easysms_hook_sendsms");

		$c_message_id = 0;

		$response = file_get_contents($url);

		// 358731178
		if ($response) {
			if(is_int($response) && $response > 0){
					$c_message_id = $response;
			}else{
					$c_error_code = $response;
			}
		}

		if ($c_message_id) {
			_log("sent smslog_id:" . $smslog_id . " message_id:" . $c_message_id . " smsc:" . $smsc, 2, "easysms_hook_sendsms");
			$ok = true;
			$p_status = 1;
			dlr($smslog_id, $uid, $p_status);
		} else if ($c_error_code) {
			_log("failed smslog_id:" . $smslog_id . " message_id:" . $c_message_id . " error_code:" . $c_error_code . " smsc:" . $smsc, 2, "easysms_hook_sendsms");
		} else {
			_log("invalid smslog_id:" . $smslog_id . " resp:[" . $response . "] smsc:" . $smsc, 2, "easysms_hook_sendsms");
		}
	}
	if (!$ok) {
		$p_status = 2;
		dlr($smslog_id, $uid, $p_status);
	}

	return $ok;
}
