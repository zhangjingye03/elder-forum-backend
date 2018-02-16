<?php

	check_delete_captcha();
	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);

	if (!is_admin() && !is_category_owner($cid))
		die_with_code(403);

	try {
		$q = new SQLStatement;
		$q->select("id")
		  ->from("category_{$cid}")
		  ->execute();
		if ($q->rowCount() > 0) {
			# 板块有帖子，全部删除这些表
			$r = $q->fetchAll();
			foreach ($r as $k => $v) {
				$q->dropTable("category_{$cid}_" . $v["id"])->execute();
			}
		}
		# 删除板块信息表
		$q->dropTable("category_{$cid}")->execute();
		if ($q->rowCount() < 1) throw new \Exception("删除失败。");
	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok");
?>
