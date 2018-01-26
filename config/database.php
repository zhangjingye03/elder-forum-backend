<?php
/**
 * 数据库配置文件 /config/database.php
 * =====================================
 * 包含数据库的用户名和密码等配置。
 * =====================================
 */

	$dbms = "mysql";
	$host = "localhost";
	$database = "forum";
	$username = "username";
	$password = "password";
	$dsn = "{$dbms}:host={$host};dbname={$database};charset=utf8";
?>
