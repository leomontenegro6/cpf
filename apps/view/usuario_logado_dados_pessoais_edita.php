<?php
require_once('cabecalho.php');
require_once('menu.php');

$usuario = new usuario();
$funcaoUsuario = new funcaoUsuario();

$id_usuario_sessao = $_SESSION['iduser'];

$usuario_row = $usuario->getByEdicao($id_usuario_sessao);

$nome = $usuario_row['nome'];
$id_funcao_usuario = $usuario_row['funcao'];
$valor_hora_trabalhada = funcoes::encodeMonetario($usuario_row['valor_hora_trabalhada'], 1);

$funcaoUsuario_rs = $funcaoUsuario->getAll();
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
							<div class="input-group-prepend" style="margin-right: -27px">
								<span class="input-group-text">R$</span>
							</div>
							<label class="has-float-label">
								<input class="form-control" id="valor_hora_trabalhada" name="valor_hora_trabalhada" required
									type="tel" value="<?php echo $valor_hora_trabalhada ?>" placeholder="Digite o valor"
									data-mascara="#.##0,00" data-reverso="true" data-minimo="0" data-maximo="1000" />
								<span>Valor da Hora Trabalhada</span>
							</label>
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