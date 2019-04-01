<?php
class componente extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getByFuncionalidade($id_funcionalidade){
		return $this->getFieldsByParameter("tc.descricao AS tipo_componente, td.descricao AS tipo_dado,
			c.possui_acoes, c.possui_mensagens, c.id", "c
				JOIN tipos_componentes tc ON (c.tipo_componente = tc.id)
				JOIN tipos_dados td ON (tc.tipo_dado = td.id)
			WHERE c.funcionalidade = $id_funcionalidade
			ORDER BY c.ordem");
	}
	
	private function formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_funcionalidade){
		$sql_where = 'TRUE';
		
		// Busca comum
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND co.nome LIKE '%$busca%'";
		}
		
		// Busca por filtros avançados: sistema
		if(is_numeric($id_sistema)){
			$sql_where .= " AND m.sistema = $id_sistema";
		}
		
		// Busca por filtros avançados: modulo
		if(is_numeric($id_modulo)){
			$sql_where .= " AND f.modulo = $id_modulo";
		}
		
		// Busca por filtros avançados: funcionalidade
		if(is_numeric($id_funcionalidade)){
			$sql_where .= " AND co.funcionalidade = $id_funcionalidade";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca, $id_sistema, $id_modulo, $id_funcionalidade){
		$sql_where = $this->formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_funcionalidade);
		
		$componente_rs = $this->getFieldsByParameter("COUNT(co.id) AS total", "co
				JOIN tipos_componentes tco ON (co.tipo_componente = tco.id)
				JOIN funcionalidades f ON (co.funcionalidade = f.id)
				JOIN modulos m ON (f.modulo = m.id)
			WHERE $sql_where
			LIMIT 1");
		if(count($componente_rs) > 0){
			return $componente_rs[0]['total'];
		} else {
			return 0;
		}
	}
	
	public function getByListagem($busca, $id_sistema, $id_modulo, $id_funcionalidade, $ordenacao='co.nome', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca, $id_sistema, $id_modulo, $id_funcionalidade);
		
		$componente_rs = $this->getFieldsByParameter("CONCAT(f.ordem, '. ', co.ordem, '.') AS ordem, s.nome AS sistema, m.nome AS modulo, f.nome AS funcionalidade,
			tco.descricao AS tipo_componente, '' AS complexidade, '' AS valor_pf, co.possui_acoes, co.possui_mensagens, co.id", "co
				JOIN tipos_componentes tco ON (co.tipo_componente = tco.id)
				JOIN funcionalidades f ON (co.funcionalidade = f.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE $sql_where
			ORDER BY $ordenacao $filtragem
			LIMIT $limit OFFSET $offset");
		foreach($componente_rs as $i=>$componente_row){
			$complexidade_valor = $this->calcularComplexidadeValorPF($componente_row['id']);
			$componente_rs[$i]['complexidade'] = funcoes::capitaliza($complexidade_valor['complexidade']);
			$componente_rs[$i]['valor_pf'] = $complexidade_valor['valor'];
	}
		return $componente_rs;
	}
	
	public function getByDetalhes($id){
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		$componente_rs = $this->getFieldsByParameter("CONCAT(f.ordem, '. ', c.ordem, '.') AS ordem,
			s.nome AS sistema, m.nome AS modulo, f.nome AS funcionalidade, tc.descricao AS tipo_componente,
			c.possui_acoes, c.possui_mensagens, td.descricao AS tipo_dado,
			tc.tipo_dado AS id_tipo_dado", "c
				JOIN funcionalidades f ON (c.funcionalidade = f.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
				JOIN tipos_componentes tc ON (c.tipo_componente = tc.id)
				JOIN tipos_dados td ON (tc.tipo_dado = td.id)
			WHERE c.id = $id
			ORDER BY c.ordem");
		if(count($componente_rs) > 0){
			$componente_row = $componente_rs[0];
			
			$componente_row['campos'] = $campo->getByComponente($id);
			$componente_row['arquivos_referenciados'] = $arquivoReferenciado->getByComponente($id);
			
			return $componente_row;
		} else {
			return array();
		}
	}
	
	public function getByPlanilhaContagemPontos($id_sistema, $id_modulo, $detalhar_campos_arquivos=false){
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		$sql_where = 'TRUE';
		
		// Filtro por sistema
		if(is_numeric($id_sistema)){
			$sql_where .= " AND s.id = $id_sistema";
		}
		
		// Filtro por módulo
		if(is_numeric($id_modulo)){
			$sql_where .= " AND m.id = $id_modulo";
		}
		
		// Executando consulta de obtenção dos dados da planilha
		$componente_rs = $this->getFieldsByParameter("CONCAT(f.ordem, '. ', co.ordem, '.') AS ordem,
			s.nome AS sistema, m.nome AS modulo, f.nome AS funcionalidade,
			tco.descricao AS componente, td.descricao AS tipo_funcional,
			(CASE WHEN td.id = 1 THEN 'e' WHEN td.id = 2 THEN 's' WHEN td.id = 3 THEN 'c' ELSE '' END) AS letra_tipo_funcional,
			co.possui_acoes, co.possui_mensagens, f.id AS id_funcionalidade, co.id AS id_componente, tco.id AS id_tipo_componente", "co
				JOIN tipos_componentes tco ON (co.tipo_componente = tco.id)
				JOIN tipos_dados td ON (tco.tipo_dado = td.id)
				JOIN funcionalidades f ON (co.funcionalidade = f.id)
				JOIN modulos m ON (f.modulo = m.id)
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE $sql_where
			ORDER BY 2, 3, 1");
		
		// Iterando pelos resultados uma vez, para obter os campos, arquivo referenciados
		// e calcular os valores para posterior união vertical de linhas (rowspan)
		$rowspans = array(
			'funcionalidades' => array(),
			'componentes' => array()
		);
		foreach($componente_rs as $i=>$componente_row){
			$id_funcionalidade = $componente_row['id_funcionalidade'];
			$id_componente = $componente_row['id_componente'];
			
			$campo_rs = $campo->getByComponente($id_componente);
			$arquivoReferenciado_rs = $arquivoReferenciado->getByComponente($id_componente);
			
			$quantidade_tipos_dados = count($campo_rs);
			$quantidade_arquivos_referenciados = count($arquivoReferenciado_rs);
			if($componente_row['possui_acoes'] == '1'){
				$quantidade_tipos_dados++;
			}
			if($componente_row['possui_mensagens'] == '1'){
				$quantidade_tipos_dados++;
			}
			
			$componente_rs[$i]['campos'] = $campo_rs;
			$componente_rs[$i]['arquivos_referenciados'] = $arquivoReferenciado_rs;
			$componente_rs[$i]['quantidade_tipos_dados'] = $quantidade_tipos_dados;
			$componente_rs[$i]['quantidade_arquivos_referenciados'] = $quantidade_arquivos_referenciados;
			
			if($detalhar_campos_arquivos){
				if($quantidade_tipos_dados >= $quantidade_arquivos_referenciados){
					$rowspans['componentes'][$id_componente] = $quantidade_tipos_dados;
				} else {
					$rowspans['componentes'][$id_componente] = $quantidade_arquivos_referenciados;
				}
			} else {
				$rowspans['componentes'][$id_componente] = 1;
			}
			
			if(isset($rowspans['funcionalidades'][$id_funcionalidade])){
				if($i > 0){
					$componenteAnterior_row = $componente_rs[$i - 1];

					if($componente_row['id_funcionalidade'] == $componenteAnterior_row['id_funcionalidade']){
						$rowspans['funcionalidades'][$id_funcionalidade] += $rowspans['componentes'][$id_componente];
					}
				}
			} else {
				$rowspans['funcionalidades'][$id_funcionalidade] = $rowspans['componentes'][$id_componente];
			}
		}
		
		// Iterando pelos resultados outra vez, para formatar valores e calcular
		// complexidade e valor de cada componente, em pontos de função
		foreach($componente_rs as $i=>$componente_row){
			$id_funcionalidade = $componente_row['id_funcionalidade'];
			$id_componente = $componente_row['id_componente'];
			$tipo_funcional = $componente_row['letra_tipo_funcional'];
			
			$quantidade_tipos_dados = $componente_row['quantidade_tipos_dados'];
			$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];
			
			$complexidade = cpf::calcularComplexidade($tipo_funcional, $quantidade_tipos_dados, $quantidade_arquivos_referenciados);
			$valor_pf = cpf::calcularValor($tipo_funcional, $complexidade);
			
			$componente_rs[$i]['rowspan_funcionalidade_modulo'] = $rowspans['funcionalidades'][$id_funcionalidade];
			$componente_rs[$i]['rowspan_componente'] = $rowspans['componentes'][$id_componente];
			$componente_rs[$i]['complexidade'] = funcoes::capitaliza($complexidade);
			$componente_rs[$i]['valor_pf'] = $valor_pf;
		}
		
		// Retornando resultados ao fim do processo
		return $componente_rs;
	}
	
	// Métodos de validações e cálculos
	public function calcularComplexidadeValorPF($id){
		$componente_row = $this->getByDetalhes($id);
		$campos = $componente_row['campos'];
		$arquivos_referenciados = $componente_row['arquivos_referenciados'];
		
		$tipo_funcional = '';
		if($componente_row['id_tipo_dado'] == 1){
			$tipo_funcional = 'e';
		} elseif($componente_row['id_tipo_dado'] == 2){
			$tipo_funcional = 's';
		} elseif($componente_row['id_tipo_dado'] == 3){
			$tipo_funcional = 'c';
		}
		
		$quantidade_tipos_dados = count($campos);
		$quantidade_arquivos_referenciados = count($arquivos_referenciados);
		
		if($componente_row['possui_acoes'] == '1'){
			$quantidade_tipos_dados++;
		}
		if($componente_row['possui_mensagens'] == '1'){
			$quantidade_tipos_dados++;
		}
		
		$complexidade = cpf::calcularComplexidade($tipo_funcional, $quantidade_tipos_dados, $quantidade_arquivos_referenciados);
		$valor = cpf::calcularValor($tipo_funcional, $complexidade);
		
		return array(
			'complexidade' => $complexidade,
			'valor' => $valor
		);
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		$nomes_campos = (isset($post['campos'])) ? ($post['campos']) : (array());
		$nomes_arquivos_referenciados = (isset($post['arquivos_referenciados'])) ? ($post['arquivos_referenciados']) : (array());
		
		if(isset($post['possui_acoes']) && ($post['possui_acoes'] == 'true')){
			$post['possui_acoes'] = 'true';
		} else {
			$post['possui_acoes'] = 'false';
		}
		if(isset($post['possui_mensagens']) && ($post['possui_mensagens'] == 'true')){
			$post['possui_mensagens'] = 'true';
		} else {
			$post['possui_mensagens'] = 'false';
		}
		
		// Inserindo registro na tabela "componentes"
		$id_componente = $this->getNextid();
		$post['id'] = $id_componente;
		$retorno = parent::set($post, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros na tabela "campos"
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
		
		// Inserindo registros na tabela "arquivos_referenciados"
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
		
		// Commitando alterações via transação
		if($commit){
			return $this->commit();
		} else {
			return true;
		}
	}
	
	public function update($post, $id, $commit=true){
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		$nomes_campos = (isset($post['campos'])) ? ($post['campos']) : (array());
		$nomes_arquivos_referenciados = (isset($post['arquivos_referenciados'])) ? ($post['arquivos_referenciados']) : (array());
		
		if(isset($post['possui_acoes']) && ($post['possui_acoes'] == 'true')){
			$post['possui_acoes'] = 'true';
		} else {
			$post['possui_acoes'] = 'false';
		}
		if(isset($post['possui_mensagens']) && ($post['possui_mensagens'] == 'true')){
			$post['possui_mensagens'] = 'true';
		} else {
			$post['possui_mensagens'] = 'false';
		}
		
		// Editando registro na tabela "componentes"
		$retorno = parent::update($post, $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registros na tabela "campos", para posterior reinserção
		$retorno = $campo->deleteByCampo('componente', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros na tabela "campos"
		foreach($nomes_campos as $nome){
			$post_campo = array(
				'nome' => $nome,
				'componente' => $id
			);
			$retorno = $campo->set($post_campo, false);
			if($retorno !== true){
				return $retorno;
			}
		}
		
		// Excluindo registros na tabela "arquivos_referenciados",
		// para posterior reinserção
		$retorno = $arquivoReferenciado->deleteByCampo('componente', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros na tabela "arquivos_referenciados"
		foreach($nomes_arquivos_referenciados as $nome){
			$post_arquivo_referenciado = array(
				'nome' => $nome,
				'componente' => $id
			);
			$retorno = $arquivoReferenciado->set($post_arquivo_referenciado, false);
			if($retorno !== true){
				return $retorno;
			}
		}
		
		// Commitando alterações via transação
		if($commit){
			return $this->commit();
		} else {
			return true;
		}
	}
	
	public function delete($id, $commit=true){
		$campo = new campo();
		$arquivoReferenciado = new arquivoReferenciado();
		
		// Excluindo registros na tabela "campos"
		$retorno = $campo->deleteByCampo('componente', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registros na tabela "arquivos_referenciados"
		$retorno = $arquivoReferenciado->deleteByCampo('componente', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registro na tabela "componentes"
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
	
	public function setByFuncionalidade($post, $commit=true){
		return parent::set($post, $commit);
	}
}