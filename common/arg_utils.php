<?php
/**
 * 处理各种参数的函数库 /common/arg_utils.php
 * ==================================================================
 * 各种处理参数的函数
 * check_method(...$method) 检查请求的方法
 * get_args_from_other_method($method) 处理PUT、DELETE、PATCH的参数
 * check_X_args(...$args) 检查方法X中是否有对应参数
 * check_X_args_from_json($json) 检查方法X中的参数是否符合json中的定义
 * read_required_X_args(...$args) 将提交方法X中的参数读到全局变量中
 * read_optional_X_args(...$args) 同上，只不过参数可选
 * allow_remaining_slash_arg_count(...$num) 允许URL中以/分隔参数的个数
 * get_remaining_slash_arg_count() 获取以/分隔的参数的个数
 * get_next_slash_arg() 获取下一个以/分隔的参数
 * ==================================================================
 */

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

	function read_optional_args($arr, $args) {
		foreach ($args as $k => $v) {
			if (isset($arr[$v])) {
				global $$v;
				$$v = $arr[$v];
			}
		}
	}

	function read_optional_get_args(...$args) {
		read_optional_args($_GET, $args);
	}

	function read_optional_post_args(...$args) {
		read_optional_args($_POST, $args);
	}

	function read_optional_put_args(...$args) {
		read_optional_args(get_args_from_other_method("PUT"), $args);
	}

	function read_optional_delete_args(...$args) {
		read_optional_args(get_args_from_other_method("DELETE"), $args);
	}

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
		global $_request, $_arg_pos;
		if (!isset($_request[$_arg_pos + 1]))
			throw new \Exception("No next slash arg.", 1);

		return $_request[++$_arg_pos];
	}


?>
