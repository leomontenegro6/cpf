<?php
class tipoComponenteDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(			
			'descricao'=>array(
				'tipo'=>'string',
				'required'=>true
			),
			'tipo_dado'=>array(
				'tipo'=>'int',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'tipos_componentes');
	}
}