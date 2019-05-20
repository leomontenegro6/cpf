<?php
// Arrays de estilizações
$estilizacoes = array(
	// Principais
	'titulo' => array(
		'fill' => array(
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'color' => array('rgb' => 'DDDDDD')
		),
		'font' => array(
			'bold' => true,
			'size' => 10
		),
		'borders' => array(
			'top' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
			'left' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
			'right' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
			'bottom' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE)
		),
		'alignment' => array(
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
		)
	),
	'subtitulo' => array(
		'fill' => array(
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'color' => array('rgb' => 'DDDDDD')
		),
		'font' => array(
			'bold' => true,
			'size' => 10
		),
		'borders' => array(
			'top' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE),
			'left' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
			'right' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
			'bottom' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => array('rgb' => '000000'))
		),
		'alignment' => array(
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
		)
	),
	'cabecalho' => array(
		'font' => array(
			'bold' => true,
		),
		'fill' => array(
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'color' => array('rgb' => 'EEEEEE')
		),
		'borders' => array(
			'allBorders' => array(
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		),
		'alignment' => array(
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
		)
	),
	'corpo' => array(
		'borders' => array(
			'allBorders' => array(
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		),
		'alignment' => array(
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
		)
	),
	'rodape' => array(
		'font' => array(
			'bold' => true,
		),
		'fill' => array(
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'color' => array('rgb' => 'EEEEEE')
		),
		'borders' => array(
			'allBorders' => array(
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('rgb' => '000000')
			)
		),
		'alignment' => array(
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
		)
	),
	// Adicionais
	'negrito' => array(
		'font' => array(
			'bold' => true,
		)
	)
);