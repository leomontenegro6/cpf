<?php
class feriadoDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(			
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'data'=>array(
				'tipo' => 'date',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'feriados_customizados');
	}
}
