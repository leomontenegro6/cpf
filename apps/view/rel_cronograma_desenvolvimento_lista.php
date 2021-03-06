<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();
$usuario = new usuario();
$tipoSistema = new tipoSistema();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ($_SESSION['sistema_sessao']);
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ($_SESSION['modulo_sessao']);
$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ($_SESSION['funcionalidade_sessao']);
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
if(isset($_GET['Submit'])){
	$mostrarOrdem = (isset($_GET['mostrar_ordem']) && ($_GET['mostrar_ordem'] == 'true'));
	$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
	$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
	$mostrarTempo = (isset($_GET['mostrar_tempo']) && ($_GET['mostrar_tempo'] == 'true'));
	$arredondarZeros = (isset($_GET['arredondar_zeros']) && ($_GET['arredondar_zeros'] == 'true'));
} else {
	$mostrarOrdem = (isset($_GET['mostrar_ordem'])) ? ($_GET['mostrar_ordem'] == 'true') : (false);
	$mostrarComplexidade = (isset($_GET['mostrar_complexidade'])) ? ($_GET['mostrar_complexidade'] == 'true') : (true);
	$mostrarValorPF = (isset($_GET['mostrar_valor_pf'])) ? ($_GET['mostrar_valor_pf'] == 'true') : (true);
	$mostrarTempo = (isset($_GET['mostrar_tempo'])) ? ($_GET['mostrar_tempo'] == 'true') : (true);
	$arredondarZeros = (isset($_GET['arredondar_zeros'])) ? ($_GET['arredondar_zeros'] == 'true') : (false);
}

