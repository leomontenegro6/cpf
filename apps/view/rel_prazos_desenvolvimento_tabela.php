<?php
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();
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
$mostrarOrdem = (isset($_GET['mostrar_ordem']) && ($_GET['mostrar_ordem'] == 'true'));
$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
$arredondarZeros = (isset($_GET['arredondar_zeros']) && ($_GET['arredondar_zeros'] == 'true'));
$modo_exibicao_tempo = (isset($_GET['modo_exibicao_tempo'])) ? ($_GET['modo_exibicao_tempo']) : ('u');
$formato_tempo = (isset($_GET['formato_tempo'])) ? ($_GET['formato_tempo']) : ('hhm');
$percentual_reducao_unico = (isset($_GET['percentual_reducao_unico'])) ? ($_GET['percentual_reducao_unico']) : ('45');
if(isset($_GET['esforco_disciplinas'])){
	$esforco_disciplinas = $_GET['esforco_disciplinas'];
	$esforco_disciplinas['analise']['percentual'] = round($esforco_disciplinas['analise']['percentual']);
	$esforco_disciplinas['desenvolvimento']['percentual'] = round($esforco_disciplinas['desenvolvimento']['percentual']);
	$esforco_disciplinas['testes']['percentual'] = round($esforco_disciplinas['testes']['percentual']);
	$esforco_disciplinas['implantacao']['percentual'] = round($esforco_disciplinas['implantacao']['percentual']);
} else {
	$esforco_disciplinas = array(
		'analise' => array(
			'percentual' => 25,
			'exibir' => true
		),
		'desenvolvimento' => array(
			'percentual' => 45,
			'exibir' => true
		),
		'testes' => array(
			'percentual' => 15,
			'exibir' => true
		),
		'implantacao' => array(
			'percentual' => 15,
			'exibir' => true
		)
	);
}

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

$componente_rs = $componente->getByPlanilhaPrazosDesenvolvimento($sistema_lista, $modulo_lista, $funcionalidade_lista, $metodo_estimativa_prazo_lista, $recursos_lista, $tempo_dedicacao_lista, $indice_produtividade_lista, $expoente_capers_jones_lista, $modo_exibicao_tempo, $percentual_reducao_unico, $esforco_disciplinas, $formato_tempo, $ordenacao);

if($modo_exibicao_tempo == 'u'){
	$rowspan_padrao = 1;
} else {
	$rowspan_padrao = 2;
}

$rowspan_tempo = 1;
if(isset($esforco_disciplinas['analise']['exibir'])){
	$rowspan_tempo++;
}
if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
	$rowspan_tempo++;
}
if(isset($esforco_disciplinas['testes']['exibir'])){
	$rowspan_tempo++;
}
if(isset($esforco_disciplinas['implantacao']['exibir'])){
	$rowspan_tempo++;
}
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
		Prazos de Desenvolvimento de Funcionalidades
	</h3>
	<div class="card-tools">
		<button type="button" class="btn btn-success float-right" onclick="phpspreadsheet.gerar(this)" title='Gerar Planilha'
			data-titulo="<?php echo $titulo ?>" data-subtitulo="Prazos de Desenvolvimento de Funcionalidades" data-tabela="tabela_prazos_desenvolvimento"
			data-nome-arquivo="Prazos de Desenvolvimento de Funcionalidades - <?php echo $sigla_sistema ?>">
			<i class="fas fa-file-excel"></i>
			<span class='d-none d-sm-inline'>Gerar Planilha</span>
		</button>
	</div>
