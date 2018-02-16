<?php
/**
 * 数据库配置文件 /config/database.php
 * =====================================
 * 包含数据库的用户名和密码等配置。
 * =====================================
 */

 	global $_dsn, $_username, $_password;
	$_dbms = "mysql";
	$_host = "localhost";
	$_database = "forum";
	$_username = "username";
	$_password = "password";
	$_dsn = "{$_dbms}:host={$_host};dbname={$_database};charset=utf8";
	$_debug = false;
?>
