<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$tipoFuncionalidade = new tipoFuncionalidade();
$componente = new componente();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$funcionalidade_row = $funcionalidade->get($id);
	
	$id_modulo = $funcionalidade_row['modulo'];
	$id_sistema = $modulo->getSistema($id_modulo);
	$nome = $funcionalidade_row['nome'];
	$id_tipo_funcionalidade = $funcionalidade_row['tipo_funcionalidade'];
	$ordem = $funcionalidade_row['ordem'];
} else {
	$acao = 'cadastrar';
	
	$id = $id_modulo = $id_sistema = $nome = $id_tipo_funcionalidade = $ordem = '';
}

$tipoFuncionalidade_rs = $tipoFuncionalidade->getAll();
?>
<div class="card <?php if($acao == 'editar') echo 'card-success'; else echo 'card-primary'; ?>">
	<div class="card-header">
		<h3 class="card-title"><?php echo funcoes::formatarTituloFormularioPorAcao($acao, 'f') ?> Funcionalidade</h3>
	</div>

	<form action="funcionalidade_crud.php" method="POST" id="form_sistema" name="form_sistema"
		class="needs-validation" onsubmit="return validaForm(this)" data-ajax='true' novalidate>
		<div class="card-body">
			<div class="row">
				<div class="col-6">
					<label class="form-group has-float-label">
						<select id="sistema" name="funcionalidade[sistema]" class="select form-control"
							data-pagina="sistema_autocomplete.php" data-limite-caracteres="0"
							required onchange="select.limpar( gE('modulo') )">
							<option value="">Escolha um sistema</option>
							<?php if(is_numeric($id_sistema)){ ?>
								<option value="<?php echo $id_sistema ?>" selected><?php echo $sistema->getNome($id_sistema, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Sistema</span>
					</label>
				</div>
				<div class="col-6">
					<label class="form-group has-float-label">
						<select id="modulo" name="funcionalidade[modulo]" class="select form-control"
							data-pagina="modulo_autocomplete.php?sistema={sistema}" data-limite-caracteres="0"
							required onchange="definirModuloSistema(this, 'modulo', 'sistema')">
							<option value="">Escolha um módulo</option>
							<?php if(is_numeric($id_modulo)){ ?>
								<option value="<?php echo $id_modulo ?>" selected><?php echo $modulo->getNome($id_modulo, 'n') ?></option>
							<?php } ?>
						</select>
						<span>Módulo</span>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-6">
					<div class="form-group has-float-label">
						<input class="form-control" id="nome" name="funcionalidade[nome]" type="text" required
							placeholder="Digite o nome da funcionalidade" value="<?php echo $nome ?>">
						<label for="nome">Nome</label>
					</div>
				</div>
				<div class="col-3">
					<div class="form-group has-float-label">
						<input class="form-control" id="ordem" name="funcionalidade[ordem]" type="number"
							placeholder="Digite a ordem" value="<?php echo $ordem ?>" min="0">
						<label for="ordem">Ordem</label>
					</div>
				</div>
				<div class="col-3">
					<label class="form-group has-float-label">
						<select id="tipo_funcionalidade" name="funcionalidade[tipo_funcionalidade]" class="select form-control"
							required <?php if($acao == 'cadastrar'){ ?>onchange="carregarComponentesByTipoFuncionalidade(this)"<?php } ?>>
							<option value="">Escolha um tipo de funcionalidade</option>
							<?php foreach($tipoFuncionalidade_rs as $tipoFuncionalidade_row){
								if($tipoFuncionalidade_row['id'] == $id_tipo_funcionalidade){
									$selected = 'selected';
								} else {
									$selected = '';
								}
								?>
								<option value="<?php echo $tipoFuncionalidade_row['id'] ?>" <?php echo $selected ?>><?php echo $tipoFuncionalidade_row['descricao'] ?></option>
							<?php } ?>
						</select>
						<span>Tipo de Funcionalidade</span>
					</label>
				</div>
			</div>
			<div id="componentes" <?php if($acao == 'cadastrar') echo 'class="d-none"'; ?>></div>
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