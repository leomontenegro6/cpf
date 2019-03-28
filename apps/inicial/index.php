<?php
include('cabecalho.php');
?>
<div class="login-box">
	<div class="login-logo">
		CPF - Contador de Pontos de Função
	</div>
	<!-- /.login-logo -->
	<div class="card">
		<div class="card-body login-card-body">
			<p class="login-box-msg">LOGIN</p>

			<form action="login.php" method="post">
				<div class="input-group mb-3">
					<input type="text" class="form-control"name="login" placeholder="Usuário" autofocus />
					<div class="input-group-append">
						<span class="fas fa-user-circle input-group-text"></span>
					</div>
				</div>
				<div class="input-group mb-3">
					<input type="password" class="form-control" name="senha" placeholder="Senha">
					<div class="input-group-append">
						<span class="fas fa-lock input-group-text"></span>
					</div>
				</div>
				<div class="row">
					<div class="col-6 offset-3">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Log in</button>
					</div>
					<!-- /.col -->
				</div>
			</form>
		</div>
		<!-- /.login-card-body -->
	</div>
</div>
<!-- /.login-box -->
<?php
include('rodape.php');
?>