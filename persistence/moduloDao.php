<?php
class moduloDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'sistema'=>array(
				'tipo'=>'int',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'modulos');
	}
}