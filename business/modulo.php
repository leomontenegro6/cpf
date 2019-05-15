<?php
class modulo extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getBySistema($id_sistema, $ordenacao='m.nome'){
		return $this->getFieldsByParameter("m.nome, s.nome AS sistema, m.id", "m
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE m.sistema = $id_sistema
			ORDER BY $ordenacao");
	}
	
	public function getIdsBySistema($id_sistema){
		$ids_modulos = array();
		
		$modulo_rs = $this->getBySistema($id_sistema);
		
		foreach($modulo_rs as $modulo_row){
			array_push($ids_modulos, $modulo_row['id']);
		}
		
		return $ids_modulos;
	}
	
	private function formataSQLByListagem($busca){
		$sql_where = 'TRUE';
		
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (s.nome LIKE '%$busca%' OR m.nome ILIKE '%$busca%')";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca){
		$sql_where = $this->formataSQLByListagem($busca);
		
		return $this->getTotal("m JOIN sistemas s ON (m.sistema = s.id) WHERE $sql_where");
	}
	
	public function getByListagem($busca, $ordenacao='m.nome', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca);
		
		return $this->getFieldsByParameter("m.nome, s.nome AS sistema, m.id", "m
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE $sql_where
			ORDER BY $ordenacao $filtragem
			LIMIT $limit OFFSET $offset");
	}
	
	private function formataSQLAutocomplete($busca, $id_sistema) {
		$sql_where = 'WHERE TRUE';

		if(!empty($busca)){
			$nome = str_replace(' ', '%', $busca);
            $sql_where .= " AND m.nome LIKE '%$nome%'";
		}
		if(is_numeric($id_sistema)){
			$sql_where .= " AND m.sistema = $id_sistema";
		}

		return $sql_where;
	}

	public function getTotalByAutocomplete($busca, $id_sistema) {
		$sql_where = $this->formataSQLAutocomplete($busca, $id_sistema);

		$retorno = $this->getFieldsByParameter("COUNT(m.id) AS total_consulta", "m $sql_where LIMIT 1");
		if (count($retorno) > 0) {
			return $retorno[0]['total_consulta'];
		} else {
			return 0;
		}
	}

	public function getByAutocomplete($busca, $id_sistema, $limit = 30, $offset = 0) {
		$sql_where = $this->formataSQLAutocomplete($busca, $id_sistema);

		return $this->getFieldsByParameter("m.nome, s.nome AS sistema,
			s.sigla AS sigla_sistema, m.sistema AS id_sistema, m.id", "m
				JOIN sistemas s ON (m.sistema = s.id)
			$sql_where
			ORDER BY s.nome, m.nome
			LIMIT $limit OFFSET $offset");
	}
	
	// Métodos de validações e cálculos
	public function calcularValorPF($id){
		$componenteModulo_rs = $this->getFieldsByParameter("co.id AS id_componente, tco.tipo_dado AS id_tipo_dado, co.possui_acoes, co.possui_mensagens,
			COUNT(DISTINCT c.id) AS quantidade_campos, COUNT(DISTINCT ar.id) AS quantidade_arquivos_referenciados", "m
				JOIN funcionalidades f ON (f.modulo = m.id)
				LEFT JOIN componentes co ON (co.funcionalidade = f.id)
				LEFT JOIN tipos_componentes tco ON (co.tipo_componente = tco.id)
				LEFT JOIN campos c ON (c.componente = co.id)
				LEFT JOIN arquivos_referenciados ar ON (ar.componente = co.id)
			WHERE m.id = $id
			GROUP BY co.id, tco.tipo_dado, co.possui_acoes, co.possui_mensagens");
		
		$valor_total_pf = 0;
		foreach($componenteModulo_rs as $componente_row){
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
}