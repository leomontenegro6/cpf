<?php
session_start();
require_once 'cabecalho.php';

$usuario = new usuario();

$login = (isset($_POST['login'])) ? ($_POST['login']) : ('');
$senha = (isset($_POST['senha'])) ? ($_POST['senha']) : ('');
$dispositivo_usuario = (isset($_POST['dispositivo_usuario'])) ? ($_POST['dispositivo_usuario']) : ('xl');

if(isset($_POST['login']) && isset($_POST['senha'])){
	$usuario_row = $usuario->getLogin($_POST['login'], $_POST['senha']);
} else {
	header("Location: index.php");
}

if($usuario_row != false){
	$_SESSION['iduser'] = $usuario_row['id'];
	$_SESSION['login'] = $usuario_row['login'];
	$_SESSION['nome'] = $usuario_row['nome'];
	$_SESSION['nome_exibicao'] = funcoes::formataNomeExibicao($usuario_row['nome']);
	$_SESSION['foto'] = $usuario_row['foto'];
	$_SESSION['admin'] = ($usuario_row['admin'] == '1');
	$_SESSION['menu'] = menu::carregar();
	$_SESSION['menu_minimizado'] = ($dispositivo_usuario == 'xs') ? (false) : ($usuario_row['menu_minimizado']);
	$_SESSION['sistema_sessao'] = '';
	$_SESSION['modulo_sessao'] = '';
	$_SESSION['funcionalidade_sessao'] = '';
	
	setcookie('auth', $usuario_row['login']);
	session_regenerate_id();
	header("Location: ../view/");
} else {
	header('Location: index.php?erro=true');
}

require_once 'rodape.php';