<?php
class depuracao{	
	
	public static function salvarConsultaSessao($sql){
		$ambiente = funcoes::getAmbienteDesenvolvimento();
		if(isset($_SESSION) && ($ambiente == 'D' || $ambiente == 'H')){
			if(!isset($_SESSION['consultas_sessao'])){
				$_SESSION['consultas_sessao'] = array();
			}
			array_push($_SESSION['consultas_sessao'], $sql);
		} else {
			return false;
		}
	}
	
	public static function mostrarConsultasSessao(){
		$ambiente = funcoes::getAmbienteDesenvolvimento();
		if(isset($_SESSION) && ($ambiente == 'D' || $ambiente == 'H')){
			if(isset($_SESSION['consultas_sessao']) && is_array($_SESSION['consultas_sessao'])){
				$total_consultas = count($_SESSION['consultas_sessao']);
				foreach($_SESSION['consultas_sessao'] as $consulta_sessao){
					echo $consulta_sessao;
					if($total_consultas > 1){
						echo '<br />';
					}
				}
			}
		}
	}
	
	public static function limparConsultasSessao(){
		$ambiente = funcoes::getAmbienteDesenvolvimento();
		if(isset($_SESSION) && ($ambiente == 'D' || $ambiente == 'H')){
			if(isset($_SESSION['consultas_sessao']) && is_array($_SESSION['consultas_sessao'])){
				$_SESSION['consultas_sessao'] = array();
			}
		}
	}
	
}