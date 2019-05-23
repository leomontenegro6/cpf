<?php
require_once('cabecalho.php');
require_once('menu.php');

$usuario = new usuario();

$id_usuario_sessao = $_SESSION['iduser'];

$usuario_row = $usuario->getByEdicao($id_usuario_sessao);

$nome = $usuario_row['nome'];
$foto = $usuario_row['foto'];

if(file_exists($foto)){
	$tamanho_foto = filesize($foto) / 1024;
	
	$atributos_arquivo = "data-value='$foto' data-tamanho-arquivo='$tamanho_foto'";
} else {
	$atributos_arquivo = '';
}
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Editar Foto</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Menu do Usuário</a></li>
					<li class="breadcrumb-item active">Editar Foto</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div class="row">
		<div class="col-md-6 col-xs-12 mx-auto">
			<div class="card card-success">
				<div class="card-header">
					<h3 class="card-title">Editar Foto</h3>
				</div>
				
				<form action="usuario_crud.php" method="POST" id="form" name="form" onsubmit="return validaForm(this)" novalidate>
					<div class="card-body">
						<div class="form-group">
							<label for="nome" class="col-12 control-label">Nome:</label>
							<div class="col-12"><?php echo $nome ?></div>
						</div>
						<div class="form-group">
							<label for="foto" class="col-12 control-label">Foto:</label>
							<div class="col-12">
								<input type='file' id="foto" name='foto' class="fileuploader"
									data-formatos="jpg,jpeg,png,gif" data-tamanho-limite="2097152"
									<?php echo $atributos_arquivo ?> />
							</div>
						</div>
					</div>
					
					<div class="card-footer text-center">
						<input type="hidden" name="acao" value="editar_foto" />
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