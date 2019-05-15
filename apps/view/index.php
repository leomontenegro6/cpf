<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$tipoComponente = new tipoComponente();

$id_sistema_sessao = $_SESSION['sistema_sessao'];
$id_modulo_sessao = $_SESSION['modulo_sessao'];
$id_funcionalidade_sessao = $_SESSION['funcionalidade_sessao'];

$sistema_rs = $sistema->getByValoresSistemas();
$tipoComponenteTipoDado_rs = $tipoComponente->getForSelect();
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-md-12">
				<h1 class="text-center">
					<div class="cpf_dashboard_logo d-inline-block">&nbsp;</div>
					<div class="d-inline-block">CPF - Contador de Pontos de Função</div>
				</h1>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div class="row">
		<div class="col-8">
			<div class="card card-warning card-outline">
				<div class="card-header">
					<h3 class="card-title">Valores de Sistemas</h3>
				</div>

				<div class="card-body">
					<table id="valores_sistemas" class="table table-bordered table-sm">
						<thead>
							<tr>
								<th>Sistema</th>
								<th>Valor (PF)</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($sistema_rs as $sistema_row){ ?>
								<tr>
									<td><?php echo $sistema_row['sistema'] ?></td>
									<td><?php echo $sistema_row['valor_pf'] ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-4">
			<div class="card card-success card-outline">
				<div class="card-header">
					<h3 class="card-title">Filtros Globais</h3>
				</div>

				<div class="card-body">
					<label class="form-group has-float-label">
						<select id="sistema_sessao" name="sistema_sessao" class="select form-control"
							data-pagina="sistema_autocomplete.php" data-limite-caracteres="0"
							onchange="select.limpar( gE('modulo_sessao') )">
							<option value="">Todos</option>
							<?php if(is_numeric($id_sistema_sessao)){ ?>
								<option value="<?php echo $id_sistema_sessao ?>" selected><?php echo $sistema->getNome($id_sistema_sessao, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Sistema</span>
					</label>
					
					<label class="form-group has-float-label">
						<select id="modulo_sessao" name="modulo_sessao" class="select form-control"
							data-pagina="modulo_autocomplete.php?sistema={sistema_sessao}" data-limite-caracteres="0"
							onchange="definirModuloSistema(this, 'modulo_sessao', 'sistema_sessao', salvarSistemaModuloFuncionalidadeSessao)">
							<option value="">Todos</option>
							<?php if(is_numeric($id_modulo_sessao)){ ?>
								<option value="<?php echo $id_modulo_sessao ?>" selected><?php echo $modulo->getNome($id_modulo_sessao, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Módulo</span>
					</label>
					
					<label class="form-group has-float-label">
						<select id="funcionalidade_sessao" name="funcionalidade_sessao" class="select form-control"
							data-pagina="funcionalidade_autocomplete.php?sistema={sistema_sessao}&modulo={modulo_sessao}" data-limite-caracteres="0"
							onchange="definirModuloSistema(this, 'modulo_sessao', 'sistema_sessao', salvarSistemaModuloFuncionalidadeSessao)">
							<option value="">Todas</option>
							<?php if(is_numeric($id_funcionalidade_sessao)){ ?>
								<option value="<?php echo $id_funcionalidade_sessao ?>" selected><?php echo $funcionalidade->getNome($id_funcionalidade_sessao, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Funcionalidade</span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card card-info card-outline">
				<div class="card-header">
					<h3 class="card-title">Calculadora de PF</h3>
				</div>

				<div class="card-body">
					<div id="campos_componentes" class="campo_multiplo" data-aceita-valores-duplicados='true'>
						<table class="conteiner">
							<thead>
								<tr>
									<th class='border'>
										<div class="row componente_calculadora_pf">
											<div class="col-4" style="padding-left: 10px">Tipo</div>
											<div class="col-2" style="padding-left: 10px">Campos</div>
											<div class="col-2" style="padding-left: 10px" title="Arquivos Referenciados">AR's</div>
											<div class="col-2">Complexidade</div>
											<div class="col-2">Valor (PF)</div>
										</div>
									</th>
									<td style="width: 5%"></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="corpo border"></td>
									<td class="acoes" valign="top">
										<button type="button" class="btn btn-default adicionar" title="Adicionar componente">
											<i class="fas fa-plus"></i>
										</button>
										<button type="button" class="btn btn-danger remover" title="Remover componente">
											<i class="fas fa-minus"></i>
										</button>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th class='border'>
										<div class="row componente_calculadora_pf">
											<div class="col-10 text-right">TOTAL:</div>
											<div class="col-2 total_pf">---</div>
										</div>
									</th>
									<td style="width: 5%"></td>
								</tr>
							</tfoot>
						</table>
						<div class="template">
							<div class="row componente_inclusao_funcionalidade_orcamento_manutencao">
								<div class="col-4" style='padding: 5px 10px'>
									<select id="componente_{iterador}_tipo" class="select form-control"
										name="componentes[{iterador}][tipo_componente]" data-nome="tipo_componente" required
										onchange="concatenarLabelOptgroupParaTemplateSelection(this); dashboard.calcularValorComponenteFuncionalidade(this)">
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
												onchange="dashboard.calcularValorComponenteFuncionalidade(this)" />
											<label class="custom-control-label" for="componente_{iterador}_possui_acoes">Possui Ações</label>
										</div>
									</div>
									<div class="float-right">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="componente_{iterador}_possui_mensagens"
												name="componentes[{iterador}][possui_mensagens]" data-nome="possui_mensagens" value="true"
												onchange="dashboard.calcularValorComponenteFuncionalidade(this)" />
											<label class="custom-control-label" for="componente_{iterador}_possui_mensagens">Possui Mensagens</label>
										</div>
									</div>
								</div>
								<div class="col-2">
									<input type="number" min="0" data-nome="campos" id="componente_{iterador}_campos"
										name="componentes[{iterador}][campos]" class="form-control" required
										onchange="dashboard.calcularValorComponenteFuncionalidade(this)" />
								</div>
								<div class="col-2">
									<input type="number" min="0" data-nome="arquivos_referenciados" id="componente_{iterador}_arquivos_referenciados"
										name="componentes[{iterador}][arquivos_referenciados]" class="form-control" required
										onchange="dashboard.calcularValorComponenteFuncionalidade(this)" />
								</div>
								<div class="col-2 complexidade">---</div>
								<div class="col-2 valor_pf">---</div>
							</div>
						</div>
						<div class="valores">[]</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include_once('bibliotecas_js.php') ?>
<script src="../common/js/dashboard.js?<?php echo filemtime('../common/js/dashboard.js') ?>"></script>
<script type="text/javascript">
	$(function(){
		dashboard.instanciarTabelaValoresSistemas();
	});
</script>
<?php include('rodape.php') ?>