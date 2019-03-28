<?php
class campo extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de obtenção de dados
	public function getByComponente($id_componente){
		return $this->getByParameter("WHERE componente = $id_componente ORDER BY id");
	}
	
	public function getByFuncionalidade($id_funcionalidade){
		return $this->getFieldsByParameter("c.*, co.id AS id_componente", "c
				JOIN componentes co ON (c.componente = co.id)
			WHERE co.funcionalidade = $id_funcionalidade
			ORDER BY c.id");
	}
	
	// Métodos de escrita de dados
	public function deleteByFuncionalidade($id_funcionalidade, $commit=true){
		$campo_rs = $this->getByFuncionalidade($id_funcionalidade);
		
		foreach($campo_rs as $campo_row){
			$id_componente = $campo_row['id_componente'];
			
			// Excluindo registros na tabela "campos"
			$retorno = $this->deleteByCampo('componente', $id_componente, false);
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
}