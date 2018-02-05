<?php

	check_get_args("type");

	switch ($_GET['type']) {
		case "login":
			die(file_get_contents("config/template_login.json"));
		case "reg":
			die(file_get_contents("config/template_reg.json"));
		case "user":
			die(file_get_contents("config/template_user.json"));
	}
?>
