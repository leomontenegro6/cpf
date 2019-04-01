<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$usuario = new usuario();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$usuario_row = $usuario->get($id);

	$nome = $usuario_row['nome'];
	$login = $usuario_row['login'];
	$indice_produtividade = $usuario_row['indice_produtividade'];
	$admin = ($usuario_row['admin'] == '1');
} else {
	$acao = 'cadastrar';
	
	$id = $nome = $login = $indice_produtividade = '';
	$admin = false;
}
?>
<div class="card <?php if($acao == 'editar') echo 'card-success'; else echo 'card-primary'; ?>">
	<div class="card-header">
		<h3 class="card-title"><?php echo funcoes::formatarTituloFormularioPorAcao($acao) ?> Usuário</h3>
	</div>

	<form action="usuario_crud.php" method="POST" id="form_usuario" name="form_usuario"
		class="needs-validation" onsubmit="return validaForm(this)" data-ajax='true' novalidate>
		<div class="card-body">
			<div class="form-group has-float-label">
				<input class="form-control" id="nome" name="nome" type="text" required
					placeholder="Digite o nome" value="<?php echo $nome ?>">
				<label for="nome">Nome</label>
			</div>
			<div class="form-group has-float-label">
				<input class="form-control" id="login" name="login" type="text" required autocapitalize="off"
					placeholder="Digite o login" value="<?php echo $login ?>">
				<label for="login">Login</label>
			</div>
			<div class="form-group has-float-label">
				<input class="form-control" id="indice_produtividade" name="indice_produtividade" type="number"
					min="0.4" max="1.0" step="0.1" required placeholder="Digite um valor entre 0.4 e 1"
					value="<?php echo $indice_produtividade ?>">
				<label for="indice_produtividade">Índice Médio de Produtividade</label>
			</div>
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="admin"
						name="admin" value="true" <?php if($admin) echo 'checked' ?> />
					<label class="custom-control-label" for="admin">Administrador</label>
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