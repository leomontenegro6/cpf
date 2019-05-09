<?php
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();
$usuario = new usuario();
$tipoSistema = new tipoSistema;

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ('');
$metodo_estimativa_prazo_lista = (isset($_GET['metodo_estimativa_prazo_lista'])) ? ($_GET['metodo_estimativa_prazo_lista']) : ('e');
$recursos_lista = (isset($_GET['recursos_lista'])) ? ($_GET['recursos_lista']) : ('1');
$tempo_dedicacao_lista = (isset($_GET['tempo_dedicacao_lista'])) ? ($_GET['tempo_dedicacao_lista']) : ('4');
$indice_produtividade_lista = (isset($_GET['indice_produtividade_lista'])) ? ($_GET['indice_produtividade_lista']) : (0.5);
$tipo_sistema_lista = (isset($_GET['tipo_sistema_lista'])) ? ($_GET['tipo_sistema_lista']) : ('');
$expoente_capers_jones_lista = (isset($_GET['expoente_capers_jones_lista'])) ? ($_GET['expoente_capers_jones_lista']) : ('');
$metodo_calculo_orcamento_lista = (isset($_GET['metodo_calculo_orcamento_lista'])) ? ($_GET['metodo_calculo_orcamento_lista']) : ('vht');
$valor_hora_trabalhada_lista = (isset($_GET['valor_hora_trabalhada_lista'])) ? ($_GET['valor_hora_trabalhada_lista']) : ($usuario->getValorHoraTrabalhada($_SESSION['iduser']));
$valor_ponto_funcao_lista = (isset($_GET['valor_ponto_funcao_lista'])) ? ($_GET['valor_ponto_funcao_lista']) : (1061.00);

if(isset($_GET['ordenacao'])){
	$ordenacao = array();
	foreach($_GET['ordenacao'] as $o){
		array_push($ordenacao, array('ordenacao' => $o));
	}
} else {
	$ordenacao = array(
		array('ordenacao' => 'm.id'),
		array('ordenacao' => 'f.ordem'),
		array('ordenacao' => 'co.ordem'),
	);
}
$formato_tempo = (isset($_GET['formato_tempo'])) ? ($_GET['formato_tempo']) : ('hhm');
$percentual_reducao = (isset($_GET['percentual_reducao'])) ? ($_GET['percentual_reducao']) : ('100');
$mostrarOrdem = (isset($_GET['mostrar_ordem']) && ($_GET['mostrar_ordem'] == 'true'));
$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
$mostrarTempo = (isset($_GET['mostrar_tempo']) && ($_GET['mostrar_tempo'] == 'true'));
$arredondarZeros = (isset($_GET['arredondar_zeros']) && ($_GET['arredondar_zeros'] == 'true'));

if(is_numeric($sistema_lista)){
	$nome_sistema = $sistema->getNome($sistema_lista, 'n');
	$moduloSistema_rs = $modulo->getBySistema($sistema_lista);
} else {
	$nome_sistema = '';
	$moduloSistema_rs = array();
}
if(is_numeric($modulo_lista)){
	$nome_modulo = $modulo->getNome($modulo_lista, 'n');
	$funcionalidadeModulo_rs = $funcionalidade->getByModulo($modulo_lista);
	$checkModuloUnico = true;
} else {
	$nome_modulo = '';
	$funcionalidadeModulo_rs = array();
	if(is_numeric($sistema_lista)){
		$checkModuloUnico = (count( $modulo->getBySistema($sistema_lista) ) == 1);
	} else {
		$checkModuloUnico = false;
	}
}
if(is_numeric($funcionalidade_lista)){
	$nome_funcionalidade = $funcionalidade->getNome($funcionalidade_lista, 'n');
	$checkFuncionalidadeUnica = true;
} else {
	$nome_funcionalidade = '';
	if(is_numeric($modulo_lista)){
		$checkFuncionalidadeUnica = (count( $funcionalidade->getByModulo($modulo_lista) ) == 1);
	} else {
		$checkFuncionalidadeUnica = false;
	}
}
if(is_numeric($tipo_sistema_lista)){
	$nome_tipo_sistema = $tipoSistema->getNome($tipo_sistema_lista, 'n');
} else {
	$nome_tipo_sistema = '';
}

