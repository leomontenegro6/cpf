<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$componente = new componente();
$sistema = new sistema();
$modulo = new modulo();

$id_sistema = (isset($_GET['sistema'])) ? ($_GET['sistema']) : ('');
$id_modulo = (isset($_GET['modulo'])) ? ($_GET['modulo']) : ('');
$detalhar_campos_arquivos = (isset($_GET['detalhar_campos_arquivos']) && ($_GET['detalhar_campos_arquivos'] == 'true'));

if(is_numeric($id_sistema)){
	$nome_sistema = $sistema->getNome($id_sistema, 'n');
	$moduloSistema_rs = $modulo->getBySistema($id_sistema);
} else {
	$nome_sistema = '';
	$moduloSistema_rs = array();
}
if(is_numeric($id_modulo)){
	$nome_modulo = $modulo->getNome($id_modulo, 'n');
	$checkModuloUnico = true;
} else {
	$nome_modulo = '';
	if(is_numeric($id_sistema)){
		$checkModuloUnico = (count( $modulo->getBySistema($id_sistema) ) == 1);
	} else {
		$checkModuloUnico = false;
	}
}

$componente_rs = $componente->getByPlanilhaContagemPontos($id_sistema, $id_modulo, $detalhar_campos_arquivos);
$colunas_tabela = array(
	'funcionalidade' => 'Funcionalidade', 'componente' => 'Componente',
	'tipo_funcional' => 'Tipo Funcional', 'tipos_dados' => 'Tipos de Dados',
	'arquivos_referenciados' => 'Arquivos Referenciados',
	'complexidade' => 'Complexidade', 'valor_pf' => 'Valor (PF)'
);
if(!$detalhar_campos_arquivos){
	$colunas_tabela['tipos_dados'] = "Tipos de\nDados";
	$colunas_tabela['arquivos_referenciados'] = "Arquivos\nReferenciados";
}
if(!$checkModuloUnico){
	$colunas_tabela = array_merge(array('modulo' => 'Módulo'), $colunas_tabela);

}
$total_colunas_tabela = count($colunas_tabela);

if($checkModuloUnico){
	if(empty($nome_modulo)) $nome_modulo = $moduloSistema_rs['0']['nome'];
	$titulo_caption = $nome_sistema . ' - Módulo ' . $nome_modulo;
} else {
	$titulo_caption = $nome_sistema;
}

// Geração do XLS
include('../common/relatorios/phpexcel/PHPExcel.php');

$arquivo_xls = new PHPExcel();

$arquivo_xls->getProperties()->setCreator("Leonardo José Montenegro Santiago")->setTitle("Planilha de Contagem de Pontos");

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
$planilha_ativa->setTitle("Contagem de Pontos");

// Configurações de impressão
$planilha_ativa->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$planilha_ativa->getPageSetup()->setScale(85);

