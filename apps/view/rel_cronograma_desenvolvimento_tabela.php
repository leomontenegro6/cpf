<?php
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();
$tipoSistema = new tipoSistema();
$feriado = new feriado();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ('');
$metodo_estimativa_prazo_lista = (isset($_GET['metodo_estimativa_prazo_lista'])) ? ($_GET['metodo_estimativa_prazo_lista']) : ('e');
$recursos_lista = (isset($_GET['recursos_lista'])) ? ($_GET['recursos_lista']) : ('1');
$tempo_dedicacao_lista = (isset($_GET['tempo_dedicacao_lista'])) ? ($_GET['tempo_dedicacao_lista']) : ('4');
$indice_produtividade_lista = (isset($_GET['indice_produtividade_lista'])) ? ($_GET['indice_produtividade_lista']) : (0.5);
$tipo_sistema_lista = (isset($_GET['tipo_sistema_lista'])) ? ($_GET['tipo_sistema_lista']) : ('');
$expoente_capers_jones_lista = (isset($_GET['expoente_capers_jones_lista'])) ? ($_GET['expoente_capers_jones_lista']) : ('');
$data_inicio_atividades_lista = (isset($_GET['data_inicio_atividades_lista'])) ? ($_GET['data_inicio_atividades_lista']) : (date('d/m/Y'));

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
$formato_tempo = (isset($_GET['formato_tempo'])) ? ($_GET['formato_tempo']) : ('dnr');
$percentual_reducao = (isset($_GET['percentual_reducao'])) ? ($_GET['percentual_reducao']) : ('100');
$mostrarOrdem = (isset($_GET['mostrar_ordem']) && ($_GET['mostrar_ordem'] == 'true'));
$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
$mostrarTempo = (isset($_GET['mostrar_tempo']) && ($_GET['mostrar_tempo'] == 'true'));
$arredondarZeros = (isset($_GET['arredondar_zeros']) && ($_GET['arredondar_zeros'] == 'true'));

