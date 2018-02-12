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
	$tid = get_next_slash_arg();
	if (!is_integer($tid)) die_with_code(400);

	$user = $_SESSION["username"];

	try {
		$q->select("id")
		  ->from("category_{$cid}_topic_{$tid}");
		if ($reply_id != 0)
			$q->where("id = ?", $reply_id, PDO::PARAM_INT);
		$q->execute();
		if ($q->rowCount() < 1) throw new \Exception("帖子或者指定楼层不存在！");

		$q->insertInto("category_{$cid}_topic_{$tid}", ["author", "content", "reply_id"], [$user, $content, $reply_id])
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("插入category_{$cid}_{$tid}表失败。");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$cn}/{$tid}");



?>
