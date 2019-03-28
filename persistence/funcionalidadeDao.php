<?php
class funcionalidadeDao extends abstractDao{
	
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
			'tipo_funcionalidade'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'modulo'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'ordem'=>array(
				'tipo'=>'int'
			),
		);
		
		parent::__construct('cpf', 'funcionalidades');
	}
}