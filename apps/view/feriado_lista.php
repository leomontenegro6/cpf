<?php
include('cabecalho.php');
include('menu.php');

if(isset($_GET['ano_lista']) && is_numeric($_GET['ano_lista'])){
	$ano_lista = $_GET['ano_lista'];
} else {
	$ano_lista = date('Y');
}
$customizado_lista = (isset($_GET['customizado_lista'])) ? ($_GET['customizado_lista']) : ('');

$anos = funcoes::getAnosForSelect();
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Feriados</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Cadastros</a></li>
					<li class="breadcrumb-item active">Feriados</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row collapse show">
		<div class="col-sm-10 col-xs-12 mx-auto">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Filtros de Busca</h3>
				</div>

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return pesquisarTabelaFeriados(this)">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="ano_lista" name="ano_lista" class="select form-control"
										required onchange="$(this).closest('form').submit()">
										<?php foreach($anos as $ano){
											if($ano == $ano_lista){
												$selected = 'selected';
											} else {
												$selected = '';
											}
											?> 
											<option value="<?php echo $ano ?>" <?php echo $selected ?>><?php echo $ano ?></option>
										<?php } ?>
									</select>
									<span>Ano</span>
								</label>
							</div>
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="customizado_lista" name="customizado_lista" class="select form-control"
										onchange="$(this).closest('form').submit()">
										<option value="">Indiferente</option>
										<option value="true" <?php if($customizado_lista == 'true') echo 'selected' ?>>Sim</option>
										<option value="false" <?php if($customizado_lista == 'false') echo 'selected' ?>>Não</option>
									</select>
									<span>Feriados Customizados</span>
								</label>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12" id="feriado_tabela">
			<?php
			$_GET['ajax'] = true;
			include('feriado_tabela.php');
			?>
		</div>
	</div>
</section>
<?php
include('rodape.php');
?>