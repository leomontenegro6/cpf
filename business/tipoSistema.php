<?php
class tipoSistema extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	private function formataSQLAutocomplete($busca) {
		$sql_where = 'WHERE TRUE';

		if (!empty($busca)) {
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND (nome LIKE '%$busca%' OR descricao LIKE '%$busca%')";
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

		return $this->getFieldsByParameter('*', "$sql_where ORDER BY id LIMIT $limit OFFSET $offset");
	}
}