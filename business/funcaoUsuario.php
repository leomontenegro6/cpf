<?php
class funcaoUsuario extends abstractBusiness{
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
		
		$funcaoUsuario_rs = $this->getFieldsByParameter("descricao, valor_hora_trabalhada, id", "WHERE $sql_where ORDER BY $ordenacao $filtragem LIMIT $limit OFFSET $offset");
		foreach($funcaoUsuario_rs as $i=>$funcaoUsuario_row){
			$funcaoUsuario_rs[$i]['descricao'] = funcoes::capitaliza( $funcaoUsuario_row['descricao'] );
			$funcaoUsuario_rs[$i]['valor_hora_trabalhada'] = 'R$ ' . funcoes::encodeMonetario($funcaoUsuario_row['valor_hora_trabalhada']);
		}
		return $funcaoUsuario_rs;
	}
}