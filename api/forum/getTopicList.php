<?php
	$tn = get_next_slash_arg();
	$count = 20;
	$page = 1;
	$sort = "time_dsc";
	read_optional_get_args("count", "page", "sort");
	if ($count > 100) $count = 20;

	$q = new SQLStatement;
	$q->select("id")
	  ->from("category")
	  ->where("name = ?", $tn)
	  ->execute();

	if ($q->rowCount() < 1)
		die_with_code(410);

	$r = $q->fetch();

	$tid = $r["id"] - 0;
	$q->select("*")
	  ->from("category_" . $tid);
	if ($sort == "create_time_dsc")
		$q->orderBy("create_time")->desc();
	else if ($sort == "modify_time_asc")
		$q->orderBy("modify_time")->asc();
	else if ($sort == "reply_dsc")
		$q->orderBy("reply")->desc();

	$q->limit(calc_limit_offset($count, $page), $count);
	  ->execute();
	$r = $q->fetchAll();

	die(json_encode($r));
?>
