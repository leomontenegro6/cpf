<?php
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();
$usuario = new usuario();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ('');
$recursos_lista = (isset($_GET['recursos_lista'])) ? ($_GET['recursos_lista']) : ('1');
$tempo_dedicacao_lista = (isset($_GET['tempo_dedicacao_lista'])) ? ($_GET['tempo_dedicacao_lista']) : ('4');
$indice_produtividade_lista = (isset($_GET['indice_produtividade_lista'])) ? ($_GET['indice_produtividade_lista']) : ($usuario->getIndiceProdutividade($_SESSION['iduser']));

$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
$modo_exibicao_tempo = (isset($_GET['modo_exibicao_tempo'])) ? ($_GET['modo_exibicao_tempo']) : ('u');
$formato_tempo = (isset($_GET['formato_tempo'])) ? ($_GET['formato_tempo']) : ('hm');
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

$componente_rs = $componente->getByPlanilhaPrazosDesenvolvimento($sistema_lista, $modulo_lista, $funcionalidade_lista, $recursos_lista, $tempo_dedicacao_lista, $indice_produtividade_lista, $modo_exibicao_tempo, $percentual_reducao_unico, $esforco_disciplinas);

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
		Prazos de Desenvolvimento de Funcionalidades
	</h3>
	<div class="card-tools">
		<button type="button" class="btn btn-success float-right"
			onclick="abrirPagina('rel_prazos_desenvolvimento_xls.php?sistema=<?php echo $sistema_lista ?>&modulo=<?php echo $modulo_lista ?>&recursos_lista=<?php echo $recursos_lista ?>&tempo_dedicacao_lista=<?php echo $tempo_dedicacao_lista ?>&indice_produtividade_lista=<?php echo $indice_produtividade_lista ?>', '', '_blank');">
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
						<th rowspan="2" class="align-middle" style="background-color: #fafafa">Módulo</th>
					<?php } ?>
					<?php if(!$checkFuncionalidadeUnica){ ?>
						<th rowspan="2" class="align-middle" style="background-color: #fafafa">Funcionalidade</th>
					<?php } ?>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Componente</th>
					<?php if($mostrarComplexidade){ ?>
						<th rowspan="2" class="align-middle" style="background-color: #fafafa">Complexidade</th>
					<?php } ?>
					<?php if($mostrarValorPF){ ?>
						<th rowspan="2" class="align-middle" style="background-color: #fafafa">Valor (PF)</th>
					<?php } ?>
					<?php if($modo_exibicao_tempo == 'u'){ ?>
						<th rowspan="2" class="align-middle" style="background-color: #fafafa">
							Tempo (<?php echo ($formato_tempo == 'hm') ? ('Horas / Minutos') : ('Horas') ?>)
						</th>
					<?php } else { ?>
						<th colspan="<?php echo $rowspan_tempo ?>" class="text-center" style="background-color: #fafafa">
							Tempo (<?php echo ($formato_tempo == 'hm') ? ('Horas / Minutos') : ('Horas') ?>)
						</th>
					<?php } ?>
				</tr>
				<?php if($modo_exibicao_tempo == 'd'){ ?>
					<tr>
						<?php if(isset($esforco_disciplinas['analise']['exibir'])){ ?>
							<th style="background-color: #fafafa">Análise</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){ ?>
							<th style="background-color: #fafafa">Desenvolvimento</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['testes']['exibir'])){ ?>
							<th style="background-color: #fafafa">Testes</th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['implantacao']['exibir'])){ ?>
							<th style="background-color: #fafafa">Implantação</th>
						<?php } ?>
						<th style="background-color: #fafafa">TOTAL</th>
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
				//if(!$mostrarValorPF) $colspan_totais--;
				foreach($componente_rs as $componente_row){
					$rowspan = $componente_row['rowspan'];

					$tempo_total = 0;
					if($modo_exibicao_tempo == 'u'){
						if($formato_tempo == 'ni'){
							$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
						} elseif($formato_tempo == 'nr'){
							$tempos = round($componente_row['tempo'], 2);
						} else {
							$tempos = $componente_row['tempo'];
						}

						$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
						$totais_gerais['tempo']['total'] += $tempos;

						if(in_array($formato_tempo, array('hm', 'nr'))){
							$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
						}
					} else {
						if($formato_tempo == 'ni'){
							$tempos = array(
								'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo),
								'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo),
								'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo),
								'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo)
							);
						} elseif($formato_tempo == 'nr'){
							$tempos = array(
								'analise' => round($componente_row['tempo']['analise'], 2),
								'desenvolvimento' => round($componente_row['tempo']['desenvolvimento'], 2),
								'testes' => round($componente_row['tempo']['testes'], 2),
								'implantacao' => round($componente_row['tempo']['implantacao'], 2)
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

						if(in_array($formato_tempo, array('hm', 'nr'))){
							$tempos = array(
								'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo),
								'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo),
								'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo),
								'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo)
							);
						}
						$tempo_total = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($tempo_total, $formato_tempo);
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
								<td rowspan="<?php echo $componente_row['rowspan'] ?>"><?php echo $componente_row['funcionalidade'] ?></td>
							<?php } ?>
						<?php } ?>
						<td><?php echo $componente_row['componente'] ?></td>
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
						<th>
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
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['analise'], $formato_tempo) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['desenvolvimento'], $formato_tempo) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['testes']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['testes'], $formato_tempo) ?></th>
						<?php } ?>
						<?php if(isset($esforco_disciplinas['implantacao']['exibir'])){ ?>
							<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['implantacao'], $formato_tempo) ?></th>
						<?php } ?>
					<?php } ?>
					<th><?php echo funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['total'], $formato_tempo) ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>