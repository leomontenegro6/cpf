<?php
class usuario extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	private function formataSQLByListagem($busca){
		$sql_where = 'TRUE';
		
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (nome LIKE '%$busca%' OR login LIKE '%$busca%')";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca){
		$sql_where = $this->formataSQLByListagem($busca);
		
		return $this->getTotal("WHERE $sql_where");
	}
	
	public function getByListagem($busca, $ordenacao='nome', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca);
		
		$usuario_rs = $this->getFieldsByParameter("nome, login, foto, id", "WHERE $sql_where ORDER BY $ordenacao $filtragem LIMIT $limit OFFSET $offset");
		foreach($usuario_rs as $i=>$usuario_row){
			$foto = $usuario_row['foto'];
			
			if(!file_exists($foto)){
				$foto = '../common/img/user.png';
			}
			$img_foto = "<img src='$foto' class='img-circle' style='width: 2.1rem' />";
			$usuario_rs[$i]['nome'] = $img_foto . ' ' . funcoes::capitaliza( $usuario_row['nome'] );
		}
		return $usuario_rs;
	}
	
	public function getLogin($login, $senha){
		$senha_sha1 = sha1($senha);
		
		$usuario_rs = $this->getFieldsByParameter("nome, login, foto, id", "WHERE login = '$login' AND senha_sha1 = '$senha_sha1' LIMIT 1");
		if(count($usuario_rs) > 0){
			return $usuario_rs[0];
		} else {
			return array();
		}
	}
	
	public function getByEdicao($id){
		$usuario_rs = $this->getFieldsByParameter("nome, login, foto, id", "WHERE id = $id LIMIT 1");
		if(count($usuario_rs) > 0){
			return $usuario_rs[0];
		} else {
			return array();
		}
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$post['senha_sha1'] = sha1('123456');
		
		return parent::set($post, $commit);
	}
	
	public function updateFoto($post, $id, $commit=true){
        if (empty($post['foto'])) {
            $foto = array();
        } else {
            $foto = $post['foto'];
        }
        $caminhoFotoAtual = $this->getFoto($id, 'n');

        if (count($foto) > 0) {
            $nome = base64_decode($foto['name']);
            $origem = base64_decode($foto['tmp_name']);
            $extensao = funcoes::getExtensaoByNomeArquivo($nome);

            $nomeArquivo = funcoes::removerEspacos(funcoes::normalize($nome));
            $caminhoFoto = '../common/arquivos/foto_' . funcoes::gerarNomeArquivo($nomeArquivo, $extensao);

            // Salvando arquivo na pasta
            $ok = rename($origem, $caminhoFoto);
            if (!$ok) {
                if (file_exists($caminhoFoto)) {
                    unlink($caminhoFoto);
                }
                return "Falha ao copiar arquivo.";
            }

            // Gerando foto reduzida de 100x100
            $caminhoFotoRedimensionada = funcoes::redimensionaFoto($caminhoFoto, $extensao);
            if (file_exists($caminhoFoto)) {
                unlink($caminhoFoto);
            }
            if ($caminhoFotoRedimensionada === false) {
                return 'Não foi possível criar a imagem reduzida!';
            } else {
                $caminhoFoto = $caminhoFotoRedimensionada;
            }
        } else {
            $caminhoFoto = '';
        }

        // Editando registro na tabela "pessoas"
        $retorno = $this->setFoto($caminhoFoto, $id, false);
        if ($retorno !== true) {
            return $retorno;
        }

        // Commitando alterações via transação
        if ($commit) {
            $retorno = $this->commit();
        } else {
            $retorno = true;
        }

        // Se o commit der certo, excluir a imagem antiga na pasta "arquivos".
        // Do contrário, excluir a imagem nova, se existir.
        if ($retorno === true) {
            if (file_exists($caminhoFotoAtual)) {
                unlink($caminhoFotoAtual);
            }
            return true;
        } else {
            if (file_exists($caminhoFoto)) {
                unlink($caminhoFoto);
            }
            return $retorno;
        }
	}
	
	public function updateSenha($post, $id, $commit=true){
        if (!empty ($post['senha'])){
			$senha = $post['senha'];
			$confirmar_senha = $post['confirmar_senha'];
		} else {
			return 'Senha não informada!';
		}
		
		// Validando tamanho mínimo da senha
		$tamanho_senha = strlen($senha);
		if($tamanho_senha <= 4){
			return 'A senha deve ter tamanho mínimo de 4 caracteres!';
		}
		
		// Verificando se ambas as senhas conferem
		$checkSenhasConferem = ($senha == $confirmar_senha);
        if (!$checkSenhasConferem) {
            return 'Senhas não conferem!<br />Por favor, digite novamente!';
        }
		
		$post_editar = array(
			'senha_sha1'=>sha1($senha),
		);
		return parent::update($post_editar, $id, true, $commit);
	}
}