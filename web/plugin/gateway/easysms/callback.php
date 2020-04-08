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
error_reporting(0);

if (!$called_from_hook_call) {
	chdir("../../../");

	// ignore CSRF
	$core_config['init']['ignore_csrf'] = TRUE;

	include "init.php";
	include $core_config['apps_path']['libs'] . "/function.php";
	chdir("plugin/gateway/easysms/");
	$requests = $_REQUEST;
}

$log = '';
if (is_array($requests)) {
	foreach ($requests as $key => $val) {
		$log .= $key . ':' . $val . ' ';
	}
	_log("pushed " . $log, 2, "easysms callback");
}

$remote_smslog_id = $requests['msgid'];
$local_smslog_id = $requests['smslog_id'];
$message_status = $requests['status'];

// delivery receipt
if ($remote_smslog_id && $message_status && $local_smslog_id) {
	$smslog_id = $local_smslog_id;
	if ($smslog_id) {
		$data = sendsms_get_sms($smslog_id);
		$uid = $data['uid'];
		$p_status = $data['p_status'];
		switch ($message_status) {
			case "d":
				$p_status = 3;
				break; // delivered
			default :
				$p_status = 2;
				break; // failed
		}
		_log("dlr uid:" . $uid . " smslog_id:" . $smslog_id . " messageid:" . $remote_smslog_id . " status:" . $message_status, 2, "easysms callback");
		dlr($smslog_id, $uid, $p_status);

		ob_end_clean();
		echo "ACK/easysms";
		exit();
	}
}
