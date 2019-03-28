<?php
class tipoDado extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	private function formataSQLByListagem($busca){
		$sql_where = 'TRUE';
		
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND descricao LIKE '%$busca%'";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca){
		$sql_where = $this->formataSQLByListagem($busca);
		
		return $this->getTotal("WHERE $sql_where");
	}
	
	public function getByListagem($busca, $ordenacao='descricao', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca);
		
		$tipoDado_rs = $this->getFieldsByParameter("descricao, id", "WHERE $sql_where ORDER BY $ordenacao $filtragem LIMIT $limit OFFSET $offset");
		foreach($tipoDado_rs as $i=>$tipoDado_row){
			$tipoDado_rs[$i]['descricao'] = funcoes::capitaliza( $tipoDado_row['descricao'] );
		}
		return $tipoDado_rs;
	}
}