<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$usuario = new usuario();

$pagina_redirecionamento = (isset($_POST['endereco_anterior'])) ? ($_POST['endereco_anterior']) : ('index.php');

if(isset($_POST['acao'])) {
	if ($_POST['acao'] == 'cadastrar') {
		$retorno = $usuario->set($_POST);
		if($retorno === true) {
			aviso::retornar('Usuário cadastrado com sucesso!', 'usuario_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'usuario_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'editar') {
		$retorno = $usuario->update($_POST, $_POST['id']);
		if($retorno === true) {
			aviso::retornar('Usuário editado com sucesso!', 'usuario_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'usuario_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'excluir') {
		$retorno = $usuario->delete($_POST['id']);
		if($retorno === true) {
			aviso::retornar('Usuário excluído com sucesso!', 'usuario_lista.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'usuario_lista.php', 'erro', $ajax);
		}
	} elseif ($_POST['acao'] == 'alterar_dados_pessoais') {
		$retorno = $usuario->updateDadosPessoais($_POST, $_SESSION['iduser']);
		if($retorno === true) {
			$_SESSION['nome'] = $_POST['nome'];
			$_SESSION['nome_exibicao'] = funcoes::formataNomeExibicao($_POST['nome']);
			
			aviso::retornar('Dados pessoais alterados com sucesso!', $pagina_redirecionamento, 'sucesso', false);
		} else {
			aviso::retornar($retorno, 'usuario_logado_dados_pessoais_edita.php', 'erro', false);
		}
	} elseif ($_POST['acao'] == 'editar_foto') {
		$retorno = $usuario->updateFoto($_POST, $_SESSION['iduser']);
		if($retorno === true) {
			$foto = $usuario->getFoto($_SESSION['iduser']);
			if(file_exists($foto)){
				$_SESSION['foto'] = $foto;
			} else {
				$_SESSION['foto'] = '../common/img/user.png';
			}
			
			aviso::retornar('Foto alterada com sucesso!', $pagina_redirecionamento, 'sucesso', false);
		} else {
			aviso::retornar($retorno, 'usuario_logado_foto_edita.php', 'erro', false);
		}
	} elseif ($_POST['acao'] == 'redefinir_senha') {
		$retorno = $usuario->updateSenha($_POST, $_SESSION['iduser']);
		if($retorno === true) {
			aviso::retornar('Senha redefinida com sucesso!<br />Realize o login com a nova senha!', 'logoff.php', '', $ajax);
		} else {
			aviso::retornar($retorno, 'usuario_logado_senha_edita.php', 'erro', $ajax);
		}
	}
}