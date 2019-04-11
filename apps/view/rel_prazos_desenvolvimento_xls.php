<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$sistema = new sistema();
$modulo = new modulo();
$funcionalidade = new funcionalidade();
$componente = new componente();

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');
$id_modulo = (isset($_GET['modulo'])) ? ($_GET['modulo']) : ('');
$id_funcionalidade = (isset($_GET['funcionalidade'])) ? ($_GET['funcionalidade']) : ('');
$recursos = (isset($_GET['recursos'])) ? ($_GET['recursos']) : ('1');
$tempo_dedicacao = (isset($_GET['tempo_dedicacao'])) ? ($_GET['tempo_dedicacao']) : ('4');
$indice_produtividade = (isset($_GET['indice_produtividade'])) ? ($_GET['indice_produtividade']) : (0.5);

$mostrarOrdem = (isset($_GET['mostrar_ordem']) && ($_GET['mostrar_ordem'] == 'true'));
$mostrarComplexidade = (isset($_GET['mostrar_complexidade']) && ($_GET['mostrar_complexidade'] == 'true'));
$mostrarValorPF = (isset($_GET['mostrar_valor_pf']) && ($_GET['mostrar_valor_pf'] == 'true'));
$arredondarZeros = (isset($_GET['arredondar_zeros']) && ($_GET['arredondar_zeros'] == 'true'));
$modo_exibicao_tempo = (isset($_GET['modo_exibicao_tempo'])) ? ($_GET['modo_exibicao_tempo']) : ('u');
$formato_tempo = (isset($_GET['formato_tempo'])) ? ($_GET['formato_tempo']) : ('hm');
$percentual_reducao_unico = (isset($_GET['percentual_reducao_unico'])) ? ($_GET['percentual_reducao_unico']) : ('45');
if(isset($_GET['esforco_disciplinas'])){
	$esforco_disciplinas = $_GET['esforco_disciplinas'];
	$esforco_disciplinas['analise']['percentual'] = round($esforco_disciplinas['analise']['percentual']);
	$esforco_disciplinas['desenvolvimento']['percentual'] = round($esforco_disciplinas['desenvolvimento']['percentual']);
	$esforco_disciplinas['testes']['percentual'] = round($esforco_disciplinas['testes']['percentual']);
	$esforco_disciplinas['implantacao']['percentual'] = round($esforco_disciplinas['implantacao']['percentual']);
} else {
	$esforco_disciplinas = array(
		'analise' => array(
			'percentual' => 25,
			'exibir' => true
		),
		'desenvolvimento' => array(
			'percentual' => 45,
			'exibir' => true
		),
		'testes' => array(
			'percentual' => 15,
			'exibir' => true
		),
		'implantacao' => array(
			'percentual' => 15,
			'exibir' => true
		)
	);
}

if(is_numeric($id_sistema)){
	$nome_sistema = $sistema->getNome($id_sistema, 'n');
	$moduloSistema_rs = $modulo->getBySistema($id_sistema);
} else {
	$nome_sistema = '';
	$moduloSistema_rs = array();
}
if(is_numeric($id_modulo)){
	$nome_modulo = $modulo->getNome($id_modulo, 'n');
	$funcionalidadeModulo_rs = $funcionalidade->getByModulo($id_modulo);
	$checkModuloUnico = true;
} else {
	$nome_modulo = '';
	$funcionalidadeModulo_rs = array();
	if(is_numeric($id_sistema)){
		$checkModuloUnico = (count( $modulo->getBySistema($id_sistema) ) == 1);
	} else {
		$checkModuloUnico = false;
	}
}
if(is_numeric($id_funcionalidade)){
	$nome_funcionalidade = $funcionalidade->getNome($id_funcionalidade, 'n');
	$checkFuncionalidadeUnica = true;
} else {
	$nome_funcionalidade = '';
	if(is_numeric($id_modulo)){
		$checkFuncionalidadeUnica = (count( $funcionalidade->getByModulo($id_modulo) ) == 1);
	} else {
		$checkFuncionalidadeUnica = false;
	}
}

$componente_rs = $componente->getByPlanilhaPrazosDesenvolvimento($id_sistema, $id_modulo, $id_funcionalidade, $recursos, $tempo_dedicacao, $indice_produtividade, $modo_exibicao_tempo, $percentual_reducao_unico, $esforco_disciplinas);
$colunas_tabela = array(
	'funcionalidade' => 'Funcionalidade', 'componente' => 'Componente'
);
if($mostrarComplexidade) $colunas_tabela = array_merge($colunas_tabela, array('complexidade' => 'Complexidade'));
if($mostrarValorPF) $colunas_tabela = array_merge($colunas_tabela, array('valor_pf' => 'Valor (PF)'));
$total_subcolunas_tempo = 1;

