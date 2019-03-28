/* Plugin modal de diálogos
 * 
 * Baseado no componente de modal do Bootstrap, porém mantendo a mesma sintaxe
 * do seu antecessor, jQuery Alerts.
 * 
 * Formas de Uso:
 *	jInfo( mensagem, [titulo, callback] )
 *	jConfirm( mensagem, [titulo, callback] )
 *	jConfirmSimNao( mensagem, [titulo, callback] )
 *	jConfirmSimNaoCancelar( mensagem, [titulo, callback] )
 *	jAlert( mensagem, [titulo, callback] )
 *	jError( mensagem, [titulo, callback] )
 *	jPrompt( mensagem, [valor, titulo, callback] )
 *	jModal( pagina, [titulo, callback] )
 *	jModalLocal( html, [titulo, callback] )
 *	jModalRemove( [callback] )
 *	jImage( imagem )
 *	jLoadingWheel( [tem_modal, tem_animacao] )
 *	jLoadingWheelRemove( [ callback ] )
 */

function modal(){}

modal.instanciar = function(mensagem, titulo, tipo, callback, tem_modal, tem_animacao, fechar_ao_clicar_no_fundo){
	if(typeof tipo == 'undefined') tipo = 'i';
	if(typeof tem_modal == 'undefined') tem_modal = true;
	if(typeof tem_animacao == 'undefined') tem_animacao = true;
	if(typeof fechar_ao_clicar_no_fundo == 'undefined') fechar_ao_clicar_no_fundo = true;
	var classe_titulo, classe_header, classe_icone;
	var resultado;
	if(tipo == 'csnc'){
		resultado = undefined;
	} else {
		resultado = false;
	}
	var botoes_modal = $("<button />").addClass('btn btn-secondary btn-dynamic btn-ok').attr('data-dismiss', 'modal').html('OK').click(function(){
		resultado = true;
	});
	var backdrop;
	if(!fechar_ao_clicar_no_fundo){
		backdrop = 'static';
	} else if(tem_modal){
		if(tipo == 'img' || tipo == 'm' || tipo == 'ml'){
			backdrop = true;
		} else {
			backdrop = 'static';
		}
	} else {
		backdrop = false;
	}
	if(tipo == 'i'){
		if(typeof titulo == 'undefined') titulo = 'Informação';
		classe_titulo = 'janela_modal informacao';
		classe_header = 'bg-info';
		classe_icone = 'fa-info-circle';
	} else if(tipo == 'a'){
		if(typeof titulo == 'undefined') titulo = 'Aviso';
		classe_titulo = 'janela_modal aviso';
		classe_header = 'bg-warning';
		classe_icone = 'fa-exclamation-triangle';
	} else if(tipo == 'c' || tipo == 'csn'){
		if(typeof titulo == 'undefined') titulo = 'Confirmação';
		classe_titulo = 'janela_modal confirmacao';
		classe_header = 'bg-primary';
		classe_icone = 'fa-question-circle';
		var html_botao1, html_botao2;
		if(tipo == 'csn' || tipo == 'csnc'){
			html_botao1 = 'Sim';
			html_botao2 = 'Não';
		} else {
			html_botao1 = 'OK';
			html_botao2 = 'Cancelar';
		}
		var botao_ok = botoes_modal.html(html_botao1).removeClass('btn-secondary').addClass('btn-primary');
		var botao_cancelar = $("<button />").addClass('btn btn-secondary btn-dynamic btn-cancel').attr('data-dismiss', 'modal').html(html_botao2).click(function(){
			resultado = false;
		});
		botoes_modal = botao_cancelar.add(botao_ok);
	} else if(tipo == 'csnc'){
		if(typeof titulo == 'undefined') titulo = 'Confirmação';
		classe_titulo = 'janela_modal confirmacao';
		classe_header = 'bg-primary';
		classe_icone = 'fa-question-circle';
		var botao_sim = botoes_modal.html('Sim');
		var botao_nao = $("<button />").addClass('btn btn-warning btn-dynamic btn-nao').attr('data-dismiss', 'modal').html('Não').click(function(){
			resultado = false;
		});
		var botao_cancelar = $("<button />").addClass('btn btn-warning btn-dynamic btn-cancel').attr('data-dismiss', 'modal').html('Cancelar').click(function(){
			resultado = undefined;
		});
		botoes_modal = botao_cancelar.add(botao_nao).add(botao_sim);
	} else if(tipo == 'e'){
		if(typeof titulo == 'undefined') titulo = 'Erro';
		classe_titulo = 'janela_modal erro';
		classe_header = 'bg-danger';
		classe_icone = 'fa-exclamation-triangle';
	} else if(tipo == 'l'){
		titulo = '';
		classe_titulo = 'janela_modal loadingwheel';
		classe_header = '';
		classe_icone = '';
		botoes_modal = '';
	} else if(tipo == 'img'){
		titulo = '';
		classe_titulo = 'janela_modal imagem';
		classe_header = '';
		classe_icone = '';
		botoes_modal = '';
	} else if(tipo == 'ml'){
		classe_titulo = 'janela_modal';
		classe_header = '';
		classe_icone = '';
	} else {
		classe_titulo = 'janela_modal modal_pagina';
		classe_header = 'bg-default';
		classe_icone = '';
	}
	if(tem_animacao) classe_titulo += ' fade';
	
	var $janela_modal = $("<div />").data('tipo', tipo).addClass('modal ' + classe_titulo).append(
		$("<div />").addClass('modal-dialog').append(
			$("<div />").addClass('modal-content')
		)
	);
	var $dialogo_janela_modal = $janela_modal.find('div.modal-dialog');
	var $conteudo_janela_modal = $janela_modal.find('div.modal-content');
	var $corpo_janela_modal = '';
	var $mensagem_janela_modal = '';
	if(tipo == 'l'){
		$conteudo_janela_modal.append(
			$("<div />").addClass('modal-header ' + classe_header).append(
				$("<i />").addClass('fas fa-circle-notch fa-spin fa-3x fa-fw')
			).append('<div>Processando...</div>')
		);
	} else if(tipo == 'img'){
		$janela_modal.addClass('carregando');
		$conteudo_janela_modal.html(
			$("<i />").addClass('fas fa-spinner fa-pulse fa-3x fa-fw')
		);
	} else {
		var $modal_header = $("<div />").addClass('modal-header ' + classe_header).append(
			$("<button />").attr({
				"type": "button",
				"data-dismiss": "modal",
				"aria-label": "Close"
			}).addClass('close').html(
				$("<span />").attr('aria-hidden', 'true').html('&times;')
			)
		);
		if(tipo != 'm'){
			$modal_header.prepend(
				$("<h5 />").addClass('modal-title').html(titulo)
			);
		}
		var $modal_body = $("<div />").addClass('modal-body');
		$conteudo_janela_modal.append($modal_header).append($modal_body);
		
		if(tipo != 'm' && tipo != 'ml'){
			$conteudo_janela_modal.append(
				$("<div />").addClass('modal-footer').append(botoes_modal)
			)
		}
		$corpo_janela_modal = $conteudo_janela_modal.find('div.modal-body');
		
		if(tipo == 'm' || tipo == 'ml' || tipo == 'img'){
			var mensagem_modal;
			if(tipo == 'm' || tipo == 'img'){
				mensagem_modal = 'Carregando...';
			} else {
				mensagem_modal = mensagem;
			}
			if(tipo == 'ml'){
				$mensagem_janela_modal = mensagem_modal;
			} else {
				$mensagem_janela_modal = $("<table />").attr('align', 'center').html(
					$("<tr />").append(
						$("<td />").attr('colspan', '2').addClass('mensagem_modal').html(mensagem_modal)
					)
				);
			}
		} else {
			$mensagem_janela_modal = $("<table />").attr('align', 'center').html(
				$("<tr />").append(
					$("<td />").html(
						$("<i />").addClass('fas ' + classe_icone + ' fa-4x').html('')
					)
				).append(
					$("<td />").addClass('mensagem_modal').html(mensagem)
				)
			)
		}
		$corpo_janela_modal.append($mensagem_janela_modal);
	}
	
	var id = gerarIdAleatorio( $janela_modal[0] );
	$janela_modal.appendTo('body').attr('id', id);
	$janela_modal.on({
		'show.bs.modal': function(e) {
			if(!tem_animacao && tipo != 'l'){
				var botao_ok = $janela_modal.find('button.btn-ok');
				if(botao_ok.length > 0){
					setTimeout(function(){
						botao_ok.trigger('focus');
					}, 25);
				} else {
					$janela_modal.find('button.close').focus();
				}
			}
		},
		'shown.bs.modal': function(e) {
			if(tem_animacao && tipo != 'l'){
				var botao_ok = $janela_modal.find('button.btn-ok');
				if(botao_ok.length > 0){
					botao_ok.trigger('focus');
				} else {
					$janela_modal.find('button.close').focus();
				}
			}
			
			// Desativando alternância da tecla TAB em todos os
			// elementos focáveis da página, exceto os que estão
			// dentro do modal
			var total_janelas_modais = $('body').children('div.janela_modal').length;
			if(total_janelas_modais == 1){
				modal.desativarElementosFocaveis($janela_modal);
			}
		},
		'hide.bs.modal': function(e) {
			removerTodosAvisos();
			
			$('body').css('paddingRight','0');
		},
		'hidden.bs.modal': function(e) {
			var $body = $('body');
			
			if(tipo != 'm' && tipo != 'ml') $(window).off('resize.centralizaModal' + id);
			$janela_modal.remove();
			
			if(tipo == 'i' || tipo == 'a' || tipo == 'c' || tipo == 'csn' || tipo == 'csnc' || tipo == 'e' || tipo == 'ml') if(callback) callback(resultado);
			
			// Reativa alternância da tecla TAB em todos os
			// elementos focáveis da página, exceto os que estão
			// dentro do modal
			var total_janelas_modais = $('body').children('div.janela_modal').length;
			if(total_janelas_modais > 0){
				$body.addClass('modal-open');
			} else {
				$body.removeClass('modal-open');
				modal.ativarElementosFocaveis();
			}
			
			$body.css('paddingRight','0');
		}
	});
	$janela_modal.modal({
		'show': true,
		'backdrop': backdrop
	});
	
	if(tipo == 'm'){
		var pagina = mensagem;
		var parametros = titulo;
		var metodo;
		if(typeof parametros != 'undefined'){
			metodo = 'POST';
		} else {
			metodo = 'GET';
		}
		$.ajax({
			type: metodo,
			cache: false,
			url: pagina,
			data: parametros,
			timeout: 5000,
			error: function(jqXHR, textStatus){
				$corpo_janela_modal.html("<b style='color: red; font-weight: bold'>Erro ao carregar página!</b>");
			},
			success: function(d) {
				$corpo_janela_modal.html(d);
				if(callback) callback(resultado);
			}
		});
	} else if(tipo == 'img'){
		var caminho_imagem = mensagem;
		var $botao_fechar = $("<button />").attr({
			"type": "button",
			"data-dismiss": "modal",
			"aria-label": "Close"
		}).addClass('close').html(
			$("<span />").attr('aria-hidden', 'true').html('&times;')
		);
		
		// Carregando imagem para exibir no modal
		$("<img />", {"src": caminho_imagem}).css({
			"width": "auto",
			"height": getAltura() - (getAltura() * 20/100),
			"borderRadius": "3px"
		}).load(function(){
			var $img = $(this);
			$janela_modal.removeClass('carregando');
			$conteudo_janela_modal.html($img).prepend($botao_fechar);
			$dialogo_janela_modal.css({
				'width': $img.width()
			});
			$botao_fechar.focus();
		}).error(function(){
			$corpo_janela_modal.html(
				$("<div />", {"text": "<b style='color: red; font-weight: bold'>Erro ao carregar página!</b>"}).css({"color": "#FF0000", "fontWeight": "bold"})
			).prepend($botao_fechar);
		});
	}
	
	return $janela_modal;
}

