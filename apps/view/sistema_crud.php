<?php
require_once('cabecalho.php');
require_once('menu.php');

$sistema = new sistema();

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'cadastrar') {
		$retorno = $sistema->set($_POST);
		if($retorno === true) {
			aviso::retornar('Sistema cadastrado com sucesso!', 'sistema_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'sistema_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'editar') {
		$retorno = $sistema->update($_POST, $_POST['id']);
		if($retorno === true) {
			aviso::retornar('Sistema editado com sucesso!', 'sistema_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'sistema_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'excluir') {
		$retorno = $sistema->delete($_POST['id']);
		if($retorno === true) {
			aviso::retornar('Sistema excluído com sucesso!', 'sistema_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'sistema_lista.php', 'erro', $ajax);
		}
	}
}
require_once('rodape.php');