if($modo_exibicao_tempo == 'u'){
	$colunas_tabela['tempo'] = 'Tempo (' . (($formato_tempo == 'hm') ? ('Horas / Minutos') : ('Horas')) . ')';
} else {
	if(isset($esforco_disciplinas['analise']['exibir'])){
		$colunas_tabela['tempo_analise'] = 'Análise';
		$total_subcolunas_tempo++;
	}
	if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
		$colunas_tabela['tempo_desenvolvimento'] = 'Desenvolvimento';
		$total_subcolunas_tempo++;
	}
	if(isset($esforco_disciplinas['testes']['exibir'])){
		$colunas_tabela['tempo_testes'] = 'Testes';
		$total_subcolunas_tempo++;
	}
	if(isset($esforco_disciplinas['implantacao']['exibir'])){
		$colunas_tabela['tempo_implantacao'] = 'Implantação';
		$total_subcolunas_tempo++;
	}
	$colunas_tabela['tempo'] = 'TOTAL';
}
if(!$checkModuloUnico){
	$colunas_tabela = array_merge(array('modulo' => 'Módulo'), $colunas_tabela);

}
$total_colunas_tabela = count($colunas_tabela);

$titulo_caption = $nome_sistema;
if($checkModuloUnico){
	if(empty($nome_modulo)) $nome_modulo = $moduloSistema_rs['0']['nome'];
	$titulo_caption .= ' - Módulo ' . $nome_modulo;
}
if($checkFuncionalidadeUnica){
	if(empty($nome_funcionalidade)) $nome_funcionalidade = $funcionalidadeModulo_rs['0']['nome'];
	$titulo_caption .= ' - ' . $nome_funcionalidade;
}

// Geração do XLS
include('../common/relatorios/phpexcel/PHPExcel.php');

$arquivo_xls = new PHPExcel();

$arquivo_xls->getProperties()->setCreator("Leonardo José Montenegro Santiago")->setTitle("Planilha de Prazos de Desenvolvimento");

$arquivo_xls->setActiveSheetIndex(0);
$planilha_ativa = $arquivo_xls->getActiveSheet();

// Configurações globais
//PHPExcel_Shared_Font::setTrueTypeFontPath('/usr/share/fonts/truetype/');
//PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

// Estilizações padrões da planilha
$planilha_ativa->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

// Arrays de Estilizações
$cor_cinza_medio = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'DDDDDD')
	)
);
$cor_cinza_claro = array(
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'EEEEEE')
	)
);
$cor_borda = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000')
		)
	)
);
$cor_borda_sem_baixo = array(
	'borders' => array(
		'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
		'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
		'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))
	)
);
$cor_borda_sem_cima = array(
	'borders' => array(
		'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
		'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
		'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))
	)
);

$colunas_letras = array();
$primeira_coluna = $coluna = 'A';
$ultima_coluna = funcoes::converteNumeroParaLetra($total_colunas_tabela);

// Nome da planilha
$planilha_ativa->setTitle("Prazos de Desenvolvimento");

// Configurações de impressão
$planilha_ativa->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$planilha_ativa->getPageSetup()->setScale(85);

