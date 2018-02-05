<?php
/**
 * 创建版块 /api/forum/createCategory.php
 * ============================================
 * 创建一个新的版块。
 * 检查验证码 ->
 * 检查是否管理员 ->
 * 检查提交参数 ->
 * 检查版块是否存在 ->
 * 创建新版块（更新category表，复制category_T表）
 * ============================================
 */

	check_post_captcha();
	if (!is_admin()) die_with_code(403);
	check_post_args("name", "alias", "description", "icon", "owner");
	read_required_post_args("name", "alias", "description", "icon", "owner");

	try {
		$q = new SQLStatement;
		$q->select("*")
		  ->from("category")
		  ->where("`name` = ? OR `alias` = ?", [$name, $alias])
		  ->execute();
		if ($q->rowCount() > 0) throw new \Exception("failed", "所创建版块的名称冲突。");

		$q->insertInto("category", ["name", "alias", "description", "icon", "owner"], $name, $alias, $description, $icon, $owner)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("插入category表失败。");
		$cid = $q->lastInsertId();

		$q->createTable("category_{$cid}")
		  ->like("category_T")
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("创建category_{$cid}表失败！");

	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$name}");



?>
