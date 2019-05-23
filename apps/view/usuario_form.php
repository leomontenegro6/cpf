<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$usuario = new usuario();
$funcaoUsuario = new funcaoUsuario();

if(isset($_POST['id'])){
	$acao = 'editar';
	
	$id = $_POST['id'];

	$usuario_row = $usuario->get($id);

	$nome = $usuario_row['nome'];
	$login = $usuario_row['login'];
	$id_funcao_usuario = $usuario_row['funcao'];
	$valor_hora_trabalhada = funcoes::encodeMonetario($usuario_row['valor_hora_trabalhada'], 1);
	$admin = ($usuario_row['admin'] == '1');
} else {
	$acao = 'cadastrar';
	
	$id = $nome = $login = $id_funcao_usuario = $valor_hora_trabalhada = '';
	$admin = false;
}

$funcaoUsuario_rs = $funcaoUsuario->getAll();
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
			<label class="form-group has-float-label">
				<select id="funcao" name="funcao" class="select form-control" required>
					<option value="">Escolha uma função</option>
					<?php foreach($funcaoUsuario_rs as $funcaoUsuario_row){
						if($funcaoUsuario_row['id'] == $id_funcao_usuario){
							$selected = 'selected';
						} else {
							$selected = '';
						}
						?>
						<option value="<?php echo $funcaoUsuario_row['id'] ?>" <?php echo $selected ?>><?php echo $funcaoUsuario_row['descricao'] ?></option>
					<?php } ?>
				</select>
				<span>Função</span>
			</label>
			<div class="form-group input-group with-float-label">
				<div class="input-group-prepend">
					<span class="input-group-text">R$</span>
				</div>
				<label class="has-float-label">
					<input class="form-control" id="valor_hora_trabalhada" name="valor_hora_trabalhada" required
						type="tel" value="<?php echo $valor_hora_trabalhada ?>" placeholder="Digite o valor"
						data-mascara="#.##0,00" data-reverso="true" data-minimo="0" data-maximo="1000" />
					<span>Valor da Hora Trabalhada</span>
				</label>
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