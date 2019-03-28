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
		} else {
			return 'Alta';
		}
	}
}

// Instanciando objeto da classe acima
var cpf = new cpf();