modal.obterSeletorElementosFocaveis = function(){
	var seletor = 'a[href], area[href], input:not([disabled]):not([type="hidden"]), ';
	seletor += 'select:not([disabled]), textarea:not([disabled]), button:not([disabled]), ';
	seletor += 'iframe, object, embed, *[tabindex], *[contenteditable]';
	return seletor;
}

modal.desativarElementosFocaveis = function(janela_modal){
	var seletorElementosFocaveis = modal.obterSeletorElementosFocaveis();
	
	var elementosDesativar = [], total_elementos_desativar, tabindex,
        elementosFocaveisPagina = document.querySelectorAll( seletorElementosFocaveis ),
        total_elementos_focaveis_pagina = elementosFocaveisPagina.length,
        janela_modal = janela_modal[0],
        elementosFocaveisModal = janela_modal.querySelectorAll( seletorElementosFocaveis );

    // Converte objeto de elementos focáveis para array, para ser possível usar o método "indexOf"
    elementosFocaveisModal = Array.prototype.slice.call( elementosFocaveisModal );
	
    // Adicionar o contêiner para dentro do array
    elementosFocaveisModal.push( janela_modal );

    // Separar métodos de obtenção de atributos, dos de alteração de atributos
    while( total_elementos_focaveis_pagina-- ) {
        // Não desativar elemento, se estiver dentro do modal
        if ( elementosFocaveisModal.indexOf(elementosFocaveisPagina[total_elementos_focaveis_pagina]) !== -1 ) {
            continue;
        }
		
		// Adicionar elemento para o array de elementos a serem ocultados, se o tabindex não for negativo
        tabindex = parseInt(elementosFocaveisPagina[total_elementos_focaveis_pagina].getAttribute('tabindex'));
        if ( isNaN( tabindex ) ) {
            elementosDesativar.push([elementosFocaveisPagina[total_elementos_focaveis_pagina], 'inline']);
        } else if ( tabindex >= 0 ) {
            elementosDesativar.push([elementosFocaveisPagina[total_elementos_focaveis_pagina], tabindex]);
        } 

    }

    // Desativando os elementos focáveis da página
    total_elementos_desativar = elementosDesativar.length;
    while( total_elementos_desativar-- ) {
        elementosDesativar[total_elementos_desativar][0].setAttribute('data-tabindex', elementosDesativar[total_elementos_desativar][1]);
        elementosDesativar[total_elementos_desativar][0].setAttribute('tabindex', -1);
    }
}

