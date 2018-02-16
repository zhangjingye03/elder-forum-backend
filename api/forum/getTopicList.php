<?php
	$cn = get_next_slash_arg();
	$count = 20;
	$page = 1;
	$sort = "time_dsc";
	read_optional_get_args("count", "page", "sort");
	if ($count > 100 || $count < 1) $count = 20;

	$cid = get_category_id($cn);

	$q = new SQLStatement;
	$q->select("*")
	  ->from("category_" . $cid)
	  ->where("`draft` != 1");
	if ($sort == "create_time_dsc")
		$q->orderBy("create_time")->desc();
	else if ($sort == "reply_time_dsc")
		$q->orderBy("reply_time")->desc();
	else if ($sort == "reply_dsc")
		$q->orderBy("reply")->desc();

	$q->limit(calc_limit_offset($count, $page), $count)
	  ->execute();
	$r = $q->fetchAll();

	die_arr_in_json($r);
?>
