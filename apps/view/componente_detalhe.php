<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$componente = new componente();

$id = $_GET['id'];

$componente_row = $componente->getByDetalhes($id);
$campo_rs = $componente_row['campos'];
$arquivoReferenciado_rs = $componente_row['arquivos_referenciados'];

$complexidade_valor_pf = $componente->calcularComplexidadeValorPF($id);
$complexidade = funcoes::capitaliza($complexidade_valor_pf['complexidade']);
$valor_pf = $complexidade_valor_pf['valor'];
?>
<div class="card card-info">
	<div class="card-header">
		<h3 class="card-title">Detalhes do Componente</h3>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="col-4">
				<div class="form-group">
					<label>Sistema</label>
					<div><?php echo $componente_row['sistema'] ?></div>
				</div>
			</div>
			<div class="col-4">
				<div class="form-group">
					<label>Módulo</label>
					<div><?php echo $componente_row['modulo'] ?></div>
				</div>
			</div>
			<div class="col-4">
				<div class="form-group">
					<label>Funcionalidade</label>
					<div><?php echo $componente_row['funcionalidade'] ?></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-4">
				<div class="form-group">
					<label>Tipo de Componente</label>
					<div><?php echo $componente_row['tipo_componente'] ?></div>
				</div>
			</div>
			<div class="col-2">
				<div class="form-group">
					<label>Ordem</label>
					<div><?php echo $componente_row['ordem'] ?></div>
				</div>
			</div>
			<div class="col-3">
				<div class="form-group">
					<label>Possui Ações</label>
					<div><?php echo ($componente_row['possui_acoes'] == '1') ? ('Sim') : ('Não') ?></div>
				</div>
			</div>
			<div class="col-3">
				<div class="form-group">
					<label>Possui Mensagens</label>
					<div><?php echo ($componente_row['possui_mensagens'] == '1') ? ('Sim') : ('Não') ?></div>
				</div>
			</div>
		</div>
		<div id="componentes">
			<b>Componentes</b>
			
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Campos</th>
						<th>Arquivos Referenciados</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php foreach($campo_rs as $campo_row){ ?>
								<span class="badge badge-info"><?php echo $campo_row['nome'] ?></span>
							<?php } ?>
							<span class="badge badge-warning float-right"><?php echo count($campo_rs) ?></span>
						</td>
						<td>
							<?php foreach($arquivoReferenciado_rs as $arquivoReferenciado_row){ ?>
								<span class="badge badge-info"><?php echo $arquivoReferenciado_row['nome'] ?></span>
							<?php } ?>
							<span class="badge badge-warning float-right"><?php echo count($arquivoReferenciado_rs) ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="row">
			<div class="col-6">
				<div class="form-group">
					<label>Complexidade</label>
					<div><?php echo $complexidade ?></div>
				</div>
			</div>
			<div class="col-6">
				<div class="form-group">
					<label>Valor (Pontos de Função)</label>
					<div><?php echo $valor_pf ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="card-footer text-center">
		<button type="submit" name="Submit" class="btn btn-info" onclick="jModalRemove()">
			<i class="fas fa-times"></i>
			Fechar
		</button>
	</div>
</div>