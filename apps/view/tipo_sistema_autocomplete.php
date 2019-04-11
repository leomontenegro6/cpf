<?php
session_start();
if(!isset($_SESSION['login'])){
	$array = array("error"=>"expired");
	echo json_encode($array);
	exit;
}
require_once '../../utils/autoload.php';

$tipoSistema = new tipoSistema();

$dados = autocomplete::interpretarDados($_GET);
$busca = $dados['busca'];
$limit = $dados['limit'];
$offset = $dados['offset'];

$total_consulta = $tipoSistema->getTotalByAutocomplete($busca);
$tipoSistema_rs = $tipoSistema->getByAutocomplete($busca, $limit, $offset);

$json = array();	
foreach($tipoSistema_rs as $tipoSistema_row) {
	$nome = $tipoSistema_row['nome'];
	$descricao = $tipoSistema_row['descricao'];
	$expoente_minimo = str_replace('.', ',', $tipoSistema_row['expoente_minimo']);
	$expoente_maximo = str_replace('.', ',', $tipoSistema_row['expoente_maximo']);
	
	if($tipoSistema_row['expoente_minimo'] == $tipoSistema_row['expoente_maximo']){
		$expoentes_exibir_label = "Expoente:<br />$expoente_minimo";
	} else {
		$expoentes_exibir_label = "Mín.: $expoente_minimo<br />Máx.: $expoente_maximo";
	}
	
	$label = "<div class='row' style='line-height: 1'>";
	$label .=	"<div class='col-10'>";
	$label .=		"$nome";
	if(!empty($descricao)) $label .= "<br />(<small>$descricao</small>)";
	$label .=	"</div>";
	$label .=	"<div class='col-2'>";
	$label .=		"<span class='badge badge-dark text-right float-right'>$expoentes_exibir_label</span>";
	$label .=	"</div>";
	$label .= "</div>";
	
	array_push($json, array(
		'value'=>$nome,
		'label'=>$label,
		'id'=>$tipoSistema_row['id'],
		'expoente_minimo'=>$tipoSistema_row['expoente_minimo'],
		'expoente_maximo'=>$tipoSistema_row['expoente_maximo']
	));
}

echo autocomplete::encodarRetornoJSON($json, $total_consulta, $busca, $limit, $offset);