modal.ativarElementosFocaveis = function(){
	var elementosAtivar = [], total_elementos_ativar, data_tabindex,
        elementosFocaveisDesativados = document.querySelectorAll('[data-tabindex]'),
        total_elementos_focaveis_desativados = elementosFocaveisDesativados.length;
		
	// Separando os métodos de obtenção e definição de atributos
    while( total_elementos_focaveis_desativados-- ) {
        data_tabindex = elementosFocaveisDesativados[total_elementos_focaveis_desativados].getAttribute('data-tabindex');
        if ( data_tabindex !== null ) {
            elementosAtivar.push([elementosFocaveisDesativados[total_elementos_focaveis_desativados], (data_tabindex == 'inline') ? 0 : data_tabindex]);
        }
    }

    // Reativando os elementos focáveis da página
    total_elementos_ativar = elementosAtivar.length;
    while( total_elementos_ativar-- ) {
        elementosAtivar[total_elementos_ativar][0].removeAttribute('data-tabindex');
        elementosAtivar[total_elementos_ativar][0].setAttribute('tabindex', elementosAtivar[total_elementos_ativar][1] ); 
    }
}

function jInfo(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Informação';
	return modal.instanciar(mensagem, titulo, 'i', callback, tem_modal, tem_animacao);
}

function jAlert(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Aviso';
	return modal.instanciar(mensagem, titulo, 'a', callback, tem_modal, tem_animacao);
}