$componente_rs = $componente->getByPlanilhaOrcamentoDesenvolvimento($sistema_lista, $modulo_lista, $funcionalidade_lista, $metodo_estimativa_prazo_lista, $recursos_lista, $tempo_dedicacao_lista, $indice_produtividade_lista, $expoente_capers_jones_lista, $metodo_calculo_orcamento_lista, $valor_hora_trabalhada_lista, $valor_ponto_funcao_lista, $percentual_reducao, $formato_tempo, $arredondarZeros, $ordenacao);
?>
<div class="card-header">
	<h3 class="card-title" style="font-weight: bold">
		<?php
		echo $nome_sistema;
		if($checkModuloUnico){
			if(empty($nome_modulo)) $nome_modulo = $moduloSistema_rs['0']['nome'];
			echo ' - Módulo ' . $nome_modulo;
		}
		if($checkFuncionalidadeUnica){
			if(empty($nome_funcionalidade)) $nome_funcionalidade = $funcionalidadeModulo_rs['0']['nome'];
			echo ' - ' . $nome_funcionalidade;
		} 
		?>
		<br />
		Orçamento de Desenvolvimento de Funcionalidades
	</h3>
	<div class="card-tools">
		<?php
		$parametros = "sistema=$sistema_lista&modulo=$modulo_lista&funcionalidade=$funcionalidade_lista";
		$parametros .= "&metodo_estimativa_prazo=$metodo_estimativa_prazo_lista&recursos=$recursos_lista";
		$parametros .= "&tempo_dedicacao=$tempo_dedicacao_lista&indice_produtividade=$indice_produtividade_lista";
		$parametros .= "&tipo_sistema_lista=$tipo_sistema_lista&expoente_capers_jones_lista=$expoente_capers_jones_lista";
		$parametros .= "&metodo_calculo_orcamento_lista=$metodo_calculo_orcamento_lista&valor_hora_trabalhada_lista=$valor_hora_trabalhada_lista&valor_ponto_funcao_lista=$valor_ponto_funcao_lista";
		if($mostrarOrdem) $parametros .= "&mostrar_ordem=true";
		if($mostrarComplexidade) $parametros .= "&mostrar_complexidade=true";
		if($mostrarValorPF) $parametros .= "&mostrar_valor_pf=true";
		if($mostrarTempo) $parametros .= "&mostrar_tempo=true";
		if($arredondarZeros) $parametros .= "&arredondar_zeros=true";
		$parametros .= "&formato_tempo=$formato_tempo";
		?>
		<button type="button" class="btn btn-success float-right"
			onclick="abrirPagina('rel_orcamento_desenvolvimento_xls.php?<?php echo $parametros ?>', '', '_blank');">
			<i class="fas fa-file-excel"></i> Gerar Planilha
		</button>
	</div>
