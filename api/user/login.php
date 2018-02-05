<?php
/**
 * 用户登录接口 /api/user/login.php
 * ===============================
 * 负责处理用户登录。
 * ===============================
 */

	check_method("POST");
	check_post_captcha();
	check_post_args("username", "password");

	$username = $_POST["username"];
	$password = $_POST["password"];

	try {
		$q = new SQLStatement;

		$q->select("*")
		  ->from("user")
		  ->where("username = ? AND password = ?", [$username, $password])
		  ->execute();

		if ($q->rowCount() < 1)
			throw new \Exception("用户名或密码错误。", 1);

		$r = $q->fetch();

		push_user_session($r["uid"], $username, $r["email"], $r["alias"], $r["avatar"]);

		# TODO: 跳转到登录前的页面
		die_in_json("ok", null, "/forum/");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}
?>
