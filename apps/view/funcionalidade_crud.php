<?php
require_once('cabecalho.php');
require_once('menu.php');

$funcionalidade = new funcionalidade();

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'cadastrar') {
		$retorno = $funcionalidade->set($_POST);
		if($retorno === true) {
			modal::retornar('Funcionalidade cadastrada com sucesso!', 'funcionalidade_lista.php', '', $ajax);
		} else {
			modal::retornar($retorno, 'funcionalidade_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'editar') {
		$retorno = $funcionalidade->update($_POST, $_POST['id']);
		if($retorno === true) {
			modal::retornar('Funcionalidade editada com sucesso!', 'funcionalidade_lista.php', '', $ajax);
		} else {
			modal::retornar($retorno, 'funcionalidade_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'excluir') {
		$retorno = $funcionalidade->delete($_POST['id']);
		if($retorno === true) {
			modal::retornar('Funcionalidade excluída com sucesso!', 'funcionalidade_lista.php', '', $ajax);
		} else {
			modal::retornar($retorno, 'funcionalidade_lista.php', 'erro', $ajax);
		}
	}
}
require_once('rodape.php');