<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$funcionalidade = new funcionalidade();
$tipoComponente = new tipoComponente();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ($_SESSION['sistema_sessao']);

$sistema_rs = $sistema->getAll();
$tipoComponenteTipoDado_rs = $tipoComponente->getForSelect();
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-md-6">
				<h1>Orçamento de Manutenção</h1>
			</div>
			<div class="col-md-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Relatórios</a></li>
					<li class="breadcrumb-item active">Orçamento de Manutenção</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row">
		<div class="col-12 mx-auto">
			<div class="row">
				<div class="col-8">
					<div class="card card-info">
						<div class="card-header">
							<h3 class="card-title">Funcionalidades</h3>
						</div>

						<div class="card-body">
							<label class="form-group has-float-label">
								<select id="sistema_lista" name="sistema_lista" class="select form-control"
									required onchange="orcamentoManutencao.configurarRelatorio(this)">
									<option value="">Escolha um sistema</option>
									<?php foreach($sistema_rs as $sistema_row){
										if($sistema_lista == $sistema_row['id']){
											$selected = 'selected';
										} else {
											$selected = '';
										}
										?>
										<option value="<?php echo $sistema_row['id'] ?>" <?php echo $selected ?>>
											<?php echo $sistema_row['sigla'] . ' - ' . $sistema_row['nome'] ?>
										</option>
									<?php } ?>
								</select>
								<span>Sistema</span>
							</label>

							<ul class="nav nav-pills">
								<li class="nav-item">
									<a class="nav-link" href="#">
										<span class="fa-stack">
											<i class="fa fa-cogs fa-stack-2x"></i>
											<i class="fa fa-circle fa-stack-1x fa-alinhado-topo-direito fa-fundo-16 text-info" style="top: -9px; left: 13px"></i>
											<i class="fa fa-plus fa-stack-1x fa-alinhado-topo-direito fa-plano-branco" style="top: -9px; left: 13px; font-size: 0.7em"></i>
										</span>
										Inclusões
									</a>
								</li>
								<li class="nav-item" data-callback="orcamentoManutencao.instanciarComponenteAlteracaoExclusaoFuncionalidades()">
									<a class="nav-link active" href="#">
										<span class="fa-stack">
											<i class="fa fa-cogs fa-stack-2x"></i>
											<i class="fa fa-circle fa-stack-1x fa-alinhado-topo-direito fa-fundo-16 text-success" style="top: -9px; left: 13px; background: linear-gradient(30deg, #28a745 50%, #dc3545 50%); -webkit-background-clip: text; -webkit-text-fill-color: transparent"></i>
											<i class="fa fa-infinity fa-stack-1x fa-alinhado-topo-direito fa-plano-branco" style="top: -9px; left: 13px; font-size: 0.7em"></i>
										</span>
										Alterações / Exclusões
									</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane" role="tabpanel">
									<form method="GET" id="form_incluir_funcionalidade" name="form_incluir_funcionalidade"
										onsubmit="return orcamentoManutencao.incluirFuncionalidade(this)" onreset="orcamentoManutencao.limparValoresComponenteFuncionalidade(); return resetaForm(this)" novalidate>
										<div class="row">
											<div class="col-6">
												<div class="form-group has-float-label">
													<input type="text" id="modulo_lista" name="modulo_lista" class="form-control"
														required placeholder="Digite o nome do módulo" />
													<label for="modulo_lista">Módulo</label>
												</div>
											</div>
											<div class="col-6">
												<div class="form-group has-float-label">
													<input type="text" id="funcionalidade_lista" name="funcionalidade_lista" class="form-control"
														required placeholder="Digite o nome da funcionalidade" />
													<label for="funcionalidade_lista">Funcionalidade</label>
												</div>
											</div>
										</div>
										<div id="campos_componentes" class="campo_multiplo" data-aceita-valores-duplicados='true'>
											<table class="conteiner">
												<thead>
													<tr>
														<th>Componentes</th>
														<td rowspan="2" style="width: 5%"></td>
													</tr>
													<tr>
														<th class='border'>
															<div class="row componente_inclusao_funcionalidade_orcamento_manutencao">
																<div class="col-6" style="padding-left: 10px">Tipo</div>
																<div class="col-2"style="padding-left: 10px">Campos</div>
																<div class="col-2"style="padding-left: 10px" title="Arquivos Referenciados">AR's</div>
																<div class="col-2">Valor (PF)</div>
															</div>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="corpo border"></td>
														<td class="acoes" valign="top">
															<button type="button" class="btn btn-default adicionar" title="Adicionar outro componente">
																<i class="fas fa-plus"></i>
															</button>
															<button type="button" class="btn btn-danger remover" title="Remover componente">
																<i class="fas fa-minus"></i>
															</button>
														</td>
													</tr>
												</tbody>
											</table>
											<div class="template">
												<div class="row componente_inclusao_funcionalidade_orcamento_manutencao">
													<div class="col-6" style='padding: 5px 10px'>
														<select id="componente_{iterador}_tipo" class="select form-control"
															name="componentes[{iterador}][tipo_componente]" data-nome="tipo_componente" required
															onchange="concatenarLabelOptgroupParaTemplateSelection(this); orcamentoManutencao.calcularValorComponenteFuncionalidade(this)">
															<option value="">Escolha um tipo de componente</option>
															<?php foreach($tipoComponenteTipoDado_rs as $tipo_dado=>$tipoComponente_rs){ ?>
																<optgroup label="<?php echo $tipo_dado ?>">
																	<?php foreach($tipoComponente_rs as $tipoComponente_row){ ?>
																		<option value="<?php echo $tipoComponente_row['id'] ?>"
																			data-alias="<?php echo $tipoComponente_row['alias'] ?>">
																			<?php echo $tipoComponente_row['descricao'] ?>
																		</option>
																	<?php } ?>
																</optgroup>
															<?php } ?>
														</select>
														<div class="float-left">
															<div class="custom-control custom-checkbox">
																<input type="checkbox" class="custom-control-input" id="componente_{iterador}_possui_acoes"
																	name="componentes[{iterador}][possui_acoes]" data-nome="possui_acoes" value="true"
																	onchange="orcamentoManutencao.calcularValorComponenteFuncionalidade(this)" />
																<label class="custom-control-label" for="componente_{iterador}_possui_acoes">Possui Ações</label>
															</div>
														</div>
														<div class="float-right">
															<div class="custom-control custom-checkbox">
																<input type="checkbox" class="custom-control-input" id="componente_{iterador}_possui_mensagens"
																	name="componentes[{iterador}][possui_mensagens]" data-nome="possui_mensagens" value="true"
																	onchange="orcamentoManutencao.calcularValorComponenteFuncionalidade(this)" />
																<label class="custom-control-label" for="componente_{iterador}_possui_mensagens">Possui Mensagens</label>
															</div>
														</div>
													</div>
													<div class="col-2">
														<input type="number" min="0" data-nome="campos" id="componente_{iterador}_campos"
															name="componentes[{iterador}][campos]" class="form-control" required
															onchange="orcamentoManutencao.calcularValorComponenteFuncionalidade(this)" />
													</div>
													<div class="col-2">
														<input type="number" min="0" data-nome="arquivos_referenciados" id="componente_{iterador}_arquivos_referenciados"
															name="componentes[{iterador}][arquivos_referenciados]" class="form-control" required
															onchange="orcamentoManutencao.calcularValorComponenteFuncionalidade(this)" />
													</div>
													<div class="col-2 valor_pf">---</div>
												</div>
											</div>
											<div class="valores">[]</div>
										</div>
										<div class="row" style="margin-top: 10px">
											<div class="col text-center">
												<div class="btn-group">
													<button type="submit" name="Submit" class="btn btn-info" title="Incluir nova funcionalidade no orçamento">
														<i class="fas fa-arrow-down"></i>
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
								<div class="tab-pane" role="tabpanel">
									<form method="GET" id="form_alterar_excluir_funcionalidades" name="form_alterar_excluir_funcionalidades"
										onsubmit="return orcamentoManutencao.alterarExcluirFuncionalidades(this)" novalidate>
										<?php
										$_GET['sistema'] = (is_numeric($sistema_lista)) ? ($sistema_lista) : ('');
										include('rel_orcamento_manutencao_funcionalidade_preenche.php');
										?>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="card card-info">
						<div class="card-header">
							<h3 class="card-title">Parâmetros / Personalizações</h3>
						</div>

						<div class="card-body">
							<ul class="nav nav-pills">
								<li class="nav-item">
									<a class="nav-link active" href="#">
										<i class="fas fa-sliders-h"></i> Parâmetros
									</a>
								</li>
								<li class="nav-item" data-callback="concatenarLabelOptgroupParaTemplateSelection(gE('formato_tempo'), true)">
									<a class="nav-link" href="#">
										<i class="fas fa-wrench"></i> Personalização
									</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane" role="tabpanel">
									<div class="row">
										<div class="col-12">
											<label class="form-group has-float-label">
												<select id="metodo_estimativa_prazo_lista" name="metodo_estimativa_prazo_lista" class="select form-control"
													onchange="toggleCamposEstimativaPrazo(this); orcamentoManutencao.atualizarValoresCorpoTabela()">
													<option value="e">Estimativa de Esforço (< 100 PF)</option>
													<option value="cj">Fórmula de Capers Jones (> 100 PF)</option>
												</select>
												<span>Método de Estimativa de Prazo</span>
											</label>
										</div>
										<div class="col-12">
											<div class="form-group input-group with-float-label">
												<label class="has-float-label">
													<input class="form-control" id="recursos_lista" name="recursos_lista" required
														type="number" min="1" step="1" value="1" placeholder="Recursos"
														onchange="orcamentoManutencao.atualizarValoresCorpoTabela()" />
													<span>Recursos</span>
												</label>
												<div class="input-group-append">
													<span class="input-group-text">Desenvolvedor(es)</span>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group input-group with-float-label">
												<label class="has-float-label">
													<input class="form-control" id="tempo_dedicacao_lista" name="tempo_dedicacao_lista" type="number"
														min="1" value="4" placeholder="Tempo de Dedicação" required
														onchange="orcamentoManutencao.atualizarValoresCorpoTabela()" />
													<span>Tempo de Dedicação</span>
												</label>
												<div class="input-group-append">
													<span class="input-group-text">Hora(s) / Dia</span>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group input-group with-float-label">
												<label class="has-float-label">
													<input class="form-control" id="indice_produtividade_lista" name="indice_produtividade_lista"
														type="number" min="0.4" max="1.0" step="0.1" value="0.5"
														required placeholder="Digite um valor entre 0,4 e 1"
														onchange="orcamentoManutencao.atualizarValoresCorpoTabela()" />
													<span>Índice de Produtividade</span>
												</label>
												<div class="input-group-append">
													<span class="input-group-text">Horas / PF</span>
												</div>
											</div>
										</div>
										<div class="col-12" style="display: none">
											<label class="form-group has-float-label">
												<select id="tipo_sistema_lista" name="tipo_sistema_lista" class="select form-control"
													data-pagina="tipo_sistema_autocomplete.php" data-limite-caracteres="0"
													onchange="toggleCampoExpoenteCapersJones(this); orcamentoManutencao.atualizarValoresCorpoTabela()">
													<option value="">Escolha um tipo de sistema</option>
												</select>
												<span>Tipo de Sistema</span>
											</label>
										</div>
										<div class="col-12" style="display: none">
											<div class="form-group has-float-label">
												<input class="form-control" id="expoente_capers_jones_lista" name="expoente_capers_jones_lista"
													type="number" step="0.01" disabled onchange="orcamentoManutencao.atualizarValoresCorpoTabela()" />
												<label for="expoente_capers_jones_lista">Expoente de Capers Jones</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<label class="form-group has-float-label">
												<select id="metodo_calculo_orcamento_lista" name="metodo_calculo_orcamento_lista" class="select form-control"
													onchange="toggleCamposValorOrcamento(this); orcamentoManutencao.atualizarValoresCorpoTabela()">
													<optgroup label="Setores Públicos">
														<option value="vpf">Valor do Ponto de Função</option>
													</optgroup>
													<optgroup label="Setores Privados">
														<option value="vht" selected>Valor da Hora Trabalhada</option>
													</optgroup>
												</select>
												<span>Método de Cálculo do Orçamento</span>
											</label>
										</div>
										<div class="col-12" style="display: none">
											<div class="form-group input-group with-float-label">
												<div class="input-group-prepend" style="margin-right: -14px">
													<span class="input-group-text">R$</span>
												</div>
												<label class="has-float-label">
													<input class="form-control" id="valor_ponto_funcao_lista" name="valor_ponto_funcao_lista" required
														type="tel" value="1061,00" placeholder="Digite o valor" disabled
														data-mascara="#.##0,00" data-reverso="true" data-minimo="0" data-maximo="10000"
														onchange="orcamentoManutencao.atualizarValoresCorpoTabela()" />
													<span>Valor do Ponto de Função</span>
												</label>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group input-group with-float-label">
												<div class="input-group-prepend" style="margin-right: -14px">
													<span class="input-group-text">R$</span>
												</div>
												<label class="has-float-label">
													<input class="form-control" id="valor_hora_trabalhada_lista" name="valor_hora_trabalhada_lista"
														type="tel" value="40,00" placeholder="Digite o valor" onchange="orcamentoManutencao.atualizarValoresCorpoTabela()"
														data-mascara="#.##0,00" data-reverso="true" data-minimo="0" data-maximo="10000" />
													<span>Valor da Hora Trabalhada</span>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" role="tabpanel">
									<div class="row">
										<div class="col-12">
											<div class="form-group input-group with-float-label" style="margin-bottom: 6px">
												<label class="has-float-label">
													<input class="form-control" id="percentual_reducao" name="percentual_reducao"
														type="number" value="100" min="1" max="100" step="1" onchange="orcamentoManutencao.atualizarValoresCorpoTabela()"
														required placeholder="Digite um valor entre 1 e 100" />
													<span>Percentual de Redução</span>
												</label>
												<div class="input-group-append">
													<span class="input-group-text">%</span>
												</div>
											</div>
											<label class="form-group has-float-label" style="margin-bottom: 3px">
												<select id="formato_tempo" name="formato_tempo" class="select form-control"
														onchange="concatenarLabelOptgroupParaTemplateSelection(this, true); toggleCheckboxArredondarZeros(this); orcamentoManutencao.personalizarFormatoTempoTabela()">
													<optgroup label="Horas / Minutos">
														<option value="hhm">HH:MM</option>
														<option value="hni">Números Inteiros</option>
														<option value="hnr">Números Reais (2 Casas Decimais)</option>
													</optgroup>
													<optgroup label="Dias">
														<option value="dni">Números Inteiros</option>
														<option value="dnr">Números Reais (2 Casas Decimais)</option>
													</optgroup>
													<optgroup label="Meses">
														<option value="mnr">Números Reais (2 Casas Decimais)</option>
													</optgroup>
												</select>
												<span>Formato de Tempo</span>
											</label>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" onchange="orcamentoManutencao.atualizarValoresCorpoTabela()"
													id="arredondar_zeros" name="arredondar_zeros" value="true" disabled />
												<label class="custom-control-label" for="arredondar_zeros">Arredondar Zeros para Cima</label>
											</div>
										</div>
										<div class="col-12">
											<label class="form-group has-float-label" style="margin-top: 5px; margin-bottom: 3px">
												<select id="mostrar_valor" name="mostrar_valor" class="select form-control"
													onchange="orcamentoManutencao.toggleColunasTabela()">
													<option value="oa">Original e Ajustado</option>
													<option value="a">Apenas Ajustado</option>
												</select>
												<span>Mostrar Valores (PF)</span>
											</label>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="mostrar_tempo"
													name="mostrar_tempo" value="true" checked onchange="orcamentoManutencao.toggleColunasTabela()" />
												<label class="custom-control-label" for="mostrar_tempo">Mostrar Tempo</label>
											</div>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="mostrar_custo"
													name="mostrar_custo" value="true" checked onchange="orcamentoManutencao.toggleColunasTabela()" />
												<label class="custom-control-label" for="mostrar_custo">Mostrar Custo (R$)</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card" id="tabela_orcamento_manutencao" <?php if(!is_numeric($sistema_lista)) echo 'style="display: none"' ?>>
		<?php
		$_GET['ajax'] = true;
		include('rel_orcamento_manutencao_tabela.php');
		?>
	</div>
</section>

<?php include_once('bibliotecas_js.php') ?>
<script src="../common/js/orcamentoManutencao.js?<?php echo filemtime('../common/js/orcamentoManutencao.js') ?>"></script>
<script type="text/javascript">
	$(function(){
		orcamentoManutencao.carregarRotinasPrincipais();
	});
</script>
<?php include('rodape.php') ?>