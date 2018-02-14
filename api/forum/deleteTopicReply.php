<?php

	check_delete_captcha();
	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = get_next_slash_arg();
	$rid = get_next_slash_arg();

	if (!is_integer($tid) || !is_integer($rid)) die_with_code(400);

	if (!is_admin() || !is_category_owner($cid) || !is_topic_reply_owner($cid, $tid, $rid))
		die_with_code(403);

	if ($rid < 2)
		die_in_json("failed", "不能删除帖子的一楼哦，请直接删除帖子。");

	try {
		$q = new SQLStatement;
		$q->deleteFrom("category_{$cid}_{$tid}")
		  ->where("id = ?", $rid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("删除失败。");

		# 更新主题回复数量
		$q->update("category_{$cid}")
		  ->set("`reply` = `reply` - 1")
		  ->where("`id` = ?", $tid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category_{$cid}表失败！");

		$q->update("category")
		  ->set("`reply` = `reply` - 1")
		  ->where("`id` = ?", $cid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category表失败！");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok");
?>
