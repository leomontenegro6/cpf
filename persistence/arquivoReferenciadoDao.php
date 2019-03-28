<?php
class arquivoReferenciadoDao extends abstractDao{
	
	public function __construct(){
		$this->campos = array(
			'nome'=>array(
				'tipo'=>'string',
				'required'=>true,
				'normalize'=>false
			),
			'componente'=>array(
				'tipo'=>'int',
				'required'=>true
			)
		);
		
		parent::__construct('cpf', 'arquivos_referenciados');
	}
}