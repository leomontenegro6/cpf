<?php
include('cabecalho.php');
include('menu.php');

$sistema = new sistema();
$modulo = new modulo();
$componente = new componente();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
$detalhar_campos_arquivos = (isset($_GET['detalhar_campos_arquivos']) && ($_GET['detalhar_campos_arquivos'] == 'true'));

if(is_numeric($sistema_lista)){
	$nome_sistema = $sistema->getNome($sistema_lista, 'n');
	$moduloSistema_rs = $modulo->getBySistema($sistema_lista);
} else {
	$nome_sistema = '';
	$moduloSistema_rs = array();
}
if(is_numeric($modulo_lista)){
	$nome_modulo = $modulo->getNome($modulo_lista, 'n');
	$checkModuloUnico = true;
} else {
	$nome_modulo = '';
	if(is_numeric($sistema_lista)){
		$checkModuloUnico = (count( $modulo->getBySistema($sistema_lista) ) == 1);
	} else {
		$checkModuloUnico = false;
	}
}
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

				<form method="GET" id="form_lista" name="form_lista" onsubmit="return validaForm(this)" novalidate>
					<div class="card-body">
						<div class="row">
							<div class="col-md-5">
								<label class="form-group has-float-label">
									<select id="sistema_lista" name="sistema_lista" class="select form-control"
										data-pagina="sistema_autocomplete.php" data-limite-caracteres="0" required
										onchange="select.limpar( gE('modulo_lista') )">
										<option value="">Escolha um sistema</option>
										<?php if(is_numeric($sistema_lista)){ ?>
											<option value="<?php echo $sistema_lista ?>" selected><?php echo $nome_sistema ?></option>
										<?php } ?>
									</select>
									<span>Sistema</span>
								</label>
							</div>
							<div class="col-md-4">
								<label class="form-group has-float-label">
									<select id="modulo_lista" name="modulo_lista" class="select form-control"
										data-pagina="modulo_autocomplete.php?sistema={sistema_lista}" data-limite-caracteres="0"
										onchange="definirModuloSistema(this, 'modulo_lista', 'sistema_lista')">
										<option value="">Todos</option>
										<?php if(is_numeric($modulo_lista)){ ?>
											<option value="<?php echo $modulo_lista ?>" selected><?php echo $nome_modulo ?></option>
										<?php } ?>
									</select>
									<span>Módulo</span>
								</label>
							</div>
							<div class="col-md-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="detalhar_campos_arquivos"
										name="detalhar_campos_arquivos" value="true"
										<?php if($detalhar_campos_arquivos) echo 'checked' ?> />
									<label class="custom-control-label" for="detalhar_campos_arquivos">Detalhar Campos / Arquivos</label>
								</div>
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
		$componente_rs = $componente->getByPlanilhaContagemPontos($sistema_lista, $modulo_lista, $detalhar_campos_arquivos);
		?>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title" style="font-weight: bold">
					<?php
					if($checkModuloUnico){
						if(empty($nome_modulo)) $nome_modulo = $moduloSistema_rs['0']['nome'];
						echo $nome_sistema . ' - Módulo ' . $nome_modulo;
					} else {
						echo $nome_sistema;
					}
					?>
					<br />
					Contabilização de Pontos de Função
				</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-success float-right"
						onclick="abrirPagina('rel_contagem_pontos_xls.php?sistema=<?php echo $sistema_lista ?>&modulo=<?php echo $modulo_lista ?>&detalhar_campos_arquivos=<?php echo ($detalhar_campos_arquivos) ? ('true') : ('false') ?>', '', '_blank');">
						<i class="fas fa-file-excel"></i> Gerar Planilha
					</button>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead>
							<?php if($detalhar_campos_arquivos){ ?>
								<tr>
									<?php if(!$checkModuloUnico){ ?>
										<th class="align-middle" style="background-color: #fafafa">Módulo</th>
									<?php } ?>
									<th class="align-middle" style="background-color: #fafafa">Funcionalidade</th>
									<th class="align-middle" style="background-color: #fafafa">Componente</th>
									<th class="align-middle" style="background-color: #fafafa">Tipo Funcional</th>
									<th class="align-middle" style="background-color: #fafafa">Tipos de Dados</th>
									<th class="align-middle" style="background-color: #fafafa">Arquivos Referenciados</th>
									<th class="align-middle" style="background-color: #fafafa">Complexidade</th>
									<th class="align-middle" style="background-color: #fafafa">Valor (PF)</th>
								</tr>
							<?php } else { ?>
								<tr>
									<?php if(!$checkModuloUnico){ ?>
										<th rowspan="2" class="align-middle" style="background-color: #fafafa">Módulo</th>
									<?php } ?>
									<th rowspan="2" class="align-middle" style="background-color: #fafafa">Funcionalidade</th>
									<th rowspan="2" class="align-middle" style="background-color: #fafafa">Componente</th>
									<th rowspan="2" class="align-middle" style="background-color: #fafafa">Tipo Funcional</th>
									<th colspan="2" class="text-center" style="background-color: #fafafa">Quantidades</th>
									<th rowspan="2" class="align-middle" style="background-color: #fafafa">Complexidade</th>
									<th rowspan="2" class="align-middle" style="background-color: #fafafa">Valor (PF)</th>
								</tr>
								<tr>
									<th style="background-color: #fafafa">Tipos de Dados</th>
									<th style="background-color: #fafafa">Arquivos Referenciados</th>
								</tr>
							<?php } ?>
						</thead>
						<tbody>
							<?php
							$valor_total_pf = 0;
							$linhas_esconder = array(
								'modulo' => 0,
								'funcionalidade' => 0
							);
							$colspan_rodape_branco = ($checkModuloUnico) ? (5) : (6);
							foreach($componente_rs as $componente_row){
								$id_tipo_componente = $componente_row['id_tipo_componente'];
								$rowspan_funcionalidade_modulo = $componente_row['rowspan_funcionalidade_modulo'];
								$rowspan_componente = $componente_row['rowspan_componente'];
								$valor_total_pf += $componente_row['valor_pf'];
								
								$campo_rs = $componente_row['campos'];
								$arquivoReferenciado_rs = $componente_row['arquivos_referenciados'];
								
								$quantidade_tipos_dados = $componente_row['quantidade_tipos_dados'];
								$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];
								
								if($detalhar_campos_arquivos){
									if($quantidade_tipos_dados >= $quantidade_arquivos_referenciados){
										$rowspan_campos_arquivos = $quantidade_tipos_dados;
									} else {
										$rowspan_campos_arquivos = $quantidade_arquivos_referenciados;
									}
								} else {
									$rowspan_campos_arquivos = 1;
								}
								
								if($id_tipo_componente == 2){
									$categoria_tipo_dado = 'Coluna';
								} else {
									$categoria_tipo_dado = 'Campo';
								}
								?>
								<tr>
									<?php if(!$checkModuloUnico){ ?>
										<?php
										if($linhas_esconder['modulo'] > 0){
											$linhas_esconder['modulo'] -= $rowspan_componente;
										} elseif($rowspan_funcionalidade_modulo > 1){
											$linhas_esconder['modulo'] = ($rowspan_funcionalidade_modulo - $rowspan_componente);
											$rowspan = $rowspan_funcionalidade_modulo;
											?>
											<td rowspan="<?php echo $rowspan ?>"><?php echo $componente_row['modulo'] ?></td>
										<?php } ?>
									<?php } ?>
									<?php
									if($linhas_esconder['funcionalidade'] > 0){
										$linhas_esconder['funcionalidade'] -= $rowspan_componente;
									} elseif($rowspan_funcionalidade_modulo > 1){
										$linhas_esconder['funcionalidade'] = ($rowspan_funcionalidade_modulo - $rowspan_componente);
										$rowspan = $rowspan_funcionalidade_modulo;
										?>
										<td rowspan="<?php echo $rowspan ?>"><?php echo $componente_row['funcionalidade'] ?></td>
									<?php } ?>
									<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['componente'] ?></td>
									<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['tipo_funcional'] ?></td>
									<?php if($detalhar_campos_arquivos){ ?>
										<td>
											<?php
											$nome_campo = $campo_rs[0]['nome'];
											if(substr($nome_campo, 0, 5) == 'Campo'){
												$nome_campo = trim( substr($nome_campo, 5) );
												echo "$categoria_tipo_dado $nome_campo";
											} else {
												echo "$categoria_tipo_dado \"$nome_campo\"";
											}
											?>
										</td>
										<td><?php echo $arquivoReferenciado_rs[0]['nome'] ?></td>
									<?php } else { ?>
										<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['quantidade_tipos_dados'] ?></td>
										<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['quantidade_arquivos_referenciados'] ?></td>
									<?php } ?>
									<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['complexidade'] ?></td>
									<td rowspan="<?php echo $rowspan_campos_arquivos ?>"><?php echo $componente_row['valor_pf'] ?></td>
								</tr>
								<?php if($detalhar_campos_arquivos){ ?>
									<?php
									$checkPossuiAcoesInserido = $checkPossuiMensagensInserido = false;
									for($i=1; $i<$rowspan_campos_arquivos; $i++){
										if(isset($campo_rs[$i]['nome'])){
											$nome_campo = $campo_rs[$i]['nome'];
											if(substr($nome_campo, 0, 5) == 'Campo'){
												$nome_campo = trim( substr($nome_campo, 5) );
												$nome_campo = "$categoria_tipo_dado $nome_campo";
											} else {
												$nome_campo = "$categoria_tipo_dado \"$nome_campo\"";
											}
										} elseif($componente_row['possui_acoes'] == '1' && !$checkPossuiAcoesInserido){
											$nome_campo = 'Possui Ações';
											$checkPossuiAcoesInserido = true;
										} elseif($componente_row['possui_mensagens'] == '1' && !$checkPossuiMensagensInserido){
											$nome_campo = 'Possui Mensagens';
											$checkPossuiMensagensInserido = true;
										} else {
											$nome_campo = '';
										}
										
										$nome_arquivo = (isset($arquivoReferenciado_rs[$i]['nome'])) ? ($arquivoReferenciado_rs[$i]['nome']) : ('&nbsp;');
										?>
										<tr>
											<td><?php echo $nome_campo ?></td>
											<td><?php echo $nome_arquivo ?></td>
										</tr>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>							
								<th colspan="<?php echo $colspan_rodape_branco ?>">&nbsp;</th>
								<th>TOTAL:</th>
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