if(is_numeric($sistema_lista)){
	$sistema_row = $sistema->get($sistema_lista);
	$nome_sistema = $sistema_row['nome'];
	$sigla_sistema = $sistema_row['sigla'];
	$descricao_sistema = $sigla_sistema . ' - ' . $nome_sistema;
	$moduloSistema_rs = $modulo->getBySistema($sistema_lista);
} else {
	$sigla_sistema = $descricao_sistema = '';
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

$componente_rs = $componente->getByPlanilhaCronogramaDesenvolvimento($sistema_lista, $modulo_lista, $funcionalidade_lista, $metodo_estimativa_prazo_lista, $recursos_lista, $tempo_dedicacao_lista, $indice_produtividade_lista, $expoente_capers_jones_lista, $percentual_reducao, $formato_tempo, $arredondarZeros, $ordenacao);
?>
<div class="card-header">
	<h3 class="card-title" style="font-weight: bold">
		<?php
		$titulo = $descricao_sistema;
		if($checkModuloUnico){
			if(empty($nome_modulo)) $nome_modulo = $moduloSistema_rs['0']['nome'];
			$titulo .= ' - Módulo ' . $nome_modulo;
		}
		if($checkFuncionalidadeUnica){
			if(empty($nome_funcionalidade)) $nome_funcionalidade = $funcionalidadeModulo_rs['0']['nome'];
			$titulo .= ' - ' . $nome_funcionalidade;
		}
		echo $titulo;
		?>
		<br />
		Cronograma de Desenvolvimento de Funcionalidades<br />
		Data de Início das Atividades: <?php echo $data_inicio_atividades_lista ?>
	</h3>
	<div class="card-tools">
		<button type="button" class="btn btn-success float-right" onclick="phpspreadsheet.gerar(this)" title='Gerar Planilha'
			data-titulo="<?php echo $titulo ?>" data-subtitulo="Cronograma de Desenvolvimento de Funcionalidades" data-tabela="tabela_cronograma_desenvolvimento"
			data-nome-arquivo="Cronograma de Desenvolvimento de Funcionalidades - <?php echo $sigla_sistema ?>">
			<i class="fas fa-file-excel"></i>
			<span class='d-none d-sm-inline'>Gerar Planilha</span>
		</button>
	</div>
</div>
<div class="card-body">
	<div class="table-responsive">
		<table id="tabela_cronograma_desenvolvimento" class="table table-bordered table-sm">
			<thead>
				<tr>
					<?php if(!$checkModuloUnico){ ?>
						<th class="align-middle" rowspan="2">Módulo</th>
					<?php } ?>
					<?php if(!$checkFuncionalidadeUnica){ ?>
						<th class="align-middle" rowspan="2">Funcionalidade</th>
					<?php } ?>
					<th class="align-middle" rowspan="2">Componente</th>
					<?php if($mostrarComplexidade){ ?>
						<th class="align-middle" rowspan="2">Complexidade</th>
					<?php } ?>
					<?php if($mostrarValorPF){ ?>
						<th class="align-middle" rowspan="2">Valor (PF)</th>
					<?php } ?>
					<?php if($mostrarTempo){ ?>
						<th class="align-middle" rowspan="2">
							Tempos (<?php echo funcoes::formatarTituloTempoByFormato($formato_tempo) ?>)
						</th>
					<?php } ?>
					<th class="align-middle text-center" colspan="2">Períodos de Atividades</th>
					<th class="align-middle" rowspan="2">Observações</th>
				</tr>
				<tr>
					<th class="align-middle">Início</th>
					<th class="align-middle">Fim</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$totais_gerais = array(
					'valor_pf' => 0,
					'tempo' => 0
				);
				$linhas_esconder = array(
					'modulo' => 0,
					'funcionalidade' => 0
				);
				$colspan_totais = 4;
				if($checkModuloUnico) $colspan_totais--;
				if($checkFuncionalidadeUnica) $colspan_totais--;
				if(!$mostrarComplexidade) $colspan_totais--;
				
				$data_inicio_atividades_lista .= ' 00:00:00';
				$data_inicio_atividades_lista = funcoes::decodeDataHora($data_inicio_atividades_lista);
				$objeto_data_inicio_atividades = new DateTime($data_inicio_atividades_lista);
				$ano_inicial = $objeto_data_inicio_atividades->format('Y');
				$intervalo_anos_feriados = range($ano_inicial, ($ano_inicial + 2));
				$dias_nao_uteis = $feriado->getDiasNaoUteisByAnos($intervalo_anos_feriados);
				
				foreach($componente_rs as $componente_row){
					$rowspan = $componente_row['rowspan'];
					
					if($formato_tempo == 'dnr'){
						// Dias - reais
						$tempo = round($componente_row['tempo'], 2);
					} else {
						// Dias - inteiro
						$tempo = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo, $arredondarZeros);	
					}

					$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
					$totais_gerais['tempo'] += $tempo;

					if($formato_tempo == 'dnr'){
						$tempo = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
					}
					
					$periodo_inicial = $objeto_data_inicio_atividades->format('d/m/Y');
					if($formato_tempo == 'dnr'){
						$tempo_horas_minutos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato(($componente_row['tempo'] * 24), 'hhm');
					} else {
						$tempo = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo, $arredondarZeros);
						$tempo_horas_minutos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato(($tempo * 24), 'hhm');
					}
					$tempo_horas_minutos = explode(':', $tempo_horas_minutos);
					$tempo_horas = (int)$tempo_horas_minutos[0];
					$tempo_minutos = (int)$tempo_horas_minutos[1];
					
					if($tempo_horas > 0){
						$objeto_data_inicio_atividades->modify("+{$tempo_horas} hours");
					}
					if($tempo_minutos > 0){
						$objeto_data_inicio_atividades->modify("+{$tempo_minutos} minutes");
					}
					
					$observacoes = array();
					while(array_key_exists($objeto_data_inicio_atividades->format('Y-m-d'), $dias_nao_uteis)){
						$nome_dia_nao_util = $dias_nao_uteis[$objeto_data_inicio_atividades->format('Y-m-d')];
						$objeto_data_inicio_atividades->modify('+1 day');
						
						if(!in_array($nome_dia_nao_util, $observacoes)){
							$observacoes[] = $nome_dia_nao_util;
						}
					}
					
					$observacoes = implode(', ', $observacoes);
					
					$periodo_final = $objeto_data_inicio_atividades->format('d/m/Y');
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
						<td><?php echo $periodo_inicial ?></td>
						<td><?php echo $periodo_final ?></td>
						<td><?php echo $observacoes ?></td>
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
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>