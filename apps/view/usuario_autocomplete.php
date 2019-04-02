<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$usuario = new usuario();

$dados = autocomplete::interpretarDados($_GET);
$busca = $dados['busca'];
$limit = $dados['limit'];
$offset = $dados['offset'];

$total_consulta = $usuario->getTotalByAutocomplete($busca);
$usuario_rs = $usuario->getByAutocomplete($busca, $limit, $offset);

$json = array();	
foreach($usuario_rs as $usuario_row) {
	$nome = funcoes::capitaliza($usuario_row['nome']);
	$indice_produtividade = funcoes::encodeMonetario($usuario_row['indice_produtividade'], 1) . ' PF';
	if(!empty($usuario_row['foto'])){
		$foto = $usuario_row['foto'];
	} else {
		$foto = "../common/img/user.png";
	}
	
	$label = "<img src='{$foto}' class='img-circle' style='width: 2.1rem' />";
	$label .= "$nome";
	$label .= "<span class='badge badge-primary float-right'>IMP: $indice_produtividade</span>";
	
	array_push($json, array(
		'value'=>$nome,
		'label'=>$label,
		'id'=>$usuario_row['id']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);