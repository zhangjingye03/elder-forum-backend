<?php
	check_method("GET", "PUT");

	if ($_method == "GET") {
		# GET方法：获取个人信息（登录后）、获取他人信息
		if (is_logged_in()) {
			allow_remaining_slash_arg_count(0, 1);
		} else {
			allow_remaining_slash_arg_count(1);
		}
		require("getUserInfo.php");
	} else if ($_method == "PUT") {
		# PUT方法（登录后）：更新个人信息、更新他人信息（管理员）
		require_login();
		if (is_admin()) {
			allow_remaining_slash_arg_count(0, 1);
		} else {
			allow_remaining_slash_arg_count(0);
		}
		require("putUserInfo.php");
	}
?>