function jConfirm(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Confirmação';
	return modal.instanciar(mensagem, titulo, 'c', callback, tem_modal, tem_animacao);
}

function jConfirmSimNao(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Confirmação';
	return modal.instanciar(mensagem, titulo, 'csn', callback, tem_modal, tem_animacao);
}

function jConfirmSimNaoCancelar(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Confirmação';
	return modal.instanciar(mensagem, titulo, 'csnc', callback, tem_modal, tem_animacao);
}

function jError(mensagem, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Erro';
	return modal.instanciar(mensagem, titulo, 'e', callback, tem_modal, tem_animacao);
}

function jModal(pagina, parametros, callback, tamanho, tem_modal, tem_animacao, fechar_ao_clicar_no_fundo){
	var $janela_modal = modal.instanciar(pagina, parametros, 'm', function(){
		if(tamanho == 'p'){
			tamanho = 'modal-sm';
		} else if(tamanho == 'm'){
			tamanho = 'modal-lg';
		} else if(tamanho == 'g'){
			tamanho = 'modal-xl';
		} else {
			tamanho = '';
		}
		
		$janela_modal.children('div.modal-dialog').addClass(tamanho);
		
		if(callback) callback();
	}, tem_modal, tem_animacao, false);
	
	return $janela_modal;
}

