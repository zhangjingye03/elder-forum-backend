<?php
/**
 * 帖子内容搜索接口 /api/forum/search.php
 * =====================================
 * 负责搜索帖子内容。
 * 搜索关键字必须大于3个字符。
 * 检查验证码、提交参数 ->
 * 去除关键字% ->
 * 进行查询
 * ================================
 */

		check_post_captcha();
		allow_remaining_slash_arg_count(0, 1, 2);
		$ac = get_remaining_slash_arg_count();
		$cn = $cid = $tid = null;
		if ($ac != 0) {
			$cn = get_next_slash_arg();
			$cid = get_category_id($cn);
			if ($ac != 1) $tid = intval(get_next_slash_arg());
		}
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
			/* 还是用索引表吧，这样遍历性能太低，而且没办法计算页数..
			if ($ac == 0) {
				# 0个参数：搜索整个论坛
				# 查版块id
				$q->select("`id`")
				  ->from("`category`")
				  ->execute();
				$r = $q->fetchAll();
				foreach ($r as $k => $v) {
					# 进入指定版块，获取帖子id
					$i = $v["id"];
					$q->select("`id`, `title`, `create_time`")
					  ->from("`category_{$i}`")
					  ->execute();
					$rr = $q->fetchAll();
					foreach ($rr as $kk => $vv) {
						# 进入指定帖子，查询楼层回复
						$ii = $v["id"];
						$q->select("`id`, `author`, `create_time`, `content`")
						  ->from("`category_{$i}_topic_{$ii}`")
						  ->where("`content` LIKE CONCAT('%', ?, '%')", $content)
						  ->execute();
						$rrr = $q->fetchAll();
					}
				}
			}
			*/
			$q->select("*")
			  ->from("reply_log")
			  ->where("`content` LIKE CONCAT('%', ?, '%')", [$content, $content]);

			if ($cid != null)
			  $q->and("`category_id` = ?", $cid, PDO::PARAM_INT);
			if ($tid != null)
			  $q->and("`topic_id` = ?", $tid, PDO::PARAM_INT);

			$q->limit(calc_limit_offset($count, $page), $count)
			  ->execute();
			$r = $q->fetchAll();

			$r = copy_array_without_specified_content("ip", "category_id");
			die_arr_in_json($r);
		} catch(Exception $ex) {
			die_arr_in_json([]);
		}
?>
