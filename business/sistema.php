<?php
class sistema extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getDescricao($id){
		$sistema_row = $this->get($id);
		
		if(count($sistema_row) > 0){
			return $sistema_row['sigla'] . ' - ' . $sistema_row['nome'];
		} else {
			return '';
		}
	}
	
	private function formataSQLByListagem($busca){
		$sql_where = 'TRUE';
		
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (s.nome LIKE '%$busca%' OR s.sigla LIKE '%$busca%' OR m.nome LIKE '%$busca%')";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca){
		$sql_where = $this->formataSQLByListagem($busca);
		
		$sistema_rs = $this->getFieldsByParameter("COUNT(s.id) AS total", "s
				LEFT JOIN modulos m ON (m.sistema = s.id)
			WHERE $sql_where
			LIMIT 1");
		if(count($sistema_rs) > 0){
			return $sistema_rs[0]['total'];
		} else {
			return 0;
		}
	}
	
	public function getByListagem($busca, $ordenacao='s.nome', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca);
		
		$sistema_rs = $this->getFieldsByParameter("s.nome, s.sigla, GROUP_CONCAT(m.nome ORDER BY m.nome ASC) AS modulos, s.id", "s
				LEFT JOIN modulos m ON (m.sistema = s.id)
			WHERE $sql_where
			GROUP BY s.nome, s.sigla, s.id
			ORDER BY $ordenacao $filtragem
			LIMIT $limit OFFSET $offset");
		foreach($sistema_rs as $i=>$sistema_row){
			if(!empty($sistema_row['modulos'])){
				$sistemas = explode(',', $sistema_row['modulos']);
				$sistema_rs[$i]['modulos'] = '<ul style="margin-bottom: 0; padding-left: 15px">';
				foreach($sistemas as $nome_sistema){
					$sistema_rs[$i]['modulos'] .= "<li>$nome_sistema</li>";
				}
				$sistema_rs[$i]['modulos'] .= '</ul>';
			} else {
				$sistema_rs[$i]['modulos'] = '---';
			}
		}
		return $sistema_rs;
	}
	
	private function formataSQLAutocomplete($busca) {
		$sql_where = 'WHERE TRUE';

		if (!empty($busca)) {
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (nome LIKE '%$busca%' OR sigla LIKE '%$busca%')";
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
	
	public function getByValoresSistemas(){
		$sistema_rs = $this->getFieldsByParameter("CONCAT(sigla, ' - ', nome) AS sistema, id", "ORDER BY 1");
		
		foreach($sistema_rs as $i=>$sistema_row){
			$sistema_rs[$i]['valor_pf'] = $this->calcularValorPF($sistema_row['id']);
		}
		
		return $sistema_rs;
	}
	
	// Métodos de validações e cálculos
	public function calcularValorPF($id){
		$componenteSistema_rs = $this->getFieldsByParameter("co.id AS id_componente, tco.tipo_dado AS id_tipo_dado, co.possui_acoes, co.possui_mensagens,
			COUNT(DISTINCT c.id) AS quantidade_campos, COUNT(DISTINCT ar.id) AS quantidade_arquivos_referenciados", "s
				JOIN modulos m ON (m.sistema = s.id)
				JOIN funcionalidades f ON (f.modulo = m.id)
				LEFT JOIN componentes co ON (co.funcionalidade = f.id)
				LEFT JOIN tipos_componentes tco ON (co.tipo_componente = tco.id)
				LEFT JOIN campos c ON (c.componente = co.id)
				LEFT JOIN arquivos_referenciados ar ON (ar.componente = co.id)
			WHERE s.id = $id
			GROUP BY co.id, tco.tipo_dado, co.possui_acoes, co.possui_mensagens");
		
		$valor_total_pf = 0;
		foreach($componenteSistema_rs as $componente_row){
			$quantidade_tipos_dados = $componente_row['quantidade_campos'];
			$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];
			
			if($componente_row['possui_acoes'] == '1'){
				$quantidade_tipos_dados++;
			}
			if($componente_row['possui_mensagens'] == '1'){
				$quantidade_tipos_dados++;
			}

			$tipo_funcional = '';
			if($componente_row['id_tipo_dado'] == 1){
				$tipo_funcional = 'e';
			} elseif($componente_row['id_tipo_dado'] == 2){
				$tipo_funcional = 's';
			} elseif($componente_row['id_tipo_dado'] == 3){
				$tipo_funcional = 'c';
			}			

			$complexidade = cpf::calcularComplexidade($tipo_funcional, $quantidade_tipos_dados, $quantidade_arquivos_referenciados);
			$valor = cpf::calcularValor($tipo_funcional, $complexidade);
			
			$valor_total_pf += $valor;
		}
		
		return $valor_total_pf;
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$modulo = new modulo();
		
		$post_modulos = $post['modulos'];
		
		// Inserindo registro na tabela "sistemas"
		$id_sistema = $this->getNextid();
		$post['id'] = $id_sistema;
		$retorno = parent::set($post, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros na tabela "modulos"
		foreach($post_modulos as $modulo_row){
			$nome_modulo = $modulo_row['nome'];
			if(!empty($nome_modulo)){
				$post_inserir = array(
					'nome' => $nome_modulo,
					'sistema' => $id_sistema
				);
				$retorno = $modulo->set($post_inserir, false);
				if($retorno !== true){
					return $retorno;
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
		$modulo = new modulo();
		
		$post_modulos = $post['modulos'];
		
		// Editando registro na tabela "sistemas"
		$retorno = parent::update($post, $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Inserindo registros na tabela "modulos"
		foreach($post_modulos as $modulo_row){
			$nome_modulo = (isset($modulo_row['nome'])) ? ($modulo_row['nome']) : ('');
			$id_modulo = $modulo_row['id'];
			$acao = $modulo_row['acao'];
			
			if($acao == 'cadastrar'){
				if(!empty($nome_modulo)){
					$post_inserir = array(
						'nome' => $nome_modulo,
						'sistema' => $id
					);
					$retorno = $modulo->set($post_inserir, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			} elseif($acao == 'editar'){
				if(!empty($nome_modulo)){
					$post_editar = array(
						'nome' => $nome_modulo
					);
					$retorno = $modulo->update($post_editar, $id_modulo, false);
					if($retorno !== true){
						return $retorno;
					}
				}
			} elseif($acao == 'excluir'){
				$retorno = $modulo->delete($id_modulo, false);
				if($retorno !== true){
					return $retorno;
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
	
	public function delete($id, $commit=true){
		$modulo = new modulo();
		
		// Excluindo registros na tabela "modulos"
		$retorno = $modulo->deleteByCampo('sistema', $id, false);
		if($retorno !== true){
			return $retorno;
		}
		
		// Excluindo registro na tabela "sistemas"
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