function jModalPequeno(pagina, parametros, callback, tem_modal, tem_animacao){
	return jModal(pagina, parametros, callback, 'p', tem_modal, tem_animacao);
}

function jModalMedio(pagina, parametros, callback, tem_modal, tem_animacao){
	return jModal(pagina, parametros, callback, 'm', tem_modal, tem_animacao);
}

function jModalGrande(pagina, parametros, callback, tem_modal, tem_animacao){
	return jModal(pagina, parametros, callback, 'g', tem_modal, tem_animacao);
}

function jModalLocal(html, titulo, callback, tem_modal, tem_animacao){
	if(!titulo) titulo = 'Modal';
	return modal.instanciar(html, titulo, 'ml', callback, tem_modal, tem_animacao);
}

function jModalRemove(callback){
	var $janela_modal = $('div.janela_modal').last();
	$janela_modal.on('hidden.bs.modal', callback);
	return $janela_modal.modal('hide');
}

function jImage(caminho_imagem, tem_modal, tem_animacao){
	return modal.instanciar(caminho_imagem, '', 'img', null, null, tem_modal, tem_animacao);
}

function jLoadingWheel(tem_modal, tem_animacao){
	return modal.instanciar('', '', 'l', null, tem_modal, tem_animacao);
}

function jLoadingWheelRemove(callback){
	return jModalRemove(callback);
}

function jForm(pagina, parametros, callback, tamanho){
	if(typeof parametros == 'undefined' || $.trim(parametros) == ''){
		parametros = '?ajax=true';
	} else {
		parametros += '&ajax=true';
	}
	return jModal(pagina, parametros, function(){
		var $janela_modal = $('div.janela_modal');
		var $form = $janela_modal.find('form');
		
		instanciarComponentes(null, $janela_modal);
		if($form.length > 0){
			var $primeiroElementoFocavel = $form.find('[autofocus]:visible').first();
			if($primeiroElementoFocavel.length == 0){
				$primeiroElementoFocavel = $form.find(':input:visible:first').first();
			}
			$primeiroElementoFocavel.focus();
		}
		
		if(callback) callback();
	}, tamanho, true, false, false);
}

function jFormMedio(pagina, parametros, callback){
	return jForm(pagina, parametros, callback, 'm');
}

function jFormGrande(pagina, parametros, callback){
	return jForm(pagina, parametros, callback, 'g');
}

function mostraCarregando(tem_modal, tem_animacao){
	if(typeof tem_modal == 'undefined') tem_modal = true;
	if(typeof tem_animacao == 'undefined') tem_animacao = true;
	return jLoadingWheel(tem_modal, tem_animacao);
}

function ocultaCarregando(callback){
	return jModalRemove(callback);
}

function exibirBgBody(pagina, titulo){
	return jModal(pagina, titulo);
}

function removerDivs(callback){
	return jModalRemove(callback);
}