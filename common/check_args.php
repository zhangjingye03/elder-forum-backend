<?php
	function check_method(...$method) {
		foreach ($method as $k => $m) {
			if ($m == $_SERVER["REQUEST_METHOD"]) return true;
		}
		die_with_code(405);
	}

	function get_args_from_other_method($method = null) {
		if ($method != null) check_method($method);
		$t = [];
		$f = file_get_contents("php://input");
		parse_str($f, $t);
		return $t;
	}

	function check_args($arr, $args) {
		foreach ($args as $k => $s) {
			if (!isset($arr[$s])) die_with_code(400);
		}
	}

	function check_args_from_json($arr, $json) {
		$j = json_decode(file_get_contents($json));
		foreach ($j as $k => $s) {
			# 如果是非必须参数（flag中无must属性），可以跳过
			if (strpos(implode(", ", $s->flag), "must") !== false)
				continue;
			# 检查是否存在
			if (!isset($arr[$s->key])) die_with_code(400);
			$v = $_GET[$s->key];
			# 检查正则是否匹配
			if (preg_match($s->regex, $v) != 1) die_in_json("failed", "提交参数格式错误！");
		}
	}

	function check_post_args(...$args) {
		check_args($_POST, $args);
	}

	function check_get_args(...$args) {
		check_args($_GET, $args);
	}

	function check_put_args(...$args) {
		check_args(get_args_from_other_method("PUT"), $args);
	}

	function check_delete_args(...$args) {
		check_args(get_args_from_other_method("DELETE"), $args);
	}

	function check_post_args_from_json($json) {
		check_args_from_json($_POST, $json);
	}

	function check_get_args_from_json($json) {
		check_args_from_json($_GET, $json);
	}

	function read_required_args($arr, $args) {
		foreach ($args as $k => $v) {
			if (!isset($arr[$v])) die_with_code(400);
			global $$v;
			$$v = $arr[$v];
		}
	}

	function read_required_get_args(...$args) {
		read_required_args($_GET, $args);
	}

	function read_required_post_args(...$args) {
		read_required_args($_POST, $args);
	}

	function read_required_put_args(...$args) {
		read_required_args(get_args_from_other_method("PUT"), $args);
	}

	function read_required_delete_args(...$args) {
		read_required_args(get_args_from_other_method("DELETE"), $args);
	}
?>
