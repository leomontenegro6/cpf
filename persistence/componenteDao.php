<?php
class componenteDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(
			'id'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'tipo_componente'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'possui_acoes'=>array(
				'tipo'=>'boolean'
			),
			'possui_mensagens'=>array(
				'tipo'=>'boolean'
			),
			'funcionalidade'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'ordem'=>array(
				'tipo'=>'int'
			)
		);
		
		parent::__construct('cpf', 'componentes');
	}
}