</div>
<div class="card-body">
	<div class="table-responsive">
		<table id="tabela_prazos_desenvolvimento" class="table table-bordered table-sm">
			<thead>
				<tr>
					<?php if(!$checkModuloUnico){ ?>
						<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">Módulo</th>
					<?php } ?>
					<?php if(!$checkFuncionalidadeUnica){ ?>
						<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">Funcionalidade</th>
					<?php } ?>
					<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">Componente</th>
					<?php if($mostrarComplexidade){ ?>
						<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">Complexidade</th>
					<?php } ?>
					<?php if($mostrarValorPF){ ?>
						<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">Valor (PF)</th>
					<?php } ?>
					<?php if($modo_exibicao_tempo == 'u'){ ?>
						<th rowspan="<?php echo $rowspan_padrao ?>" class="align-middle">
							Tempo (<?php echo funcoes::formatarTituloTempoByFormato($formato_tempo) ?>)
						</th>
					<?php } else { ?>
						<th colspan="<?php echo $rowspan_tempo ?>" class="text-center">
							Tempo (<?php echo funcoes::formatarTituloTempoByFormato($formato_tempo) ?>)
						</th>
					<?php } ?>
				</tr>
				<?php if($modo_exibicao_tempo == 'd'){ ?>
					<tr>
						<?php if(isset($esforco_disciplinas['analise']['exibir'])){ ?>
							<th>Análise</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){ ?>
							<th>Desenvolvimento</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['testes']['exibir'])){ ?>
							<th>Testes</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['implantacao']['exibir'])){ ?>
							<th>Implantação</th>
						<?php } ?>
						<th>TOTAL</th>
					</tr>
				<?php } ?>
			</thead>
			<tbody>
				<?php
				$totais_gerais = array(
					'valor_pf' => 0,
					'tempo' => array(
						'analise' => 0,
						'desenvolvimento' => 0,
						'testes' => 0,
						'implantacao' => 0,
						'total' => 0
					)
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

					$tempo_total = 0;
					if($modo_exibicao_tempo == 'u'){
						if(in_array($formato_tempo, array('hni', 'dni'))){
							$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo, $arredondarZeros);
						} elseif(in_array($formato_tempo, array('hnr', 'dnr', 'mnr'))){
							$tempos = round($componente_row['tempo'], 2);
						} else {
							$tempos = $componente_row['tempo'];
						}

						$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
						$totais_gerais['tempo']['total'] += $tempos;

						if(in_array($formato_tempo, array('hhm', 'hnr', 'dnr', 'mnr'))){
							$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
						}
					} else {
						if(in_array($formato_tempo, array('hni', 'dni'))){
							$tempos = array(
								'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo, $arredondarZeros),
								'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo, $arredondarZeros),
								'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo, $arredondarZeros),
								'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo, $arredondarZeros)
							);
						} elseif(in_array($formato_tempo, array('hnr', 'dnr'))){
							$tempos = array(
								'analise' => round($componente_row['tempo']['analise'], 2),
								'desenvolvimento' => round($componente_row['tempo']['desenvolvimento'], 2),
								'testes' => round($componente_row['tempo']['testes'], 2),
								'implantacao' => round($componente_row['tempo']['implantacao'], 2)
							);
						} elseif($formato_tempo == 'mnr'){
							$tempos = array(
								'analise' => round($componente_row['tempo']['analise'], 4),
								'desenvolvimento' => round($componente_row['tempo']['desenvolvimento'], 4),
								'testes' => round($componente_row['tempo']['testes'], 4),
								'implantacao' => round($componente_row['tempo']['implantacao'], 4)
							);
						} else {
							$tempos = array(
								'analise' => $componente_row['tempo']['analise'],
								'desenvolvimento' => $componente_row['tempo']['desenvolvimento'],
								'testes' => $componente_row['tempo']['testes'],
								'implantacao' => $componente_row['tempo']['implantacao']
							);
						}

						$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
						if(isset($esforco_disciplinas['analise']['exibir'])){
							$totais_gerais['tempo']['analise'] += $tempos['analise'];
							$tempo_total += $tempos['analise'];
						}
						if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
							$totais_gerais['tempo']['desenvolvimento'] += $tempos['desenvolvimento'];
							$tempo_total += $tempos['desenvolvimento'];
						}
						if(isset($esforco_disciplinas['testes']['exibir'])){
							$totais_gerais['tempo']['testes'] += $tempos['testes'];
							$tempo_total += $tempos['testes'];
						}
						if(isset($esforco_disciplinas['implantacao']['exibir'])){
							$totais_gerais['tempo']['implantacao'] += $tempos['implantacao'];
							$tempo_total += $tempos['implantacao'];
						}
						$totais_gerais['tempo']['total'] += $tempo_total;

						if(in_array($formato_tempo, array('hhm', 'hnr', 'dnr', 'mnr'))){
							$tempos = array(
								'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo),
								'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo),
								'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo),
								'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo)
							);
						}
						$tempo_total = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($tempo_total, $formato_tempo, $arredondarZeros);
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
						<?php if($modo_exibicao_tempo == 'd'){ ?>
							<?php if(isset($esforco_disciplinas['analise']['exibir'])){ ?>
								<td><?php echo $tempos['analise'] ?></td>
							<?php } ?>
							<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){ ?>
								<td><?php echo $tempos['desenvolvimento'] ?></td>
							<?php } ?>
							<?php if(isset($esforco_disciplinas['testes']['exibir'])){ ?>
								<td><?php echo $tempos['testes'] ?></td>
							<?php } ?>
							<?php if(isset($esforco_disciplinas['implantacao']['exibir'])){ ?>
								<td><?php echo $tempos['implantacao'] ?></td>
							<?php } ?>
						<?php } ?>
						<th data-phpspreadsheet-classe="negrito">
							<?php
							if($modo_exibicao_tempo == 'd'){
								echo $tempo_total;
							} else {
								echo $tempos;
							}
							?>
						</th>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="<?php echo $colspan_totais ?>" class="text-right">TOTAIS:</th>
					<?php if($mostrarValorPF){ ?>
						<th><?php echo $totais_gerais['valor_pf'] ?></th>
					<?php } ?>
					<?php if($modo_exibicao_tempo == 'd'){ ?>
						<?php if(isset($esforco_disciplinas['analise']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['analise'], $formato_tempo, $arredondarZeros) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['desenvolvimento'], $formato_tempo, $arredondarZeros) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['testes']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['testes'], $formato_tempo, $arredondarZeros) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['implantacao']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['implantacao'], $formato_tempo, $arredondarZeros) ?></th>
						<?php } ?>
					<?php } ?>
					<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['total'], $formato_tempo, $arredondarZeros) ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>