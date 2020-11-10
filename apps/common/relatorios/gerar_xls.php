<?php
session_start();
ini_set('max_execution_time', 300);
include_once('../../../utils/funcoes.php');

if(!isset($_SESSION['login'])){
	header('Location: ../../inicial/index.php?sessao_expirada=true');
	exit;
}

session_write_close();

require_once("phpspreadsheet/autoload.php");

use PhpOffice\PhpSpreadsheet\Writer\Xls;

$leitor_html = new \PhpOffice\PhpSpreadsheet\Reader\Html();

$titulo = $_POST['titulo'];
$subtitulo = $_POST['subtitulo'];
$arquivo = $_POST['arquivo'];
$nome_arquivo_zip = $_POST['arquivo']['nome'];
$caminho_arquivo_zip = $arquivo['caminho'];
$nome_arquivo = str_replace('.zip', '.xls', $nome_arquivo_zip);
$nome_arquivo = utf8_decode($nome_arquivo);
$coordenadas_linhas_tabela = $_POST['coordenadas_linhas_tabela'];
$classes_celulas = (isset($_POST['classes_celulas'])) ? ($_POST['classes_celulas']) : (array());

foreach($coordenadas_linhas_tabela as $i=>$coordenadas){
	$coordenadas_linhas_tabela[$i] = explode(',', $coordenadas);
}

if(!file_exists($caminho_arquivo_zip)){
	die('O arquivo da planilha, enviado ao servidor, não existe.');
}

$zip = new ZipArchive();

$handler_arquivo = $zip->open($caminho_arquivo_zip);
if($handler_arquivo !== true){
	die('Erro ao abrir arquivo compactado.');
}

$arquivo_html = $zip->getNameIndex(0);
$zip->extractTo('/tmp/');
$zip->close();

$caminho_arquivo_html = '/tmp/' . $arquivo_html;
if(!file_exists($caminho_arquivo_html)){
	unlink($caminho_arquivo_zip);
	die('Erro ao abrir arquivo html do relatório.');
}

// Tabela HTML convertida para planilha XLS
$arquivo_xls = $leitor_html->load($caminho_arquivo_html);
unlink($caminho_arquivo_html);

$arquivo_xls->getProperties()->setCreator("Leonardo José Montenegro Santiago")->setTitle($titulo);

$arquivo_xls->setActiveSheetIndex(0);
$planilha_ativa = $arquivo_xls->getActiveSheet();

// Configurações globais
\PhpOffice\PhpSpreadsheet\Shared\Font::setTrueTypeFontPath('/usr/share/fonts/truetype/msttcorefonts/');
\PhpOffice\PhpSpreadsheet\Shared\Font::setAutoSizeMethod(\PhpOffice\PhpSpreadsheet\Shared\Font::AUTOSIZE_METHOD_EXACT);

// Estilizações padrões da planilha
$arquivo_xls->getDefaultStyle()->getFont()->setName('Arial');
$arquivo_xls->getDefaultStyle()->getFont()->setSize(10);

// Arrays de estilizações
include('phpspreadsheet_estilizacoes.php');

// Nome da planilha
$planilha_ativa->setTitle( substr($titulo, 0, 31) );

// Configurações de impressão
$planilha_ativa->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$planilha_ativa->getPageSetup()->setScale(85);

// Obtendo informações de coordenadas de linhas e colunas em uso na planilha
$primeira_coluna = 'A';
$ultima_coluna = $planilha_ativa->getHighestColumn();
$primeira_linha = 1;
$ultima_linha = $planilha_ativa->getHighestRow();
$linha_titulo = $coordenadas_linhas_tabela['titulo_subtitulo'][0];
$linha_subtitulo = $coordenadas_linhas_tabela['titulo_subtitulo'][1];
$primeira_linha_cabecalho = $coordenadas_linhas_tabela['cabecalho'][0];
$ultima_linha_cabecalho = end($coordenadas_linhas_tabela['cabecalho']);
$primeira_linha_corpo = $coordenadas_linhas_tabela['corpo'][0];
$ultima_linha_corpo = end($coordenadas_linhas_tabela['corpo']);
$primeira_linha_rodape = $coordenadas_linhas_tabela['rodape'][0];
$ultima_linha_rodape = end($coordenadas_linhas_tabela['rodape']);

$intervalo_celulas_titulo = "{$primeira_coluna}{$linha_titulo}:{$ultima_coluna}{$linha_titulo}";
$intervalo_celulas_subtitulo = "{$primeira_coluna}{$linha_subtitulo}:{$ultima_coluna}{$linha_subtitulo}";
$intervalo_celulas_cabecalho = "{$primeira_coluna}{$primeira_linha_cabecalho}:{$ultima_coluna}{$ultima_linha_cabecalho}";
$intervalo_celulas_corpo = "{$primeira_coluna}{$primeira_linha_corpo}:{$ultima_coluna}{$ultima_linha_corpo}";
$intervalo_celulas_rodape = "{$primeira_coluna}{$primeira_linha_rodape}:{$ultima_coluna}{$ultima_linha_rodape}";