// Caption
$planilha_ativa->SetCellValue("{$coluna}1", $titulo_caption);
$planilha_ativa->SetCellValue("{$coluna}2", 'Contabilização de Pontos de Função');
$planilha_ativa->mergeCells("{$coluna}1:{$ultima_coluna}1");
$planilha_ativa->mergeCells("{$coluna}2:{$ultima_coluna}2");
$planilha_ativa->getStyle("{$coluna}1:{$coluna}2")->getFont()->setBold(true);
$planilha_ativa->getStyle("{$coluna}1:{$ultima_coluna}1")->applyFromArray($cor_borda_sem_baixo);
$planilha_ativa->getStyle("{$coluna}2:{$ultima_coluna}2")->applyFromArray($cor_borda_sem_cima);
$planilha_ativa->getStyle("{$coluna}1:{$ultima_coluna}2")->applyFromArray($cor_cinza_medio)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// Cabeçalho
$coluna = 'A';
if($detalhar_campos_arquivos){
	$linha_cabecalho_inicio = $linha_cabecalho_fim = '3';
} else {
	$linha_cabecalho_inicio = '3';
	$linha_cabecalho_fim = '4';
}
foreach($colunas_tabela as $nome=>$descricao){
	if(in_array($nome, array('tipos_dados', 'arquivos_referenciados')) && !$detalhar_campos_arquivos){
		if($nome == 'tipos_dados'){
			$coluna_final_quantidades = funcoes::converteNumeroParaLetra(2 + funcoes::converteLetraParaNumero($coluna) - 1);

			$planilha_ativa->SetCellValue("{$coluna}{$linha_cabecalho_inicio}", 'Quantidades'); // Colspan até 2
			$planilha_ativa->mergeCells("{$coluna}{$linha_cabecalho_inicio}:{$coluna_final_quantidades}{$linha_cabecalho_inicio}");
			$planilha_ativa->getStyle("{$coluna}{$linha_cabecalho_inicio}:{$coluna_final_quantidades}{$linha_cabecalho_inicio}")->applyFromArray($cor_cinza_claro)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$planilha_ativa->getStyle("{$coluna}{$linha_cabecalho_fim}:{$coluna_final_quantidades}{$linha_cabecalho_fim}")->applyFromArray($cor_cinza_claro);
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
$linha = $primeira_linha_corpo = ($detalhar_campos_arquivos) ? (4) : (5);
$valor_total_pf = 0;
$linhas_esconder = array(
	'modulo' => 0,
	'funcionalidade' => 0
);
foreach($componente_rs as $j=>$componente_row){
	$id_tipo_componente = $componente_row['id_tipo_componente'];
	$coluna = 'A';
	$rowspan_funcionalidade_modulo = $componente_row['rowspan_funcionalidade_modulo'];
	$rowspan_componente = $componente_row['rowspan_componente'];
	$valor_total_pf += $componente_row['valor_pf'];
	
	$campo_rs = $componente_row['campos'];
	$arquivoReferenciado_rs = $componente_row['arquivos_referenciados'];

	$quantidade_tipos_dados = $componente_row['quantidade_tipos_dados'];
	$quantidade_arquivos_referenciados = $componente_row['quantidade_arquivos_referenciados'];

	if($detalhar_campos_arquivos){
		if($quantidade_tipos_dados >= $quantidade_arquivos_referenciados){
			$rowspan_campos_arquivos = $quantidade_tipos_dados;
		} else {
			$rowspan_campos_arquivos = $quantidade_arquivos_referenciados;
		}
	} else {
		$rowspan_campos_arquivos = 1;
	}
	
	if($id_tipo_componente == 2){
		$categoria_tipo_dado = 'Coluna';
	} else {
		$categoria_tipo_dado = 'Campo';
	}
	
	// Definindo conteúdo do corpo
	if(!$checkModuloUnico){
		if($linhas_esconder['modulo'] > 0){
			$linhas_esconder['modulo'] -= $rowspan_componente;
		} elseif($rowspan_funcionalidade_modulo > 0){
			if($rowspan_funcionalidade_modulo > 1){
				$linhas_esconder['modulo'] = ($rowspan_funcionalidade_modulo - $rowspan_componente);
			}
			$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['modulo']);
			$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_funcionalidade_modulo - 1));
		}
		$coluna++;
	}
	
	if($linhas_esconder['funcionalidade'] > 0){
		$linhas_esconder['funcionalidade'] -= $rowspan_componente;
	} elseif($rowspan_funcionalidade_modulo > 0){
		if($rowspan_funcionalidade_modulo > 1){
			$linhas_esconder['funcionalidade'] = ($rowspan_funcionalidade_modulo - $rowspan_componente);
		}
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['funcionalidade']);
		$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_funcionalidade_modulo - 1));
	}
	$coluna++;
	
	$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['componente']);
	$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
	$coluna++;
	
	$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['tipo_funcional']);
	$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
	$coluna++;
	
	if($detalhar_campos_arquivos){
		$nome_campo = $campo_rs[0]['nome'];
		if(substr($nome_campo, 0, 5) == 'Campo'){
			$nome_campo = trim( substr($nome_campo, 5) );
			$primeiro_campo = "$categoria_tipo_dado $nome_campo";
		} else {
			$primeiro_campo = "$categoria_tipo_dado \"$nome_campo\"";
		}
		$primeiro_arquivo = $arquivoReferenciado_rs[0]['nome'];
		
		$coluna_tipos_dados = $coluna;
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $primeiro_campo);
		$coluna++;
		
		$coluna_arquivos_referenciados = $coluna;
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $primeiro_arquivo);
		$coluna++;
	} else {
		$coluna_tipos_dados = $coluna;
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['quantidade_tipos_dados']);
		$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
		$coluna++;
		
		$coluna_arquivos_referenciados = $coluna;
		$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['quantidade_arquivos_referenciados']);
		$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
		$coluna++;
	}
	
	$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['complexidade']);
	$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
	$coluna++;
	
	$planilha_ativa->SetCellValue("{$coluna}{$linha}", $componente_row['valor_pf']);
	$planilha_ativa->mergeCells("{$coluna}{$linha}:{$coluna}" . ($linha + $rowspan_campos_arquivos - 1));
	$coluna++;
	
	// Exibindo restante dos campos e arquivos, no caso de estar detalhando-os
	if($detalhar_campos_arquivos){
		$checkPossuiAcoesInserido = $checkPossuiMensagensInserido = false;
		for($i=1; $i<$rowspan_campos_arquivos; $i++){
			if(isset($campo_rs[$i]['nome'])){
				$nome_campo = $campo_rs[$i]['nome'];
				if(substr($nome_campo, 0, 5) == 'Campo'){
					$nome_campo = trim( substr($nome_campo, 5) );
					$nome_campo = "$categoria_tipo_dado $nome_campo";
				} else {
					$nome_campo = "$categoria_tipo_dado \"$nome_campo\"";
				}
			} elseif($componente_row['possui_acoes'] == '1' && !$checkPossuiAcoesInserido){
				$nome_campo = 'Possui Ações';
				$checkPossuiAcoesInserido = true;
			} elseif($componente_row['possui_mensagens'] == '1' && !$checkPossuiMensagensInserido){
				$nome_campo = 'Possui Mensagens';
				$checkPossuiMensagensInserido = true;
			} else {
				$nome_campo = '';
			}

			$nome_arquivo = (isset($arquivoReferenciado_rs[$i]['nome'])) ? ($arquivoReferenciado_rs[$i]['nome']) : ('');
			
			$planilha_ativa->SetCellValue("{$coluna_tipos_dados}" . ($linha + $i), $nome_campo);
			$planilha_ativa->SetCellValue("{$coluna_arquivos_referenciados}" . ($linha + $i), $nome_arquivo);
		}
	}
	
	if($detalhar_campos_arquivos){
		$linha += $rowspan_campos_arquivos;
	} else {
		$linha++;
	}
}
$ultima_linha_corpo = $linha - 1;

