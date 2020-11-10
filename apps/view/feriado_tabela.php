<?php
require_once('cabecalho.php');

$feriado = new feriado();

if(isset($_GET['ano_lista']) && is_numeric($_GET['ano_lista'])){
	$ano_lista = $_GET['ano_lista'];
} else {
	$ano_lista = date('Y');
}
$customizado_lista = (isset($_GET['customizado_lista'])) ? ($_GET['customizado_lista']) : ('');

$feriado_rs = $feriado->getAllByAno($ano_lista, $customizado_lista);
?>
<div class="card conteiner_tabelaaberta">
	<div class="card-header">
		<div class="btn-group float-left">
			<button type="button" class="btn btn-warning" onclick="history.back()">
				<i class="fas fa-arrow-left"></i> Voltar
			</button>
			<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#filtros"
				aria-expanded="false" aria-controls="filtros" title="Mostrar filtros de busca"
				onclick="setTimeout(function(){ instanciarComponentes(null, $('#filtros')) }, 25)">
				<i class="fas fa-filter"></i> Filtros
			</button>
		</div>
		<button type="button" class="btn btn-primary float-right" onclick="cadastrarNovoFeriadoCustomizado()">
			<i class="fas fa-plus-circle"></i> Novo
		</button>
	</div>
	<div class="card-body">
		<div class="tabelaaberta" data-ordenacao="1" data-filtragem="asc" data-paginacao="true">
			<table>
				<thead>
					<tr>
						<th class="align-middle">Data</th>
						<th class="align-middle">Dia da Semana</th>
						<th class="align-middle">Nome</th>
						<th class="align-middle">Customizado</th>
						<th width="75" class="acoes align-middle">Ações</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($feriado_rs as $feriado_row) { ?>
					<tr>
						<td><?php echo $feriado_row['data'] ?></td>
						<td><?php echo $feriado_row['dia_semana'] ?></td>
						<td><?php echo $feriado_row['nome'] ?></td>
						<td><?php echo $feriado_row['customizado'] ?></td>
						<td width="75" class="acoes align-middle">
							<?php if(is_numeric($feriado_row['id'])){ ?>
								<button type="button" title="Editar" class="btn-success btn-sm" onclick="jForm('feriado_customizado_form.php', 'id=<?php echo $feriado_row['id'] ?>')">
									<i class="fa fa-edit"></i>
								</button>
								<button type="button" title="Excluir" class="btn-danger btn-sm" onclick="apagarFeriadoCustomizado('<?php echo $feriado_row['id'] ?>')">
									<i class="fa fa-trash-alt"></i>
								</button>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th class="align-middle">Data</th>
						<th class="align-middle">Dia da Semana</th>
						<th class="align-middle">Nome</th>
						<th class="align-middle">Customizado</th>
						<th width="75" class="acoes align-middle">Ações</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>