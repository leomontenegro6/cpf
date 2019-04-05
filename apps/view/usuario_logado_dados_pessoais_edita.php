<?php
require_once('cabecalho.php');
require_once('menu.php');

$usuario = new usuario();

$id_usuario_sessao = $_SESSION['iduser'];

$usuario_row = $usuario->getByEdicao($id_usuario_sessao);

$nome = $usuario_row['nome'];
$indice_produtividade = $usuario_row['indice_produtividade'];
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Alterar Dados Pessoais</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Menu do Usuário</a></li>
					<li class="breadcrumb-item active">Alterar Dados Pessoais</li>
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
					<h3 class="card-title">Alterar Dados Pessoais</h3>
				</div>
				
				<form action="usuario_crud.php" method="POST" id="form" name="form" onsubmit="return validaForm(this)" novalidate>
					<div class="card-body">
						<div class="form-group has-float-label">
							<input class="form-control" id="nome" name="nome" type="text" required
								placeholder="Digite o nome" value="<?php echo $nome ?>">
							<label for="nome">Nome</label>
						</div>
						<div class="form-group input-group with-float-label">
							<label class="has-float-label">
								<input class="form-control" id="indice_produtividade" name="indice_produtividade"
									type="number" value="<?php echo $indice_produtividade ?>" min="0.4" max="1" step="0.1"
									placeholder="Digite um valor entre 0,4 e 1" />
								<span>Índice de Médio Produtividade</span>
							</label>
							<div class="input-group-append">
								<span class="input-group-text">Horas / PF</span>
							</div>
						</div>
					</div>
					
					<div class="card-footer text-center">
						<input type="hidden" name="acao" value="alterar_dados_pessoais" />
						<input type="hidden" name="endereco_anterior" value="<?php echo $endereco_anterior ?>" />
						
						<div class="btn-group">
							<button type="button" class="btn btn-warning" onclick="history.back()">
								<i class="fas fa-arrow-left"></i> Voltar
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