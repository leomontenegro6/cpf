<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');

if(is_numeric($id_sistema)){
	$modulo_rs = $modulo->getBySistema($id_sistema);
} else {
	$modulo_rs = array();
}

foreach($modulo_rs as $modulo_row){
	$nome_modulo = $modulo_row['nome'];
	$id_modulo = $modulo_row['id'];
	?>
	<label class="checkbox-menu modulo" tabindex="0" onkeydown="marcarCheckboxMenuNoEnterOuEspaco(this, event)">
		<div class="custom-control custom-checkbox">
			<input type="checkbox" id="modulo_<?php echo $id_modulo ?>" class="custom-control-input" tabindex="-1"
				onchange="orcamentoManutencao.toggleFuncionalidadesFilhasModulo(this)" />
			<label for="modulo_<?php echo $id_modulo ?>" class="custom-control-label">&nbsp;</label>
			<span class="checkbox-label-block">
				Módulo "<?php echo $nome_modulo ?>"
			</span>
		</div>
	</label>
	<?php
	$funcionalidade_rs = $funcionalidade->getByModulo($id_modulo, 'f.ordem');
	$total_funcionalidades = count($funcionalidade_rs);
	foreach($funcionalidade_rs as $i=>$funcionalidade_row){
		$id_funcionalidade = $funcionalidade_row['id'];
		$nome_funcionalidade = $funcionalidade_row['nome'];
		
		$componente_rs = $componente->getByFuncionalidade($id_funcionalidade);;
		$total_componentes = count($componente_rs);
		if($total_componentes > 1){
			$string_qtde_componentes = $total_componentes . ' Componentes';
		} else {
			$string_qtde_componentes = $total_componentes . ' Componente';
		}
		
		$valor_pf_funcionalidade = 0;
		foreach($componente_rs as $i=>$componente_row){
			$quantidade_tipos_dados = $componente_row['quantidade_campos'];
			$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];
			
			if($componente_row['possui_acoes'] == '1'){
				$quantidade_tipos_dados++;
			}
			if($componente_row['possui_mensagens'] == '1'){
				$quantidade_tipos_dados++;
			}

			$tipo_funcional = '';
			if($componente_row['id_tipo_dado'] == 1){
				$tipo_funcional = 'e';
			} elseif($componente_row['id_tipo_dado'] == 2){
				$tipo_funcional = 's';
			} elseif($componente_row['id_tipo_dado'] == 3){
				$tipo_funcional = 'c';
			}			

			$complexidade = cpf::calcularComplexidade($tipo_funcional, $quantidade_tipos_dados, $quantidade_arquivos_referenciados);
			$componente_rs[$i]['valor_pf'] = cpf::calcularValor($tipo_funcional, $complexidade);
			
			$valor_pf_funcionalidade += $componente_rs[$i]['valor_pf'];
		}
		?>
		<label class="checkbox-menu funcionalidade" tabindex="0" onkeydown="marcarCheckboxMenuNoEnterOuEspaco(this, event)">
			<div class="custom-control custom-checkbox">
				<span class="boxurvr">
					<?php if($i == ($total_funcionalidades - 1)) echo '&boxur;'; else echo '&boxvr;';  ?>
				</span>
				<input type="checkbox" id="funcionalidade_<?php echo $id_funcionalidade ?>" class="custom-control-input"
					name="funcionalidades[<?php echo $id_funcionalidade ?>][marcada]" value="<?php echo $id_funcionalidade ?>"
					required tabindex="-1" onchange="orcamentoManutencao.toggleModuloPaiFuncionalidade(this)" />
				<label for="funcionalidade_<?php echo $id_funcionalidade ?>" class="custom-control-label">&nbsp;</label>
				<span class="checkbox-label-block">
					<?php echo $nome_funcionalidade ?> - <?php echo $string_qtde_componentes ?> - <?php echo $valor_pf_funcionalidade ?> PF
					<button type="button" class="btn btn-sm btn-info ver_detalhes_funcionalidade" title="Ver detalhes da funcionalidade"
						onclick="jModalGrande('funcionalidade_detalhe.php?id=<?php echo $id_funcionalidade ?>')" tabindex="-1">
						<i class="fa fa-search-plus"></i>
					</button>
				</span>
			</div>
			
			<input type="hidden" name="funcionalidades[<?php echo $id_funcionalidade ?>][modulo]" value="<?php echo $nome_modulo ?>" />
			<input type="hidden" name="funcionalidades[<?php echo $id_funcionalidade ?>][nome]" value="<?php echo $nome_funcionalidade ?>" />
			
			<?php foreach($componente_rs as $componente_row){
				$id_componente = $componente_row['id'];
				$tipo_componente = $componente_row['tipo_componente'];
				$valor_pf = $componente_row['valor_pf'];
				?>
				<div class="d-none inputs_componentes" data-id-componente-banco="<?php echo $id_componente ?>">
					<input type="hidden" name="funcionalidades[<?php echo $id_funcionalidade ?>][componentes][<?php echo $id_componente ?>][tipo_componente]"
						value="<?php echo $tipo_componente ?>" />
					<input type="hidden" name="funcionalidades[<?php echo $id_funcionalidade ?>][componentes][<?php echo $id_componente ?>][valor_pf]"
						value="<?php echo $valor_pf ?>" />
				</div>
			<?php } ?>
		</label>
	<?php
	}
}