// Caption
$planilha_ativa->SetCellValue("{$coluna}1", $titulo_caption);
$planilha_ativa->SetCellValue("{$coluna}2", 'Prazo de Desenvolvimento de Funcionalidades');
$planilha_ativa->mergeCells("{$coluna}1:{$ultima_coluna}1");
$planilha_ativa->mergeCells("{$coluna}2:{$ultima_coluna}2");
$planilha_ativa->getStyle("{$coluna}1:{$coluna}2")->getFont()->setBold(true);
$planilha_ativa->getStyle("{$coluna}1:{$ultima_coluna}1")->applyFromArray($cor_borda_sem_baixo);
$planilha_ativa->getStyle("{$coluna}2:{$ultima_coluna}2")->applyFromArray($cor_borda_sem_cima);
$planilha_ativa->getStyle("{$coluna}1:{$ultima_coluna}2")->applyFromArray($cor_cinza_medio)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// Cabeçalho
$coluna = 'A';
if($modo_exibicao_tempo == 'u'){
	$linha_cabecalho_inicio = $linha_cabecalho_fim = '3';
} else {
	$linha_cabecalho_inicio = '3';
	$linha_cabecalho_fim = '4';
}
$checkInseriuDisciplina = false;
foreach($colunas_tabela as $nome=>$descricao){
	if(($modo_exibicao_tempo == 'd') && in_array($nome, array('tempo_analise', 'tempo_desenvolvimento', 'tempo_testes', 'tempo_implantacao', 'tempo'))){
		if(!$checkInseriuDisciplina){
			$checkInseriuDisciplina = true;
			$coluna_final_tempos = funcoes::converteNumeroParaLetra($total_subcolunas_tempo + funcoes::converteLetraParaNumero($coluna) - 1);

			$planilha_ativa->SetCellValue("{$coluna}{$linha_cabecalho_inicio}", 'Tempo (' . (($formato_tempo == 'hm') ? ('Horas / Minutos') : ('Horas')) . ')');
			$planilha_ativa->mergeCells("{$coluna}{$linha_cabecalho_inicio}:{$coluna_final_tempos}{$linha_cabecalho_inicio}");
			$planilha_ativa->getStyle("{$coluna}{$linha_cabecalho_inicio}:{$coluna_final_tempos}{$linha_cabecalho_inicio}")->applyFromArray($cor_cinza_claro)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$planilha_ativa->getStyle("{$coluna}{$linha_cabecalho_fim}:{$coluna_final_tempos}{$linha_cabecalho_fim}")->applyFromArray($cor_cinza_claro);
		}

		$planilha_ativa->SetCellValue("{$coluna}{$linha_cabecalho_fim}", $descricao);
		$colunas_letras[$coluna] = $nome;
		$coluna++;
	} else {
		$planilha_ativa->SetCellValue("{$coluna}{$linha_cabecalho_inicio}", $descricao);
		$planilha_ativa->mergeCells("{$coluna}{$linha_cabecalho_inicio}:{$coluna}{$linha_cabecalho_fim}");
		$planilha_ativa->getStyle("{$coluna}{$linha_cabecalho_inicio}:{$coluna}{$linha_cabecalho_fim}")->applyFromArray($cor_cinza_claro);

		$colunas_letras[$coluna] = $nome;
		$coluna++;
	}
}