</div>
<div class="card-body">
	<div class="table-responsive">
		<table class="table table-bordered table-sm">
			<thead>
				<tr>
					<?php if(!$checkModuloUnico){ ?>
						<th class="align-middle" style="background-color: #fafafa">Módulo</th>
					<?php } ?>
					<?php if(!$checkFuncionalidadeUnica){ ?>
						<th class="align-middle" style="background-color: #fafafa">Funcionalidade</th>
					<?php } ?>
					<th class="align-middle" style="background-color: #fafafa">Componente</th>
					<?php if($mostrarComplexidade){ ?>
						<th class="align-middle" style="background-color: #fafafa">Complexidade</th>
					<?php } ?>
					<?php if($mostrarValorPF){ ?>
						<th class="align-middle" style="background-color: #fafafa">Valor (PF)</th>
					<?php } ?>
					<?php if($mostrarTempo){ ?>
						<th class="align-middle" style="background-color: #fafafa">
							Tempo (<?php echo funcoes::formatarTituloTempoByFormato($formato_tempo) ?>)
						</th>
					<?php } ?>
					<th class="align-middle" style="background-color: #fafafa">Custo (R$)</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$totais_gerais = array(
					'valor_pf' => 0,
					'tempo' => 0,
					'custo' => 0
				);
				$linhas_esconder = array(
					'modulo' => 0,
					'funcionalidade' => 0
				);
				$colspan_totais = 4;
				if($checkModuloUnico) $colspan_totais--;
				if($checkFuncionalidadeUnica) $colspan_totais--;
				if(!$mostrarComplexidade) $colspan_totais--;
				
				foreach($componente_rs as $componente_row){
					$rowspan = $componente_row['rowspan'];
					
					if(in_array($formato_tempo, array('hni', 'dni'))){
						$tempo = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo, $arredondarZeros);
					} elseif(in_array($formato_tempo, array('hnr', 'dnr', 'mnr'))){
						$tempo = round($componente_row['tempo'], 2);
					} else {
						$tempo = $componente_row['tempo'];
					}

					$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
					$totais_gerais['tempo'] += $tempo;
					$totais_gerais['custo'] += $componente_row['custo'];

					if(in_array($formato_tempo, array('hhm', 'hnr', 'dnr', 'mnr'))){
						$tempo = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
					}
					?>
					<tr>
						<?php if(!$checkModuloUnico){ ?>
							<?php
							if($linhas_esconder['modulo'] > 0){
								$linhas_esconder['modulo']--;
							} elseif($rowspan > 0){
								if($rowspan > 1){
									$linhas_esconder['modulo'] = ($rowspan - 1);
								}
								?>
								<td rowspan="<?php echo $componente_row['rowspan'] ?>"><?php echo $componente_row['modulo'] ?></td>
							<?php } ?>
						<?php } ?>
						<?php if(!$checkFuncionalidadeUnica){ ?>
							<?php
							if($linhas_esconder['funcionalidade'] > 0){
								$linhas_esconder['funcionalidade']--;
							} elseif($rowspan > 0){
								if($rowspan > 1){
									$linhas_esconder['funcionalidade'] = ($rowspan - 1);
								}
								?>
								<td rowspan="<?php echo $componente_row['rowspan'] ?>">
									<?php
									if($mostrarOrdem) echo $componente_row['ordem_funcionalidade'] . '. ';
									echo $componente_row['funcionalidade'];
									?>
								</td>
							<?php } ?>
						<?php } ?>
						<td>
							<?php
							if($mostrarOrdem) echo $componente_row['ordem_componente'] . '. ';
							echo $componente_row['componente'];
							?>
						</td>
						<?php if($mostrarComplexidade){ ?>
							<td><?php echo $componente_row['complexidade'] ?></td>
						<?php } ?>
						<?php if($mostrarValorPF){ ?>
							<td><?php echo $componente_row['valor_pf'] ?></td>
						<?php } ?>
						<?php if($mostrarTempo){ ?>
							<td><?php echo $tempo ?></td>
						<?php } ?>
						<th><?php echo funcoes::encodeMonetario($componente_row['custo'], 1) ?></th>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="<?php echo $colspan_totais ?>" class="text-right">TOTAIS:</th>
					<?php if($mostrarValorPF){ ?>
						<th><?php echo $totais_gerais['valor_pf'] ?></th>
					<?php } ?>
					<?php if($mostrarTempo){ ?>
						<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo'], $formato_tempo, $arredondarZeros) ?></th>
					<?php } ?>
					<th><?php echo 'R$ ' . funcoes::encodeMonetario($totais_gerais['custo'], 1) ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>