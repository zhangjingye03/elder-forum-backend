<?php
	require_once('vendor/autoload.php');
	use Gregwar\Captcha\CaptchaBuilder;

	session_start();

	$builder = new CaptchaBuilder;
	$builder->build();
	$_SESSION['captcha'] = $builder->getPhrase();

	header('Content-type: image/jpeg');
	$builder->output();
?>
