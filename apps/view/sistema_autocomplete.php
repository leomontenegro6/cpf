<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$sistema = new sistema();

$dados = autocomplete::interpretarDados($_GET);
$busca = $dados['busca'];
$limit = $dados['limit'];
$offset = $dados['offset'];

$total_consulta = $sistema->getTotalByAutocomplete($busca);
$sistema_rs = $sistema->getByAutocomplete($busca, $limit, $offset);

$json = array();	
foreach($sistema_rs as $sistema_row) {
	$nome = funcoes::capitaliza($sistema_row['nome']);
	
	array_push($json, array(
		'value'=>$nome,
		'label'=>$nome,
		'id'=>$sistema_row['id']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);