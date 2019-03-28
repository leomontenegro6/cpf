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
			<button type="button" class="btn btn-warning float-left" onclick="history.back()">
				<i class="fas fa-arrow-left"></i> Voltar
			</button>
			<button type="button" class="btn btn-primary float-right" onclick="jForm('usuario_form.php')">
				<i class="fas fa-plus-circle"></i> Novo
			</button>
		</div>
		<div class="card-body">
			<div <?php echo $atributo_id ?> class="<?php echo $classe_tabela ?>" data-pagina="<?php echo $pagina ?>"
				data-parametros="<?php echo $parametros ?>" data-ordenacao="1" data-filtragem="asc" data-paginacao="<?php echo $temPaginacao ?>"
				data-limite="<?php echo $limite ?>">
				<table>
					<thead>
						<tr>
							<th>Nome</th>
							<th>Login</th>
							<th width="75" class="acoes">Ações</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>							
							<th>Nome</th>
							<th>Login</th>
							<th width="75" class="acoes">Ações</th>
						</tr>
					</tfoot>
				</table>
				<div class="acoes">
					<button type="button" title="Editar" data-onclick="jForm('usuario_form.php', 'id={id}')" class="btn-success btn-sm">
						<i class="fa fa-edit"></i>
					</button>
					<button type="button" title="Excluir" data-onclick="apagaRegistro('usuario_crud.php', {id})" class="btn-danger btn-sm">
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
	
	$usuario = new usuario();
	
	$busca = tabela::obterValorCampoBusca($_GET);
	
	$total_geral = $usuario->getTotal();
	$total_consulta = $usuario->getTotalByListagem($busca);

	$dados_requisicao = tabela::interpretarDados($_GET, $total_consulta);

	$numero_de_pesquisas = $dados_requisicao['numero_de_pesquisas'];
	$colunas = $dados_requisicao['colunas'];
	$ordenacao = $dados_requisicao['ordenacao'];
	$filtragem = $dados_requisicao['filtragem'];
	$limit = $dados_requisicao['limit'];
	$offset = $dados_requisicao['offset'];

	// Consulta de pesquisa da tabela
	$usuario_rs = $usuario->getByListagem($busca, $ordenacao, $filtragem, $limit, $offset);

	// Colunas da tabela a serem retornadas via array para o componente (para ações)
	$dados_coluna = array('id');

	// Formatação dos dados da pesquisa na tabela, para o formato do componente
	$dados_tabela = tabela::formatarDadosTabela($usuario_rs, $colunas, $dados_coluna);

	// Exibição dos dados de retorno, encodados em JSON
	echo tabela::encodarRetornoJSON($numero_de_pesquisas, $total_geral, $total_consulta, $dados_tabela, $busca, $ordenacao, $filtragem, $limit, $offset);
}