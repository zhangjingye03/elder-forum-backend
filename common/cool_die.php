<?php
/**
 * 璀璨的死法 /common/cool_die.php
 * ====================================================
 * 包含几个自定义的die()函数，让你的代码死得与众不同 [滑稽]
 * die_with_code($code)
 *   终止代码并返回错误代码$code
 * die_in_json($status, $reason = null, $go = null)
 *   终止代码并返回json
 * ====================================================
 */

	function die_with_code($code) {
		http_response_code($code);
		die();
	}

	function die_in_json($status, $reason = null, $go = null) {
		$j = [];
		$j['status'] = $status;
		if (isset($reason))
			$j['reason'] = $reason;
		if (isset($go))
			$j['go'] = $go;
		header('Content-Type: application/json');
		die(json_encode($j));
	}

	function die_arr_in_json($arr) {
		header('Content-Type: application/json');
		die(json_encode($arr));
	}
?>
