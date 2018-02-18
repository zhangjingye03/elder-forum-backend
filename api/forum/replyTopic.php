<?php
/**
 * 回复话题(回帖) /api/forum/replyTopic.php
 * =======================================
 * 回复一个存在的话题。
 * 检查验证码 ->
 * 检查提交参数 ->
 * 检查话题是否存在 ->
 * 检查回复的楼层是否存在 ->
 * 创建回复
 * =======================================
 */

	check_post_captcha();
	read_required_post_args("content");
	anti_xss("content");
	$reply_id = 0;
	read_optional_post_args("reply_id");

	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = intval(get_next_slash_arg());

	$user = $_SESSION["username"];

	try {
		$q = new SQLStatement;
		$q->selectCount()
		  ->from("category_{$cid}")
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->fetchCount() < 1) throw new \Exception("帖子不存在！");

		if ($reply_id != 0) {
			$q->select("COUNT(*)")
			  ->from("category_{$cid}_topic_{$tid}")
			  ->where("id = ?", $reply_id, PDO::PARAM_INT)
			  ->execute();
			if ($q->fetchCount() < 1) throw new \Exception("指定楼层不存在！");
		}

		$q->insertInto("category_{$cid}_topic_{$tid}", ["author", "content", "reply_id"], [$user, $content, $reply_id])
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("插入category_{$cid}_topic_{$tid}表失败。");
		$rid = $q->lastInsertId();

		log_to_reply_index($q, $cn, $cid, $tid, $rid, $user, $content);

		# 更新版块中帖子信息
		$q->update("category_{$cid}")
		  ->set("`last_replier` = ?, `reply` = `reply` + 1, `last_reply_time` = CURRENT_TIMESTAMP", $user)
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category_{$cid}表失败！");

		# 更新版块回复数量
		$q->update("category")
		  ->set("`reply` = `reply` + 1")
		  ->where("`id` = ?", $cid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category表失败！");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$cn}/{$tid}");



?>
