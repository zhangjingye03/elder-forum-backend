<?php
/**
 * 论坛处理数据公用函数 /common/forum_utils.php
 * ===========================================
 * 论坛中查询数据、处理数据的函数库
 * get_category_id($cn)
 *   传入版块名称，返回版块id
 * anti_xss(...$arr)
 *   传入参数列表，防止对应的变量做XSS攻击
 * log_to_reply_index(...)
 *   记入回复索引，用于搜索/归档
 * log_to_topic_index(...)
 *   记入标题索引
 * ===========================================
 */

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

	function anti_xss(...$arr) {
		foreach ($arr as $k => $v) {
			global $$v;
			if (!isset($$v)) continue;
			$$v = htmlspecialchars($$v);
		}
	}

	function log_to_reply_index(SQLStatment $q, $cn, $cid, $tid, $rid, $user, $content) {
		$q->insertInto("reply_log", ["category", "category_id", "topic_id", "reply_id", "author", "content", "ip"], $cn, $cid, $tid, $rid, $user, $content, $_SERVER['REMOTE_ADDR'])
		  ->execute();
	}
?>
