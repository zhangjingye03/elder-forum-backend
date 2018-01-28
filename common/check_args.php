<?php
	function check_method(...$method) {
		foreach ($method as $m) {
			if ($m == $_SERVER["REQUEST_METHOD"]) return true;
		}
		die_with_code(405);
	}

	function check_args($method, $args) {
		foreach ($args as $s) {
			switch ($method) {
				case "GET":
					if (!isset($_GET[$s]))	die_with_code(400);
					break;
				case "POST":
					if (!isset($_POST[$s])) die_with_code(400);
					break;
				// TODO: PUT & DELETE
			}
		}
	}

	function check_args_from_json($method, $json) {
		$j = json_decode(file_get_contents($json));
		foreach ($j as $s) {
			# 如果是非必须参数（flag中无must属性），可以跳过
			if (strpos(implode(", ", $s->flag), "must") !== false)
				continue;
			# 检查是否存在
			$v = null;
			if ($method == "GET") {
				if (!isset($_GET[$s->key])) die_with_code(400);
				$v = $_GET[$s->key];
			} else if ($method == "POST") {
				if (!isset($_POST[$s->key])) die_with_code(400);
				$v = $_POST[$s->key];
			} else {
				// TODO: PUT & DELTET
			}
			# 检查正则是否匹配
			if (preg_match($s->regex, $v) != 1) die_in_json("failed", "提交参数格式错误！");
		}
	}

	function check_post_args(...$args) {
		check_args("POST", $args);
	}

	function check_get_args(...$args) {
		check_args("GET", $args);
	}

	function check_post_args_from_json($json) {
		check_args_from_json("POST", $json);
	}

	function check_get_args_from_json($json) {
		check_args_from_json("GET", $json);
	}


?>
