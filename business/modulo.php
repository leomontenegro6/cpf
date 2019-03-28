<?php
class modulo extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getBySistema($id_sistema){
		return $this->getFieldsByParameter("m.nome, s.nome AS sistema, m.id", "m
				JOIN sistemas s ON (m.sistema = s.id)
			WHERE m.sistema = $id_sistema
			ORDER BY m.nome");
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

		return $this->getFieldsByParameter('m.nome, s.nome AS sistema,
			m.sistema AS id_sistema, m.id', "m
				JOIN sistemas s ON (m.sistema = s.id)
			$sql_where
			ORDER BY s.nome, m.nome
			LIMIT $limit OFFSET $offset");
	}
}