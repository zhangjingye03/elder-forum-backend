<?php
	require_login();
	session_destory();
	# TODO: 跳转到注销前的页面
	die_in_json("ok", null, "/forum/");
?>
