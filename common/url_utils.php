<?php

	function allow_remaining_slash_arg_count(...$num) {
		global $_request, $_found;
		$now_count = get_remaining_slash_arg_count();
		foreach ($num as $n => $v) {
			if ($now_count == $v) return true;
		}
		die_with_code(406);
	}

	function get_remaining_slash_arg_count() {
		global $_request, $_found;
		return sizeof($_request) - $_found - 1;
	}

	function get_next_slash_arg() {
		global $_request, $_found;
		if (!isset($_request[$_found + 1]))
			throw new \Exception("No next slash arg.", 1);

		return $_request[++$_found];
	}

	function read_optional_get_args(...$arg) {
		foreach ($arg as $k => $v) {
			if (isset($_GET[$v])) {
				global $$v;
				$$v = $_GET[$v];
			}
		}
	}
?>
