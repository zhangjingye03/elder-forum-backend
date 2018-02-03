<?php
	function check_captcha($input) {
		return true;
		session_start();
		if (!isset($_SESSION["captcha"])) die_with_code(400);
		if ($_SESSION["captcha"] != $input) die_in_json("failed", "验证码不正确！");
		# 及时销毁暂存验证码，防止重复利用
		$_SESSION["captcha"] = null;
	}

	function check_post_captcha() {
		return true;
		if (!isset($_POST["captcha"])) die_in_json("没有填写验证码！");
		check_captcha($_POST["captcha"]);
	}

	function check_get_captcha() {
		return true;
		if (!isset($_GET["captcha"])) die_with_code("没有填写验证码！");
		check_captcha($_GET["captcha"]);
	}
?>
