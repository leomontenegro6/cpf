<?php
if(isset($_GET['montar_tabela']) && $_GET['montar_tabela'] === true) {
	// Montagem da tabela
	if(!isset($_SESSION['login'])) {
		header('Location: index.php');
		exit;
	}
	$pagina = $_GET['pagina'];
	$parametros = $_GET['parametros'];
	$oculta = $_GET['oculta'];
	$id_tabela = $_GET['id_tabela'];
	$temPaginacao = ($_GET['tem_paginacao']) ? ('true') : ('false');
	$limite = $_GET['limite'];

	if($oculta === true) {
		$classe_tabela = 'tabelaaberta oculta';
	} else {
		$classe_tabela = 'tabelaaberta';
	}
	if(!empty($id_tabela)) {
		$atributo_id = "id='$id_tabela'";
	} else {
		$atributo_id = '';
	}
	?>
	<div class="card">
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
			<button type="button" class="btn btn-primary float-right" onclick="jFormMedio('componente_form.php')">
				<i class="fas fa-plus-circle"></i> Novo
			</button>
		</div>
		<div class="card-body">
			<div <?php echo $atributo_id ?> class="<?php echo $classe_tabela ?>" data-pagina="<?php echo $pagina ?>"
				data-parametros="<?php echo $parametros ?>" data-ordenacao="2, 3, 1" data-filtragem="asc, asc, asc" data-paginacao="<?php echo $temPaginacao ?>"
				data-limite="<?php echo $limite ?>">
				<table>
					<thead>
						<tr>
							<th class="align-middle">Ordem</th>
							<th class="align-middle">Sistema</th>
							<th class="align-middle">Módulo</th>
							<th class="align-middle">Funcionalidade</th>
							<th class="align-middle">Tipo de Componente</th>
							<th class="align-middle" data-ordenavel="false">Complexidade</th>
							<th class="align-middle" data-ordenavel="false">Valor (PF)</th>
							<th width="75" class="acoes">Ações</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>							
							<th class="align-middle">Ordem</th>
							<th class="align-middle">Sistema</th>
							<th class="align-middle">Módulo</th>
							<th class="align-middle">Funcionalidade</th>
							<th class="align-middle">Tipo de Componente</th>
							<th class="align-middle">Complexidade</th>
							<th class="align-middle">Valor (PF)</th>
							<th width="75" class="acoes">Ações</th>
						</tr>
					</tfoot>
				</table>
				<div class="acoes">
					<button type="button" title="Detalhes" data-onclick="jModalMedio('componente_detalhe.php?id={id}')" class="btn-info btn-sm">
						<i class="fa fa-search"></i>
					</button>
					<button type="button" title="Editar" data-onclick="jFormMedio('componente_form.php', 'id={id}')" class="btn-success btn-sm">
						<i class="fa fa-edit"></i>
					</button>
					<button type="button" title="Excluir" data-onclick="apagaRegistro('componente_crud.php', {id})" class="btn-danger btn-sm">
						<i class="fa fa-trash-alt"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {
	// Obtenção dos dados da tabela, via Ajax
	session_start();
	if(!isset($_SESSION['login'])){
		$array = array("error"=>"expired");
		echo json_encode($array);
		exit;
	}
	require_once '../../utils/autoload.php';
	
	$componente = new componente();
	
	$busca = tabela::obterValorCampoBusca($_GET);
	$sistema_lista = (isset($_GET['sistema_lista'])) ? ($_GET['sistema_lista']) : ('');
	$modulo_lista = (isset($_GET['modulo_lista'])) ? ($_GET['modulo_lista']) : ('');
	$funcionalidade_lista = (isset($_GET['funcionalidade_lista'])) ? ($_GET['funcionalidade_lista']) : ('');
	
	$total_geral = $componente->getTotal();
	$total_consulta = $componente->getTotalByListagem($busca, $sistema_lista, $modulo_lista, $funcionalidade_lista);

	$dados_requisicao = tabela::interpretarDados($_GET, $total_consulta);

	$numero_de_pesquisas = $dados_requisicao['numero_de_pesquisas'];
	$colunas = $dados_requisicao['colunas'];
	$ordenacao = $dados_requisicao['ordenacao'];
	$filtragem = $dados_requisicao['filtragem'];
	$limit = $dados_requisicao['limit'];
	$offset = $dados_requisicao['offset'];

	// Consulta de pesquisa da tabela
	$sistema_rs = $componente->getByListagem($busca, $sistema_lista, $modulo_lista, $funcionalidade_lista, $ordenacao, $filtragem, $limit, $offset);

	// Colunas da tabela a serem retornadas via array para o componente (para ações)
	$dados_coluna = array('id');

	// Formatação dos dados da pesquisa na tabela, para o formato do componente
	$dados_tabela = tabela::formatarDadosTabela($sistema_rs, $colunas, $dados_coluna);

	// Exibição dos dados de retorno, encodados em JSON
	echo tabela::encodarRetornoJSON($numero_de_pesquisas, $total_geral, $total_consulta, $dados_tabela, $busca, $ordenacao, $filtragem, $limit, $offset);
}