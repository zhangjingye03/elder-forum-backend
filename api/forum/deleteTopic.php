<?php

	check_delete_captcha();
	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);
	$tid = get_next_slash_arg();
	if (!is_integer($tid)) die_with_code(400);

	if (!is_admin() || !is_category_owner($cid) || !is_topic_owner($cid, $tid))
		die_with_code(403);

	try {
		$q = new SQLStatement;
		$q->dropTable("category_{$cid}_{$tid}")->execute();
		if ($q->rowCount() < 1) throw new \Exception("删除失败。");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok");
?>
