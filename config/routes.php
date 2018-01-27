<?php
/**
 * 所有URL路由配置文件 /config/routes.php
 * ============================================
 * 包含所有的URL路由配置，严格按照接口文档说明编写。
 * ============================================
 */

	$_routes = [];

	# 论坛 API
	$_routes["api"] = [];

	# 用户接口 API
	$_routes["api"]["user"] = [];
	$_routes["api"]["user"][""] = "api/user/getUserInfo.php";
	$_routes["api"]["user"]["login"] = "api/user/login.php";
	$_routes["api"]["user"]["template"] = "api/user/getTemplate.php";

	# 论坛相关 API
	# 由于设计板块、帖子、回复等各种操作，单独分离到论坛路由器
	$_routes["t"] = [];
	$_routes["t"][""] = "api/forum/forum_router.php";

?>
