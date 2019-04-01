<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$funcionalidade = new funcionalidade();
$componente = new componente();
$campo = new campo();
$arquivoReferenciado = new arquivoReferenciado();

$id = $_GET['id'];

$funcionalidade_row = $funcionalidade->getByDetalhes($id);
$componente_rs = $componente->getByFuncionalidade($id);
?>
<div class="card card-info">
	<div class="card-header">
		<h3 class="card-title">Detalhes da Funcionalidade</h3>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="col-6">
				<div class="form-group">
					<label>Sistema</label>
					<div><?php echo $funcionalidade_row['sistema'] ?></div>
				</div>
			</div>
			<div class="col-6">
				<div class="form-group">
					<label>Módulo</label>
					<div><?php echo $funcionalidade_row['modulo'] ?></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<div class="form-group">
					<label>Nome</label>
					<div><?php echo $funcionalidade_row['nome'] ?></div>
				</div>
			</div>
			<div class="col-3">
				<div class="form-group">
					<label>Ordem</label>
					<div><?php echo $funcionalidade_row['ordem'] ?></div>
				</div>
			</div>
			<div class="col-3">
				<div class="form-group">
					<label>Tipo de Funcionalidade</label>
					<div><?php echo $funcionalidade_row['tipo_funcionalidade'] ?></div>
				</div>
			</div>
		</div>
		<div id="componentes">
			<b>Componentes</b>
			
			<table class="table table-bordered">
				<thead>
					<tr>
						<th rowspan="2" class="align-middle" style="width: 200px">Tipo</th>
						<th colspan="2" class="text-center">Possui</th>
						<th rowspan="2" class="align-middle">Campos</th>
						<th rowspan="2" class="align-middle">Arquivos Referenciados</th>
						<th rowspan="2" class="align-middle">Complexidade</th>
						<th rowspan="2" class="align-middle">Valor (PF)</th>
					</tr>
					<tr>
						<th>Ações</th>
						<th>Mensagens</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total_valor_pf = 0;
					foreach($componente_rs as $componente_row){
						$campo_rs = $campo->getByComponente($componente_row['id']);
						$arquivoReferenciado_rs = $arquivoReferenciado->getByComponente($componente_row['id']);
						
						$total_tipos_dados = count($campo_rs);
						$total_arquivos_referenciados = count($arquivoReferenciado_rs);
						if($componente_row['possui_acoes'] == '1'){
							$total_tipos_dados++;
						}
						if($componente_row['possui_mensagens'] == '1'){
							$total_tipos_dados++;
						}
						
						$complexidade_valor_pf = $componente->calcularComplexidadeValorPF($componente_row['id']);
						$complexidade = funcoes::capitaliza($complexidade_valor_pf['complexidade']);
						$valor_pf = $complexidade_valor_pf['valor'];
						$total_valor_pf += $valor_pf;
						?>
						<tr>
							<td>
								<?php echo $componente_row['tipo_componente'] ?>
								<span class="badge badge-primary float-right"><?php echo $componente_row['tipo_dado'] ?></span>
							</td>
							<td><?php echo ($componente_row['possui_acoes'] == '1') ? ('Sim') : ('Não') ?></td>
							<td><?php echo ($componente_row['possui_mensagens'] == '1') ? ('Sim') : ('Não') ?></td>
							<td>
								<?php foreach($campo_rs as $campo_row){ ?>
									<span class="badge badge-info"><?php echo $campo_row['nome'] ?></span>
								<?php } ?>
								<span class="badge badge-warning float-right" title="<?php echo $total_tipos_dados ?> tipo(s) de dado(s)">
									<?php echo $total_tipos_dados ?> TD
								</span>
							</td>
							<td>
								<?php foreach($arquivoReferenciado_rs as $arquivoReferenciado_row){ ?>
									<span class="badge badge-info"><?php echo $arquivoReferenciado_row['nome'] ?></span>
								<?php } ?>
								<span class="badge badge-warning float-right" title="<?php echo $total_arquivos_referenciados ?> arquivo(s) referenciado(s)">
									<?php echo $total_arquivos_referenciados ?> AR
								</span>
							</td>
							<td><?php echo $complexidade ?></td>
							<td><?php echo $valor_pf ?></td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5">&nbsp;</td>
						<th>Total:</th>
						<th><?php echo $total_valor_pf ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="card-footer text-center">
		<button type="submit" name="Submit" class="btn btn-info" onclick="jModalRemove()">
			<i class="fas fa-times"></i>
			Fechar
		</button>
	</div>
</div>