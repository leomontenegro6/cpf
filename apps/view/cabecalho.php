<?php
date_default_timezone_set('America/Fortaleza');
session_start();
if(isset($_GET['ajax']) || isset($_POST['ajax'])){
	if((isset($_GET['ajax']) && $_GET['ajax'] == 'true') || (isset($_POST['ajax']) && $_POST['ajax'] == 'true')){
		$ajax = true;
	} else {
		$ajax = false;
	}
} else {
	$ajax = false;
}
if(!isset($_SESSION['login'])){
	header("Location: logoff.php?sessao_expirada=true");
	exit;
}
require_once '../../utils/autoload.php';

if (!empty($_POST)){
	$_POST = seguranca::antiInjection($_POST);
}
if (!empty($_GET)){
	$_GET = seguranca::antiInjection($_GET);
}
$iduser = $_SESSION["iduser"];
$login = $_SESSION["login"];
$nome_exibicao = $_SESSION["nome_exibicao"];
$foto = $_SESSION["foto"];
$admin = $_SESSION["admin"];
$ambiente = funcoes::getAmbienteDesenvolvimento();
if(!$ajax){
	$endereco = funcoes::getEnderecoPagina();
	$endereco_anterior = funcoes::getEnderecoPaginaAnterior();
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<title>CPF | Contador de Pontos de Função</title>
			<link rel="shortcut icon" type="image/x-icon" href="../common/img/favicon.ico" />
			<!-- Tell the browser to be responsive to screen width -->
			<meta name="viewport" content="width=device-width, initial-scale=1">

			<!-- Font Awesome -->
			<link rel="stylesheet" href="../common/css/fontawesome5-all.min.css">
			<!-- Select2 -->
			<link rel="stylesheet" href="../common/css/select2.min.css">
			<!-- Theme style -->
			<link rel="stylesheet" href="../common/css/adminlte.min.css">
			<!-- Bootstrap Float Label -->
			<link rel="stylesheet" href="../common/css/bootstrap-float-label.css?<?php echo filemtime('../common/css/bootstrap-float-label.css') ?>">
			<!-- Hullabaloo.js - Para exibição de notificações com alerts flutuantes -->
			<link rel="stylesheet" href="../common/css/hullabaloo.min.css">
			<!-- Tagsinput -->
			<link href="../common/css/tagsinput.css" rel="stylesheet">
			<!-- Bootstrap Slider -->
			<link rel="stylesheet" href="../common/css/slider.css">
			<!-- Datatables -->
			<link href="../common/css/dataTables.bootstrap4.min.css" rel="stylesheet">
			<link href="../common/css/dataTables.responsive.css" rel="stylesheet">
			<!-- Custom CSS -->
			<link rel="stylesheet" href="../common/css/css.css?<?php echo filemtime('../common/css/css.css') ?>">
			<link rel="stylesheet" href="../common/css/css-xs.css?<?php echo filemtime('../common/css/css-xs.css') ?>">
		</head>
<?php } ?>