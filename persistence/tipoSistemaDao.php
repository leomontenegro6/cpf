<?php
class tipoSistemaDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true
			),
			'descricao'=>array(
				'tipo'=>'string'
			),
			'expoente_minimo'=>array(
				'tipo'=>'decimal',
				'required'=>true
			),
			'expoente_maximo'=>array(
				'tipo'=>'decimal',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'tipos_sistemas');
	}
}