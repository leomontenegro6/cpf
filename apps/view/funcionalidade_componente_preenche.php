<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$tipoComponente = new tipoComponente();
$tipoFuncionalidade = new tipoFuncionalidade();

$id_tipo_funcionalidade = $_GET['tipo_funcionalidade'];
$nome_funcionalidade = $_GET['nome_funcionalidade'];

$tipoComponenteTipoDado_rs = $tipoComponente->getForSelect();
$componenteTipoFuncionalidade_rs = $tipoFuncionalidade->getComponentesFormularioCadastroFuncionalidades($id_tipo_funcionalidade, $nome_funcionalidade);
?>
<b>Componentes</b>
<div class="row">
	<div class="col-3" style="border: 1px solid #dee2e6"><b>Tipo</b></div>
	<div class="col-3" style="border: 1px solid #dee2e6"><b>Campos</b></div>
	<div class="col-3" style="border: 1px solid #dee2e6"><b>Arquivos Referenciados</b></div>
	<div class="col" style="border: 1px solid #dee2e6"><b>Complexidade</b></div>
	<div class="col" style="border: 1px solid #dee2e6"><b>Valor (PF)</b></div>
</div>
<?php foreach($componenteTipoFuncionalidade_rs as $i=>$componenteTipoFuncionalidade_row){ ?>
	<div class="row componente_funcionalidade">
		<div class="col-3">
			<select id="tipo_componente<?php echo $i ?>" class="select form-control"
				name="componentes[<?php echo $i ?>][tipo_componente]" required
				onchange="concatenarLabelOptgroupParaTemplateSelection(this); calcularComplexidadeEValorComponenteFuncionalidade(this)">
				<option value="">Escolha um tipo de componente</option>
				<?php foreach($tipoComponenteTipoDado_rs as $tipo_dado=>$tipoComponente_rs){ ?>
					<optgroup label="<?php echo $tipo_dado ?>">
						<?php foreach($tipoComponente_rs as $tipoComponente_row){
							if($tipoComponente_row['id'] == $componenteTipoFuncionalidade_row['tipo_componente']){
								$selected = 'selected';
							} else {
								$selected = '';
							}
							?>
							<option value="<?php echo $tipoComponente_row['id'] ?>" <?php echo $selected ?>
								data-alias="<?php echo $tipoComponente_row['alias'] ?>">
								<?php echo $tipoComponente_row['descricao'] ?>
							</option>
						<?php } ?>
					</optgroup>
				<?php } ?>
			</select>
			<div class="float-left">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="possui_acoes<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][possui_acoes]" value="true"
						onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)"
						<?php if($componenteTipoFuncionalidade_row['possui_acoes']) echo 'checked' ?> />
					<label class="custom-control-label" for="possui_acoes<?php echo $i ?>">Possui Ações</label>
				</div>
			</div>
			<div class="float-right">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="possui_mensagens<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][possui_mensagens]" value="true"
						onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)"
						<?php if($componenteTipoFuncionalidade_row['possui_mensagens']) echo 'checked' ?> />
					<label class="custom-control-label" for="possui_mensagens<?php echo $i ?>">Possui Mensagens</label>
				</div>
			</div>
		</div>
		<div class="col-3">
			<div class="d-inline-block">
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" id="modo_preenchimento_campo_quantidade<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][modo_preenchimento_campos]" value="q"
						checked onchange="toggleQuantidadeOuNomeCamposArquivosComponente(this); calcularComplexidadeEValorComponenteFuncionalidade(this)" />
					<label class="custom-control-label" for="modo_preenchimento_campo_quantidade<?php echo $i ?>">
						Quantidade
					</label>
				</div>
			</div>
			<div class="d-inline-block">
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" id="modo_preenchimento_campo_nome<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][modo_preenchimento_campos]" value="n"
						onchange="toggleQuantidadeOuNomeCamposArquivosComponente(this); calcularComplexidadeEValorComponenteFuncionalidade(this)" />
					<label class="custom-control-label" for="modo_preenchimento_campo_nome<?php echo $i ?>">
						Nomes
					</label>
				</div>
			</div>
			<input class="form-control" id="quantidade_campos<?php echo $i ?>"
				name="componentes[<?php echo $i ?>][quantidade_campos]" type="number"
				placeholder="Digite a quantidade de campos" min="0"
				onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)" />
			<select id="nomes_campos<?php echo $i ?>" name="componentes[<?php echo $i ?>][nomes_campos][]"
				multiple data-role="tagsinput" placeholder="Digite o nome dos campos" class="d-none"
				onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)"></select>
		</div>
		<div class="col-3">
			<div class="d-inline-block">
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" id="modo_preenchimento_arquivo_quantidade<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][modo_preenchimento_arquivos_referenciados]" value="q"
						checked onchange="toggleQuantidadeOuNomeCamposArquivosComponente(this); calcularComplexidadeEValorComponenteFuncionalidade(this)" />
					<label class="custom-control-label" for="modo_preenchimento_arquivo_quantidade<?php echo $i ?>">
						Quantidade
					</label>
				</div>
			</div>
			<div class="d-inline-block">
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" id="modo_preenchimento_arquivo_nome<?php echo $i ?>"
						name="componentes[<?php echo $i ?>][modo_preenchimento_arquivos_referenciados]" value="n"
						onchange="toggleQuantidadeOuNomeCamposArquivosComponente(this); calcularComplexidadeEValorComponenteFuncionalidade(this)" />
					<label class="custom-control-label" for="modo_preenchimento_arquivo_nome<?php echo $i ?>">
						Nomes
					</label>
				</div>
			</div>
			<input class="form-control" id="quantidade_arquivos<?php echo $i ?>"
				name="componentes[<?php echo $i ?>][quantidade_arquivos_referenciados]" type="number"
				placeholder="Digite a quantidade de arquivos" min="0"
				onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)" />
			<select id="nomes_arquivos<?php echo $i ?>" name="componentes[<?php echo $i ?>][nomes_arquivos_referenciados][]"
				multiple data-role="tagsinput" placeholder="Digite o nome dos arquivos" class="d-none"
				onchange="calcularComplexidadeEValorComponenteFuncionalidade(this)"></select>
		</div>
		<div class="col complexidade">---</div>
		<div class="col valor">---</div>
	</div>
<?php } ?>
<div class="row">
	<div class="col-3">&nbsp;</div>
	<div class="col-3">&nbsp;</div>
	<div class="col-3">&nbsp;</div>
	<div class="col" style="border: 1px solid #dee2e6; text-align: right"><b>Total:</b></div>
	<div class="col" style="border: 1px solid #dee2e6"><b id="valor_total">---</b></div>
</div>