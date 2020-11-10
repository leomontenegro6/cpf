/* Plugin de campos timepicker, de seleção de horários
 * 
 * Baseado no componente "Bootstrap Timeicker" (https://jdewit.github.io/bootstrap-timepicker/),
 * em conjunto com estilizações nos padrões do Bootstrap.
 * 
 * Dependências:
 * - bootstrap-timepicker.min.css
 * - bootstrap-timepicker.js (modificado para atender nossas necessidades)
 * - jquery.mask.min.js
 * - Estilizações do campo, nos arquivos CSS
 * 
 * Funções adicionadas:
 *	timepicker.instanciar( [ seletor_campo ] , [ escopo ] )
 */
function timepicker(){}

// Propriedades
timepicker.parametros = [];

// Métodos
timepicker.instanciar = function(seletor_campo, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_campo){
		busca = $(escopo).find(seletor_campo).not("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("input.timepicker").not("[data-instanciado='true']");
	}
	busca.each(function(){
		var $campo = $(this);
		
		var checkTemIntervalo, mostrarSegundos;
		
		// Verificando se foi definido para mostrar segundos ou não
		if(($campo.is("[data-mostrar-segundos]") && ($.trim( $campo.attr('data-mostrar-segundos') ) != ''))){
			mostrarSegundos = ( $.trim( $campo.attr('data-mostrar-segundos') ) == 'true');
		} else {
			mostrarSegundos = false;
		}
		
		// Obtenção do tipo do campo.
		// Se não existir, utilizar como padrão o tipo "data"
		if(($campo.is("[data-intervalo]") && ($.trim( $campo.attr('data-intervalo') ) != ''))){
			checkTemIntervalo = ( $.trim( $campo.attr('data-intervalo') ) == 'true');
		} else {
			checkTemIntervalo = false;
		}
		
		// Obtendo id do campo input. Caso não exista, criar
		var id_campo;
		if($campo.is('[id]')){
			id_campo = $campo.attr('id');
		} else {
			id_campo = gerarIdAleatorio($campo);
			$campo.attr('id', id_campo);
		}
		
		// Parâmetros de instanciação do componente
		var parametros = {
			
			minuteStep: 1,
			showMeridian: false,
			disableFocus: true,
			showInputs: false,
			showSeconds: mostrarSegundos,
			icons: {
				up: 'fa fa-chevron-up',
				down: 'fa fa-chevron-down'
			}
		};
		
		if(mostrarSegundos){
			parametros.secondStep = 1;
		}
		
		var checkAlvoDentroDeModal = $campo.closest('div.janela_modal').length > 0;
		if(checkAlvoDentroDeModal){
			parametros.appendWidgetTo = $campo.closest('div.janela_modal');
		} else {
			parametros.appendWidgetTo = 'body';
		}
		
		// Realizando formatações do campo em função de haver intervalo ou não
		var $campoFinal;
		if(checkTemIntervalo){
			// Possui intervalo de períodos, logo criar segundo campo ao lado
			
			// Obtenção dos valores inicial e final, se existir
			var valor_inicial, valor_final;
			if($campo.val() == ''){
				if(($campo.is("[data-valor-inicial]") && ($.trim( $campo.attr('data-valor-inicial') ) != ''))){
					valor_inicial = $.trim( $campo.attr('data-valor-inicial') );
				} else {
					valor_inicial = '';
				}
			} else {
				valor_inicial = $campo.val();
			}
			if(($campo.is("[data-valor-final]") && ($.trim( $campo.attr('data-valor-final') ) != ''))){
				valor_final = $.trim( $campo.attr('data-valor-final') );
			} else {
				valor_final = '';
			}
			
			// Duplicando campo de texto, de modo que o primeiro conterá a data inicial, e o segundo, a final
			$campoFinal = $campo.clone().removeAttr('id data-valor-inicial data-valor-final data-intervalo');
			var nome_campo = $campo.attr('name');
			$campo.attr('name', nome_campo + '[inicial]').val(valor_inicial);
			$campoFinal.attr('name', nome_campo + '[final]').val(valor_final);
			var id_campo_final = gerarIdAleatorio($campoFinal);
			$campoFinal.attr('id', id_campo_final);
			
			// Formatando HTML do conteiner principal
			var $conteiner = $('<div />').addClass('form-inline conteiner_timepicker intervalo').append(
				$('<div />').addClass('form-group inicial').prepend(
					$('<label />').attr('for', id_campo).html('De:')
				)
			).append(
				$('<div />').addClass('form-group final').prepend(
					$('<label />').attr('for', id_campo_final).html('Até:')
				)
			);
			$campo.after($conteiner).appendTo( $conteiner.children('.inicial') );
			
			// Formatando marcação HTML do campo inicial
			var $gridInicial = $('<div />').addClass('input-group bootstrap-timepicker timepicker');
			$campo.after($gridInicial).appendTo($gridInicial);
			$campo.after(
				$('<span />').addClass('input-group-addon').attr('tabindex', '-1').html(
					$('<i />').addClass('fa fa-clock')
				)
			);
			
			// Formatando marcação HTML do campo final
			var $gridFinal = $('<div />').addClass('input-group bootstrap-timepicker timepicker');
			$campoFinal.appendTo( $conteiner.children('.final') );
			$campoFinal.after($gridFinal).appendTo($gridFinal);
			$campoFinal.after(
				$('<span />').addClass('input-group-addon').attr('tabindex', '-1').html(
					$('<i />').addClass('fa fa-clock')
				)
			);
	
			// Instanciando componente inicial
			parametros.defaultTime = valor_inicial;
			$campo.timepicker(parametros);
			
			// Instanciando componente final
			parametros.defaultTime = valor_final;
			$campoFinal.timepicker(parametros);
		} else {
			// Formatando marcação HTML dos campos
			var $grid = $('<div />').addClass('input-group bootstrap-timepicker timepicker conteiner_timepicker comum');
			$campo.after($grid).appendTo($grid);
			$campo.after(
				$('<span />').addClass('input-group-addon').attr('tabindex', '-1').html(
					$('<i />').addClass('fa fa-clock')
				)
			);
			
			// Obtendo valor do campo, para defini-lo no componente
			var valor = $campo.val();
			parametros.defaultTime = valor;
			
			// Instanciando componente
			$campo.timepicker(parametros);
		}
		
		// Salvando parâmetros instanciados desse campo, para manuseio posterior
		timepicker.parametros[id_campo] = parametros;
		
		// Realizando formatações adicionais nos campos
		$campo.add($campoFinal).each(function(){
			var $campoAtual = $(this);
			
			// Adicionando máscara personalizada
			if(mostrarSegundos){
				$campoAtual.mask('00:00:00', {placeholder: "__:__:__"}).attr('maxlength', 8);
			} else {
				$campoAtual.mask('00:00', {placeholder: "__:__"}).attr('maxlength', 5);
			}
			
			// Eventos do campo
			$campoAtual.timepicker().on({
				// Show: chamado quando o widget de dropdown / modal é exibido
				'show.timepicker': function(e){
					if(e.time.value == ''){
						var $grid = $campoAtual.closest('div.form-group');
						var $conteiner_timepicker = $grid.closest('div.conteiner_timepicker');

						// Flags de validação
						var checkCampoInicial = $grid.hasClass('inicial');
						var checkTemIntervalo = ($conteiner_timepicker.hasClass('intervalo'));

						// Obtendo campos inicial e final, para tratamentos posteriores
						var $campoInicial, $campoFinal;
						if(checkTemIntervalo){
							if(checkCampoInicial){
								$campoInicial = $campoAtual;
								$campoFinal = $conteiner_timepicker.find('div.final').find('input.timepicker');
							} else {
								$campoInicial = $conteiner_timepicker.find('div.inicial').find('input.timepicker');
								$campoFinal = $campoAtual;
							}
						} else {
							$campoInicial = $campoFinal = $campo;
						}

						// Caso tenha intervalo, definindo valor do campo inicial
						// em função do final, e vice-versa
						if(checkTemIntervalo){
							if(checkCampoInicial){
								var hora_final = $campoFinal.val();
								if(hora_final != '') $campoInicial.val(hora_final);
							} else {
								var hora_inicial = $campoInicial.val();
								if(hora_inicial != '') $campoFinal.val(hora_inicial);
							}
						}
					}
				},
				// Show: chamado quando o widget de dropdown / modal é ocultado
				'hide.timepicker': function(e){
					removerAviso($campoAtual);
				},
				// Show: chamado quando a data é atualizada
				'changeTime.timepicker': function(e){
					return timepicker.validarCampo($campoAtual);
				}
			})
			
			// Inserir atributo que impede desta função atuar sobre o calendário
			// duas vezes, de modo a evitar bugs.
			$campoAtual.attr('data-instanciado', 'true');
		});
	});
}

