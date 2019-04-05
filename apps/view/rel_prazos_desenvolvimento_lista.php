<?php
include('cabecalho.php');
include('menu.php');

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

if(isset($_GET['Submit'])){
	$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
	$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
} else {
	$mostrarComplexidade = (isset($_GET['mostrar_complexidade'])) ? ($_GET['mostrar_complexidade'] == 'true') : (true);
	$mostrarValorPF = (isset($_GET['mostrar_valor_pf'])) ? ($_GET['mostrar_valor_pf'] == 'true') : (true);
}
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
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-md-6">
				<h1>Prazos de Desenvolvimento</h1>
			</div>
			<div class="col-md-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Relatórios</a></li>
					<li class="breadcrumb-item active">Prazos de Desenvolvimento</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row">
		<div class="col-10 mx-auto">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Filtros de Busca</h3>
				</div>

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return validaFormPrazosDesenvolvimento(this)" novalidate>
					<div class="card-body">
						<ul class="nav nav-pills">
							<li class="nav-item">
								<a class="nav-link active" href="#">
									<i class="fas fa-filter"></i> Filtros
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">
									<i class="fas fa-cog"></i> Personalização
								</a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-4">
										<label class="form-group has-float-label">
											<select id="sistema_lista" name="sistema_lista" class="select form-control"
												data-pagina="sistema_autocomplete.php" data-limite-caracteres="0" required
												onchange="select.limpar( gE('modulo_lista') ); select.limpar( gE('funcionalidade_lista') )">
												<option value="">Escolha um sistema</option>
												<?php if(is_numeric($sistema_lista)){ ?>
													<option value="<?php echo $sistema_lista ?>" selected><?php echo $nome_sistema ?></option>
												<?php } ?>
											</select>
											<span>Sistema</span>
										</label>
									</div>
									<div class="col-md-4">
										<label class="form-group has-float-label">
											<select id="modulo_lista" name="modulo_lista" class="select form-control"
												data-pagina="modulo_autocomplete.php?sistema={sistema_lista}" data-limite-caracteres="0"
												onchange="select.limpar( gE('funcionalidade_lista') ); definirModuloSistema(this, 'modulo_lista', 'sistema_lista')">
												<option value="">Todos</option>
												<?php if(is_numeric($modulo_lista)){ ?>
													<option value="<?php echo $modulo_lista ?>" selected><?php echo $nome_modulo ?></option>
												<?php } ?>
											</select>
											<span>Módulo</span>
										</label>
									</div>
									<div class="col-md-4">
										<label class="form-group has-float-label">
											<select id="funcionalidade_lista" name="funcionalidade_lista" class="select form-control"
												data-pagina="funcionalidade_autocomplete.php?sistema={sistema_lista}&modulo={modulo_lista}" data-limite-caracteres="0"
												onchange="definirModuloSistema(this, 'modulo_lista', 'sistema_lista')">
												<option value="">Todas</option>
												<?php if(is_numeric($funcionalidade_lista)){ ?>
													<option value="<?php echo $funcionalidade_lista ?>" selected><?php echo $nome_funcionalidade ?></option>
												<?php } ?>
											</select>
											<span>Funcionalidade</span>
										</label>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="recursos_lista" name="recursos_lista"
													type="number" min="1" step="1" value="<?php echo $recursos_lista ?>"
													placeholder="Recursos" />
												<span>Recursos</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Desenvolvedor(es)</span>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="tempo_dedicacao_lista" name="tempo_dedicacao_lista" type="number"
													min="1" value="<?php echo $tempo_dedicacao_lista ?>" placeholder="Tempo de Dedicação" />
												<span>Tempo de Dedicação</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Hora(s) / Dia</span>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="indice_produtividade_lista" name="indice_produtividade_lista"
													type="number" value="<?php echo $indice_produtividade_lista ?>" min="0.4" max="1.0" step="0.1"
													placeholder="Digite um valor entre 0,4 e 1" />
												<span>Índice de Produtividade</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Horas / PF</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6">
										<div class="custom-control custom-checkbox" style="margin-bottom: 10px">
											<input type="checkbox" class="custom-control-input" id="mostrar_complexidade"
												name="mostrar_complexidade" value="true"
												<?php if($mostrarComplexidade) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_complexidade">Mostrar Complexidade</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="custom-control custom-checkbox" style="margin-bottom: 10px">
											<input type="checkbox" class="custom-control-input" id="mostrar_valor_pf"
												name="mostrar_valor_pf" value="true"
												<?php if($mostrarValorPF) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_valor_pf">Mostrar Valor (Pontos de Função)</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<label class="form-group has-float-label">
											<select id="modo_exibicao_tempo" name="modo_exibicao_tempo" class="select form-control"
												onchange="toggleCamposModoExibicao(this)">
												<option value="u" <?php if($modo_exibicao_tempo == 'u') echo 'selected' ?>>Tempo Único</option>
												<option value="d" <?php if($modo_exibicao_tempo == 'd') echo 'selected' ?>>Tempos Divididos por Disciplinas</option>
											</select>
											<span>Modo de Exibição do Tempo</span>
										</label>
									</div>
									<div class="col-md-6">
										<label class="form-group has-float-label">
											<select id="formato_tempo" name="formato_tempo" class="select form-control">
												<option value="hm" <?php if($formato_tempo == 'hm') echo 'selected' ?>>
													Horas / Minutos
												</option>
												<option value="ni" <?php if($formato_tempo == 'ni') echo 'selected' ?>>
													Números Inteiros (Arredondados)
												</option>
												<option value="nr" <?php if($formato_tempo == 'nr') echo 'selected' ?>>
													Números Reais (2 Casas Decimais)
												</option>
											</select>
											<span>Formato de Tempo</span>
										</label>
									</div>
								</div>
								
								<div id="modo_exibicao_tempo_unico" <?php if($modo_exibicao_tempo == 'd') echo 'style="display: none"' ?>>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group input-group with-float-label">
												<label class="has-float-label">
													<input class="form-control" id="percentual_reducao_unico" name="percentual_reducao_unico"
														type="number" value="<?php echo $percentual_reducao_unico ?>" min="1" max="100" step="1"
														required placeholder="Digite um valor entre 1 e 100" />
													<span>Percentual de Redução</span>
												</label>
												<div class="input-group-append">
													<span class="input-group-text">%</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="modo_exibicao_tempos_divididos" <?php if($modo_exibicao_tempo == 'u') echo 'style="display: none"' ?>>
									<b>Percentuais de Esforço de Disciplinas</b>
									<div class="row grid-table header">
										<div class="col-3">Disciplina</div>
										<div class="col-9">Percentual</div>
									</div>
									<div class="row grid-table">
										<div class="col-md-3">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="esforco_disciplinas_analise_exibir"
													name="esforco_disciplinas[analise][exibir]" value="true"
													<?php if(isset($esforco_disciplinas['analise']['exibir'])) echo 'checked' ?> />
												<label class="custom-control-label" for="esforco_disciplinas_analise_exibir">Análise</label>
											</div>
										</div>
										<div class="col-md-8" style="padding-right: 15px">
											<input type="text" id="esforco_disciplinas_analise_percentual"
												name="esforco_disciplinas[analise][percentual]" class="slider" onchange="balancearPercentuaisEsforcoDisciplinas(this, event)"
												value="<?php echo $esforco_disciplinas['analise']['percentual'] ?>" data-min="0"
												data-max="100" data-step="0.01" data-slider-handle="custom" />
										</div>
										<div class="col-md-1">
											<span class="badge bg-primary">
												<?php echo $esforco_disciplinas['analise']['percentual'] ?>%
											</span>
										</div>
									</div>
									<div class="row grid-table">
										<div class="col-md-3">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="esforco_disciplinas_desenvolvimento_exibir"
													name="esforco_disciplinas[desenvolvimento][exibir]" value="true"
													<?php if(isset($esforco_disciplinas['desenvolvimento']['exibir'])) echo 'checked' ?> />
												<label class="custom-control-label" for="esforco_disciplinas_desenvolvimento_exibir">Desenvolvimento</label>
											</div>
										</div>
										<div class="col-md-8" style="padding-right: 15px">
											<input type="text" id="esforco_disciplinas_desenvolvimento_percentual"
												name="esforco_disciplinas[desenvolvimento][percentual]" class="slider" onchange="balancearPercentuaisEsforcoDisciplinas(this, event)"
												value="<?php echo $esforco_disciplinas['desenvolvimento']['percentual'] ?>" data-min="0"
												data-max="100" data-step="0.01" data-slider-handle="custom" />
										</div>
										<div class="col-md-1">
											<span class="badge bg-primary">
												<?php echo $esforco_disciplinas['desenvolvimento']['percentual'] ?>%
											</span>
										</div>
									</div>
									<div class="row grid-table">
										<div class="col-md-3">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="esforco_disciplinas_testes_exibir"
													name="esforco_disciplinas[testes][exibir]" value="true"
													<?php if(isset($esforco_disciplinas['testes']['exibir'])) echo 'checked' ?> />
												<label class="custom-control-label" for="esforco_disciplinas_testes_exibir">Testes</label>
											</div>
										</div>
										<div class="col-md-8" style="padding-right: 15px">
											<input type="text" id="esforco_disciplinas_testes_percentual"
												name="esforco_disciplinas[testes][percentual]" class="slider" onchange="balancearPercentuaisEsforcoDisciplinas(this, event)"
												value="<?php echo $esforco_disciplinas['testes']['percentual'] ?>" data-min="0"
												data-max="100" data-step="0.01" data-slider-handle="custom" />
										</div>
										<div class="col-md-1">
											<span class="badge bg-primary">
												<?php echo $esforco_disciplinas['testes']['percentual'] ?>%
											</span>
										</div>
									</div>
									<div class="row grid-table">
										<div class="col-md-3">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="esforco_disciplinas_implantacao_exibir"
													name="esforco_disciplinas[implantacao][exibir]" value="true"
													<?php if(isset($esforco_disciplinas['implantacao']['exibir'])) echo 'checked' ?> />
												<label class="custom-control-label" for="esforco_disciplinas_implantacao_exibir">Implantação</label>
											</div>
										</div>
										<div class="col-md-8" style="padding-right: 15px">
											<input type="text" id="esforco_disciplinas_implantacao_percentual"
												name="esforco_disciplinas[implantacao][percentual]" class="slider" onchange="balancearPercentuaisEsforcoDisciplinas(this, event)"
												value="<?php echo $esforco_disciplinas['implantacao']['percentual'] ?>" data-min="0"
												data-max="100" data-step="0.01" data-slider-handle="custom" />
										</div>
										<div class="col-md-1">
											<span class="badge bg-primary">
												<?php echo $esforco_disciplinas['implantacao']['percentual'] ?>%
											</span>
										</div>
									</div>
									<div class="row grid-table">
										<div class="col-md-11">&nbsp;</div>
										<div class="col-md-1">
											<span class="badge bg-primary">100%</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="card-footer text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-warning" onclick="history.back()">
								<i class="fas fa-arrow-left"></i> Voltar
							</button>
							<button type="submit" name="Submit" class="btn btn-info">
								<i class="fas fa-search"></i>
								Pesquisar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="card" id="tabela_prazos_desenvolvimento">
		<?php
		if(isset($_GET['Submit'])){
			$_GET['ajax'] = true;
			include('rel_prazos_desenvolvimento_tabela.php');
		}
		?>
	</div>
</section>
<?php
include('rodape.php');
?>