$planilha_ativa->getStyle("{$primeira_coluna}{$linha_cabecalho_inicio}:{$ultima_coluna}{$linha_cabecalho_fim}")->getFont()->setBold(true)->getColor()->setRGB('000000');
$planilha_ativa->getStyle("{$primeira_coluna}{$linha_cabecalho_inicio}:{$ultima_coluna}{$linha_cabecalho_fim}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$planilha_ativa->getStyle("{$primeira_coluna}{$linha_cabecalho_inicio}:{$ultima_coluna}{$linha_cabecalho_fim}")->applyFromArray($cor_borda);

// Corpo
$linha = $primeira_linha_corpo = ($modo_exibicao_tempo == 'u') ? (4) : (5);
$totais_gerais = array(
	'valor_pf' => 0,
	'tempo' => array(
		'analise' => 0,
		'desenvolvimento' => 0,
		'testes' => 0,
		'implantacao' => 0,
		'total' => 0
	)
);
$linhas_esconder = array(
	'modulo' => 0,
	'funcionalidade' => 0
);
foreach($componente_rs as $j=>$componente_row){
	$coluna = 'A';
	$rowspan = $componente_row['rowspan'];

	$tempo_total = 0;
	if($modo_exibicao_tempo == 'u'){
		if($formato_tempo == 'ni'){
			$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo, $arredondarZeros);
		} elseif($formato_tempo == 'nr'){
			$tempos = round($componente_row['tempo'], 2);
		} else {
			$tempos = $componente_row['tempo'];
		}

		$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
		$totais_gerais['tempo']['total'] += $tempos;

		if(in_array($formato_tempo, array('hm', 'nr'))){
			$tempos = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo'], $formato_tempo);
		}
	} else {
		if($formato_tempo == 'ni'){
			$tempos = array(
				'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo, $arredondarZeros),
				'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo, $arredondarZeros),
				'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo, $arredondarZeros),
				'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo, $arredondarZeros)
			);
		} elseif($formato_tempo == 'nr'){
			$tempos = array(
				'analise' => round($componente_row['tempo']['analise'], 2),
				'desenvolvimento' => round($componente_row['tempo']['desenvolvimento'], 2),
				'testes' => round($componente_row['tempo']['testes'], 2),
				'implantacao' => round($componente_row['tempo']['implantacao'], 2)
			);
		} else {
			$tempos = array(
				'analise' => $componente_row['tempo']['analise'],
				'desenvolvimento' => $componente_row['tempo']['desenvolvimento'],
				'testes' => $componente_row['tempo']['testes'],
				'implantacao' => $componente_row['tempo']['implantacao']
			);
		}

		$totais_gerais['valor_pf'] += $componente_row['valor_pf'];
		if(isset($esforco_disciplinas['analise']['exibir'])){
			$totais_gerais['tempo']['analise'] += $tempos['analise'];
			$tempo_total += $tempos['analise'];
		}
		if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
			$totais_gerais['tempo']['desenvolvimento'] += $tempos['desenvolvimento'];
			$tempo_total += $tempos['desenvolvimento'];
		}
		if(isset($esforco_disciplinas['testes']['exibir'])){
			$totais_gerais['tempo']['testes'] += $tempos['testes'];
			$tempo_total += $tempos['testes'];
		}
		if(isset($esforco_disciplinas['implantacao']['exibir'])){
			$totais_gerais['tempo']['implantacao'] += $tempos['implantacao'];
			$tempo_total += $tempos['implantacao'];
		}
		$totais_gerais['tempo']['total'] += $tempo_total;

		if(in_array($formato_tempo, array('hm', 'nr'))){
			$tempos = array(
				'analise' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['analise'], $formato_tempo),
				'desenvolvimento' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['desenvolvimento'], $formato_tempo),
				'testes' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['testes'], $formato_tempo),
				'implantacao' => funcoes::encodarTempoPrazosDesenvolvimentoByFormato($componente_row['tempo']['implantacao'], $formato_tempo)
			);
		}
		$tempo_total = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($tempo_total, $formato_tempo, $arredondarZeros);
	}
	
	// Definindo conteúdo do corpo
	if(!$checkModuloUnico){
		if($linhas_esconder['modulo'] > 0){
			$linhas_esconder['modulo'] -= 1;
		} elseif($rowspan > 0){
			if($rowspan > 1){
				$linhas_esconder['modulo'] = ($rowspan - 1);
			}
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['modulo']);
			$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan - 1));
		}
		$coluna++;
	}
	
	if($linhas_esconder['funcionalidade'] > 0){
		$linhas_esconder['funcionalidade'] -= 1;
	} elseif($rowspan > 0){
		if($rowspan > 1){
			$linhas_esconder['funcionalidade'] = ($rowspan - 1);
		}
		
		$nome_funcionalidade = '';
		if($mostrarOrdem) $nome_funcionalidade .= $componente_row['ordem_funcionalidade'] . '. ';
		$nome_funcionalidade .= $componente_row['funcionalidade'];
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $nome_funcionalidade);
		$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan - 1));
	}
	$coluna++;
	
	$nome_componente = '';
	if($mostrarOrdem) $nome_componente .= ($componente_row['ordem_componente'] . '. ');
	$nome_componente .= $componente_row['componente'];
	$planilha_ativa->SetCellValue("{$coluna}{$linha}", $nome_componente);
	$coluna++;
	
	
	if($mostrarComplexidade){
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['complexidade']);
		$coluna++;
	}
	
	if($mostrarValorPF){
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['valor_pf']);
		$coluna++;
	}
	
	if($modo_exibicao_tempo == 'd'){
		if(isset($esforco_disciplinas['analise']['exibir'])){
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempos['analise']);
			$coluna++;
		}
		
		if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempos['desenvolvimento']);
			$coluna++;
		}
		
		if(isset($esforco_disciplinas['testes']['exibir'])){
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempos['testes']);
			$coluna++;
		}
		
		if(isset($esforco_disciplinas['implantacao']['exibir'])){
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempos['implantacao']);
			$coluna++;
		}
		
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempo_total);
		$planilha_ativa->getStyle("{$coluna}{$linha}")->getFont()->setBold(true);
		$coluna++;
	} else {
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $tempos);
		$planilha_ativa->getStyle("{$coluna}{$linha}")->getFont()->setBold(true);
		$coluna++;
	}
	
	$linha++;
}
$ultima_linha_corpo = $linha - 1;