timepicker.validarCampo = function(campo){
	var $campo = $(campo);
	var $grid = $campo.closest('div.form-group');
	var $conteiner_timepicker = $grid.closest('div.conteiner_timepicker');
	
	// Flags de validação
	var checkCampoInicial = $grid.hasClass('inicial');
	var checkTemIntervalo = ($conteiner_timepicker.hasClass('intervalo'));
	
	// Obtendo campos inicial e final, para tratamentos posteriores
	var $campoInicial, $campoFinal;
	if(checkTemIntervalo){
		if(checkCampoInicial){
			$campoInicial = $campo;
			$campoFinal = $conteiner_timepicker.find('div.final').find('input.timepicker');
		} else {
			$campoInicial = $conteiner_timepicker.find('div.inicial').find('input.timepicker');
			$campoFinal = $campo;
		}
	} else {
		$campoInicial = $campoFinal = $campo;
	}
	
	// Obtendo id do campo inicial, para dele poder extrair os parâmetros
	var id_campo = $campoInicial.attr('id');
	
	// Obtendo parâmetros dessa instância do componente
	var parametros = timepicker.parametros[id_campo];
	var mostrarSegundos = parametros.showSeconds;
	var formato;
	if(mostrarSegundos){
		formato = 'HH:mm:ss';
	} else {
		formato = 'HH:mm';
	}
	
	// Obtendo hora do campo. Se não existir, abortar
	var hora_selecionada = $.trim( $campo.val() );
	if(hora_selecionada == ''){
		return false;
	}
	hora_selecionada = moment(hora_selecionada, formato, true);
	
	// Exibindo aviso para caso a hora fornecida seja inválida
	var checkHoraValida = (hora_selecionada.isValid());
	if(!checkHoraValida){
		var mensagem = 'Hora inválida!';
		aviso($campo, mensagem, 8, 't');
		$campo.timepicker('setTime', '');
		return false;
	}
	
	// Avisos para campos com intervalo
	if(checkTemIntervalo){
		var hora_inicial = moment($campoInicial.val(), formato);
		var hora_final = moment($campoFinal.val(), formato);

		// Exibindo aviso para caso a hora inicial seja maior do que a final, ou o contrário
		if(hora_inicial.isAfter(hora_final)){
			var mensagem;
			if(checkCampoInicial){
				mensagem = 'A hora inicial tem que ser menor do que a final!';
				$campo.timepicker('setTime', hora_final.format(formato));
			} else {
				mensagem = 'A hora final tem que ser maior do que a inicial!';
				$campo.timepicker('setTime', hora_inicial.format(formato));
			}
			aviso($campo, mensagem, 8, 't');
			return false;
		}
	}
	
	removerAviso($campo);
	return true;
}

timepicker.limpar = function(campo){
	var $campo = $(campo);
	
	$campo.timepicker('setTime', '');
}