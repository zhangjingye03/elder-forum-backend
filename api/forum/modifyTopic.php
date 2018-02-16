<?php
/**
 * 修改话题(改帖子) /api/forum/modifyTopic.php
 * ===========================================
 * 修改帖子的属性或一楼内容。
 * 检查验证码 ->
 * 检查提交参数 ->
 * 检查是否楼主/管理员/版主 ->
 * 检查帖子是否存在 ->
 * 修改帖子内容
 * ===========================================
 */

	check_put_captcha();
	read_required_put_args("title", "content");
	anti_xss("title", "content");
	$draft = $top = false;
	read_optional_put_args("draft", "top");

	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = intval(get_next_slash_arg());

	if (!is_topic_owner($cid, $tid) && !is_admin() && !is_category_owner($cid)) die_with_code(403);

	# 只有版主/管理员才可以设置置顶
	if ($top) {
		if (!is_admin() && !is_category_owner($cid)) die_with_code(403);
	}

	$user = $_SESSION["username"];

	try {
		$q = new SQLStatement;
		$q->selectCount()
		  ->from("category_{$cid}")
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->fetchCount() < 1) throw new \Exception("帖子不存在！");

		$q->update("category_{$cid}")
		  ->set("`title` = ?, `top` = ?, `draft` = ?", [$title, $top, $draft])
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category_{$cid}表失败！");

		$q->update("category_{$cid}_topic_{$tid}")
		  ->set("`content` = ?", $content)
		  ->where("`id` = 0")
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category_{$cid}_topic_{$tid}表失败！");

	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$cn}/{$tid}");

?>
