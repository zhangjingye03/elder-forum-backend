<?php

	$username = "";
	if (get_remaining_slash_arg_count() == 0) {
		$username = $_SESSION["username"];
	} else {
		$username = get_next_slash_arg();
	}

	$q = new SQLStatement;
	$q->select("*")
	  ->from("user")
	  ->where("username = ?", $username)
	  ->execute();

	if ($q->rowCount() < 1) die_with_code(410);
	$r = $q->fetch();

	# 过滤掉密码列
	$r = copy_array_without_specified_content($r, "password");

	# 自己请求自己：返回全部信息
	# 管理员请求任何人：返回全部信息
	# 其它情况：返回概要信息
	if (get_remaining_slash_arg_count() != 0 && !is_admin()) {
		$r = copy_array_from_specified_content($r, "username", "alias", "avatar");
	}

	die_arr_in_json($r);
?>
