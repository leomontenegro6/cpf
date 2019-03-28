<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$componente = new componente();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
?>
<section class="content-header">
	<div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1>Contagem de Pontos</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Relatórios</a></li>
					<li class="breadcrumb-item active">Contagem de Pontos</li>
				</ol>
			</div>
        </div>
	</div>
</section>

<section id="corpo" class="content">
	<div id="filtros" class="row">
		<div class="col-10 mx-auto">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Filtros de Busca</h3>
				</div>

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return validaForm(this)">
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
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
							<div class="col-md-6">
								<label class="form-group has-float-label">
									<select id="modulo_lista" name="modulo_lista" class="select form-control"
										data-pagina="modulo_autocomplete.php?sistema={sistema_lista}" data-limite-caracteres="0"
										onchange="definirModuloSistema(this, 'modulo_lista', 'sistema_lista')">
										<option value="">Todos</option>
										<?php if(is_numeric($modulo_lista)){ ?>
											<option value="<?php echo $modulo_lista ?>" selected><?php echo $modulo->getNome($modulo_lista, 'n') ?></option>
										<?php } ?>
									</select>
									<span>Módulo</span>
								</label>
							</div>
						</div>
					</div>
					
					<div class="card-footer text-center">
						<div class="btn-group">
							<button type="button" class="btn btn-warning" onclick="history.back()">
								<i class="fas fa-arrow-left"></i> Voltar
							</button>
							<button type="submit" name="Submit" class="btn btn-info">
								<i class="fas fa-search"></i>
								Pesquisar
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php if(isset($_GET['Submit'])){
		$componente_rs = $componente->getByPlanilhaContagemPontos($sistema_lista, $modulo_lista);
		?>
		<div class="card">
			<div class="card-header">
				<button type="button" class="btn btn-primary float-right" onclick="">
					<i class="fas fa-file-excel"></i> Gerar Planilha
				</button>
			</div>
			<div class="card-body">
				<div class="tabelaaberta" data-paginacao="false">
					<table>
						<thead>
							<tr>
								<th rowspan="2">Ordem</th>
								<th rowspan="2">Sistema</th>
								<th rowspan="2">Módulo</th>
								<th rowspan="2">Funcionalidade</th>
								<th rowspan="2">Componente</th>
								<th rowspan="2">Tipo Funcional</th>
								<td colspan="2">Quantidades</td>
								<th rowspan="2">Complexidade</th>
								<th rowspan="2">Valor (PF)</th>
							</tr>
							<tr>
								<th>Tipos de Dados</th>
								<th>Arquivos Referenciados</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$valor_total_pf = 0;
							foreach($componente_rs as $componente_row){
								$valor_total_pf += $componente_row['valor_pf'];
								?>
								<tr>
									<td><?php echo $componente_row['ordem'] ?></td>
									<td><?php echo $componente_row['sistema'] ?></td>
									<td><?php echo $componente_row['modulo'] ?></td>
									<td><?php echo $componente_row['funcionalidade'] ?></td>
									<td><?php echo $componente_row['componente'] ?></td>
									<td><?php echo $componente_row['tipo_funcional'] ?></td>
									<td><?php echo $componente_row['quantidade_tipos_dados'] ?></td>
									<td><?php echo $componente_row['quantidade_arquivos_referenciados'] ?></td>
									<td><?php echo $componente_row['complexidade'] ?></td>
									<td><?php echo $componente_row['valor_pf'] ?></td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>							
								<th colspan="8">&nbsp;</th>
								<th>Total:</th>
								<th><?php echo $valor_total_pf ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>
</section>
<?php
include('rodape.php');
?>