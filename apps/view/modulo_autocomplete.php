<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$modulo = new modulo();
$funcionalidade = new funcionalidade();

$dados = autocomplete::interpretarDados($_GET);
$busca = $dados['busca'];
$limit = $dados['limit'];
$offset = $dados['offset'];

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');

$total_consulta = $modulo->getTotalByAutocomplete($busca, $id_sistema);
$modulo_rs = $modulo->getByAutocomplete($busca, $id_sistema, $limit, $offset);

$json = array();
$array_sistemas_optgroups = array();
$i = 0;
foreach($modulo_rs as $modulo_row) {
	$id_sistema = $modulo_row['id_sistema'];
	$nome_sistema = $modulo_row['sistema'];
	$sigla_sistema = $modulo_row['sigla_sistema'];
	
	if(!in_array($id_sistema, $array_sistemas_optgroups)){
		array_push($json, array(
			'id'=>$id_sistema,
			'value'=>($sigla_sistema . ' - ' . $nome_sistema),
			'children'=>array()
		));
		
		$array_sistemas_optgroups[$i] = $id_sistema;
		$i++;
	}
}

foreach($modulo_rs as $modulo_row) {
	$id_sistema = $modulo_row['id_sistema'];
	$chave_optgroup = array_search($id_sistema, $array_sistemas_optgroups);
	
	$nome_modulo = $modulo_row['nome'];
	$nome_sistema = $modulo_row['sistema'];
	
	array_push($json[$chave_optgroup]['children'], array(
		'value'=>$nome_modulo,
		'label'=>$nome_modulo,
		'nome_sistema'=>$nome_sistema,
		'id_sistema'=>$id_sistema,
		'proxima_ordem'=>$funcionalidade->getProximaOrdemByModulo($modulo_row['id']),
		'id'=>$modulo_row['id']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);