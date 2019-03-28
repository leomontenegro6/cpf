<?php
if(isset($_GET['ajax']) || isset($_POST['ajax'])){
	if((isset($_GET['ajax']) && $_GET['ajax'] == 'true') || (isset($_POST['ajax']) && $_POST['ajax'] == 'true')){
		$ajax = true;
	} else {
		$ajax = false;
	}
} else {
	$ajax = false;
}
require_once '../../utils/autoload.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>CPF | Log in</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Font Awesome -->
		<link rel="stylesheet" href="../common/css/fontawesome5-all.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="../common/css/adminlte.min.css">
		<!-- Custom CSS -->
		<link rel="stylesheet" href="../common/css/css.css?<?php echo filemtime('../common/css/css.css') ?>">
	</head>
	<body class="hold-transition login-page">