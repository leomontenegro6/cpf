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
} else {
	$acao = 'cadastrar';
	
	$id = $nome = $login = '';
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