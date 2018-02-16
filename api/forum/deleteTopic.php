<?php

	check_delete_captcha();
	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = intval(get_next_slash_arg());

	if (!is_admin() && !is_category_owner($cid) && !is_topic_owner($cid, $tid))
		die_with_code(403);

	try {
		$q = new SQLStatement;
		$q->dropTable("category_{$cid}_topic_{$tid}")->execute();

		# 更新主题数量
		$q->update("category")
		  ->set("`topic` = `topic` - 1")
		  ->where("`id` = ?", $cid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category表失败！");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok");
?>
