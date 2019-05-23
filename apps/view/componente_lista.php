<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ($_SESSION['sistema_sessao']);
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ($_SESSION['modulo_sessao']);
$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ($_SESSION['funcionalidade_sessao']);

$checkExibirFiltros = (!empty($sistema_lista) || !empty($modulo_lista) || !empty($funcionalidade_lista));
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Componentes</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Cadastros</a></li>
					<li class="breadcrumb-item active">Componentes</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row <?php if(!$checkExibirFiltros) echo 'collapse' ?>">
		<div class="col-sm-10 col-xs-12 mx-auto">
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
										onchange="select.limpar( gE('modulo_lista') ); select.limpar( gE('funcionalidade_lista') )">
										<option value="">Todos</option>
										<?php if(is_numeric($sistema_lista)){ ?>
											<option value="<?php echo $sistema_lista ?>" selected><?php echo $sistema->getDescricao($sistema_lista) ?></option>
										<?php } ?>
									</select>
									<span>Sistema</span>
								</label>
							</div>
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="modulo_lista" name="modulo_lista" class="select form-control"
										data-pagina="modulo_autocomplete.php?sistema={sistema_lista}" data-limite-caracteres="0"
										onchange="select.limpar( gE('funcionalidade_lista') ); definirModuloSistema(this, 'modulo_lista', 'sistema_lista', function(){ $('#form_lista').submit() })">
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
									<select id="funcionalidade_lista" name="funcionalidade_lista" class="select form-control"
										data-pagina="funcionalidade_autocomplete.php?sistema={sistema_lista}&modulo={modulo_lista}" data-limite-caracteres="0"
										onchange="definirModuloSistema(this, 'modulo_lista', 'sistema_lista', function(){ $('#form_lista').submit() })">
										<option value="">Todas</option>
										<?php if(is_numeric($funcionalidade_lista)){ ?>
											<option value="<?php echo $funcionalidade_lista ?>" selected><?php echo $funcionalidade->getNome($funcionalidade_lista, 'n') ?></option>
										<?php } ?>
									</select>
									<span>Funcionalidade</span>
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
			$parametros .= "&funcionalidade_lista=$funcionalidade_lista";
			tabela::instanciar('componente_tabela.php', $parametros, false, 'tabela');
			?>
		</div>
	</div>
</section>
<?php
include('rodape.php');
?>