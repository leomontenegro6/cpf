<?php
require_once('cabecalho.php');
require_once('menu.php');

$usuario = new usuario();

$id_usuario_sessao = $_SESSION['iduser'];

$usuario_row = $usuario->getByEdicao($id_usuario_sessao);

$nome = $usuario_row['nome'];
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Alterar Senha</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Menu do Usuário</a></li>
					<li class="breadcrumb-item active">Alterar Senha</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div class="row">
		<div class="col-6 mx-auto">
			<div class="card card-success">
				<div class="card-header">
					<h3 class="card-title">Alterar Senha</h3>
				</div>
				
				<form action="usuario_crud.php" method="POST" id="form" name="form" novalidate data-ajax="true" onsubmit="return validaFormAlterarSenha(this)">
					<div class="card-body">
						<div class="form-group">
							<label for="nome" class="col-12 control-label">Nome:</label>
							<div class="col-12"><?php echo $nome ?></div>
						</div>
						<div class="form-group">
							<label for="senha" class="col-12 control-label">Senha*:</label>
							<div class="col-12">
								<input type='password' id="senha" name='senha' class="form-control" required />
								<input type="hidden" id="forca_senha" name="forca_senha" />
								<input type="hidden" id="entropia_senha" name="entropia_senha" />
							</div>
						</div>
						<div class="form-group">
							<label for="confirmar_senha" class="col-12 control-label">Confirmar Senha*:</label>
							<div class="col-12">
								<input type='password' id="confirmar_senha" name='confirmar_senha' class="form-control" required />
							</div>
						</div>
					</div>
					

					<div class="card-footer text-center">
						<input type="hidden" name="acao" value="redefinir_senha" />
						<input type="hidden" name="endereco_anterior" value="<?php echo $endereco_anterior ?>" />
						
						<div class="btn-group">
							<button type="button" class="btn btn-warning" onclick="history.back()">
								<i class="fas fa-arrow-left"></i>
								Voltar
							</button>
							<button type="submit" name="Submit" class="btn btn-success">
								<i class="fas fa-save"></i>
								Salvar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<?php
require_once('rodape.php');