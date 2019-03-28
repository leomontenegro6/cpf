<?php
class tipoComponente extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	public function getForSelect(){
		$tipoComponenteTipoDado_rs = array();
		
		$tipoComponente_rs = $this->getFieldsByParameter("tc.descricao, td.descricao AS tipo_dado,
			td.id AS id_tipo_dado, tc.id", "tc
				JOIN tipos_dados td ON (tc.tipo_dado = td.id)
			ORDER BY td.id, tc.descricao");
		foreach($tipoComponente_rs as $tipoComponente_row){
			$tipo_dado = $tipoComponente_row['tipo_dado'];
			$id_tipo_dado = $tipoComponente_row['id_tipo_dado'];
			
			if($id_tipo_dado == 1){
				$tipoComponente_row['alias'] = 'e';
			} elseif($id_tipo_dado == 2){
				$tipoComponente_row['alias'] = 's';
			} else {
				$tipoComponente_row['alias'] = 'c';
			}
			
			unset($tipoComponente_row['tipo_dado']);
			
			$tipoComponenteTipoDado_rs[$tipo_dado][] = $tipoComponente_row;
		}
		
		return $tipoComponenteTipoDado_rs;
	}
}