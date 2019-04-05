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
		
		$usuario_rs = $this->getFieldsByParameter("nome, login, indice_produtividade, admin, foto, id", "WHERE $sql_where ORDER BY $ordenacao $filtragem LIMIT $limit OFFSET $offset");
		foreach($usuario_rs as $i=>$usuario_row){
			$foto = $usuario_row['foto'];
			
			if(!file_exists($foto)){
				$foto = '../common/img/user.png';
			}
			$img_foto = "<img src='$foto' class='img-circle' style='width: 2.1rem' />";
			$usuario_rs[$i]['nome'] = $img_foto . ' ' . funcoes::capitaliza($usuario_row['nome']);
			$usuario_rs[$i]['indice_produtividade'] = funcoes::encodeMonetario($usuario_row['indice_produtividade'], 1) . ' Horas / PF';
			$usuario_rs[$i]['admin'] = ($usuario_row['admin'] == '1') ? ('Sim') : ('Não');
		}
		return $usuario_rs;
	}
	
	private function formataSQLAutocomplete($busca) {
		$sql_where = 'WHERE TRUE';

		if (!empty($busca)) {
			$nome = str_replace(' ', '%', $busca);
            $sql_where .= " AND nome LIKE '%$nome%'";
		}

		return $sql_where;
	}

	public function getTotalByAutocomplete($busca) {

		$sql_where = $this->formataSQLAutocomplete($busca);

		$retorno = $this->getFieldsByParameter("COUNT(id) AS total_consulta", "$sql_where LIMIT 1");
		if (count($retorno) > 0) {
			return $retorno[0]['total_consulta'];
		} else {
			return 0;
		}
	}

	public function getByAutocomplete($busca, $limit = 30, $offset = 0) {
		$sql_where = $this->formataSQLAutocomplete($busca);

		return $this->getFieldsByParameter('*', "$sql_where ORDER BY nome LIMIT $limit OFFSET $offset");
	}
	
	public function getLogin($login, $senha){
		$senha_sha1 = sha1($senha);
		
		$usuario_rs = $this->getFieldsByParameter("nome, login, foto, admin, id", "WHERE login = '$login' AND senha_sha1 = '$senha_sha1' LIMIT 1");
		if(count($usuario_rs) > 0){
			return $usuario_rs[0];
		} else {
			return array();
		}
	}
	
	public function getByEdicao($id){
		$usuario_rs = $this->getFieldsByParameter("nome, login, foto, indice_produtividade, id", "WHERE id = $id LIMIT 1");
		if(count($usuario_rs) > 0){
			return $usuario_rs[0];
		} else {
			return array();
		}
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$post['senha_sha1'] = sha1('123456');
		$post['indice_produtividade'] = (float)$post['indice_produtividade'];
		$post['admin'] = (isset($post['admin']) && ($post['admin'] == 'true')) ? ('1') : ('0');
		
		if($post['indice_produtividade'] < 0.4 || $post['indice_produtividade'] > 1){
			return 'Digite um índice de produtividade entre 0,4 e 1.';
		}
		
		return parent::set($post, $commit);
	}
	
	public function update($post, $id, $commit=true){
		$post['indice_produtividade'] = (float)$post['indice_produtividade'];
		$post['admin'] = (isset($post['admin']) && ($post['admin'] == 'true')) ? ('1') : ('0');
		
		if($post['indice_produtividade'] < 0.4 || $post['indice_produtividade'] > 1){
			return 'Digite um índice de produtividade entre 0,4 e 1.';
		}
		
		return parent::update($post, $id, $commit);
	}
	
	public function updateDadosPessoais($post, $id, $commit=true){
		$post['indice_produtividade'] = (float)$post['indice_produtividade'];
		
		if($post['indice_produtividade'] < 0.4 || $post['indice_produtividade'] > 1){
			return 'Digite um índice de produtividade entre 0,4 e 1.';
		}
		
		$post_editar = array(
			'nome' => $post['nome'],
			'indice_produtividade' => $post['indice_produtividade']
		);
		return parent::update($post_editar, $id, $commit);
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

        // Editando registro na tabela "usuarios"
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