// Definindo borda nas células do corpo
$planilha_ativa->getStyle("{$primeira_coluna}{$primeira_linha_corpo}:{$ultima_coluna}{$ultima_linha_corpo}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
$planilha_ativa->getStyle("{$primeira_coluna}{$primeira_linha_corpo}:{$ultima_coluna}{$ultima_linha_corpo}")->applyFromArray($cor_borda);

// Rodapé
$colspan_totais = 4;
if($checkModuloUnico) $colspan_totais--;
if($checkFuncionalidadeUnica) $colspan_totais--;
if(!$mostrarComplexidade) $colspan_totais--;
$coluna_final_totais = funcoes::converteNumeroParaLetra($colspan_totais);

$planilha_ativa->SetCellValue("{$primeira_coluna}{$linha}", 'Total:');
$planilha_ativa->mergeCells("{$primeira_coluna}{$linha}:{$coluna_final_totais}{$linha}");
$planilha_ativa->getStyle("{$primeira_coluna}{$linha}:{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$coluna_final_totais++;

if($mostrarValorPF){
	$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $totais_gerais['valor_pf']);
	$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
	$coluna_final_totais++;
}

if($modo_exibicao_tempo == 'd'){
	if(isset($esforco_disciplinas['analise']['exibir'])){
		$tempo_total_analise = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['analise'], $formato_tempo, $arredondarZeros);
		$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $tempo_total_analise);
		$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
		$coluna_final_totais++;
	}

	if(isset($esforco_disciplinas['desenvolvimento']['exibir'])){
		$tempo_total_desenvolvimento = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['desenvolvimento'], $formato_tempo, $arredondarZeros);
		$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $tempo_total_desenvolvimento);
		$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
		$coluna_final_totais++;
	}

	if(isset($esforco_disciplinas['testes']['exibir'])){
		$tempo_total_testes = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['testes'], $formato_tempo, $arredondarZeros);
		$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $tempo_total_testes);
		$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
		$coluna_final_totais++;
	}

	if(isset($esforco_disciplinas['implantacao']['exibir'])){
		$tempo_total_implantacao = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['implantacao'], $formato_tempo, $arredondarZeros);
		$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $tempo_total_implantacao);
		$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
		$coluna_final_totais++;
	}
}

$tempo_total_geral = funcoes::encodarTempoPrazosDesenvolvimentoByFormato($totais_gerais['tempo']['total'], $formato_tempo, $arredondarZeros);
$planilha_ativa->SetCellValue("{$coluna_final_totais}{$linha}", $tempo_total_geral);
$planilha_ativa->getStyle("{$coluna_final_totais}{$linha}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
$coluna_final_totais++;

// Estilizações gerais do rodapé
$planilha_ativa->getStyle("{$primeira_coluna}{$linha}:{$ultima_coluna}{$linha}")->getFont()->setBold(true)->getColor()->setRGB('000000');
$planilha_ativa->getStyle("{$primeira_coluna}{$linha}:{$ultima_coluna}{$linha}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$planilha_ativa->getStyle("{$primeira_coluna}{$linha}:{$ultima_coluna}{$linha}")->applyFromArray($cor_cinza_medio)->applyFromArray($cor_borda);

// Definindo ajuste automático de largura
$intervalo_colunas = range($primeira_coluna, $ultima_coluna);
foreach($intervalo_colunas as $letra){
	if($colunas_letras[$letra] == 'modulo'){
		$planilha_ativa->getColumnDimension($letra)->setWidth(10);
	} elseif($colunas_letras[$letra] == 'funcionalidade'){
		$planilha_ativa->getColumnDimension($letra)->setWidth(25);
	} elseif($colunas_letras[$letra] == 'componente' && ($modo_exibicao_tempo == 'd')){
		$planilha_ativa->getColumnDimension($letra)->setWidth(25);
	} elseif($colunas_letras[$letra] == 'tipo_funcional' && ($modo_exibicao_tempo == 'd')){
		$planilha_ativa->getColumnDimension($letra)->setWidth(15);
	} elseif($colunas_letras[$letra] == 'tempo'){
		$planilha_ativa->getColumnDimension($letra)->setWidth(17.5);
	} elseif($colunas_letras[$letra] == 'complexidade'){
		$planilha_ativa->getColumnDimension($letra)->setWidth(13.5);
	} elseif($colunas_letras[$letra] == 'valor_pf'){
		$planilha_ativa->getColumnDimension($letra)->setWidth(10.5);
	} else {
		$planilha_ativa->getColumnDimension($letra)->setAutoSize(true);
	}
}

// Setar índice da planilha ativa para a primeira planilha,
// para o Excel abri-la como a primeira planilha
$arquivo_xls->setActiveSheetIndex(0);

// Redirecionar saída para o navegador do cliente (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="prazo_desenvolvimento_funcionalidades.xls"');
header('Cache-Control: max-age=0');

// Se usuário estiver usando IE 9, o código abaixo pode se fazer necessário
header('Cache-Control: max-age=1');

// Se usuário estiver usandi IE via SSL, o código abaixo pode se fazer necessário
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Data no passado
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // sempre modificado
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objeto_escrita = PHPExcel_IOFactory::createWriter($arquivo_xls, 'Excel5');
$objeto_escrita->save('php://output');
exit;