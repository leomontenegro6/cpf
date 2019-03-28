<?php
require_once 'cabecalho.php';

$usuario = new usuario();

session_start();

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
	$_SESSION['menu'] = menu::carregar();
	
	setcookie('auth', $usuario_row['login']);
	session_regenerate_id();
	header("Location: ../view/");
} else {
	modal::retornar('Usuário e/ou senha incorreta!', 'index.php', 'aviso');
}

require_once 'rodape.php';