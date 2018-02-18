<?php
/**
 * 创建话题(发帖) /api/forum/postTopic.php
 * ======================================================
 * 创建一个新的话题。
 * 检查验证码 ->
 * 检查提交参数 ->
 * 创建新话题（更新category_X表，复制category_X_topic_Y表）
 * ======================================================
 */

	check_post_captcha();
	# check_post_args("title", "content", "draft", "top");
	read_required_post_args("title", "content");
	anti_xss("title", "content");
	$draft = $top = false;
	read_optional_post_args("draft", "top");

	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);

	# 只有版主/管理员才可以设置置顶
	if ($top) {
		if (!is_admin() && !is_category_owner($cid)) die_with_code(403);
	}

	$user = $_SESSION["username"];

	try {
		$q = new SQLStatement;
		$q->insertInto("category_{$cid}", ["title", "author", "last_replier", "top", "draft"], [$title, $user, $user, $top, $draft])
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("插入category_{$cid}表失败。");
		$tid = $q->lastInsertId();

		$q->createTable("category_{$cid}_topic_{$tid}")
		  ->like("category_T_topic_T")
		  ->execute();

		$q->insertInto("category_{$cid}_topic_{$tid}", ["author", "content"], [$user, $content])
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("插入category_{$cid}_topic_{$tid}表失败。");

		log_to_reply_index($q, $cn, $cid, $tid, 1, $user, $content);

		# 更新主题数量
		$q->update("category")
		  ->set("`topic` = `topic` + 1")
		  ->where("`id` = ?", $cid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category表失败！");

	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$cn}/{$tid}");



?>
