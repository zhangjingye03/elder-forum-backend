<?php

	function get_category_id($cn) {
		$q = new SQLStatement;
		$q->select("id")
		  ->from("category")
		  ->where("name = ?", $cn)
		  ->execute();

		if ($q->rowCount() < 1)
			die_with_code(410);

		$r = $q->fetch();
		return $r["id"];
	}
?>
