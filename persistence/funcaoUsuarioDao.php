<?php
class funcaoUsuarioDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(			
			'descricao'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'valor_hora_programada'=>array(
				'tipo'=>'decimal',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'funcoes_usuarios');
	}
}