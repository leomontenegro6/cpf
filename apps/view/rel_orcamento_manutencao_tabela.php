<?php
require_once('cabecalho.php');
?>
<div class="card-header">
	<h3 class="card-title" style="font-weight: bold">
		<span id="nome_sistema">...</span><br />
		Orçamento de Manutenção de Funcionalidades
	</h3>
	<div class="card-tools">
		<?php
		$parametros = "";
		?>
		<button type="button" class="btn btn-success float-right"
			onclick="abrirPagina('rel_orcamento_manutencao_xls.php?<?php echo $parametros ?>', '', '_blank');">
			<i class="fas fa-file-excel"></i> Gerar Planilha
		</button>
	</div>
</div>
<div class="card-body">
	<div class="table-responsive">
		<table class="table table-bordered table-sm" data-iterador-funcionalidades="0" data-iterador-componentes="0">
			<thead>
				<tr>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Ordem</th>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Módulo</th>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Funcionalidade</th>
					<th rowspan="2" colspan="2" class="align-middle" style="background-color: #fafafa">Componente</th>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Tipo de Manutenção</th>
					<th colspan="2" class="text-center align-middle" style="background-color: #fafafa">Valor (PF)</th>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Tempo (Horas)</th>
					<th rowspan="2" class="align-middle" style="background-color: #fafafa">Custo (R$)</th>
				</tr>
				<tr>
					<th class="align-middle" style="background-color: #fafafa">Original</th>
					<th class="align-middle" style="background-color: #fafafa">Ajustado</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="6" class="text-right" style="background-color: #fafafa">TOTAL:</th>
					<th class="total_valor_pf_original_formatado" style="background-color: #fafafa">---</th>
					<th class="total_valor_pf_ajustado_formatado" style="background-color: #fafafa">---</th>
					<th class="total_tempo_formatado" style="background-color: #fafafa">---</th>
					<th class="total_custo_formatado" style="background-color: #fafafa">---</th>
				</tr>
			</tfoot>
		</table>
		
		<input type="hidden" class="total_valor_pf_original" name="totais[valor_pf_original]" value="0" />
		<input type="hidden" class="total_valor_pf_ajustado" name="totais[valor_pf_ajustado]" value="0" />
		<input type="hidden" class="total_tempo" name="totais[tempo]" value="0" />
		<input type="hidden" class="total_custo" name="totais[custo]" value="0" />
	</div>
</div>