// Personalizando layout do título da planilha
$planilha_ativa->mergeCells($intervalo_celulas_titulo);
$planilha_ativa->getStyle($intervalo_celulas_titulo)->applyFromArray($estilizacoes['titulo']);

// Personalizando layout do subtítulo da planilha
$planilha_ativa->mergeCells($intervalo_celulas_subtitulo);
$planilha_ativa->getStyle($intervalo_celulas_subtitulo)->applyFromArray($estilizacoes['subtitulo']);

// Personalizando layout do cabecalho, corpo e rodapé da planilha
$planilha_ativa->getStyle($intervalo_celulas_cabecalho)->applyFromArray($estilizacoes['cabecalho']);
$planilha_ativa->getStyle($intervalo_celulas_corpo)->applyFromArray($estilizacoes['corpo']);
$planilha_ativa->getStyle($intervalo_celulas_rodape)->applyFromArray($estilizacoes['rodape']);

// Obtendo classa de estilização das células, com base no atributo HTML
// "data-phpspreadsheet-classe" nos elementos <td> ou <th>
foreach($classes_celulas as $coordenadas=>$classe){
	$planilha_ativa->getStyle($coordenadas)->applyFromArray($estilizacoes[$classe]);
}

// Iterando colunas, para realizar personalizações finais
$intervalo_colunas = range($primeira_coluna, $ultima_coluna);
foreach($intervalo_colunas as $letra){
	// Definindo ajuste automático de largura, na planilha inteira
	$planilha_ativa->getColumnDimension($letra)->setAutoSize(true);
	
	// Caso o cabeçalho possua duas linhas ou mais, e na primeira linha exista
	// alguma célula mesclada horizontalmente, então definir alinhamento centralizado
	if($ultima_linha_cabecalho > $primeira_linha_cabecalho){
		for($i=$primeira_linha_cabecalho; $i<=$ultima_linha_cabecalho; $i++){
			if($i == $primeira_linha_cabecalho){
				$celula = $planilha_ativa->getCell("{$letra}{$i}");
				$intervalo_mesclagem = $celula->getMergeRange();
				if($intervalo_mesclagem !== false){
					$intervalo_mesclagem = explode(':', $intervalo_mesclagem);
					$intervalo_mesclagem_inicial = $intervalo_mesclagem[0];
					$intervalo_mesclagem_final = $intervalo_mesclagem[1];
					
					$coluna_intervalo_mesclagem_inicial = $intervalo_mesclagem_inicial[0];
					$linha_intervalo_mesclagem_inicial = $intervalo_mesclagem_inicial[1];
					$coluna_intervalo_mesclagem_final = $intervalo_mesclagem_final[0];
					$linha_intervalo_mesclagem_final = $intervalo_mesclagem_final[1];
					
					if(($linha_intervalo_mesclagem_inicial == $linha_intervalo_mesclagem_final) && ($coluna_intervalo_mesclagem_inicial != $coluna_intervalo_mesclagem_final)){
						$planilha_ativa->getStyle("{$letra}{$i}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					}
				}
			}
		}
	}
	
	// Caso haja, no rodapé, alguma célula com nome "TOTAL" ou "TOTAIS", definir
	// alinhamento para a direita
	for($i=$primeira_linha_rodape; $i<=$ultima_linha_rodape; $i++){
		$conteudo = funcoes::upper($planilha_ativa->getCell("{$letra}{$i}")->getValue());
		
		if(in_array($conteudo, array('TOTAL', 'TOTAL:', 'TOTAIS', 'TOTAIS:'))){
			$planilha_ativa->getStyle("{$letra}{$i}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		}
	}
}

// Setar índice da planilha ativa para a primeira planilha,
// para o Excel abri-la como a primeira planilha
$arquivo_xls->setActiveSheetIndex(0);

// Redirecionar saída para o navegador do cliente (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $nome_arquivo . '"');
header('Cache-Control: max-age=0');

// Se usuário estiver usando IE 9, o código abaixo pode se fazer necessário
header('Cache-Control: max-age=1');

// Se usuário estiver usandi IE via SSL, o código abaixo pode se fazer necessário
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Data no passado
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // sempre modificado
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

// Gerando arquivo da planilha
$objeto_escrita = new Xls($arquivo_xls);
$objeto_escrita->save('php://output');
exit;
