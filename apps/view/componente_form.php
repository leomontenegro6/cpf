<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$tipoComponente = new tipoComponente();
$componente = new componente();
$campo = new campo();
$arquivoReferenciado = new arquivoReferenciado();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$componente_row = $componente->get($id);
	$campo_rs = $campo->getByComponente($id);
	$arquivoReferenciado_rs = $arquivoReferenciado->getByComponente($id);
	
	$id_funcionalidade = $componente_row['funcionalidade'];
	$id_modulo = $funcionalidade->getModulo($id_funcionalidade);
	$id_sistema = $modulo->getSistema($id_modulo);
	$id_tipo_componente = $componente_row['tipo_componente'];
	$ordem = $componente_row['ordem'];
	$possui_acoes = ($componente_row['possui_acoes'] == '1');
	$possui_mensagens = ($componente_row['possui_mensagens'] == '1');
	
	$complexidade_valor = $componente->calcularComplexidadeValorPF($id);
	$complexidade = funcoes::capitaliza($complexidade_valor['complexidade']);
	$valor_pf = $complexidade_valor['valor'];
} else {
	$acao = 'cadastrar';
	
	$id = $id_funcionalidade = $id_modulo = $id_sistema = '';
	$id_tipo_componente = $ordem = '';
	$possui_acoes = $possui_mensagens = false;
	$complexidade = $valor_pf = '';
	$campo_rs = $arquivoReferenciado_rs = array();
}

