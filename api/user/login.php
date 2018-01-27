<?php

	require_once("common/all_utils.php");
	if ($_method != 'POST')
		die_with_code(405);

	if (!isset($_POST["username"]) || !isset($_POST["password"]) || !isset($_POST["captcha"]))
		die_with_code(400);

	$username = $_POST["username"];
	$password = $_POST["password"];
	$captcha = $_POST["captcha"];

	// TODO: captcha verification...

	require_once("common/db_utils.php");
	try {
		$q = new SQLStatement;
		$q->select("*")
		  ->from("user")
		  ->where("username = ? AND password = ?", [$username, $password], [PDO::PARAM_STR, PDO::PARAM_STR])
		  ->execute();
		if ($q->rowCount() < 1)
			throw new \Exception("用户名或密码错误。", 1);
		$r = $q->fetchAll();
		// TODO: grub info from db to session...
	} catch (Exception $ex) {
		die_in_json($ex->getMessage());
	}
?>
