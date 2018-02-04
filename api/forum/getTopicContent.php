<?php
	$cn = get_next_slash_arg();
	$tid = get_next_slash_arg();
	if (!is_integer($tid)) die_with_code(400);
	$count = 20;
	$page = 1;
	$username = "";
	read_optional_get_args("count", "page", "username");
	if ($count > 100 || $count < 1) $count = 20;

	$q = new SQLStatement;
	$q->select("id")
	  ->from("category")
	  ->where("name = ?", $cn)
	  ->execute();

	if ($q->rowCount() < 1)
		die_with_code(410);

	$r = $q->fetch();
	$cid = $r["id"] - 0;

	# 浏览量+1
	$q->update("category_" . $cid)
	  ->set("`view` = `view` + 1")
	  ->execute();

	# 返回帖子内容
	$q->select("*")
	  ->from("category_" . $cid . "_topic_" . ($tid - 0));
	  ->limit(calc_limit_offset($count, $page), $count);
	  ->execute();
	$r = $q->fetchAll();

	die(json_encode($r));
?>
