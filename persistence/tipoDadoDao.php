<?php
class tipoDadoDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(			
			'descricao'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			)
		);
		
		parent::__construct('cpf', 'tipos_dados');
	}
}