<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$feriado = new feriado();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$feriadoCustomizado_row = $feriado->get($id);

	$nome = $feriadoCustomizado_row['nome'];
	$data = funcoes::encodeData($feriadoCustomizado_row['data']);
} else {
	$acao = 'cadastrar';
	
	$ano = (isset($_GET['ano'])) ? ($_GET['ano']) : (date('Y'));
	$id = $nome = '';
	$data = date("d/m/$ano");
}
?>
<div class="card <?php if($acao == 'editar') echo 'card-success'; else echo 'card-primary'; ?>">
	<div class="card-header">
		<h3 class="card-title"><?php echo funcoes::formatarTituloFormularioPorAcao($acao, 'm') ?> Feriado Customizado</h3>
	</div>

	<form action="feriado_customizado_crud.php" method="POST" id="form_feriado" name="form_feriado"
		class="needs-validation" onsubmit="return validaFormFeriadoCustomizado(this)" data-ajax='true' novalidate>
		<div class="card-body">
			<?php if($acao == 'editar'){ ?>
				<div class="form-group has-float-label">
					<input class="form-control" id="nome" name="nome" type="text" required
						placeholder="Digite o nome" value="<?php echo $nome ?>">
					<label for="nome">Nome</label>
				</div>
				<div class="form-group has-float-label">
					<input class="form-control calendario" id="data"
						name="data" type="text" required
						value="<?php echo $data ?>">
					<label for="data">Data</label>
				</div>
			<?php } else { ?>
				<div class="row">
					<div class="col-md-9">
						<div class="form-group has-float-label">
							<input class="form-control" id="nome" name="nome" type="text" required
								placeholder="Digite o nome" value="<?php echo $nome ?>">
							<label for="nome">Nome</label>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="intervalo"
									name="intervalo" value="true" onchange="toggleCampoDataParaIntervalo()" />
								<label class="custom-control-label" for="intervalo">Intervalo</label>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group has-float-label">
					<input class="form-control calendario" id="data"
						name="data" type="text" required
						value="<?php echo $data ?>">
					<label for="data">Data</label>
				</div>
			<?php } ?>
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