$tipoComponenteTipoDado_rs = $tipoComponente->getForSelect();
?>
<div class="card <?php if($acao == 'editar') echo 'card-success'; else echo 'card-primary'; ?>">
	<div class="card-header">
		<h3 class="card-title"><?php echo funcoes::formatarTituloFormularioPorAcao($acao, 'm') ?> Componente</h3>
	</div>

	<form action="componente_crud.php" method="POST" id="form_sistema" name="form_sistema"
		class="needs-validation" onsubmit="return validaForm(this)" data-ajax='true' novalidate>
		<div class="card-body">
			
			<div class="row">
				<div class="col-4">
					<label class="form-group has-float-label">
						<select id="sistema" name="sistema" class="select form-control" required
							data-pagina="sistema_autocomplete.php" data-limite-caracteres="0"
							onchange="select.limpar( gE('modulo') ); select.limpar( gE('funcionalidade') )">
							<option value="">Escolha um sistema</option>
							<?php if(is_numeric($id_sistema)){ ?>
								<option value="<?php echo $id_sistema ?>" selected><?php echo $sistema->getNome($id_sistema, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Sistema</span>
					</label>
				</div>
				<div class="col-4">
					<label class="form-group has-float-label">
						<select id="modulo" name="modulo" class="select form-control" required
							data-pagina="modulo_autocomplete.php?sistema={sistema}" data-limite-caracteres="0"
							onchange="select.limpar( gE('funcionalidade') ); definirModuloSistema(this, 'modulo', 'sistema', function(){ $('#form').submit() })">
							<option value="">Escolha um módulo</option>
							<?php if(is_numeric($id_modulo)){ ?>
								<option value="<?php echo $id_modulo ?>" selected><?php echo $modulo->getNome($id_modulo, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Módulo</span>
					</label>
				</div>
				<div class="col-4">
					<label class="form-group has-float-label">
						<select id="funcionalidade" name="funcionalidade" class="select form-control" required
							data-pagina="funcionalidade_autocomplete.php?sistema={sistema}&modulo={modulo}" data-limite-caracteres="0"
							onchange="definirModuloSistema(this, 'modulo', 'sistema', function(){ $('#form').submit() })">
							<option value="">Escolha uma funcionalidade</option>
							<?php if(is_numeric($id_funcionalidade)){ ?>
								<option value="<?php echo $id_funcionalidade ?>" selected><?php echo $funcionalidade->getNome($id_funcionalidade, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Funcionalidade</span>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<label class="form-group has-float-label">
						<select id="tipo_componente" name="tipo_componente" class="select form-control" required
							onchange="calcularComplexidadeEValorComponente(this)">
							<option value="">Escolha um tipo de componente</option>
							<?php foreach($tipoComponenteTipoDado_rs as $tipo_dado=>$tipoComponente_rs){ ?>
								<optgroup label="<?php echo $tipo_dado ?>">
									<?php foreach($tipoComponente_rs as $tipoComponente_row){
										if($tipoComponente_row['id'] == $id_tipo_componente){
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
						<span>Tipo de Componente</span>
					</label>
				</div>
				<div class="col-2">
					<div class="form-group has-float-label">
						<input class="form-control" id="ordem" name="ordem" type="number"
							placeholder="Digite a ordem" value="<?php echo $ordem ?>" min="0"
							onchange="calcularComplexidadeEValorComponente(this)">
						<label for="ordem">Ordem</label>
					</div>
				</div>
				<div class="col-3">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="possui_acoes"
							name="possui_acoes" value="true" <?php if($possui_acoes) echo 'checked' ?>
							onchange="calcularComplexidadeEValorComponente(this)" />
						<label class="custom-control-label" for="possui_acoes">Possui Ações</label>
					</div>
				</div>
				<div class="col-3">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="possui_mensagens"
							name="possui_mensagens" value="true" <?php if($possui_mensagens) echo 'checked' ?>
							onchange="calcularComplexidadeEValorComponente(this)" />
						<label class="custom-control-label" for="possui_mensagens">Possui Mensagens</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					<label class="form-group">
						<div class="campo_multiplo" data-aceita-valores-duplicados='true' data-onadd="calcularComplexidadeEValorComponente(this)">
							<table class="conteiner">
								<thead>
									<tr>
										<th>Campos</th>
										<td style="width: 5%">
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="corpo"></td>
										<td class="acoes" valign="top">
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
								<input type="text" data-nome="campo" id="campo_{iterador}"
									name="campos[]" class="form-control" />
							</div>
							<div class="valores">
								<?php
								$campos = array();
								foreach($campo_rs as $campo_row){
									$campos[] = array(
										'campo'=>$campo_row['nome']
									);
								}
								echo json_encode($campos);
								?>
							</div>
						</div>
					</label>
				</div>
				<div class="col-6">
					<label class="form-group has-float-label">
						<div class="campo_multiplo" data-aceita-valores-duplicados='true' data-onadd="calcularComplexidadeEValorComponente(this)">
							<table class="conteiner">
								<thead>
									<tr>
										<th>Arquivos Referenciados</th>
										<td style="width: 5%">
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="corpo"></td>
										<td class="acoes" valign="top">
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
								<input type="text" data-nome="arquivo_referenciado" id="arquivo_referenciado_{iterador}"
									name="arquivos_referenciados[]" class="form-control" />
							</div>
							<div class="valores">
								<?php
								$arquivo_referenciado = array();
								foreach($arquivoReferenciado_rs as $arquivoReferenciado_row){
									$arquivo_referenciado[] = array(
										'arquivo_referenciado'=>$arquivoReferenciado_row['nome']
									);
								}
								echo json_encode($arquivo_referenciado);
								?>
							</div>
						</div>
					</label>
					
					<div class="row">
						<div class="col-12">
							<div class="form-group has-float-label">
								<input class="form-control" id="complexidade" name="complexidade" type="text"
									placeholder="Complexidade" value="<?php echo $complexidade ?>" disabled>
								<label for="complexidade">Complexidade</label>
							</div>
						</div>
						<div class="col-12">
							<div class="form-group has-float-label">
								<input class="form-control" id="valor_pf" name="valor_pf" type="text"
									placeholder="Valor (PF)" value="<?php echo $valor_pf ?>" disabled>
								<label for="complexidade">Valor (PF)</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card-footer text-center">
			<input type="hidden" name="acao" value="<?php echo $acao ?>" />
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			
			<button type="submit" name="Submit" class="btn <?php if($acao == 'editar') echo 'btn-success'; else echo 'btn-primary'; ?>">
				<i class="fas fa-save"></i>
				Salvar
			</button>
		</div>
	</form>
</div>