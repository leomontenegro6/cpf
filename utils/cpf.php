<?php
class cpf{	
	
	public static function calcularComplexidade($tipo_funcional, $quantidade_tipos_dados=-1, $quantidade_arquivos_referenciados=-1){
		$complexidade = '';
		
		if($tipo_funcional == 'e'){
			if($quantidade_arquivos_referenciados >= 0 && $quantidade_arquivos_referenciados <= 1){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 4){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 5 && $quantidade_tipos_dados <= 15){
					$complexidade = 'baixa';
				} else if ($quantidade_tipos_dados >= 16){
					$complexidade = 'media';
				}
			} else if($quantidade_arquivos_referenciados == 2){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 4){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 5 && $quantidade_tipos_dados <= 15){
					$complexidade = 'media';
				} else if ($quantidade_tipos_dados >= 16){
					$complexidade = 'alta';
				}
			} else if($quantidade_arquivos_referenciados >= 3){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 4){
					$complexidade = 'media';
				} else if($quantidade_tipos_dados >= 5 && $quantidade_tipos_dados <= 15){
					$complexidade = 'alta';
				} else if ($quantidade_tipos_dados >= 16){
					$complexidade = 'alta';
				}
			}
		} else if($tipo_funcional == 's'){
			if($quantidade_arquivos_referenciados >= 0 && $quantidade_arquivos_referenciados <= 1){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'baixa';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'media';
				}
			} else if($quantidade_arquivos_referenciados >= 2 && $quantidade_arquivos_referenciados <= 3){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'media';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'alta';
				}
			} else if($quantidade_arquivos_referenciados >= 4){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'media';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'alta';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'alta';
				}
			}
		} else if($tipo_funcional == 'c'){
			if($quantidade_arquivos_referenciados == 1){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'baixa';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'media';
				}
			} else if($quantidade_arquivos_referenciados >= 2 && $quantidade_arquivos_referenciados <= 3){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'baixa';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'media';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'alta';
				}
			} else if($quantidade_arquivos_referenciados >= 4){
				if($quantidade_tipos_dados >= 1 && $quantidade_tipos_dados <= 5){
					$complexidade = 'media';
				} else if($quantidade_tipos_dados >= 6 && $quantidade_tipos_dados <= 19){
					$complexidade = 'alta';
				} else if ($quantidade_tipos_dados >= 20){
					$complexidade = 'alta';
				}
			}
		}
		
		return $complexidade;
	}
	
	public static function calcularValor($tipo_funcional, $complexidade){
		$valor = 0;
		
		if($tipo_funcional == 'e'){
			if($complexidade == 'baixa'){
				$valor = 3;
			} else if($complexidade == 'media'){
				$valor = 4;
			} else if($complexidade == 'alta'){
				$valor = 6;
			}
		} else if($tipo_funcional == 's'){
			if($complexidade == 'baixa'){
				$valor = 4;
			} else if($complexidade == 'media'){
				$valor = 5;
			} else if($complexidade == 'alta'){
				$valor = 6;
			}
		} else if($tipo_funcional == 'c'){
			if($complexidade == 'baixa'){
				$valor = 3;
			} else if($complexidade == 'media'){
				$valor = 4;
			} else if($complexidade == 'alta'){
				$valor = 6;
			}
		}
		
		return $valor;
	}
	
	public static function calcularTempoDesenvolvimentoPorEstimativaEsforco($valor_pf, $recursos, $tempo_dedicacao, $indice_produtividade, $formato_tempo){
		$tipo_formato_tempo = substr($formato_tempo, 0, 1);
		
		$tempo_desenvolvimento = ($valor_pf * $indice_produtividade / ($recursos * $tempo_dedicacao));
		
		if($tipo_formato_tempo == 'h'){
			// Horas
			return $tempo_desenvolvimento * 24;
		} elseif($tipo_formato_tempo == 'd'){
			// Dias
			return $tempo_desenvolvimento;
		} elseif($tipo_formato_tempo == 'm'){
			// Meses
			return $tempo_desenvolvimento / 30;
		} else {
			return 0;
		}
	}
	
	public static function calcularTempoDesenvolvimentoPorFormulaCapersJones($total_pf, $expoente_capers_jones, $formato_tempo){
		$tipo_formato_tempo = substr($formato_tempo, 0, 1);
		
		$tempo_desenvolvimento = pow($total_pf, $expoente_capers_jones);
		
		if($tipo_formato_tempo == 'h'){
			// Horas
			return $tempo_desenvolvimento * 30 * 24;
		} elseif($tipo_formato_tempo == 'd'){
			// Dias
			return $tempo_desenvolvimento * 30;
		} elseif($tipo_formato_tempo == 'm'){
			// Meses
			return $tempo_desenvolvimento;
		} else {
			return 0;
		}
	}
	
	public static function calcularCustoDesenvolvimentoPorEstimativaEsforco($metodo_calculo_orcamento, $tempo, $valor_ponto_funcao, $valor_hora_trabalhada, $formato_tempo){
		$tipo_formato_tempo = substr($formato_tempo, 0, 1);
		
		// Calculando valor, em função da multiplicação do tempo pelo valor,
		// dependendo do método de cálculo de orçamento
		if($metodo_calculo_orcamento == 'vpf'){
			$custo = ($tempo * $valor_ponto_funcao);
		} elseif($metodo_calculo_orcamento == 'vht'){
			$custo = ($tempo * $valor_hora_trabalhada);
		} else {
			$custo = 0;
		}
		
		// Ajustando custo, em função do formato de tempo
		if($tipo_formato_tempo == 'h'){
			// Horas
			return $custo;
		} elseif($tipo_formato_tempo == 'd'){
			// Dias
			return $custo * 24;
		} elseif($tipo_formato_tempo == 'm'){
			// Meses
			return $custo * 24 * 30;
		} else {
			return 0;
		}
	}
	
	public static function calcularCustoDesenvolvimentoPorFormulaCapersJones($metodo_calculo_orcamento, $quantidade_pontos_funcao, $valor_ponto_funcao, $valor_hora_trabalhada){
		if($metodo_calculo_orcamento == 'vpf'){
			return ($quantidade_pontos_funcao * $valor_ponto_funcao);
		} elseif($metodo_calculo_orcamento == 'vht'){
			return ($quantidade_pontos_funcao * $valor_hora_trabalhada);
		} else {
			return 0;
		}
	}
}