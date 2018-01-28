<?php
/**
 * 璀璨的死法 /common/cool_die.php
 * ====================================================
 * 包含几个自定义的die()函数，让你的代码死得与众不同 [滑稽]
 * ====================================================
 */

	function die_with_code($code) {
		http_response_code($code);
		die();
	}

	function die_in_json($status, $reason='', $go = '') {
		$j = [];
		$j['status'] = 'failed';
		if (strlen($reason) != 0)
			$j['reason'] = $reason;
		if (strlen($go) != 0)
			$j['go'] = $go;
		die(json_encode($j));
	}
?>
