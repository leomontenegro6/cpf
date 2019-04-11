/* Plugin de aplicação de máscaras em campos de texto
 * 
 * Baseado no componente "jQuery Mask Plugin" (https://igorescobar.github.io/jQuery-Mask-Plugin/),
 * em conjunto com estilizações nos padrões do Bootstrap.
 * 
 * Dependências:
 * - jquery.mask.min.js
 * - Estilizações do campo, nos arquivos CSS
 * 
 * Funções adicionadas:
 *	mascara.instanciar( [ seletor_campo ] , [ escopo ] )
 */

function mascara(){}

mascara.instanciar = function(seletor_campo, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_campo){
		busca = $(escopo).find(seletor_campo).not("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("input[data-mascara]").not("[data-instanciado='true']");
	}
	busca.each(function(){
		var $campo = $(this);
		var formato_mascara, reverso, placeholder_mascara;
		
		// Definindo formato da máscara.
		if($campo.is('[data-mascara]')){
			formato_mascara = $campo.attr('data-mascara');
		} else {
			formato_mascara = '';
		}
		
		// Definindo formato da máscara.
		if($campo.is('[data-reverso]')){
			reverso = ($campo.attr('data-reverso') == 'true');
		} else {
			reverso = '';
		}
		
		// Definindo formato da máscara.
		if($campo.is('[data-placeholder-mascara]')){
			placeholder_mascara = $campo.attr('data-placeholder-mascara');
		} else {
			placeholder_mascara = '';
		}
		
		// Definindo atributo 'id' para o campo, caso não exista
		var id_campo;
		if( $campo.is("[id]") ){
			id_campo = $campo.attr('id');
		} else {
			id_campo = gerarIdAleatorio($campo[0]);
			$campo.attr('id', id_campo);
		}
		
		// Se tiver no navegador firefox, mudar tipo para "text"
		if(($campo.attr('type') == 'number') && checkNavegadorFirefox()){
			$campo.attr('type', 'text');
		}
		
		// Parâmetros de instanciação do campo de máscara
		var parametros = {
			// Definindo se a máscara será reversa ou não
			'reverse': reverso,
			// Definindo placeholder da máscara
			'placeholder': placeholder_mascara,
			// Adicionando suporte a valores negativos através da letra S
			'translation': {
				'S': {
					pattern: /-/,
					optional: true
				}
			}
		}
		
		// Formatando campo
		$campo.mask(formato_mascara, parametros);
		
		// Aplicando eventos adicionais ao campo
		var onkeydown = $campo.attr('onkeydown');
		var onpaste = $campo.attr('onpaste')
		var onchange = $campo.attr('onchange');
		
		// Removendo atributo "onkeydown", para reinseri-lo após a validação
		// do tamanho mínimo / máximo
		$campo[0].onkeydown = undefined;
		$campo.removeAttr('onkeydown');

		// Reinserindo atributo "onkeydown", contemplando as seguintes validações:
		// 1. Validação de tamanho mínimo / máximo
		// 2. Validação de dados diversos, baseado em uma função javascript passada no atributo "data-validacao"
		// 3. Execução do atributo "onkeydown" original, se houver.
		$campo.on('keydown', function(e){
			var valor = decodeMonetario( $campo.val() );
			var keyCode = e.which;
			
			var checkTeclouEnter = (keyCode == 13);
			var checkCampoVazio = (isNaN(valor));
			var checkCampoObrigatorio = ($campo.is('[required]'));
			
			// Executando validações quando o usuário tecla ENTER no campo
			if(checkTeclouEnter){
				// Se o campo for obrigatório e estiver vazio, exibir aviso e
				// abortar a submissão do formulário
				if(checkCampoObrigatorio && checkCampoVazio){
					mascara.mostrarAviso(id_campo, 'Este campo é requerido!');
					e.preventDefault();
				}
				
				// Validando valor mínimo para o campo, se houver.
				if($campo.is('[data-minimo]')){
					var minimo = decodeMonetario( $campo.attr('data-minimo') );
					
					// Se o valor for menor que o mínimo, exibir aviso, limpar campo
					// e abortar a submissão do formulário
					if(valor < minimo){
						$campo.val('');
						var valor_minimo_formatado;
						if((formato_mascara.indexOf('.') > -1) || (formato_mascara.indexOf(',') > -1)){
							valor_minimo_formatado = encodeMonetario(minimo);
						} else {
							valor_minimo_formatado = minimo;
						}
						mascara.mostrarAviso(id_campo, 'O valor mínimo aceito é ' + valor_minimo_formatado + '!');
						e.preventDefault();
					}
				}
				
				// Validando valor máximo para o campo, se houver.
				if($campo.is('[data-maximo]')){
					var maximo = decodeMonetario( $campo.attr('data-maximo') );
					
					// Se o valor for maior que o máximo, exibir aviso, limpar campo
					// e abortar a submissão do formulário
					if(valor > maximo){
						$campo.val('');
						var valor_maximo_formatado;
						if((formato_mascara.indexOf('.') > -1) || (formato_mascara.indexOf(',') > -1)){
							valor_maximo_formatado = encodeMonetario(maximo);
						} else {
							valor_maximo_formatado = maximo;
						}
						mascara.mostrarAviso(id_campo, 'O valor máximo aceito é ' + valor_maximo_formatado + '!');
						e.preventDefault();
					}
				}
				
				// Validando função de javascript de validação de dados
				if($campo.is('[data-validacao]')){
					var validacao = $campo.attr('data-validacao');
					
					if(typeof validacao != 'undefined'){
						// Executando função javascript passada. A função deve receber
						// sempre o campo de formulário, e retornar TRUE caso o dado
						// estiver válido, ou mensagem de erro caso contrário
						validacao = validacao.replace('this', 'gE("' + id_campo + '")');
						var callback_validacao = new Function('return ' + validacao)();
						
						// Se o valor passado estiver inválido segundo a função
						// de validação, exibir aviso, limpar campo e abortar a
						// submissão do formulário
						if(callback_validacao != true){
							$campo.val('');
							mascara.mostrarAviso(id_campo, callback_validacao);
							e.preventDefault();
						}
					}
				}
			}
			
			// Suprimindo a digitação da letra 'e', em campos de tipo "number"
			if($campo.attr('type') == 'number'){
				if(keyCode == 69 || keyCode == 101){
					e.preventDefault();
				}
			}
			
			// Executando atributo "onchange" original
			if(typeof onkeydown != 'undefined'){
				onkeydown = onkeydown.replace('this', 'gE("' + id_campo + '")');
				return new Function('return ' + onkeydown)();
			}
		});
		
		// Removendo atributo "onkeydown", para reinseri-lo após a validação
		// do tamanho mínimo / máximo
		$campo[0].onpaste = undefined;
		$campo.removeAttr('onpaste');

		// Reinserindo atributo "onpaste", contemplando tanto a supressão de
		// colagem de texto contendo a letra 'e', como a execução da função contida no
		// atributo "onpaste" original
		$campo.on('paste', function(e){
			// Suprimindo a colagem de texto contendo a letra 'e', em campos
			// de tipo "number"
			if($campo.attr('type') == 'number'){
				var dados_copiados, dados_colados;
				
				// Obtendo dados colados através da API clipboard
				dados_copiados = e.originalEvent.clipboardData || window.clipboardData;
				dados_colados = dados_copiados.getData('Text').toUpperCase();
				
				// Caso exista letra 'e' nos dados colados, abortar execução do evento
				if(dados_colados.indexOf('E') > -1){
					e.stopPropagation();
					e.preventDefault();
				}
			}
			
			// Executando atributo "onpaste" original
			if(typeof onpaste != 'undefined'){
				onpaste = onpaste.replace('this', 'gE("' + id_campo + '")');
				return new Function('return ' + onpaste)();
			}
		})
		
		// Removendo atributo "onchange", para reinseri-lo após a validação
		// do tamanho mínimo / máximo
		$campo[0].onchange = undefined;
		$campo.removeAttr('onchange');

		// Reinserindo atributo "onchange", contemplando tanto a validação
		// do tamanho mínimo / máximo, como a execução da função contida no
		// atributo "onchange" original
		$campo.on('change', function(){
			var valor = decodeMonetario( $campo.val() );
			
			// Validando valor mínimo para o campo, se houver.
			if($campo.is('[data-minimo]')){
				var minimo = decodeMonetario( $campo.attr('data-minimo') );
				
				// Se o valor for menor que o mínimo, exibir aviso e limpar campo
				if(valor < minimo){
					$campo.val('');
					var valor_minimo_formatado;
					if((formato_mascara.indexOf('.') > -1) || (formato_mascara.indexOf(',') > -1)){
						valor_minimo_formatado = encodeMonetario(minimo);
					} else {
						valor_minimo_formatado = minimo;
					}
					mascara.mostrarAviso(id_campo, 'O valor mínimo aceito é ' + valor_minimo_formatado + '!');
				}
			}
			
			// Validando valor máximo para o campo, se houver.
			if($campo.is('[data-maximo]')){
				var maximo = decodeMonetario( $campo.attr('data-maximo') );
				
				// Se o valor for maior que o máximo, exibir aviso e limpar campo
				if(valor > maximo){
					$campo.val('');
					var valor_maximo_formatado;
					if((formato_mascara.indexOf('.') > -1) || (formato_mascara.indexOf(',') > -1)){
						valor_maximo_formatado = encodeMonetario(maximo);
					} else {
						valor_maximo_formatado = maximo;
					}
					mascara.mostrarAviso(id_campo, 'O valor máximo aceito é ' + valor_maximo_formatado + '!');
				}
			}
			
			// Validando função de javascript de validação de dados
			if($campo.is('[data-validacao]')){
				var validacao = $campo.attr('data-validacao');

				if(typeof validacao != 'undefined'){
					// Executando função javascript passada. A função deve receber
					// sempre o campo de formulário, e retornar TRUE caso o dado
					// estiver válido, ou mensagem de erro caso contrário
					validacao = validacao.replace('this', 'gE("' + id_campo + '")');
					var callback_validacao = new Function('return ' + validacao)();

					// Se o valor passado estiver inválido segundo a função
					// de validação, exibir aviso e limpar campo
					if(callback_validacao != true){
						$campo.val('');
						mascara.mostrarAviso(id_campo, callback_validacao);
					}
				}
			}

			// Executando atributo "onchange" original
			if(typeof onchange != 'undefined'){
				onchange = onchange.replace('this', 'gE("' + id_campo + '")');
				return new Function('return ' + onchange)();
			}
		});
		
		// Inserir atributo que impede desta função atuar sobre o colorpicker
		// duas vezes, de modo a evitar bugs.
		$campo.attr('data-instanciado', 'true');
	});
}

mascara.mostrarAviso = function(id_campo, mensagem){
	var $conteiner = $('#' + id_campo).parent();
	$conteiner.addClass('was-validated');
	
	var camposAlvo = [{
		'id': id_campo,
		'mensagem': mensagem
	}];
	mostrarAvisosValidaForm(camposAlvo);
	
	setTimeout(function(){
		$conteiner.removeClass('was-validated');
	}, 8*1000);
}