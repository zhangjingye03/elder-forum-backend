<?php
/**
 * URL路由处理页面 /router.php
 * =======================================================================================
 * 负责将URL安全地重定向到在/config/routes.php中定义的文件，使URL变得更加雅观且遵循RESTful原则。
 * 在Apache/Nginx中的配置请参见说明文档。
 * =======================================================================================
 */

	$_method = $_SERVER['REQUEST_METHOD'];
	$_request = explode("/", substr($_SERVER['REQUEST_URI'], 1), 16);

	require_once('common/all_utils.php');
	require_once("config/routes.php");

	# 临时引用变量，指向第一级路由入口
	$_tmp_ref = $_routes;
	$_found = false;
	foreach ($_request as $_sub) {
		if (!isset($_tmp_ref)) break;
		# 如果存在下一级入口，则将临时引用变量设置为下一级入口
		if (isset($_tmp_ref[$_sub])) {
			$_found = true;
			$_tmp_ref = $_tmp_ref[$_sub];
		}
	}

	if (!$_found) die_with_code(404);
	if (is_array($_tmp_ref)) {
		if (!isset($_tmp_ref[""])) die_with_code(404);
		$_tmp_ref = $_tmp_ref[""];
	}
	echo($_tmp_ref);
	if (!file_exist($_tmp_ref)) die_with_code(410);
	require_once($_tmp_ref);
?>
