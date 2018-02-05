<?php
/**
 * 验证码检查函数库 /common/check_captcha.php
 * ===================================================
 * 各种检查验证码的函数，在测试阶段皆在第一行return true;
 * ===================================================
 */

	function check_captcha($input) {
		return true;
		# session_start();
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
		if (!isset($_GET["captcha"])) die_in_json("没有填写验证码！");
		check_captcha($_GET["captcha"]);
	}

	function check_delete_captcha() {
		return true;
		$_DELETE = get_args_from_other_method("DELETE");
		if (!isset($_DELETE["captcha"])) die_in_json("没有填写验证码！");
		check_captcha($_DELETE["captcha"]);
	}

	function check_put_captcha() {
		return true;
		$_PUT = get_args_from_other_method("PUT");
		if (!isset($_PUT["captcha"])) die_in_json("没有填写验证码！");
		check_captcha($_PUT["captcha"]);
	}
?>
