<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$funcionalidade = new funcionalidade();
$componente = new componente();

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
	$nome_sistema = $funcionalidade_row['sistema'];
	$sigla_sistema = $funcionalidade_row['sigla_sistema'];
	$nome_modulo = $funcionalidade_row['modulo'];
	
	if(!in_array($id_modulo, $array_sistemas_optgroups)){
		array_push($json, array(
			'id'=>$id_modulo,
			'value'=>($sigla_sistema . ' - ' . $nome_sistema . ' - Módulo "' . $nome_modulo . '"'),
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
	
	$nome_funcionalidade = $funcionalidade_row['nome'];
	$nome_modulo = $funcionalidade_row['modulo'];
	$nome_sistema = $funcionalidade_row['sistema'];
	
	array_push($json[$chave_optgroup]['children'], array(
		'value'=>$nome_funcionalidade,
		'label'=>$nome_funcionalidade,
		'nome_modulo'=>$nome_modulo,
		'id_modulo'=>$id_modulo,
		'nome_sistema'=>$nome_sistema,
		'id_sistema'=>$id_sistema,
		'proxima_ordem'=>$componente->getProximaOrdemByFuncionalidade($funcionalidade_row['id']),
		'id'=>$funcionalidade_row['id']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);