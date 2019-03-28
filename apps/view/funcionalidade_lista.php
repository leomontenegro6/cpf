<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$tipoFuncionalidade = new tipoFuncionalidade();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
$tipo_funcionalidade_lista = (isset($_GET['tipo_funcionalidade_lista'])) ? ($_GET['tipo_funcionalidade_lista']) : ('');

$checkExibirFiltros = (!empty($sistema_lista) || !empty($modulo_lista) || !empty($tipo_funcionalidade_lista));

$tipoFuncionalidade_rs = $tipoFuncionalidade->getAll();
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Funcionalidades</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Cadastros</a></li>
					<li class="breadcrumb-item active">Funcionalidades</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row <?php if(!$checkExibirFiltros) echo 'collapse' ?>">
		<div class="col-10 mx-auto">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Filtros de Busca</h3>
				</div>

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return tabela.pesquisar(this)" data-tabela="tabela">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="sistema_lista" name="sistema_lista" class="select form-control"
										data-pagina="sistema_autocomplete.php" data-limite-caracteres="0"
										onchange="select.limpar( gE('modulo_lista') )">
										<option value="">Todos</option>
										<?php if(is_numeric($sistema_lista)){ ?>
											<option value="<?php echo $sistema_lista ?>" selected><?php echo $sistema->getNome($sistema_lista, 'n') ?></option>
										<?php } ?>
									</select>
									<span>Sistema</span>
								</label>
							</div>
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="modulo_lista" name="modulo_lista" class="select form-control"
										data-pagina="modulo_autocomplete.php?sistema={sistema_lista}" data-limite-caracteres="0"
										onchange="definirModuloSistema(this, 'modulo_lista', 'sistema_lista', function(){ $('#form_lista').submit() })">
										<option value="">Todos</option>
										<?php if(is_numeric($modulo_lista)){ ?>
											<option value="<?php echo $modulo_lista ?>" selected><?php echo $modulo->getNome($modulo_lista, 'n') ?></option>
										<?php } ?>
									</select>
									<span>Módulo</span>
								</label>
							</div>
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="tipo_funcionalidade_lista" name="tipo_funcionalidade_lista" class="select form-control"
										onchange="$(this).closest('form').submit()">
										<option value="">Todos</option>
										<?php foreach($tipoFuncionalidade_rs as $tipoFuncionalidade_row){
											if($tipoFuncionalidade_row['id'] == $tipo_funcionalidade_lista){
												$selected = 'selected';
											} else {
												$selected = '';
											}
											?>
											<option value="<?php echo $tipoFuncionalidade_row['id'] ?>" <?php echo $selected ?>><?php echo $tipoFuncionalidade_row['descricao'] ?></option>
										<?php } ?>
									</select>
									<span>Tipo de Funcionalidade</span>
								</label>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<?php
			// Tabela de listagem
			$parametros = "sistema_lista=$sistema_lista";
			$parametros .= "&modulo_lista=$modulo_lista";
			$parametros .= "&tipo_funcionalidade_lista=$tipo_funcionalidade_lista";
			tabela::instanciar('funcionalidade_tabela.php', $parametros, false, 'tabela');
			?>
		</div>
	</div>
</section>
<?php
include('rodape.php');
?>