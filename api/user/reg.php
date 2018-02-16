<?php
/**
 * 用户注册接口 /api/user/login.php
 * ===============================
 * 负责处理用户注册。
 * ===============================
 */

	check_method("POST");
	check_post_captcha();
	check_post_args_from_json("config/template_reg.json");

	$username = $_POST["username"];
	$password = $_POST["password"];
	$alias = $_POST["alias"];
	$email = $_POST["email"];
	$ip = $_SERVER['REMOTE_ADDR'];

	check_user_existence($username, $email, $alias);

	try {
		$q = new SQLStatement;

		$q->insertInto("user", ["username", "password", "email", "alias", "ip"], [$username, $password, $email, $alias, $ip])
		  ->execute();
		
		if ($q->rowCount() != 1)
			throw new \Exception("注册失败，未知错误。", 1);

		$r = $q->fetch();
		push_user_session($r["uid"], $username, $email, $alias, "default");

		# TODO: 跳转到注册前的页面
		die_in_json("ok", null, "/forum/");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

?>
