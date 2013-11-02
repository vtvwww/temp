<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	list(,,,$prj_name) = explode("\\", __DIR__);
	header('Location: ' . $uri . '/' . $prj_name . '/www/u.php');
	exit;
?>
Something is wrong with the XAMPP installation :-(
