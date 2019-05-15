/* Biblioteca javascript do Contador de Pontos de Função
 * 
 */

function cpf(){
	
	// Propriedades
	
	
	// Métodos
	this.calcularComplexidade = function(tipo_funcional, quantidade_tipos_dados, quantidade_arquivos_referenciados){
		var complexidade = '';
		
		if(isNaN(quantidade_tipos_dados)) quantidade_tipos_dados = -1;
		if(isNaN(quantidade_arquivos_referenciados)) quantidade_arquivos_referenciados = -1;
		
		if(tipo_funcional == 'e'){
			if(quantidade_arquivos_referenciados >= 0 && quantidade_arquivos_referenciados <= 1){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 4){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 5 && quantidade_tipos_dados <= 15){
					complexidade = 'baixa';
				} else if (quantidade_tipos_dados >= 16){
					complexidade = 'media';
				}
			} else if(quantidade_arquivos_referenciados == 2){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 4){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 5 && quantidade_tipos_dados <= 15){
					complexidade = 'media';
				} else if (quantidade_tipos_dados >= 16){
					complexidade = 'alta';
				}
			} else if(quantidade_arquivos_referenciados >= 3){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 4){
					complexidade = 'media';
				} else if(quantidade_tipos_dados >= 5 && quantidade_tipos_dados <= 15){
					complexidade = 'alta';
				} else if (quantidade_tipos_dados >= 16){
					complexidade = 'alta';
				}
			}
		} else if(tipo_funcional == 's'){
			if(quantidade_arquivos_referenciados >= 0 && quantidade_arquivos_referenciados <= 1){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'baixa';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'media';
				}
			} else if(quantidade_arquivos_referenciados >= 2 && quantidade_arquivos_referenciados <= 3){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'media';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'alta';
				}
			} else if(quantidade_arquivos_referenciados >= 4){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'media';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'alta';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'alta';
				}
			}
		} else if(tipo_funcional == 'c'){
			if(quantidade_arquivos_referenciados == 1){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'baixa';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'media';
				}
			} else if(quantidade_arquivos_referenciados >= 2 && quantidade_arquivos_referenciados <= 3){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'baixa';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'media';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'alta';
				}
			} else if(quantidade_arquivos_referenciados >= 4){
				if(quantidade_tipos_dados >= 1 && quantidade_tipos_dados <= 5){
					complexidade = 'media';
				} else if(quantidade_tipos_dados >= 6 && quantidade_tipos_dados <= 19){
					complexidade = 'alta';
				} else if (quantidade_tipos_dados >= 20){
					complexidade = 'alta';
				}
			}
		}
		
		return complexidade;
	}
	
	this.calcularValor = function(tipo_funcional, complexidade){
		var valor = '';
		
		if(tipo_funcional == 'e'){
			if(complexidade == 'baixa'){
				valor = 3;
			} else if(complexidade == 'media'){
				valor = 4;
			} else if(complexidade == 'alta'){
				valor = 6;
			}
		} else if(tipo_funcional == 's'){
			if(complexidade == 'baixa'){
				valor = 4;
			} else if(complexidade == 'media'){
				valor = 5;
			} else if(complexidade == 'alta'){
				valor = 6;
			}
		} else if(tipo_funcional == 'c'){
			if(complexidade == 'baixa'){
				valor = 3;
			} else if(complexidade == 'media'){
				valor = 4;
			} else if(complexidade == 'alta'){
				valor = 6;
			}
		}
		
		return valor;
	}
	
	this.formataNomeComplexidade = function(complexidade){
		if(complexidade == 'baixa'){
			return 'Baixa';
		} else if(complexidade == 'media'){
			return 'Média';
		} else if(complexidade == 'alta'){
			return 'Alta';
		} else {
			return '---';
		}
	}
	
	this.calcularTempoDesenvolvimentoPorEstimativaEsforco = function(valor_pf, recursos, tempo_dedicacao, indice_produtividade, formato_tempo){
		var tipo_formato_tempo = formato_tempo.charAt(0);
		
		var tempo_desenvolvimento = (valor_pf * indice_produtividade / (recursos * tempo_dedicacao));
		
		if(tipo_formato_tempo == 'h'){
			// Horas
			return tempo_desenvolvimento * 24;
		} else if(tipo_formato_tempo == 'd'){
			// Dias
			return tempo_desenvolvimento;
		} else if(tipo_formato_tempo == 'm'){
			// Meses
			return tempo_desenvolvimento / 30;
		} else {
			return 0;
		}
	}
	
	this.calcularTempoDesenvolvimentoPorFormulaCapersJones = function(total_pf, expoente_capers_jones, formato_tempo){
		var tipo_formato_tempo = formato_tempo.charAt(0);
		
		var tempo_desenvolvimento = Math.pow(total_pf, expoente_capers_jones);
		
		if(tipo_formato_tempo == 'h'){
			// Horas
			return tempo_desenvolvimento * 30 * 24;
		} else if(tipo_formato_tempo == 'd'){
			// Dias
			return tempo_desenvolvimento * 30;
		} else if(tipo_formato_tempo == 'm'){
			// Meses
			return tempo_desenvolvimento;
		} else {
			return 0;
		}
	}
	
	this.calcularCustoDesenvolvimentoPorEstimativaEsforco = function(metodo_calculo_orcamento, tempo, valor_ponto_funcao, valor_hora_trabalhada, formato_tempo){
		var tipo_formato_tempo = formato_tempo.charAt(0);
		
		// Calculando valor, em função da multiplicação do tempo pelo valor,
		// dependendo do método de cálculo de orçamento
		var custo;
		if(metodo_calculo_orcamento == 'vpf'){
			custo = (tempo * valor_ponto_funcao);
		} else if(metodo_calculo_orcamento == 'vht'){
			custo = (tempo * valor_hora_trabalhada);
		} else {
			custo = 0;
		}
		
		// Ajustando custo, em função do formato de tempo
		if(tipo_formato_tempo == 'h'){
			// Horas
			return custo;
		} else if(tipo_formato_tempo == 'd'){
			// Dias
			return custo * 24;
		} else if(tipo_formato_tempo == 'm'){
			// Meses
			return custo * 24 * 30;
		} else {
			return 0;
		}
	}
	
	this.calcularCustoDesenvolvimentoPorFormulaCapersJones = function(metodo_calculo_orcamento, quantidade_pontos_funcao, valor_ponto_funcao, valor_hora_trabalhada){
		if(metodo_calculo_orcamento == 'vpf'){
			return (quantidade_pontos_funcao * valor_ponto_funcao);
		} else if(metodo_calculo_orcamento == 'vht'){
			return (quantidade_pontos_funcao * valor_hora_trabalhada);
		} else {
			return 0;
		}
	}
	
	this.encodarTempoByFormato = function(tempo, formato, arredondarZeros){
		if(typeof arredondarZeros == 'undefined') arredondarZeros = false;
		
		if(formato == 'hhm'){
			// Horas / Minutos (HH:MM)
			return encodeFloatToTime(tempo, true);
		} else if(formato == 'hni' || formato == 'dni'){
			// Números inteiros
			tempo = round(tempo);
			if(arredondarZeros && tempo == 0) tempo = 1;
			return tempo;
		} else {
			// Números reais (2 casas decimais)
			tempo = round(tempo, 2);
			return encodeMonetario(tempo, 2);
		}
	}
}

// Instanciando objeto da classe acima
var cpf = new cpf();