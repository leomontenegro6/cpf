<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');

if(is_numeric($id_sistema)){
	?>
	<div class="input-group">
		<input type="search" class="form-control" placeholder="Digite o nome da funcionalidade ou componente"
			onkeyup="orcamentoManutencao.buscarComponenteAlteracaoExclusaoFuncionalidades(this, event)" />
		<span class="input-group-append">
			<span class="input-group-text"><i class="fas fa-search"></i></span>
		</span>
	</div>
	<table class="table table-bordered table-sm" style="width: 100%">
		<thead>
			<tr>
				<th>Módulo</th>
				<th>Funcionalidade</th>
				<th>Funcionalidade / Componente</th>
				<th>Tipo de Manutenção</th>
				<th>Fator de Impacto</th>
				<th>Detalhes</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$modulo_rs = $modulo->getBySistema($id_sistema);
			foreach($modulo_rs as $modulo_row){
				$nome_modulo = $modulo_row['nome'];
				$id_modulo = $modulo_row['id'];

				$funcionalidade_rs = $funcionalidade->getByModulo($id_modulo, 'f.ordem');
				$total_funcionalidades = count($funcionalidade_rs);
				foreach($funcionalidade_rs as $i=>$funcionalidade_row){
					$id_funcionalidade = $funcionalidade_row['id'];
					$nome_funcionalidade = $funcionalidade_row['nome'];

					$componente_rs = $componente->getByFuncionalidade($id_funcionalidade);;
					$total_componentes = count($componente_rs);
					if($total_componentes > 1){
						$string_qtde_componentes = $total_componentes . ' Componentes';
					} else {
						$string_qtde_componentes = $total_componentes . ' Componente';
					}

					$valor_pf_funcionalidade = 0;
					foreach($componente_rs as $j=>$componente_row){
						$id_componente = $componente_row['id'];
						$tipo_componente = $componente_row['tipo_componente'];
						$quantidade_tipos_dados = $componente_row['quantidade_campos'];
						$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];

						if($componente_row['possui_acoes'] == '1'){
							$quantidade_tipos_dados++;
						}
						if($componente_row['possui_mensagens'] == '1'){
							$quantidade_tipos_dados++;
						}

						$tipo_funcional = '';
						if($componente_row['id_tipo_dado'] == 1){
							$tipo_funcional = 'e';
						} elseif($componente_row['id_tipo_dado'] == 2){
							$tipo_funcional = 's';
						} elseif($componente_row['id_tipo_dado'] == 3){
							$tipo_funcional = 'c';
						}			

						$complexidade = cpf::calcularComplexidade($tipo_funcional, $quantidade_tipos_dados, $quantidade_arquivos_referenciados);
						$valor_pf = cpf::calcularValor($tipo_funcional, $complexidade);

						$valor_pf_funcionalidade += $valor_pf;
						?>
						<tr>
							<td class="modulo">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" id="modulo_<?php echo $id_modulo ?>" class="custom-control-input"
										value="<?php echo $id_modulo ?>" onchange="orcamentoManutencao.toggleFuncionalidadesComponentesFilhosModulo(this)" />
									<label for="modulo_<?php echo $id_modulo ?>" class="custom-control-label" style="font-weight: bold !important">
										Módulo "<?php echo $nome_modulo ?>"
									</label>
								</div>
							</td>
							<td class="funcionalidade">
								<span class="boxurvr">&boxur;</span>
								<div class="custom-control custom-checkbox" style="display: inline-block">
									<input type="checkbox" id="funcionalidade_<?php echo $id_funcionalidade ?>" class="custom-control-input"
										name="funcionalidades[<?php echo $id_funcionalidade ?>][marcada]" value="<?php echo $id_funcionalidade ?>"
										data-modulo="<?php echo $id_modulo ?>"
										onchange="orcamentoManutencao.toggleModuloPaiFuncionalidade(this); orcamentoManutencao.toggleComponentesFilhosFuncionalidade(this)" />
									<label for="funcionalidade_<?php echo $id_funcionalidade ?>" class="custom-control-label">Funcionalidade "<?php echo $nome_funcionalidade ?>"</label>
								</div>

								<div class="float-right">
									<button type="button" class="btn btn-sm btn-info" title="Ver detalhes da funcionalidade"
										onclick="jModalGrande('funcionalidade_detalhe.php?id=<?php echo $id_funcionalidade ?>')">
										<i class="fa fa-search-plus"></i>
									</button>
								</div>
							</td>
							<td class="componente">
								<span class="boxurvr" style="margin-left: 18px">&boxur;</span>
								<div class="custom-control custom-checkbox" style="display: inline-block">
									<input type="checkbox" id="componente_<?php echo $id_componente ?>" class="custom-control-input"
										name="componentes[<?php echo $id_componente ?>][marcado]" value="<?php echo $id_componente ?>"
										data-funcionalidade="<?php echo $id_funcionalidade ?>"
										onchange="orcamentoManutencao.toggleModuloFuncionalidadePaiComponente(this);" />
									<label for="componente_<?php echo $id_componente ?>" class="custom-control-label"><?php echo $tipo_componente ?></label>
								</div>

								<input type="hidden" name="componentes[<?php echo $id_componente ?>][modulo]" value="<?php echo $nome_modulo ?>" />
								<input type="hidden" name="componentes[<?php echo $id_componente ?>][funcionalidade]" value="<?php echo $nome_funcionalidade ?>" />
								<input type="hidden" name="componentes[<?php echo $id_componente ?>][id_funcionalidade]" value="<?php echo $id_funcionalidade ?>" />
								<input type="hidden" name="componentes[<?php echo $id_componente ?>][tipo_componente]" value="<?php echo $tipo_componente ?>" />
								<input type="hidden" name="componentes[<?php echo $id_componente ?>][valor_pf]" value="<?php echo $valor_pf ?>" />
							</td>
							<td>
								<select class="form-control form-control-sm" disabled
									name="componentes[<?php echo $id_componente ?>][tipo_manutencao]"
									onchange="orcamentoManutencao.toggleCampoFatorImpacto(this)">
									<option value="" selected>---</option>
									<option value="a">Alteração</option>
									<option value="e">Exclusão</option>
								</select>
							</td>
							<td>
								<div class="input-group input-group-sm">
									<input class="form-control" name="componentes[<?php echo $id_componente ?>][fator_impacto]"
										type="number" min="1" max="100" step="1"
										required placeholder="---" disabled />
									<div class="input-group-append">
										<span class="input-group-text">%</span>
									</div>
								</div>
							</td>
							<td>
								<textarea name="componentes[<?php echo $id_componente ?>][detalhes]" class="form-control form-control-sm"
									disabled></textarea>
							</td>
						</tr>
						<?php
					}
				}
			} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="6" class="text-center">
					<button type="submit" class="btn btn-success" title="Adicionar funcionalidade(s) existente(s), no orçamento" disabled
						style="background-image: linear-gradient(30deg, #28a745 25%, #dc3545 75%);">
						<i class="fas fa-arrow-down"></i>
					</button>
				</th>
			</tr>
		</tfoot>
	</table>
	<?php
} else {
	?>
	<div class="alert alert-info">
		<h5><i class="icon fa fa-info"></i> Escolha o sistema antes.</h5>
	</div>
	<?php
}