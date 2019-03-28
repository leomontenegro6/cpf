<?php
class sistemaDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(
			'id'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'sigla'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			)
		);
		
		parent::__construct('cpf', 'sistemas');
	}
}