if(is_numeric($sistema_lista)){
	$nome_sistema = $sistema->getDescricao($sistema_lista);
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
	$tipoSistema_row = $tipoSistema->get($tipo_sistema_lista);
	
	$nome_tipo_sistema = $tipoSistema_row['nome'];
	$expoente_minimo = $tipoSistema_row['expoente_minimo'];
	$expoente_maximo = $tipoSistema_row['expoente_maximo'];
} else {
	$nome_tipo_sistema = '';
	$expoente_minimo = 0.36;
	$expoente_maximo = 0.45;
}
$placeholder_expoente_capers_jones = 'Digite um valor entre ' . str_replace('.', ',', (string)$expoente_minimo) . ' e ' . str_replace('.', ',', (string)$expoente_maximo);
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-md-6">
				<h1>Cronograma de Desenvolvimento</h1>
			</div>
			<div class="col-md-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Relatórios</a></li>
					<li class="breadcrumb-item active">Cronograma de Desenvolvimento</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row">
		<div class="col-12 mx-auto">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Filtros / Personalização</h3>
				</div>

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return validaFormCronogramaDesenvolvimento(this)" novalidate>
					<div class="card-body">
						<ul class="nav nav-pills">
							<li class="nav-item">
								<a class="nav-link active" href="#">
									<i class="fas fa-filter"></i> Filtros
								</a>
							</li>
							<li class="nav-item" data-callback="concatenarLabelOptgroupParaTemplateSelection(gE('formato_tempo'), true)">
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
									<div class="col-md-3">
										<label class="form-group has-float-label">
											<select id="metodo_estimativa_prazo_lista" name="metodo_estimativa_prazo_lista" class="select form-control"
												onchange="toggleCamposEstimativaPrazo(this)">
												<option value="e" <?php if($metodo_estimativa_prazo_lista == 'e') echo 'selected' ?>>
													Estimativa de Esforço (< 100 PF)
												</option>
												<option value="cj" <?php if($metodo_estimativa_prazo_lista == 'cj') echo 'selected' ?>>
													Fórmula de Capers Jones (> 100 PF)
												</option>
											</select>
											<span>Método de Estimativa de Prazo</span>
										</label>
									</div>
									<div class="col-md-3" <?php if($metodo_estimativa_prazo_lista != 'e') echo 'style="display: none"' ?>>
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="recursos_lista" name="recursos_lista"
													<?php if($metodo_estimativa_prazo_lista == 'e') echo 'required' ?>
													type="number" min="1" step="1" value="<?php echo $recursos_lista ?>"
													placeholder="Recursos" />
												<span>Recursos</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Desenvolvedor(es)</span>
											</div>
										</div>
									</div>
									<div class="col-md-3" <?php if($metodo_estimativa_prazo_lista != 'e') echo 'style="display: none"' ?>>
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="tempo_dedicacao_lista" name="tempo_dedicacao_lista" type="number"
													min="1" value="<?php echo $tempo_dedicacao_lista ?>" placeholder="Tempo de Dedicação"
													<?php if($metodo_estimativa_prazo_lista == 'e') echo 'required' ?> />
												<span>Tempo de Dedicação</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Hora(s) / Dia</span>
											</div>
										</div>
									</div>
									<div class="col-md-3" <?php if($metodo_estimativa_prazo_lista != 'e') echo 'style="display: none"' ?>>
										<div class="form-group input-group with-float-label">
											<label class="has-float-label">
												<input class="form-control" id="indice_produtividade_lista" name="indice_produtividade_lista"
													type="number" value="<?php echo $indice_produtividade_lista ?>" min="0.4" max="1.0" step="0.1"
													<?php if($metodo_estimativa_prazo_lista == 'e') echo 'required' ?>
													placeholder="Digite um valor entre 0,4 e 1" />
												<span>Índice de Produtividade</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">Horas / PF</span>
											</div>
										</div>
									</div>
									<div class="col-md-4" <?php if($metodo_estimativa_prazo_lista != 'cj') echo 'style="display: none"' ?>>
										<label class="form-group has-float-label">
											<select id="tipo_sistema_lista" name="tipo_sistema_lista" class="select form-control"
												data-pagina="tipo_sistema_autocomplete.php" data-limite-caracteres="0"
												<?php if($metodo_estimativa_prazo_lista == 'cj') echo 'required' ?>
												onchange="toggleCampoExpoenteCapersJones(this)">
												<option value="">Escolha um tipo de sistema</option>
												<?php if(is_numeric($tipo_sistema_lista)){ ?>
													<option value="<?php echo $tipo_sistema_lista ?>" selected><?php echo $nome_tipo_sistema ?></option>
												<?php } ?>
											</select>
											<span>Tipo de Sistema</span>
										</label>
									</div>
									<div class="col-md-5" <?php if($metodo_estimativa_prazo_lista != 'cj') echo 'style="display: none"' ?>>
										<div class="form-group has-float-label">
											<input class="form-control" id="expoente_capers_jones_lista" name="expoente_capers_jones_lista"
												type="number" value="<?php echo $expoente_capers_jones_lista ?>"
												min="<?php echo $expoente_minimo ?>" max="<?php echo $expoente_maximo ?>" step="0.01"
												<?php echo (is_numeric($tipo_sistema_lista) && ($metodo_estimativa_prazo_lista == 'cj')) ? ('required') : ('disabled') ?> 
												placeholder="<?php echo $placeholder_expoente_capers_jones ?>" />
											<label for="expoente_capers_jones_lista">Expoente de Capers Jones</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group has-float-label">
											<input class="form-control calendario" id="data_inicio_atividades_lista" name="data_inicio_atividades_lista"
												type="text" value="<?php echo $data_inicio_atividades_lista ?>" required
												data-dias-desativados="0,6" />
											<label for="data_inicio_atividades_lista">Data de Início das Atividades</label>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-4">
										<label for="percentual_reducao"><b>Tempo</b></label>
										<div class="form-group input-group with-float-label" style="margin-bottom: 6px">
											<label class="has-float-label">
												<input class="form-control" id="percentual_reducao" name="percentual_reducao"
													type="number" value="<?php echo $percentual_reducao ?>" min="1" max="100" step="1"
													required placeholder="Digite um valor entre 1 e 100" />
												<span>Percentual de Redução</span>
											</label>
											<div class="input-group-append">
												<span class="input-group-text">%</span>
											</div>
										</div>
										<label class="form-group has-float-label" style="margin-bottom: 3px">
											<select id="formato_tempo" name="formato_tempo" class="select form-control"
												onchange="concatenarLabelOptgroupParaTemplateSelection(this, true); toggleCheckboxArredondarZeros(this)">
												<optgroup label="Dias">
													<option value="dni" <?php if($formato_tempo == 'dni') echo 'selected' ?>>Números Inteiros</option>
													<option value="dnr" <?php if($formato_tempo == 'dnr') echo 'selected' ?>>Números Reais (2 Casas Decimais)</option>
												</optgroup>
											</select>
											<span>Formato de Tempo</span>
										</label>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input"
												id="arredondar_zeros" name="arredondar_zeros" value="true"
												<?php if(!in_array($formato_tempo, array('hni', 'dni'))) echo 'disabled'; elseif($arredondarZeros) echo 'checked'; ?> />
											<label class="custom-control-label" for="arredondar_zeros">Arredondar Zeros para Cima</label>
										</div>
									</div>
									<div class="col-md-4">
										<label class="form-group">
											<label for="ordenacao_0"><b>Ordenação</b></label>
											<div id="campos_campos" class="campo_multiplo" data-aceita-valores-duplicados='true'>
												<table class="conteiner">
													<tbody>
														<tr>
															<td class="corpo"></td>
															<td class="acoes" valign="top" style="width: 5%">
																<button type="button" class="btn btn-default adicionar" title="Adicionar">
																	<i class="fas fa-plus"></i>
																</button>
																<button type="button" class="btn btn-danger remover" title="Remover">
																	<i class="fas fa-minus"></i>
																</button>
															</td>
														</tr>
													</tbody>
												</table>
												<div class="template">
													<div style="margin-bottom: 6px">
														<select id="ordenacao_{iterador}" name="ordenacao[]" data-nome="ordenacao" class="select form-control">
															<option value="">Escolha uma coluna</option>
															<optgroup label="Módulo">
																<option value="m.id">ID do módulo</option>
																<option value="m.nome">Nome do módulo</option>
															</optgroup>
															<optgroup label="Funcionalidade">
																<option value="f.ordem">Ordem da funcionalidade</option>
																<option value="f.nome">Nome da funcionalidade</option>
															</optgroup>
															<optgroup label="Componente">
																<option value="co.ordem">Ordem do componente</option>
																<option value="tco.descricao">Nome do componente</option>
															</optgroup>
														</select>
													</div>
												</div>
												<div class="valores"><?php echo json_encode($ordenacao) ?></div>
											</div>
										</label>
									</div>
									<div class="col-md-4">
										<label for="mostrar_ordem"><b>Mostrar</b></label>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="mostrar_ordem"
												name="mostrar_ordem" value="true" <?php if($mostrarOrdem) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_ordem">Ordem</label>
										</div>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="mostrar_complexidade"
												name="mostrar_complexidade" value="true" <?php if($mostrarComplexidade) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_complexidade">Complexidade</label>
										</div>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="mostrar_valor_pf"
												name="mostrar_valor_pf" value="true" <?php if($mostrarValorPF) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_valor_pf">Valor (Pontos de Função)</label>
										</div>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="mostrar_tempo"
												name="mostrar_tempo" value="true" <?php if($mostrarTempo) echo 'checked' ?> />
											<label class="custom-control-label" for="mostrar_tempo">Tempo</label>
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
	<div class="card" id="conteiner_tabela_cronograma_desenvolvimento">
		<?php
		if(isset($_GET['Submit'])){
			$_GET['ajax'] = true;
			include('rel_cronograma_desenvolvimento_tabela.php');
		}
		?>
	</div>
</section>
<?php
include('rodape.php');
?>