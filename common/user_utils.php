<?php
/**
 * 用户处理函数库 /common/user_utils.php
 * =======================================================
 * 各种用户数据的打杂
 * is_logged_in() 判断是否登录
 * require_login() 此页面需要登录才能访问
 * push_user_session(...) 将用户数据压入session
 * check_user_existence(...) 检查用户是否被注册
 * is_admin() 检查当前用户是否管理员
 * is_category_owner($cid) 检查当前用户是否指定版块的版主
 * is_topic_owner($cid, $tid) 检查当前用户是否指定帖子的楼主
 * is_topic_reply_owner($cid, $tid, $rid)
 *   检查当前用户是否指定楼层的层主
 * =======================================================
 */

	function is_logged_in() {
		if (!isset($_SESSION["username"])) return false;
		return true;
	}

	function require_login() {
		if (!is_logged_in()) die_with_code(403);
	}

	function push_user_session($uid, $username, $email, $alias, $avatar) {
		$_SESSION["uid"] = $uid;
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
			  ->where("username = ? OR email = ? OR alias = ?", [$username, $email, $alias])
			  ->execute();
			if ($q->rowCount() != 0) {
				$r = $q->fetch();
				if ($username == $r["username"]) die_in_json("failed", "来晚咯，用户名被人家捎走了哦");
				if ($email == $r["email"]) die_in_json("failed", "你不够特立独行哦，邮箱已经被注册啦");
				if ($alias == $r["alias"]) die_in_json("failed", "你的昵称不够酷炫，跟别人重了哦");
			}
		} catch (Exception $ex) {
			die_in_json("failed", $ex->getMessage());
		}
	}

	function is_admin() {
		// TODO
		return false;
	}

	function is_category_owner($cid) {
		try {
			$q = new SQLStatement;
			$q->select("owner")
			  ->from("category")
			  ->where("id = ?", $cid, PDO::PARAM_INT)
			  ->execute();
			$r = $q->fetch();
  			if (sizeof($r) == 0) return false;
  			if ($r["owner"] == $_SESSION["username"]) return true;
		} catch (Exception $ex) {}
		return false;
	}

	function is_topic_owner($cid, $tid) {
		try {
			$q = new SQLStatement;
			$q->select("author")
			  ->from("category_{$cid}")
			  ->where("id = ?", $tid, PDO::PARAM_INT)
			  ->execute();
			$r = $q->fetch();
  			if (sizeof($r) == 0) return false;
  			if ($r["author"] == $_SESSION["username"]) return true;
		} catch (Exception $ex) {}
		return false;
	}

	function is_topic_reply_owner($cid, $tid, $rid) {
		try {
			$q = new SQLStatement;
			$q->select("author")
			  ->from("category_{$cid}_topic_{$tid}")
			  ->where("id = ?", $rid, PDO::PARAM_INT)
			  ->execute();
			$r = $q->fetch();
			if (sizeof($r) == 0) return false;
			if ($r["author"] == $_SESSION["username"]) return true;
		} catch (Exception $ex) {}
		return false;
	}
?>
