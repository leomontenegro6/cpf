<?php
class funcionalidade extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getByModulo($id_modulo){
		return $this->getFieldsByParameter("f.nome, m.nome AS modulo, f.id", "f
				JOIN sistemas m ON (f.modulo = m.id)
			WHERE f.modulo = $id_modulo
			ORDER BY f.nome");
	}
	
	private function formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_tipo_funcionalidade){
		$sql_where = 'TRUE';
		
		// Busca comum
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (";
            $sql_where .= "f.ordem LIKE '%$busca%'";
            $sql_where .= " OR s.nome LIKE '%$busca%'";
            $sql_where .= " OR m.nome LIKE '%$busca%'";
            $sql_where .= " OR f.nome LIKE '%$busca%'";
            $sql_where .= " OR tf.descricao LIKE '%$busca%'";
            $sql_where .= ")";
		}
		
		// Busca por filtros avançados: sistema
		if(is_numeric($id_sistema)){
			$sql_where .= " AND m.sistema = $id_sistema";
		}
		
		// Busca por filtros avançados: modulo
		if(is_numeric($id_modulo)){
			$sql_where .= " AND f.modulo = $id_modulo";
		}
		
		// Busca por filtros avançados: tipo de funcionalidade
		if(is_numeric($id_tipo_funcionalidade)){
			$sql_where .= " AND f.tipo_funcionalidade = $id_tipo_funcionalidade";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca, $id_sistema, $id_modulo, $id_tipo_funcionalidade){
		$sql_where = $this->formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_tipo_funcionalidade);
		
		$funcionalidade_rs = $this->getFieldsByParameter("COUNT(f.id) AS total", "f
			JOIN tipos_funcionalidades tf ON (f.tipo_funcionalidade = tf.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE $sql_where
			LIMIT 1");
		if(count($funcionalidade_rs) > 0){
			return $funcionalidade_rs[0]['total'];
		} else {
			return 0;
		}
	}
	
	public function getByListagem($busca, $id_sistema, $id_modulo, $id_tipo_funcionalidade, $ordenacao='f.nome', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_tipo_funcionalidade);
		
		$funcionalidade_rs = $this->getFieldsByParameter("f.ordem, s.nome AS sistema, m.nome AS modulo, f.nome,
			tf.descricao AS tipo_funcionalidade, COUNT(c.id) AS total_componentes, f.id", "f
				JOIN tipos_funcionalidades tf ON (f.tipo_funcionalidade = tf.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
				LEFT JOIN componentes c ON (c.funcionalidade = f.id)
			WHERE $sql_where
			GROUP BY f.ordem, s.nome, m.nome, f.nome, tf.descricao, f.id
			ORDER BY $ordenacao $filtragem
			LIMIT $limit OFFSET $offset");
		foreach($funcionalidade_rs as $i=>$funcionalidade_row){
			
		}
		return $funcionalidade_rs;
	}
	
	public function getByDetalhes($id){
		$funcionalidade_rs = $this->getFieldsByParameter("s.nome AS sistema, m.nome AS modulo, f.nome, f.ordem,
			tf.descricao AS tipo_funcionalidade", "f
				JOIN tipos_funcionalidades tf ON (f.tipo_funcionalidade = tf.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE f.id = $id
			LIMIT 1");
		
		if(count($funcionalidade_rs) > 0){
			$funcionalidade_row = $funcionalidade_rs[0];
			
			return $funcionalidade_row;
		} else {
			return array();
		}
	}
	
	private function formataSQLAutocomplete($busca, $id_sistema, $id_modulo) {
		$sql_where = 'WHERE TRUE';

		if(!empty($busca)){
			$nome = str_replace(' ', '%', $busca);
            $sql_where .= " AND f.nome LIKE '%$nome%'";
		}
		if(is_numeric($id_sistema)){
			$sql_where .= " AND m.sistema = $id_sistema";
		}
		if(is_numeric($id_modulo)){
			$sql_where .= " AND f.modulo = $id_modulo";
		}

		return $sql_where;
	}

	public function getTotalByAutocomplete($busca, $id_sistema, $id_modulo) {
		$sql_where = $this->formataSQLAutocomplete($busca, $id_sistema, $id_modulo);

		$retorno = $this->getFieldsByParameter("COUNT(f.id) AS total_consulta", "f
				JOIN modulos m ON (f.modulo = m.id)
			$sql_where
			LIMIT 1");
		if (count($retorno) > 0) {
			return $retorno[0]['total_consulta'];
		} else {
			return 0;
		}
	}

	public function getByAutocomplete($busca, $id_sistema, $id_modulo, $limit = 30, $offset = 0) {
		$sql_where = $this->formataSQLAutocomplete($busca, $id_sistema, $id_modulo);

		return $this->getFieldsByParameter('f.nome, m.nome AS modulo, s.nome AS sistema,
			s.sigla AS sigla_sistema, f.modulo AS id_modulo, m.sistema AS id_sistema,
			f.id', "f
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
			$sql_where
			ORDER BY s.nome, m.nome, f.ordem, f.nome
			LIMIT $limit OFFSET $offset");
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$componente = new componente();
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		$post_funcionalidade = $post['funcionalidade'];
		$post_componentes = $post['componentes'];
		
		// Inserindo registro na tabela "funcionalidades"
		$id_funcionalidade = $this->getNextid();
		$post_funcionalidade['id'] = $id_funcionalidade;
		$retorno = parent::set($post_funcionalidade, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros nas tabelas "componentes", "campos" e "arquivos_referenciados"
		foreach($post_componentes as $i=>$post_componente){
			$ordem = ($i + 1);
			if(isset($post_componente['possui_acoes']) && ($post_componente['possui_acoes'] == 'true')){
				$post_componente['possui_acoes'] = 'true';
			} else {
				$post_componente['possui_acoes'] = 'false';
			}
			if(isset($post_componente['possui_mensagens']) && ($post_componente['possui_mensagens'] == 'true')){
				$post_componente['possui_mensagens'] = 'true';
			} else {
				$post_componente['possui_mensagens'] = 'false';
			}
			
			$modo_preenchimento_campos = $post_componente['modo_preenchimento_campos'];
			$quantidade_campos = $post_componente['quantidade_campos'];
			$nomes_campos = (isset($post_componente['nomes_campos'])) ? ($post_componente['nomes_campos']) : (array());
			$modo_preenchimento_arquivos_referenciados = $post_componente['modo_preenchimento_arquivos_referenciados'];
			$quantidade_arquivos_referenciados = $post_componente['quantidade_arquivos_referenciados'];
			$nomes_arquivos_referenciados = (isset($post_componente['nomes_arquivos_referenciados'])) ? ($post_componente['nomes_arquivos_referenciados']) : (array());
			
			$post_componente['id'] = $id_componente = ($componente->getNextid() + $i);
			$post_componente['funcionalidade'] = $id_funcionalidade;
			$post_componente['ordem'] = $ordem;
			
			// Inserindo registro na tabela "componentes"
			$retorno = $componente->setByFuncionalidade($post_componente, false);
			if($retorno !== true){
				return $retorno;
			}
			
			// Inserindo registros na tabela "campos", para esse componente
			if($modo_preenchimento_campos == 'q'){
				if($quantidade_campos == 0){
					return 'Pelo menos um campo deve ser fornecido!';
				}
				
				for($j=0; $j<$quantidade_campos; $j++){
					$ordem = ($j + 1);
					$post_campo = array(
						'nome' => "Campo $ordem",
						'componente' => $id_componente
					);
					$retorno = $campo->set($post_campo, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			} else {
				if(count($nomes_campos) == 0){
					return 'Pelo menos um campo deve ser fornecido!';
				}
				
				foreach($nomes_campos as $nome){
					$post_campo = array(
						'nome' => $nome,
						'componente' => $id_componente
					);
					$retorno = $campo->set($post_campo, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			}
			
			// Inserindo registros na tabela "arquivos_referenciados", para esse componente
			if($modo_preenchimento_arquivos_referenciados == 'q'){
				if($quantidade_arquivos_referenciados == 0){
					return 'Pelo menos um arquivo referenciado deve ser fornecido!';
				}
				
				for($j=0; $j<$quantidade_arquivos_referenciados; $j++){
					$ordem = $j;
					$post_campo = array(
						'nome' => ($ordem == 0) ? ('Própria tabela') : ("Tabela externa $ordem"),
						'componente' => $id_componente
					);
					$retorno = $arquivoReferenciado->set($post_campo, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			} else {
				if(count($nomes_arquivos_referenciados) == 0){
					return 'Pelo menos um arquivo referenciado deve ser fornecido!';
				}
				
				foreach($nomes_arquivos_referenciados as $nome){
					$post_arquivo_referenciado = array(
						'nome' => $nome,
						'componente' => $id_componente
					);
					$retorno = $arquivoReferenciado->set($post_arquivo_referenciado, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			}
		}
		
		// Commitando alterações via transação
		if($commit){
			return $this->commit();
		} else {
			return true;
		}
	}
	
	public function update($post, $id, $commit=true){
		$post_funcionalidade = $post['funcionalidade'];
		
		return parent::update($post_funcionalidade, $id, $commit);
	}
	
	public function delete($id, $commit=true){
		$componente = new componente();
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		// Excluindo registros na tabela "campos"
		$retorno = $campo->deleteByFuncionalidade($id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registros na tabela "arquivos_referenciados"
		$retorno = $arquivoReferenciado->deleteByFuncionalidade($id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registros na tabela "componentes"
		$retorno = $componente->deleteByCampo('funcionalidade', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registro na tabela "funcionalidades"
		$retorno = parent::delete($id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Commitando alterações via transação
		if($commit){
			return $this->commit();
		} else {
			return true;
		}
	}
}