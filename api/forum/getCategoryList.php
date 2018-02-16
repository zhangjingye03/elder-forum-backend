<?php

	$q = new SQLStatement;
	$q->select("*")
	  ->from("category")
	  ->execute();

	$r = $q->fetchAll();
	# TODO: 返回热帖/最新帖

	die_arr_in_json($r);
?>
