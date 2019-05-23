<?php
require_once('cabecalho.php');

$sistema = new sistema();

$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ($_SESSION['sistema_sessao']);

if(is_numeric($sistema_lista)){
	$sistema_row = $sistema->get($sistema_lista);
	$nome_sistema = $sistema_row['nome'];
	$sigla_sistema = $sistema_row['sigla'];
	$descricao_sistema = $sigla_sistema . ' - ' . $nome_sistema;
} else {
	$descricao_sistema = '...';
}
?>
<div class="card-header">
	<h3 class="card-title" style="font-weight: bold">
		<span id="nome_sistema"><?php echo $descricao_sistema ?></span><br />
		Orçamento de Manutenção de Funcionalidades
	</h3>
	<div class="card-tools">
		<button type="button" id="botao_gerar_planilha" class="btn btn-success float-right" onclick="orcamentoManutencao.gerarPlanilha(this)" title='Gerar Planilha'
			data-titulo="<?php echo $descricao_sistema ?>" data-subtitulo="Orçamento de Manutenção de Funcionalidades" data-tabela="tabela_orcamento_manutencao"
			data-nome-arquivo="Orçamento de Manutenção de Funcionalidades - <?php echo $sigla_sistema ?>">
			<i class="fas fa-file-excel"></i>
			<span class='d-none d-sm-inline'>Gerar Planilha</span>
		</button>
	</div>
</div>
<div class="card-body">
	<div class="table-responsive">
		<table id="tabela_orcamento_manutencao" class="table table-bordered table-sm" data-iterador-funcionalidades="0" data-iterador-componentes="0">
			<thead>
				<tr>
					<th rowspan="2" class="align-middle">Ordem</th>
					<th rowspan="2" class="align-middle">Módulo</th>
					<th rowspan="2" class="align-middle">Funcionalidade</th>
					<th rowspan="2" colspan="2" class="align-middle">Componente</th>
					<th rowspan="2" class="align-middle">Tipo de Manutenção</th>
					<th colspan="2" class="valor_pf text-center align-middle">Valor (PF)</th>
					<th rowspan="2" class="tempo align-middle">Tempo (Horas)</th>
					<th rowspan="2" class="custo align-middle">Custo (R$)</th>
					<th rowspan="2" class="detalhes align-middle">Detalhes</th>
				</tr>
				<tr>
					<th class="valor_pf_original align-middle">Original</th>
					<th class="valor_pf_ajustado align-middle">Ajustado</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="6" class="text-right">TOTAL:</th>
					<th class="total_valor_pf_original_formatado">---</th>
					<th class="total_valor_pf_ajustado_formatado">---</th>
					<th class="total_tempo_formatado">---</th>
					<th class="total_custo_formatado">---</th>
					<th class="detalhes">&nbsp;</th>
				</tr>
			</tfoot>
		</table>
		
		<input type="hidden" class="total_valor_pf_original" name="totais[valor_pf_original]" value="0" />
		<input type="hidden" class="total_valor_pf_ajustado" name="totais[valor_pf_ajustado]" value="0" />
		<input type="hidden" class="total_tempo" name="totais[tempo]" value="0" />
		<input type="hidden" class="total_custo" name="totais[custo]" value="0" />
		<input type="hidden" class="formato_tempo" name="formato_tempo" value="hhm" />
		<input type="hidden" class="mostrar_valores_pf" name="mostrar_valores_pf" value="oa" />
		<input type="hidden" class="mostrar_tempo" name="mostrar_tempo" value="true" />
		<input type="hidden" class="mostrar_custo" name="mostrar_custo" value="true" />
		<input type="hidden" class="mostrar_detalhes" name="mostrar_detalhes" value="true" />
	</div>
</div>