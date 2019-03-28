<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$funcionalidade = new funcionalidade();

$dados = autocomplete::interpretarDados($_GET);
$busca = $dados['busca'];
$limit = $dados['limit'];
$offset = $dados['offset'];

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');
$id_modulo = (isset($_GET['modulo'])) ? ($_GET['modulo']) : ('');

$total_consulta = $funcionalidade->getTotalByAutocomplete($busca, $id_sistema, $id_modulo);
$funcionalidade_rs = $funcionalidade->getByAutocomplete($busca, $id_sistema, $id_modulo, $limit, $offset);

$json = array();
$array_sistemas_optgroups = array();
$i = 0;
foreach($funcionalidade_rs as $funcionalidade_row) {
	$id_modulo = $funcionalidade_row['id_modulo'];
	$nome_modulo = funcoes::capitaliza($funcionalidade_row['modulo']);
	$nome_sistema = funcoes::capitaliza($funcionalidade_row['sistema']);
	
	if(!in_array($id_modulo, $array_sistemas_optgroups)){
		array_push($json, array(
			'id'=>$id_modulo,
			'value'=>($nome_sistema . ' - ' . $nome_modulo),
			'children'=>array()
		));
		
		$array_sistemas_optgroups[$i] = $id_modulo;
		$i++;
	}
}

foreach($funcionalidade_rs as $funcionalidade_row) {
	$id_modulo = $funcionalidade_row['id_modulo'];
	$id_sistema = $funcionalidade_row['id_sistema'];
	$chave_optgroup = array_search($id_modulo, $array_sistemas_optgroups);
	
	$nome_funcionalidade = funcoes::capitaliza($funcionalidade_row['nome']);
	$nome_modulo = funcoes::capitaliza($funcionalidade_row['modulo']);
	$nome_sistema = funcoes::capitaliza($funcionalidade_row['sistema']);
	
	array_push($json[$chave_optgroup]['children'], array(
		'value'=>$nome_funcionalidade,
		'label'=>$nome_funcionalidade,
		'nome_modulo'=>$nome_modulo,
		'id_modulo'=>$id_modulo,
		'nome_sistema'=>$nome_sistema,
		'id_sistema'=>$id_sistema,
		'id'=>$funcionalidade_row['id']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);