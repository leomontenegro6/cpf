<?php
include('cabecalho.php');
include('menu.php');

$nome_lista = (isset($_GET['nome_lista'])) ? ($_GET['nome_lista']) : ('');
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Usuários</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Cadastros</a></li>
					<li class="breadcrumb-item active">Usuários</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div class="row">
		<div class="col-12">
			<?php
			// Tabela de listagem
			tabela::instanciar('usuario_tabela.php', "nome_lista=$nome_lista", false, 'tabela');
			?>
		</div>
	</div>
</section>
<?php
include('rodape.php');
?>