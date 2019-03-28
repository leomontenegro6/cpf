<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$sistema_row = $sistema->get($id);
	$modulo_rs = $modulo->getBySistema($id);
	$total_modulos = count($modulo_rs);

	$nome = $sistema_row['nome'];
	$sigla = $sistema_row['sigla'];
} else {
	$acao = 'cadastrar';
	
	$id = $nome = $sigla = '';
	
	$modulo_rs = array();
	$total_modulos = 0;
}
?>
<div class="card <?php if($acao == 'editar') echo 'card-success'; else echo 'card-primary'; ?>">
	<div class="card-header">
		<h3 class="card-title"><?php echo funcoes::formatarTituloFormularioPorAcao($acao) ?> Sistema</h3>
	</div>

	<form action="sistema_crud.php" method="POST" id="form_sistema" name="form_sistema"
		class="needs-validation" onsubmit="return validaForm(this)" data-ajax='true' novalidate>
		<div class="card-body">
			<div class="form-group has-float-label">
				<input class="form-control" id="nome" name="nome" type="text" required
					placeholder="Digite o nome" value="<?php echo $nome ?>">
				<label for="nome">Nome</label>
			</div>
			<div class="form-group has-float-label">
				<input class="form-control" id="sigla" name="sigla" type="text" required
					placeholder="Digite a sigla" value="<?php echo $sigla ?>">
				<label for="sigla">Sigla</label>
			</div>
			<div class="form-group">
				<table data-iterador="<?php echo $total_modulos ?>">
					<thead>
						<tr>
							<th>Módulos</th>
							<td style="width: 5%">
								<button type="button" class="btn btn-default" title="Adicionar módulo"
									onclick="adicionarModuloSistema(this)">
									<i class="fas fa-plus" style="margin-right: 0"></i>
								</button>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php
							if($total_modulos == 0) {
								$modulo_rs = array(
									0 => array(
										'id' => '',
										'nome' => ''
									)
								);
							}

							foreach ($modulo_rs as $i => $modulo_row) {
								$_GET['i'] = $i;
								include('sistema_modulo_preenche.php');
							}
							?>
					</tbody>
				</table>
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