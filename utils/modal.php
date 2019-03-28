<?php
class modal{	
	public static function retornar($texto, $retorno, $tipo='', $ajax=false){
		if($ajax === true){
			if($tipo != 'aviso' && $tipo != 'erro' && (preg_match("/^Erro/", $texto) || preg_match("/^Já existe um/", $texto) || preg_match("/está sendo usad/", $texto))){
				$texto = str_replace("Erro ao inserir! \\n", "", $texto);
				$texto = str_replace("Erro ao atualizar! \\n", "", $texto);
				$tipo = 'erro';
			}
			$array = array(
				"tipo_modal"=>$tipo,
				"msg_modal"=>$texto,
				"pagina"=>$retorno
			);
			echo json_encode($array);
		} else {
			if(!in_array($tipo, array('aviso', 'erro'))){
				if(preg_match("/^Erro/", $texto) || preg_match("/^Já existe um/", $texto) || preg_match("/está sendo usad/", $texto)){
					$texto = str_replace("Erro ao inserir! \\n", "", $texto);
					$texto = str_replace("Erro ao atualizar! \\n", "", $texto);
				}
			}
			
			$_SESSION['crud'] = array(
				'texto' => $texto,
				'tipo' => $tipo
			);
			header("Location: $retorno");
		}
	}
    
    public static function notificar($texto,$tipo=""){
		switch($tipo){
			case "aviso":
				echo "<script language='javascript'>$(function(){ jAlert('$texto', 'Aviso'); } ) </script>";
				break;
			case "erro":
				echo "<script language='javascript'>$(function(){ jError('$texto', 'Erro'); } )</script>";
				break;
			default:
				echo "<script language='javascript'>$(function(){ jInfo('$texto', 'Informação'); } )</script>";
				break;
			}
	}

	public static function confirmar($id,$retorno){
		echo "<script language=\"javascript\">$(function(){ confirma('$retorno','id=".$id."') })</script>";
		exit;
	}
	
	public static function sessaoExpirada(){
		echo "<script language=\"javascript\">$(function(){ modal.sessaoExpirada() })</script>";
		exit;
	}
}