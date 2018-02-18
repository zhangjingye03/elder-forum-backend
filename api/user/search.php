<?php
/**
 * 用户搜索接口 /api/user/search.php
 * ================================
 * 负责处理用户搜索。
 * 搜索关键字必须大于3个字符。
 * 检查验证码、提交参数 ->
 * 去除关键字% ->
 * 进行查询
 * ================================
 */

		check_post_captcha();
		$content = "";
		read_required_post_args("content");
		$count = 20;
		$page = 1;
		read_optional_get_args("count", "page");
		if ($count > 100 || $count < 1) $count = 20;

		# 防止遍历
		$content = str_replace("%", "", $content);
		if (strlen($content) < 3) die_with_code(400);

		try {
			$q = new SQLStatement;
			$q->select("`username`, `alias`, `avatar`")
			  ->from("user")
			  ->where("`username` LIKE CONCAT('%', ?, '%') OR `alias` LIKE CONCAT('%', ?, '%')", [$content, $content])
			  ->limit(calc_limit_offset($count, $page), $count)
			  ->execute();
			$r = $q->fetchAll();

			die_arr_in_json($r);
		} catch(Exception $ex) {
			die_arr_in_json([]);
		}
?>
