/* Plugin de campos de calendario de data e hora
 * 
 * Baseado no componente "Bootstrap Date Range Picker" (http://www.daterangepicker.com/),
 * em conjunto com estilizações nos padrões do Bootstrap 4.
 * 
 * Dependências:
 * - daterangepicker.min.css
 * - daterangepicker.min.js
 * - jquery.mask.min.js
 * - bootstrap-float-label.css (v4.0.1)
 * - Estilizações do campo, nos arquivos CSS
 * 
 * Funções adicionadas:
 *	calendario.instanciar( [ seletor_campo ] , [ escopo ] )
 */

function calendario(){}

// Propriedades
calendario.parametros = [];

// Métodos
calendario.instanciar = function(seletor_campo, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_campo){
		busca = $(escopo).find(seletor_campo).not("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("input.calendario").not("[data-instanciado='true']");
	}
	var dispositivo = getDispositivo();
	var data_atual = moment(data_servidor).format('DD/MM/YYYY');
	busca.each(function(){
		var $campo = $(this);
		var tipo, checkTemIntervalo, data_minima, data_maxima, datas_desativadas, dias_desativados;
		var rotulos_flutuantes = {
			'inicial': 'De:',
			'final': 'Até:'
		};
		
		// Obtenção do tipo do campo.
		// Se não existir, utilizar como padrão o tipo "data"
		if(($campo.is("[data-tipo]") && ($.trim( $campo.attr('data-tipo') ) != ''))){
			tipo = $.trim( $campo.attr('data-tipo') );
		} else {
			tipo = 'data';
		}
		
		// Obtenção do tipo do campo.
		// Se não existir, utilizar como padrão o tipo "data"
		if(($campo.is("[data-intervalo]") && ($.trim( $campo.attr('data-intervalo') ) != ''))){
			checkTemIntervalo = ($.trim( $campo.attr('data-intervalo') ) == 'true');
		} else {
			checkTemIntervalo = false;
		}
		
		// Obtenção da data mínima permitida pelo calendário.
		// Se não existir, utilizar uma data predefinida
		if(($campo.is("[data-minima]") && ($.trim( $campo.attr('data-minima') ) != ''))){
			var atributo_data_minima = $.trim( $campo.attr('data-minima') );
			data_minima = moment(atributo_data_minima, 'DD/MM/YYYY HH:mm');
		} else {
			data_minima = moment('01/01/1920', 'DD/MM/YYYY HH:mm');
		}
		
		// Obtenção da data máxima permitida pelo calendário
		// Se não existir, utilizar uma data predefinida
		if(($campo.is("[data-maxima]") && ($.trim( $campo.attr('data-maxima') ) != ''))){
			var atributo_data_maxima = $.trim( $campo.attr('data-maxima') );
			
			data_maxima = moment(atributo_data_maxima, 'DD/MM/YYYY HH:mm');
		} else {
			data_maxima = moment('01/01/2050', 'DD/MM/YYYY HH:mm');
		}
		
		// Obtenção das datas desativadas pelo campo
		if($campo.is("[data-desativadas]")){
			datas_desativadas = ( $campo.attr('data-desativadas') ).split(',');
			for(var i in datas_desativadas){
				var data_desativada = $.trim(datas_desativadas[i]);
				datas_desativadas[i] = data_desativada;
			}
		} else {
			datas_desativadas = [];
		}
		
		// Obtenção dos dias da semana desativados pelo campo
		if($campo.is("[data-dias-desativados]")){
			dias_desativados = ( $campo.attr('data-dias-desativados') ).split(',');
			for(var i in dias_desativados){
				var dia_desativado = $.trim(dias_desativados[i]);
				if(!isNaN(dia_desativado)){
					dias_desativados[i] = parseInt(dia_desativado, 10);
				} else {
					dias_desativados[i] = calendario.converteDiaSemanaNumero(dia_desativado);
				}
			}
		} else {
			dias_desativados = [];
		}
		
		// Obtenção de valores personalizados dos rótulos flutuantes
		if($campo.is("[data-rotulo-flutuante-inicial]")){
			var rotulo = $campo.attr('data-rotulo-flutuante-inicial');
			rotulos_flutuantes.inicial = rotulo;
		}
		if($campo.is("[data-rotulo-flutuante-final]")){
			var rotulo = $campo.attr('data-rotulo-flutuante-final');
			rotulos_flutuantes.final = rotulo;
		}
		
		var checkDesativado = $campo.is(':disabled');
		var checkSomenteLeitura = $campo.is('[readonly]');
		
		// Obtendo id do campo input. Caso não exista, criar
		var id_campo;
		if($campo.is('[id]')){
			id_campo = $campo.attr('id');
		} else {
			id_campo = gerarIdAleatorio($campo);
			$campo.attr('id', id_campo);
		}
		
		// Parâmetros de instanciação do campo select
		var parametros = {
			// Data mínima aceita pelo campo
			'minDate': data_minima,
			// Data máxima aceita pelo campo
			'maxDate': data_maxima,
			// Abre o componente sempre à esquerda do botão
			'opens': 'left',
			// Define localização pt-BR (as strings de tradução estão no final do arquivo)
			"locale": {
				"separator": " - ",
				"applyLabel": "Aplicar",
				"cancelLabel": "Fechar",
				"fromLabel": "De",
				"toLabel": "Até",
				"customRangeLabel": "Customizado",
				"weekLabel": "S",
				"daysOfWeek": [
					"Dom",
					"Seg",
					"Ter",
					"Qua",
					"Qui",
					"Sex",
					"Sáb"
				],
				"monthNames": [
					"Jan",
					"Fev",
					"Mar",
					"Abr",
					"Mai",
					"Jun",
					"Jul",
					"Ago",
					"Set",
					"Out",
					"Nov",
					"Dez"
				],
				"firstDay": 0
			}
		}
		
		// Realizando formatações do campo em função do seu tipo ("data" ou "data_hora")
		var formato;
		if(tipo == 'data_hora'){
			formato = 'DD/MM/YYYY HH:mm';
			
			parametros.timePicker = true;
			parametros.timePicker24Hour = true;
		} else {
			formato = 'DD/MM/YYYY';
			
			parametros.autoApply = true;
		}
		parametros.locale.format = formato;
		
		// Datas e dias da semana desativados pelo campo
		parametros.isInvalidDate = function(d){
			var data_selecionada = d.format(formato);
			var dia_semana_selecionado = d.weekday();
			
			// Desativando data, caso esteja no conjunto de datas desativadas
			if($.inArray(data_selecionada, datas_desativadas) !== -1){
				return true;
			}
			
			// Desativando dia da semana, caso esteja no conjunto de dias desativados
			if($.inArray(dia_semana_selecionado, dias_desativados) !== -1){
				return true;
			}
			
			return false;
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
			$campoFinal = $campo.clone().removeAttr('id data-valor-inicial data-valor-final data-tipo data-minima data-maxima data-desativadas data-intervalo');
			var nome_campo = $campo.attr('name');
			$campo.attr('name', nome_campo + '[inicial]').val(valor_inicial);
			$campoFinal.attr('name', nome_campo + '[final]').val(valor_final);
			var id_campo_final = gerarIdAleatorio($campoFinal);
			$campoFinal.attr('id', id_campo_final);
			
			// Formatando marcação HTML dos campos
			var $conteiner_calendario = $('<div />').addClass('conteiner_calendario form-group input-group with-float-labels intervalo').append(
				$('<label />').addClass('has-float-label inicial').append(
					$('<span />').html(rotulos_flutuantes.inicial)
				)
			).append(
				$('<label />').addClass('has-float-label final').append(
					$('<span />').html(rotulos_flutuantes.final)
				)
			);
			$campo.after($conteiner_calendario).prependTo( $conteiner_calendario.find('label.inicial') );
			$campoFinal.prependTo( $conteiner_calendario.find('label.final') );
			
			// Formatando marcação HTML do botão que instanciará o calendário
			var $conteiner_botao_calendario = $('<span />').addClass('input-group-addon');
			var $botao_calendario = $('<button />').attr({
				'type': 'button',
				'tabindex': '-1'
			}).addClass('btn btn-default botao_calendario').html(
				$('<i />').addClass('fa fa-calendar-alt')
			).appendTo($conteiner_botao_calendario);
			
			$conteiner_calendario.append($conteiner_botao_calendario);
			
			// Adicionando parâmetros de instanciação específicos para intervalos
			if(dispositivo != 'xs'){
				parametros.alwaysShowCalendars = true;
			}
			if(valor_inicial != ''){
				parametros.startDate = moment(valor_inicial, formato);
			}
			if(valor_final != ''){
				parametros.endDate = moment(valor_final, formato);
			}
			parametros.ranges = {
				'Hoje': [moment(), moment()],
				'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
				'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
				'Mês Atual': [moment().startOf('month'), moment().endOf('month')],
				'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
			parametros.showDropdowns = true;
		} else {
			// Formatando marcação HTML dos campos
			var $conteiner_calendario = $('<div />').addClass('conteiner_calendario form-group input-group');
			$campo.after($conteiner_calendario).appendTo( $conteiner_calendario );
			
			// Formatando marcação HTML do botão que instanciará o calendário
			var $conteiner_botao_calendario = $('<div />').addClass('input-group-append');
			var $botao_calendario = $('<button />').attr({
				'type': 'button',
				'tabindex': '-1'
			}).addClass('btn btn-default botao_calendario').html(
				$('<i/>').addClass('fa fa-calendar-alt')
			).appendTo($conteiner_botao_calendario);
			$conteiner_calendario.append($conteiner_botao_calendario);
			
			// Obtendo valor do campo, para defini-lo no componente
			var valor = $campo.val();
			
			// Adicionando parâmetros de instanciação específicos para campos sem intervalo
			parametros.singleDatePicker = true;
			parametros.showDropdowns = true;
			if(valor != ''){
				parametros.startDate = moment(valor, formato);
			}
		}
		
		var checkAlvoDentroDeModal = $campo.closest('div.janela_modal').length > 0;
		if(checkAlvoDentroDeModal){
			parametros.parentEl = $campo.closest('div.janela_modal');
		} else {
			parametros.parentEl = 'body';
		}
		
		// Instanciando componente
		$botao_calendario.daterangepicker(parametros);
		calendario.parametros[id_campo] = parametros;
		
		// Efetuando formatações adicionais nos campos de texto externos ao componente
		$campo.add($campoFinal).each(function(){
			var $campoAtual = $(this);
			
			// Adicionando máscara de data nos campos
			if(tipo == 'data_hora'){
				$campoAtual.mask('00/00/0000 00:00', {placeholder: "__/__/____ __:__"}).attr('maxlength', 16);
			} else {
				$campoAtual.mask('00/00/0000', {placeholder: "__/__/____"}).attr('maxlength', 10);
			}
			
			// Definindo eventos dos campos de texto
			$campoAtual.on({
				'change': function(){
					var data_inicial = $.trim( $campo.val() );
					var data_final;
					if(checkTemIntervalo){
						data_final = $.trim( $campoFinal.val() );
					} else {
						data_final = data_inicial;
					}

					if(data_inicial == '') data_inicial = data_final;
					if(data_final == '') data_final = data_inicial;

					data_inicial = moment(data_inicial, formato);
					data_final = moment(data_final, formato);
					
					if(data_inicial.isValid()){
						$botao_calendario.data('daterangepicker').setStartDate(data_inicial);
					}
					if(data_final.isValid()){
						$botao_calendario.data('daterangepicker').setEndDate(data_final);
					}
					
					return calendario.validarCampo($campoAtual);
				},
				'keydown': function(e){
					if(e.which == 13){
						return calendario.validarCampo( $campoAtual );
					} else {
						return true;
					}
				}
			});
		});
		
		// Definindo eventos do componente
		$botao_calendario.on({
			// apply.daterangepicker: Chamado quando é selecionada uma data ou um período
			'apply.daterangepicker': function(e, p){
				// Atualizando campos de texto, após a data ter sido selecionada
				var data_inicial = (p.startDate).format(formato);
				var data_final = (p.endDate).format(formato);
				
				$campo.val(data_inicial);
				if(checkTemIntervalo){
					$campoFinal.val(data_final);	
				}
			},
			// show.daterangepicker: Chamado quando o calendário é exibido pela primeira vez
			'show.daterangepicker': function(e, p){
				var $widgetCalendarioFinal = $('div.daterangepicker');
				var $camposInternos = $widgetCalendarioFinal.find("input[name^='daterangepicker']");
				
				// Aplicando máscara para os campos de texto internos do calendário
				if(tipo == 'data_hora'){
					$camposInternos.mask('00/00/0000 00:00', {placeholder: "__/__/____ __:__"}).attr('maxlength', 16);
				} else {
					$camposInternos.mask('00/00/0000', {placeholder: "__/__/____"}).attr('maxlength', 10);
				}
			},
			// showCalendar.daterangepicker: Chamado quando o calendário é atualizado
			'showCalendar.daterangepicker': function(e, p){
				var $widgetCalendarioFinal = $('div.daterangepicker');
				var $divTabela = $widgetCalendarioFinal.find('div.calendar-table');
				var $tabelaDias = $divTabela.children('table');
				
				if((tipo == 'data') && (!checkTemIntervalo)){
					// Adicionando botão "Hoje" no calendário
					$tabelaDias.append(
						$('<tfoot />').append(
							$('<tr />').append(
								$('<td />').attr('colspan', '100%').prepend(
									$('<button />').attr('type', 'button').addClass('btn btn-default hoje').html('Hoje').click(function(){
										calendario.setarDataAtual($campo);
									})
								)
							)
						)
					);
				}
			},
			// hide.daterangepicker: Chamado quando o calendário é ocultado
			'hide.daterangepicker': function(e, p){
				removerAviso($campo);
				removerAviso($campoFinal);
			}
		});
		
		// Se campo estiver desativado ou somente leitura, desativar botões
		// do calendário, após a instanciação
		if(checkDesativado || checkSomenteLeitura){
			calendario.desativar($campo);
		}
		
		// Inserir atributo que impede desta função atuar sobre o calendário
		// duas vezes, de modo a evitar bugs.
		$campo.add($campoFinal).attr('data-instanciado', 'true');
	});
}

calendario.validarCampo = function(campo){
	var $campo = $(campo);
	var $label = $campo.closest('label.has-float-label');
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	
	// Flags de validação
	var checkCampoInicial = $label.hasClass('inicial');
	var checkTemIntervalo = ($conteiner_calendario.hasClass('intervalo'));
	
	// Obtendo campos inicial e final, para tratamentos posteriores
	var $campoInicial, $campoFinal;
	if(checkTemIntervalo){
		if(checkCampoInicial){
			$campoInicial = $campo;
			$campoFinal = $conteiner_calendario.find('label.final').children('input.calendario');
		} else {
			$campoInicial = $conteiner_calendario.find('label.inicial').children('input.calendario');
			$campoFinal = $campo;
		}
	} else {
		$campoInicial = $campoFinal = $campo;
	}
	
	// Obtendo id do campo inicial, para dele poder extrair os parâmetros
	var id_campo = $campoInicial.attr('id');
	
	// Obtendo parâmetros dessa instância do componente
	var parametros = calendario.parametros[id_campo];
	var data_minima = parametros.minDate;
	var data_maxima = parametros.maxDate;
	var formato = parametros.locale.format;
	
	// Obtendo data do campo. Se não existir, abortar
	var data_selecionada = $.trim( $campo.val() );
	if(data_selecionada == ''){
		return false;
	}
	
	// Tentando autocompletar a data, caso apenas parte dela tenha sido digitada.
	// Ex. 1: Caso apenas o dia for fornecido, autocompletar o mês e ano com os da data atual
	// Ex. 2: Caso apenas o dia e o mês for fornecido, autocompletar o ano com o da data atual
	if(formato == 'DD/MM/YYYY'){
		var objeto_data_selecionada = data_selecionada.split('/');
		var dia = objeto_data_selecionada[0];
		var mes = objeto_data_selecionada[1];
		var ano = objeto_data_selecionada[2];
		
		if(!dia || !mes || !ano){
			if(dia && parseInt(dia, 10) < 10) dia = '0' + (parseInt(dia, 10)).toString();
			if(mes){
				if(parseInt(mes, 10) < 10) mes = '0' + (parseInt(mes, 10)).toString();
			} else {
				mes = moment(data_servidor).format('MM');
			}
			if(!ano) ano = moment(data_servidor).format('YYYY');
			
			data_selecionada = dia + '/' + mes + '/' + ano;
			$campo.val(data_selecionada);
		}
	}
	
	// Convertendo data para um objeto "moment"
	data_selecionada = moment(data_selecionada, formato, true);

	// Exibindo aviso para caso a data/hora fornecida seja inválida
	var checkDataValida = (data_selecionada.isValid());
	if(!checkDataValida){
		var mensagem = 'Data inválida!';
		aviso($campo, mensagem, 8, 't');
		$campo.val('');
		return false;
	}

	// Exibindo aviso para caso a data/hora fornecida seja menor do que a mínima
	if( data_selecionada.isBefore(data_minima) ){
		var data_hora_minima_formatada = data_minima.format(formato);
		aviso($campo, 'A data mínima permitida é ' + data_hora_minima_formatada + '!', 8, 't');
		$campo.val('');
		return false;
	}

	// Exibindo aviso para caso a data/hora fornecida seja maior do que a máxima
	if( data_selecionada.isAfter(data_maxima) ){
		var data_hora_maxima_formatada = data_maxima.format(formato);
		aviso($campo, 'A data máxima permitida é ' + data_hora_maxima_formatada + '!', 8, 't');
		$campo.val('');
		return false;
	}

	// Avisos para campos com intervalo
	if(checkTemIntervalo){
		var data_hora_inicial = moment($campoInicial.val(), formato);
		var data_hora_final = moment($campoFinal.val(), formato);

		// Exibindo aviso para caso a data/hora inicial seja maior do que a final, ou o contrário
		if(data_hora_inicial.isValid() && data_hora_final.isValid() && data_hora_inicial.isAfter(data_hora_final)){
			var mensagem;
			if(checkCampoInicial){
				mensagem = 'A data inicial tem que ser menor do que a final!';
			} else {
				mensagem = 'A data final tem que ser maior do que a inicial!';
			}
			aviso($campo, mensagem, 8, 't');
			$campo.val('');
			return false;
		}
	}
	
	removerAviso($campo);
	return true;
}

calendario.converteDiaSemanaNumero = function(dia){
	var dia_numero = undefined;
	if(dia == 'dom'){
		dia_numero = 0;
	} else if(dia == 'seg'){
		dia_numero = 1;
	} else if(dia == 'ter'){
		dia_numero = 2;
	} else if(dia == 'qua'){
		dia_numero = 3;
	} else if(dia == 'qui'){
		dia_numero = 4;
	} else if(dia == 'sex'){
		dia_numero = 5;
	} else if(dia == 'sab'){
		dia_numero = 6;
	}
	return dia_numero;
}

calendario.ativar = function(campo){
	var $campo = $(campo);
	var $label = $campo.closest('label.has-float-label');
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	var $botaoCalendario = $conteiner_calendario.find('button.botao_calendario');
	
	// Flags de validação
	var checkCampoInicial = $label.hasClass('inicial');
	var checkTemIntervalo = ($conteiner_calendario.hasClass('intervalo'));
	
	// Obtendo campo final, se aplicável
	var $campoFinal;
	if(checkTemIntervalo){
		if(checkCampoInicial){
			$campoFinal = $conteiner_calendario.find('div.final').children('input.calendario');
		} else {
			$campoFinal = $campo;
			var $campo = $conteiner_calendario.find('div.inicial').children('input.calendario');
		}
	} else {
		$campoFinal = $campo;
	}
	
	// Ativando campo
	$campo.add($campoFinal).removeAttr('disabled readonly');
	$botaoCalendario.removeAttr('disabled');
}

calendario.desativar = function(campo){
	var $campo = $(campo);
	var $label = $campo.closest('label.has-float-label');
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	var $botaoCalendario = $conteiner_calendario.find('button.botao_calendario');
	
	// Flags de validação
	var checkCampoInicial = $label.hasClass('inicial');
	var checkTemIntervalo = ($conteiner_calendario.hasClass('intervalo'));
	
	// Obtendo campo final, se aplicável
	var $campoFinal;
	if(checkTemIntervalo){
		if(checkCampoInicial){
			$campoFinal = $conteiner_calendario.find('div.final').children('input.calendario');
		} else {
			$campoFinal = $campo;
			var $campo = $conteiner_calendario.find('div.inicial').children('input.calendario');
		}
	} else {
		$campoFinal = $campo;
	}
	
	// Desativando campo
	$campo.add($campoFinal).attr({
		'disabled': 'disabled',
		'readonly': 'readonly'
	});
	$botaoCalendario.attr('disabled', 'disabled');
}

calendario.limpar = function(campo){
	var $campo = $(campo);
	var $label = $campo.closest('label.has-float-label');
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	var $botao_calendario = $conteiner_calendario.find('button.botao_calendario');
	
	var data_atual = moment(data_servidor).format('DD/MM/YYYY');
	
	if($conteiner_calendario.hasClass('intervalo')){
		if($label.hasClass('inicial')){
			var $campoFinal = $conteiner_calendario.children('label.final').children('input.calendario');

			$campo.add($campoFinal).val('');
			$botao_calendario.data('daterangepicker').setStartDate(data_atual);
			$botao_calendario.data('daterangepicker').setEndDate(data_atual);
		}
	} else {
		$campo.val('');
		$botao_calendario.data('daterangepicker').setStartDate(data_atual);
		$botao_calendario.data('daterangepicker').setEndDate(data_atual);
	}
}

calendario.setarDataAtual = function(campo){
	var $campo = $(campo);
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	var $botao_calendario = $conteiner_calendario.find('button.botao_calendario');
	
	var data_atual = moment(data_servidor).format('DD/MM/YYYY');
	
	$campo.val(data_atual);
	$botao_calendario.data('daterangepicker').setStartDate(data_atual);
	$botao_calendario.data('daterangepicker').setEndDate(data_atual);
	
	$botao_calendario.data('daterangepicker').toggle();
}

calendario.fechar = function(campo){
	var $campo = $(campo);
	var $conteiner_calendario = $campo.closest('div.conteiner_calendario');
	var $botao_calendario = $conteiner_calendario.find('button.botao_calendario');
	
	$botao_calendario.data('daterangepicker').toggle();
}