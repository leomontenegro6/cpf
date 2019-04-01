<?php
session_start();
require_once '../../utils/autoload.php';

if(!isset($_SESSION['ambiente_desenvolvimento']) || empty($_SESSION['ambiente_desenvolvimento'])){
    $_SESSION['ambiente_desenvolvimento']  = funcoes::getAmbienteDesenvolvimento();
}
if($_SESSION['ambiente_desenvolvimento'] == 'D' || $_SESSION['ambiente_desenvolvimento'] == 'H') {
	depuracao::mostrarConsultasSessao();
	depuracao::limparConsultasSessao();
}