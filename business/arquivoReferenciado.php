<?php
class arquivoReferenciado extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de obtenção de dados
	public function getByComponente($id_componente){
		return $this->getByParameter("WHERE componente = $id_componente ORDER BY id");
	}
	
	public function getByFuncionalidade($id_funcionalidade){
		return $this->getFieldsByParameter("ar.*, co.id AS id_componente", "ar
				JOIN componentes co ON (ar.componente = co.id)
			WHERE co.funcionalidade = $id_funcionalidade
			ORDER BY ar.id");
	}
	
	// Métodos de escrita de dados
	public function deleteByFuncionalidade($id_funcionalidade, $commit=true){
		$arquivoReferenciado_rs = $this->getByFuncionalidade($id_funcionalidade);
		
		foreach($arquivoReferenciado_rs as $arquivoReferenciado_row){
			$id_componente = $arquivoReferenciado_row['id_componente'];
			
			// Excluindo registros na tabela "arquivos_referenciados"
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