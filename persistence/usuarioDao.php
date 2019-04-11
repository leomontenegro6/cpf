<?php
class usuarioDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(			
			'login'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'senha_sha1'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'foto'=>array(
				'tipo'=>'string',
				'normalize'=>false
			),
			'funcao'=>array(
				'tipo'=>'int',
				'required'=>true
			),
			'valor_hora_trabalhada'=>array(
				'tipo'=>'decimal',
				'required'=>true
			),
			'admin'=>array(
				'tipo'=>'bolean',
				'default'=>'false'
			)
		);
		
		parent::__construct('cpf', 'usuarios');
	}
}