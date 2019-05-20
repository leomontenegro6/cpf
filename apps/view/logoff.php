<?php
require_once '../../utils/autoload.php';

$sessao_expirada = (isset($_GET['sessao_expirada']) && ($_GET['sessao_expirada'] == 'true'));

session_start();
session_destroy();
setcookie('auth');

$pagina = "../inicial/index.php";
if($sessao_expirada){
	$pagina .= "?sessao_expirada=true";
}
header("Location: $pagina");