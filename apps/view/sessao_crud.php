<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$usuario = new usuario();

$id_usuario_sessao = $_SESSION['iduser'];

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'set_menu_minimizado') {
		$menu_minimizado = $_POST['menu_minimizado'];
		
		$retorno = $usuario->setMenuMinimizado($menu_minimizado, $id_usuario_sessao);
		if($retorno === true){
			$_SESSION['menu_minimizado'] = ($menu_minimizado == 'true');
			echo $_POST['menu_minimizado'];
		} else {
			echo $retorno;
		}
	} elseif ($_POST['acao'] == 'set_sistema_modulo_funcionalidade_sessao') {
		$id_sistema = $_POST['sistema'];
		$id_modulo = $_POST['modulo'];
		$id_funcionalidade = $_POST['funcionalidade'];
		
		$_SESSION['sistema_sessao'] = $id_sistema;
		$_SESSION['modulo_sessao'] = $id_modulo;
		$_SESSION['funcionalidade_sessao'] = $id_funcionalidade;
		
		echo 'true';
	}
}