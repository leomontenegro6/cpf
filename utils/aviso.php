<?php
class aviso{	
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
				'tipo' => self::converteTipoParaSufixoNomeClasse($tipo)
			);
			if(empty($retorno)) $retorno = 'index.php';
			header("Location: $retorno");
		}
	}
	
	private static function converteTipoParaSufixoNomeClasse($tipo){
		if($tipo == 'aviso'){
			$nome_classe = 'warning';
		} elseif($tipo == 'erro'){
			$nome_classe = 'danger';
		} elseif($tipo == 'sucesso'){
			$nome_classe = 'success';
		} else {
			$nome_classe = 'info';
		}
		
		return $nome_classe;
	}
	
	public static function exibir($texto, $tipo, $titulo=''){
		?>
		<script type="text/javascript">
			$(function(){
				exibirAvisoNotify('<?php echo $texto ?>', '<?php echo $tipo ?>');
			})
		</script>
		<?php
	}
}