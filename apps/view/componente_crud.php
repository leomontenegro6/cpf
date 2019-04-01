<?php
require_once('cabecalho.php');
require_once('menu.php');

$componente = new componente();

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'cadastrar') {
		$retorno = $componente->set($_POST);
		if($retorno === true) {
			aviso::retornar('Componente cadastrado com sucesso!', 'componente_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'componente_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'editar') {
		$retorno = $componente->update($_POST, $_POST['id']);
		if($retorno === true) {
			aviso::retornar('Componente editado com sucesso!', 'componente_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'componente_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'excluir') {
		$retorno = $componente->delete($_POST['id']);
		if($retorno === true) {
			aviso::retornar('Componente excluído com sucesso!', 'componente_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'componente_lista.php', 'erro', $ajax);
		}
	}
}
require_once('rodape.php');