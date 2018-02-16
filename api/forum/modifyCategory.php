<?php
/**
 * 修改版块 /api/forum/modifyCategory.php
 * ======================================
 * 修改指定版块的参数。
 * 检查验证码 ->
 * 检查是否管理员/版主 ->
 * 检查提交参数 ->
 * 检查版块名称是否冲突 ->
 * 检查版块是否存在 ->
 * 修改版块参数
 * ======================================
 */

	check_post_captcha();
	$cn = get_next_slash_arg();
	$cid = get_category_id($cn);

	if (!is_admin() && !is_category_owner($cid)) die_with_code(403);

	read_required_post_args("name", "alias", "description", "icon", "owner");
	anti_xss("alias", "description");

	try {
		$q = new SQLStatement;
		$q->select("*")
		  ->from("category")
		  ->where("(`name` = ? OR `alias` = ?) AND `id` != ?", [$name, $alias, $cid], [PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_INT])
		  ->execute();
		if ($q->rowCount() > 0) throw new \Exception("failed", "所提交版块的名称冲突。");

		$q->update("category")
		  ->set("`name` = ?, `alias` = ?, `description` = ?, `icon` = ?, `owner` = ?", [$name, $alias, $description, $icon, $owner])
		  ->where("`id` = ?", $cid, PDO::PARAM_INT)
		  ->execute();
		if ($q->rowCount() < 1) throw new \Exception("更新category表失败。");

	} catch (Exception $ex) {
		die_in_json("failed", $ex->getMessage());
	}

	die_in_json("ok", null, "/forum/{$name}");

?>
