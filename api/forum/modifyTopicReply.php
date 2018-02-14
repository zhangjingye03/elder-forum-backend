<?php
/**
 * 修改回帖 /api/forum/modifyTopicReply.php
 * =======================================
 * 修改回帖内容。
 * 检查验证码 ->
 * 检查提交参数 ->
 * 检查帖子/回帖楼层是否存在 ->
 * 检查是否层主/管理员 ->
 * 修改回复
 * =======================================
 */

	check_post_captcha();
	read_required_post_args("content");
	anti_xss("content");

	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = get_next_slash_arg();
	$rid = get_next_slash_arg();
	if (!is_integer($tid) || !is_integer($rid)) die_with_code(400);

	if (!is_admin() || !is_topic_reply_owner($cid, $tid, $rid)) die_with_code(403);

	$user = $_SESSION["username"];

	try {
		$q = new SQLStatement;
		$q->select("id")
		  ->from("category_{$cid}")
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("帖子不存在！");

		$q->select("id")
		  ->from("category_{$cid}_topic_{$tid}")
		  ->where("`id` = ?", $rid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("指定楼层不存在！");

		$q->update("category_{$cid}_topic_{$tid}")
		  ->set("`content` = ?", $content)
		  ->where("`id` = ?", $rid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category_{$cid}_{$tid}表失败。");

	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$cn}/{$tid}");



?>
