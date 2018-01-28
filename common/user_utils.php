<?php

	function require_login() {
		session_start();
		if (!isset($_SESSION["username"])) die_with_code(403);
	}

	function push_user_session($username, $email, $alias, $avatar) {
		$_SESSION["username"] = $username;
		$_SESSION["email"] = $email;
		$_SESSION["alias"] = $alias;
		$_SESSION["avatar"] = $avatar;
	}


	function check_user_existence($username, $email, $alias) {
		try {
			$q = new SQLStatement;
			$q->select("username, email, alias")
			  ->from("user")
			  ->where("username = ? OR email = ? OR alias = ?", [$username, $email, $alias]);
			  ->execute();
			if ($q->rowCount() != 0) {
				$r = $q->fetchAll();
				if ($username == $r[0]["username"]) die_in_json("failed", "来晚咯，用户名被人家捎走了哦");
				if ($email == $r[0]["email"]) die_in_json("failed", "你不够特立独行哦，邮箱已经被注册啦");
				if ($alias == $r[0]["alias"]) die_in_json("failed", "你的昵称不够酷炫，跟别人重了哦");
			}
		} catch (Exception $ex) {
			die_in_json("failed", $ex->getMessage());
		}
	}

	function check_sensitive_words(...$content) {
		// TODO
	}

?>