// Definindo borda nas células do corpo
$planilha_ativa->getStyle("{$primeira_coluna}{$primeira_linha_corpo}:{$ultima_coluna}{$ultima_linha_corpo}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
$planilha_ativa->getStyle("{$primeira_coluna}{$primeira_linha_corpo}:{$ultima_coluna}{$ultima_linha_corpo}")->applyFromArray($cor_borda);

// Rodapé
$colspan_rodape_branco = ($checkModuloUnico) ? (5) : (6);
$coluna_final_branco = funcoes::converteNumeroParaLetra($colspan_rodape_branco);

$planilha_ativa->SetCellValue("{$primeira_coluna}{$linha}", ''); // Colspan até a antepenúltima coluna
$planilha_ativa->mergeCells("{$primeira_coluna}{$linha}:{$coluna_final_branco}{$linha}");
$coluna_final_branco++;

$planilha_ativa->SetCellValue("{$coluna_final_branco}{$linha}", 'Total:');
$coluna_final_branco++;

$planilha_ativa->SetCellValue("{$coluna_final_branco}{$linha}", $valor_total_pf);
$coluna_final_branco++;

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
	} elseif($colunas_letras[$letra] == 'componente' && $detalhar_campos_arquivos){
		$planilha_ativa->getColumnDimension($letra)->setWidth(25);
	} elseif($colunas_letras[$letra] == 'tipo_funcional' && $detalhar_campos_arquivos){
		$planilha_ativa->getColumnDimension($letra)->setWidth(15);
	} elseif($colunas_letras[$letra] == 'tipos_dados'){
		if($detalhar_campos_arquivos){
			$planilha_ativa->getColumnDimension($letra)->setWidth(17.5);
		} else {
			$planilha_ativa->getRowDimension(4)->setRowHeight(27.5);
		}
	} elseif($colunas_letras[$letra] == 'arquivos_referenciados' && $detalhar_campos_arquivos){
		$planilha_ativa->getColumnDimension($letra)->setWidth(22.5);
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
header('Content-Disposition: attachment;filename="planilha_contagem_pontos.xls"');
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