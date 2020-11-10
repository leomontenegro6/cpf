<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$feriado = new feriado();

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'cadastrar') {
		$retorno = $feriado->set($_POST);
		if($retorno === true) {
			aviso::retornar('Feriado customizado cadastrado com sucesso!', 'feriado_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'feriado_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'editar') {
		$retorno = $feriado->update($_POST, $_POST['id']);
		if($retorno === true) {
			aviso::retornar('Feriado customizado editado com sucesso!', 'feriado_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'feriado_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'excluir') {
		$retorno = $feriado->delete($_POST['id']);
		if($retorno === true) {
			aviso::retornar('Feriado customizado excluído com sucesso!', 'feriado_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'feriado_lista.php', 'erro', $ajax);
		}
	}
}