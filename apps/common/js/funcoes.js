// Variáveis de escopo global
var navegador_IE = eval('/*@cc_on !@*/false');
var versao_jscript = (navegador_IE) ? eval("/*@cc_on @_jscript_version @*/") : (0);
var versao_IE = (navegador_IE) ? (getVersaoIE()) : (0);

function gE(ID) {
	return document.getElementById(ID);
}
function gEs(tag) {
	return document.getElementsByTagName(tag);
}

function gEn(name) {
	return document.getElementsByName(name);
}

function getLargura(){
	var largura = (top != self) ? (top.innerWidth || document.documentElement.clientWidth) : (window.innerWidth || document.documentElement.clientWidth);
	return largura;
}

function getAltura(){
	var altura = (top != self) ? (top.innerHeight || document.documentElement.clientHeight) : (window.innerHeight || document.documentElement.clientHeight);
	return altura;
}

function sortNumber(array){
	return array.sort(function(a, b){ return a - b; });
}

function removeDuplicates(array){
	return array.filter(function (item, index, self) {
        return self.indexOf(item) == index;
    });
}

function getTamanhoObjeto(obj){
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
}

function scrollarElemento(id_elemento, tempo){
	var id;
    if (typeof id_elemento == 'object') {
        if (!gE($(id_elemento).attr('id'))) {
            id = gerarIdAleatorio(id_elemento);
            $(id_elemento).attr('id', id);
        }
        else {
            id = $(id_elemento).attr('id');
        }
        id_elemento = id;
    }
    if (typeof tempo == 'undefined') {
        tempo = 500;
    }
	var posicao_scroll = (parseInt($("#" + id_elemento).offset().top) - 60);
	if(tempo > 0){
		$('html, body').animate({
			scrollTop: posicao_scroll
		}, tempo);
	} else {
		$('html, body').scrollTop(posicao_scroll);
	}
}

function gerarIdAleatorio(el){
	var nome, numero, id;
	do{
		nome = ($(el).attr('name') === undefined) ? ('sem_nome') : $(el).attr('name');
		numero = parseInt(Math.random() * 1000, 10);
		id = (nome + numero).replace("[", "").replace("]", "");
	} while($('#'+id).length > 0);
	return id;
}

/* Função que retorna o dispositivo utilizado pelo usuário, para acessar o sistema
 * Valores possíveis de retorno:
 *	- xs: Extra small (Celulares, com largura de tela menor que 768px);
 *	- sm: Small (Tablets, com largura de tela maior ou igual a 768px);
 *	- md: Medium (Desktops de monitor antigo, com largura maior ou igual a 992px);
 *	- lg: Large (Desktops de monitor widescreen, com largura maior ou igual a 1200px).
 * */
function getDispositivo(onresize) {
	if(typeof onresize == 'undefined') onresize = false;
	if(onresize){
		$(window).off('resize.atualizaVariavelGlobal').on('resize.atualizaVariavelGlobal', function(){
			window.dispositivo = getDispositivo(false);
		});
	}
	var envs = ['xs', 'sm', 'md', 'lg'];

	var $el = $('<div>');
	$el.appendTo( $('body') );

	for (var i = envs.length - 1; i >= 0; i--) {
		var env = envs[i];

		$el.addClass('hidden-'+env);
		if ($el.is(':hidden')) {
			$el.remove();
			return env
		}
	};
}

function instanciarComponentes(campo, escopo){
	aba.instanciar(campo, escopo);
	campoMultiplo.instanciar(campo, escopo);
	tabela.instanciar(campo, escopo);
	select.instanciar(campo, escopo);
	fileUploader.instanciar(campo, escopo);
	instanciarComponenteBootstrapTagsinput(campo, escopo);
}

function instanciarBuscaMenu(){
	var $sidebarForm = $('#sidebar-form');
	var $ulNavSidebar = $('ul.nav-sidebar');
	var $inputSearch = $sidebarForm.find('input.form-control');

	$sidebarForm.on('submit', function (e) {
		e.preventDefault();
	});

	$ulNavSidebar.find('li.active').data('lte.pushmenu.active', true);

	$inputSearch.on('keyup', function () {
		var term = $inputSearch.val().trim();

		if (term.length === 0) {
			$ulNavSidebar.find('li').each(function () {
				var $li = $(this);
				$li.show(0);
				$li.removeClass('active');
				if ($li.data('lte.pushmenu.active')) {
					$li.addClass('active');
				}
			});
			return;
		}

		$ulNavSidebar.find('li').each(function () {
			var $li = $(this);
			if ($li.text().toLowerCase().indexOf(term.toLowerCase()) === -1) {
				$li.hide(0);
				$li.removeClass('pushmenu-search-found menu-open', false);

				if ($li.is('.treeview')) {
					$li.removeClass('active');
				}
			} else {
				$li.show(0);
				$li.addClass('pushmenu-search-found menu-open');

				if ($li.is('.treeview')) {
					$li.addClass('active');
				}

				var $parent = $li.parents('li').first();
				if ($parent.is('.treeview')) {
					$parent.show(0);
				}
			}

			if ($li.is('.header')) {
				$li.show();
			}
		});

		$ulNavSidebar.find('li.pushmenu-search-found.treeview').each(function () {
			var $li = $(this);
			$li.find('.pushmenu-search-found').show(0);
		});
	});
}

var camposAlvo = [];

function validaForm(form, mostra_modal, mostra_aviso, callback_onupdate_ajax, callback_onmodalshown_ajax){
	mostra_modal = (typeof mostra_modal !== 'undefined') ? (mostra_modal) : (true);
	mostra_aviso = (typeof mostra_aviso !== 'undefined') ? (mostra_aviso) : (true);
	callback_onupdate_ajax = (typeof callback_onupdate_ajax !== 'undefined') ? (callback_onupdate_ajax) : ('');
	callback_onmodalshown_ajax = (typeof callback_onmodalshown_ajax !== 'undefined') ? (callback_onmodalshown_ajax) : ('');
	
	var $form = $(form);
	
	var checkSubmissaoAjax = ($form.is('[data-ajax]') && $form.attr('data-ajax') == 'true');
	var status = validaElementos(form);

	if (status == true) {
		if (mostra_modal == true) {
			mostraCarregando(true, false);
		}
		if(checkSubmissaoAjax){
			// Submeter formulário via Ajax
			submeteFormAjax(form, true, function(r){
				var tipo_modal = r.tipo;
				var mensagem = r.mensagem;
				var mostrarDetalhes = r.mostrarDetalhes;
				var detalhes = r.detalhes;
				
				ocultaCarregando(function(){
					// Fechando modal do formulário
					if(tipo_modal == 'informacao' || tipo_modal == '') jModalRemove();
					
					// Executando instruções pós-atualização, porém antes da
					// exibição do modal de submissão do formulário
					if($.isFunction(callback_onupdate_ajax)){
						callback_onupdate_ajax(r);
					} else {
						// Se a operação der resultado ok, remover janela modal do formulário e atualizar a tabela ao fundo (se existir)
						if(tipo_modal == 'informacao' || tipo_modal == ''){
							tabela.atualizar();
						}
					}

					// Exibindo janela modal de retorno da submissão deste formulário,
					// bem como executando seu callback
					mostraModalSubmissaoFormAjax(tipo_modal, mensagem, mostrarDetalhes, detalhes, function(){
						if($.isFunction(callback_onmodalshown_ajax)){
							callback_onmodalshown_ajax(r);
						}
					});
				});
			});
			$form.removeClass('was-validated');
			return false;
		} else {
			// Submeter formulário normalmente
			return true;
		}
	} else {
		$form.addClass('was-validated');
		if(mostra_aviso) mostrarAvisosValidaForm(camposAlvo);
		return false;
	}
}

function validaElementos(id_pai) {
	var id, status = true;
	var i = 0;

	camposAlvo = [];

	if (typeof id_pai == 'object') {
		if (!gE($(id_pai).attr('id'))) {
			id = gerarIdAleatorio(id_pai);
			$(id_pai).attr('id', id);
		}
		else {
			id = $(id_pai).attr('id');
		}
		id_pai = id;
	}
	var $elemento = $('#' + id_pai);
	$elemento.find("input, textarea, select").not(":disabled, [type='hidden']").each(function() {
		/*
		 * Valida elementos required
		 */
		var $campo = $(this);
		var checkCampoObrigatorio = ((($campo.is('[required]')) || ($campo.is('[data-required]'))) && (!$campo.is("[data-desativar-validacao='true']")) );
		if(checkCampoObrigatorio){
			if (!gE($campo.attr("id"))) {
				$campo.attr('id', gerarIdAleatorio(this));
			}

			if ($campo.is("input.spinner[data-aceita-valores-nulos='false']") && parseFloat($campo.val()) == 0) {
				camposAlvo[i] = {
					'id': $campo.attr("id"),
					'mensagem': 'O valor deste campo tem que ser superior a 0.'
				};
				i++;
				status = false;
			} else if ((window.tinyMCE) && ($campo.is("textarea.editor"))) {
				var editorContent = (tinyMCE.get($campo.attr("id")).getContent()).replace(/[(&nbsp;)(<p>)(</p>)(\n)(\r)( )]/g, "");
				if (editorContent == '') {
					camposAlvo[i] = {
						'id': $('#' + $campo.attr("id") + "_tbl .mceIframeContainer").attr("id", gerarIdAleatorio(this)),
						'mensagem': 'Este campo é requerido.'
					};
					i++;
					status = false;
				}
			} else if($campo.is('select.select')){
				// Campo Select instanciado com o componente Select2
				var campoVazio = (($.trim($campo.val())).replace(/ /g, '') == '');
				if(campoVazio){
					var $alvo = $campo.next();
					$alvo.attr('id', gerarIdAleatorio($alvo[0]));
					
					camposAlvo[i] = {
						'id': $alvo.attr('id'),
						'mensagem': 'Este campo é requerido.'
					};
					i++;
					status = false;
				}
			} else if ($campo.attr('type') == "checkbox") {
				var nome_tmp = ( $campo.attr('name') ).replace(/\[.*?\]/g, '');
				var $elementos = $("input[type='checkbox'][name^='" + nome_tmp + "'][required]");
				var total_elementos = $elementos.length;
				var ok = false;
				$elementos.each(function(){
					var $campoCheckbox = $(this);
					if($campoCheckbox.is(':checked')){
						ok = true;
						return false; // Sair do $.each
					}
				});
				if(!ok){
					var mensagem;
					if(total_elementos > 1){
						mensagem = 'Pelo menos um item deve ser marcado.';
					} else {
						mensagem = 'Este campo é requerido.';
					}
					
					var $alvo = $elementos.first().next();
					if($alvo.hasClass('img-check')){
						$alvo = $alvo.closest('div.grid-checkbox').parent();
					}
					if(!$alvo.is('[id]')){
						$alvo.attr('id', gerarIdAleatorio($alvo[0]));
					}
					var id_alvo = $alvo.attr('id');
					
					camposAlvo[i] = {
						'id': id_alvo,
						'mensagem': mensagem
					};
					i++;
					status = false;
				}
			} else if ($campo.attr('type') == "radio") {
				if (!$campo.is(":checked")) {
					var nome_tmp = ( $campo.attr('name') ).replace(/\[.*?\]/g, '');
					var ok = false;
					$elemento.find("input[type='radio'][name=^'"+nome_tmp+"']").each(function(){
						var $campoRadio = $(this);
						if ($campoRadio.is(":checked")) {
							ok = true;
							return false; // Sair do $.each
						}
					});
					if (!ok) {
						camposAlvo[i] = {
							'id': $campo.attr("id"),
							'mensagem': 'Este campo é requerido.'
						};
						i++;
						status = false;
					}
				}
			} else if (($.trim($campo.val())).replace(/ /g, '') == '') {
				camposAlvo[i] = {
					'id': $campo.attr("id"),
					'mensagem': 'Este campo é requerido.'
				};
				i++;
				status = false;
			}
		}
	})

	if (status) {
		return true;
	} else {
		return false;
	}
}

function mostrarAvisosValidaForm(camposAlvo){
	var setouFoco = false;
	for (var a = 0; a < camposAlvo.length; a++) {
		var alvo = camposAlvo[a];
		var mensagem = alvo['mensagem'];
		//var posicao = alvo['posicao'];
		
		var $elemento = $('#' + alvo.id);
		$elemento.siblings('div.invalid-feedback').remove();
		
		if($elemento.is("[data-role='tagsinput']")){
			$elemento.siblings('div.bootstrap-tagsinput').after(
				$('<div />').addClass('invalid-feedback').html(mensagem)
			);
		} else {
			$elemento.after(
				$('<div />').addClass('invalid-feedback').html(mensagem)
			);
		}
		
		if(!setouFoco){
			$elemento.focus();
			setouFoco = true;
		}
	}
}

function submeteFormAjax(form, retornarJSON, callback){
	if(typeof retornarJSON == 'undefined') retornarJSON = true;
	var $form = $(form);
	var acao = ($form.is('[action]')) ? ( $form.attr('action') ) : (location.pathname.split('/').pop());
	var metodo = ($form.is('[method]')) ? ( ( $form.attr('method') ).toLowerCase() ) : ('get');
	var parametros;
	$form.append(
		$("<input />", {"type": "hidden", "name": "ajax", "value": "true"})
	);
	if(metodo == 'post'){
		parametros = $form.serialize();
	} else {
		acao += '?' + $form.serialize();
		parametros = 'ajax=true';
	}
	chamarPagina(acao, parametros, function(r){
		var retorno;
		if(retornarJSON){
			var resposta = interpretarJSON(r);
			if(getTamanhoObjeto(resposta) > 0){
				retorno = {
					'tipo': resposta.tipo_modal,
					'mensagem': resposta.msg_modal,
					'pagina': resposta.pagina,
					'redirecionar': resposta.redirecionar,
					'mostrarDetalhes': false,
					'detalhes': r
				}
			} else {
				retorno = {
					'tipo': 'erro',
					'mensagem': 'Erro ao realizar operação.',
					'pagina': '',
					'redirecionar': false,
					'mostrarDetalhes': true,
					'detalhes': r
				}
			}
		} else {
			retorno = r;
		}
		if(callback) callback(retorno);
	}, function(){
		var retorno = {
			'tipo': 'erro',
			'mensagem': 'Não foi possível obter resposta do servidor.<br />Tente novamente após alguns instantes.',
			'mostrarDetalhes': true
		};
		if(callback) callback(retorno);
	});
}

function mostraModalSubmissaoFormAjax(tipo_modal, mensagem, mostrarDetalhes, detalhes, pos_callback_ajax){
	// Exibindo modal / aviso de submissão do formulário
	var $modalRetorno;
	if(tipo_modal == 'informacao' || tipo_modal == ''){
		var tipo_aviso;
		if(mensagem.indexOf('editad') !== -1){
			tipo_aviso = 'success';
		} else {
			tipo_aviso = 'info';
		}
		exibirAvisoNotify(mensagem, tipo_aviso);
		if ($.isFunction(pos_callback_ajax)) {
			pos_callback_ajax();
		}
	} else if(tipo_modal == 'aviso'){
		$modalRetorno = jAlert(mensagem);
	} else if(tipo_modal == 'erro'){
		if (typeof mensagem == 'string' && mensagem.startsWith('Sessão expirada')) {
			$modalRetorno = modal.sessaoExpirada();
		} else {
			$modalRetorno = jError(mensagem);
		}
	} else {
		$modalRetorno = jError(mensagem);
	}
	
	// Caso for solicitado exibição de detalhes do erro, inseri-las na forma de um componente
	// que exibe / oculta os erros quando clicado
	if(mostrarDetalhes){
		var $corpo_janela_modal = $modalRetorno.find('div.modal-body');
		
		// Criação do componente de detalhamento de erros
		var $fieldsetDetalhesErro = $('<fieldset />').addClass('detalhes_erro').append(
			$('<legend />').html(
				$('<button />').attr({
					'type': 'button',
					'title': 'Mostrar detalhes do erro ocorrido.'
				}).addClass('btn btn-default').html('Detalhes do Erro').click(function(){
					var $fieldset = $(this).closest('fieldset.detalhes_erro');
					var $janela_modal = $fieldset.closest('div.janela_modal');
					var $iframe = $fieldset.find('iframe');
					if($iframe.is(':visible')){
						$iframe.hide();
						$fieldset.closest('div.modal-dialog').removeClass('largura_aumentada');
					} else {
						$fieldset.closest('div.modal-dialog').addClass('largura_aumentada');
						$iframe.show();
					}
				})
			)
		).append(
			$('<iframe />').hide()
		);
		
		// Inserção do componente dentro da janela modal, abaixo do ícone e da mensagem de retorno.
		$corpo_janela_modal.append($fieldsetDetalhesErro);
		
		// Injetando detalhes do erro no <iframe> dentro do componente acima.
		// A exibição de erros é feita via <iframe> por questões de segurança da aplicação
		// no lado do cliente.
		var $iframe = $fieldsetDetalhesErro.find('iframe');
		injetarConteudoIframe($iframe[0], detalhes);
	}
}

function exibirAvisoNotify(mensagem, tipo){
	if(typeof tipo == 'undefined') tipo = 'success';
	$.hulla = new hullabaloo();
	
	$.hulla.options.offset = {
		from: "top",
		amount: 30
	};
	$.hulla.options.width = 300;
	$.hulla.options.delay = 10000;
	
	$.hulla.options.stackup_spacing = 15;

	$.hulla.send(mensagem, tipo);
}

function validaFormAbas(form){
	var $form = $(form);
	var $listasAbas = $form.find('ul.nav-tabs');
	var retorno = true;
	var camposAbas = camposAlvoAbaVisivel = [];
	$listasAbas.each(function(){
		var $listaAbas = $(this);
		var $lis = $listaAbas.children('li');
		var $divs = $listaAbas.siblings('div.tab-content').children('div.tab-pane');
		
		$divs.each(function(i){
			var $div = $(this);
			var $li = $lis.eq(i);
			if(!$li.is("[id]") || $.trim( $li.attr('id') ) == ''){
				$li.attr('id', gerarIdAleatorio($li));
			}
			
			if(!validaElementos($div)){
				retorno = false;
				if($li.hasClass('active')){
					$.merge(camposAlvoAbaVisivel, camposAlvo);
				} else {
					camposAbas.push({
						'id': $li.attr('id'),
						'mensagem': 'Há campos em branco<br />nesta aba!',
						'posicao': 't'
					});
				}
			}
		})
	});
	
	if(retorno){
		mostraCarregando();
		return true;
	} else {
		$.merge(camposAlvoAbaVisivel, camposAbas);
		mostrarAvisosValidaForm(camposAlvoAbaVisivel);
		return false;
	}
}

function confirma(pagina, parametros, mensagem, ajax, callback_sucesso, callback_erro) {
    if (!mensagem)
        mensagem = 'Deseja realizar esta operação?';
    ajax = (typeof ajax != 'undefined' && ajax == 'true');
    jConfirmSimNao(mensagem, 'Confirmação', function (valor) {
        if (valor) {
            if (ajax) {
                mostraCarregando();
                chamarPagina(pagina, parametros, function (r) {
                    // Callback de sucesso
                    if (callback_sucesso)
                        callback_sucesso(r);
                    ocultaCarregando();
                }, function () {
                    // Callback de erro
                    if (callback_erro)
                        callback_erro();
                    ocultaCarregando();
                });
            } else {
                abrirPagina(pagina, parametros);
            }
        }
    });
}

function apagaRegistro(pagina, id, mensagem, ajax) {
	if (!mensagem)
		mensagem = 'Deseja excluir este registro?';
	if(typeof ajax == 'undefined') ajax = true;
	var $modalConfirmacao = jConfirmSimNao(mensagem, 'Confirmação', function (valor) {
		if (valor) {
			var parametros = 'id=' + id + '&acao=excluir';
			if (ajax) {
				parametros += '&ajax=true';
				mostraCarregando(true, false);
				chamarPagina(pagina, parametros, function (r) {
					// Callback de sucesso
					var resposta = interpretarJSON(r);
					ocultaCarregando();
					if (getTamanhoObjeto(resposta) > 0) {
						var tipo_modal = resposta.tipo_modal;
						var msg_modal = resposta.msg_modal;
						var pagina = function () {
							location.href = resposta.pagina;
						};

						if (tipo_modal == 'informacao' || tipo_modal == '') {
							// Atualizar tabela após remoção do registro
							tabela.atualizar();
							// Remover linha da tabela
							//tabela.removerLinhaById(id);
						}
						mostraModalSubmissaoFormAjax(tipo_modal, msg_modal, false, r);
					} else {
						mostraModalSubmissaoFormAjax('erro', 'Erro ao realizar operação.', true, r);
					}
				}, function (r) {
					// Callback de erro
					ocultaCarregando();
					mostraModalSubmissaoFormAjax('erro', 'Erro ao realizar operação.', true, r);
				});
			} else {
				abrirPagina(pagina, parametros);
			}
		}
	});
	
	$modalConfirmacao.find('div.modal-header').removeClass('bg-primary').addClass('bg-danger');
	$modalConfirmacao.find('button.btn-ok').removeClass('btn-primary').addClass('btn-danger');
}

function criaObjetoDeURI(uri) {
	var obj;
	try{
		obj = $.parseJSON('{"' + decodeURI(uri.replace(/&/g, "\",\"").replace(/=/g,"\":\"")) + '"}');
	}catch(e){
		obj = {};
	}
	return obj;
}

function abrirPagina(pagina, dados, target){
	var metodo;
	if(typeof dados === 'undefined' || (typeof dados === 'string' && $.trim(dados) == '')){
		metodo = 'get';
	} else {
		metodo = 'post';
	}
	if(typeof target === 'undefined' || $.trim(target) == '') target = '_self';
	if(metodo == 'get'){
		if(target == '_self'){
			mostraCarregando();
			location.href = pagina;
		} else {
			window.open(pagina, target);
		}
	} else {
		if(target == '_self'){
			mostraCarregando();
		}
		
		var form = $('<form />', {"method": metodo, "action": pagina, "target": target}).css("opacity", "0").appendTo("body");
		
		if(typeof dados === 'string'){
			dados = criaObjetoDeURI(dados);
		}

		$.each(dados, function(k, v){
			form.append(
				$("<input />", {"type": "hidden", "name": k, "value": v})
			)
		})
		form.submit();
	}
}

function obterJanelaIframe(iframe) {
	return (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
}

function injetarConteudoIframe(iframe, conteudo){
	var documento_iframe = obterJanelaIframe(iframe).document;
	documento_iframe.open();
	documento_iframe.write(conteudo);
	documento_iframe.close();
}

function interpretarJSON(string){
	var array_json;
	try{
		array_json = $.parseJSON(string);
	}catch(e){
		array_json = {};
	};
	return array_json;
}

function chamarPagina(pagina, parametros, callback, callback_erro, timeout) {
	var metodo;
	if(typeof parametros == 'undefined' || (typeof parametros == 'string' && $.trim(parametros) == '')){
		metodo = 'get';
	} else {
		metodo = 'post';
	}
	if(typeof timeout == 'undefined') timeout = 0;
	ajax = $.ajax({
		type: metodo,
		url: pagina,
		data: parametros,
		timeout: timeout,
		error: function(jqXHR, textStatus){
			if(callback_erro) callback_erro(jqXHR, textStatus);
		},
		success: function(d) {
			if(callback) callback(d);
		}
	});
	return ajax;
}

function abortarPaginaChamada(ajax){
	try{
		return ajax.abort();
	}catch(e){
		return e;
	}
}

function adicionarModuloSistema(botao){
	var $botao = $(botao);
	var $tabela = $botao.closest('table');
	var $tbody = $tabela.children('tbody');

	var iterador = parseInt($tabela.attr('data-iterador'), 10) + 1;

	chamarPagina('sistema_modulo_preenche.php?' + '&i=' + iterador, '', function(r){
		$tbody.append(r);
		instanciarComponentes(undefined, $tbody);
		$tabela.attr('data-iterador', iterador);
	})
}

function removerModuloSistema(botao){
	var $botao = $(botao);
	var $tr = $botao.closest('tr');
	var $tbody = $tr.closest('tbody');
	
	var total_modulos_apos_remocao = $tbody.children('tr').not($tr).not('.marcar_exclusao').length;
	if(total_modulos_apos_remocao == 0){
		aviso($botao, 'Pelo menos um módulo deve constar no sistema!', 10, 'l');
		return;
	}

	if($tr.is("[data-linha-existente='true']")){
		var $inputAcao = $botao.siblings('input.acao');
		var acao = $inputAcao.val();

		if(acao == 'editar'){
			$inputAcao.val('excluir');
			$tr.addClass('marcar_exclusao').find('input').not("[type='hidden']").attr('disabled', 'disabled');
			$botao.attr('title', 'Desfazer').html('<i class="fas fa-undo" />');
			$tr.hide();
		} else {
			$inputAcao.val('editar');
			$tr.removeClass('marcar_exclusao').find('input').not("[type='hidden']").removeAttr('disabled');
			$botao.attr('title', 'Excluir').html('<i class="fas fa-minus" />');
		}
	} else {
		$tr.remove();
	}
}

function definirModuloSistema(campo, id_campo_modulo, id_campo_sistema, callback_modulo, callback_sistema) {
    var $campo = $(campo);
    var selected_item = $campo.select2('data')[0];
	if(typeof id_campo_modulo == 'undefined') id_campo_modulo = 'modulo';
	if(typeof id_campo_sistema == 'undefined') id_campo_sistema = 'sistema';

    var $campoModulo = $('#' + id_campo_modulo);
    var $campoSistema = $('#' + id_campo_sistema);

    var id_modulo = selected_item['id_modulo'];
    var id_sistema = selected_item['id_sistema'];

    var nome_modulo = selected_item['nome_modulo'];
    var nome_sistema = selected_item['nome_sistema'];

    if (typeof id_modulo != 'undefined') {
        select.setarValor($campoModulo, id_modulo, nome_modulo);
		if(callback_modulo) callback_modulo(true);
    } else {
		if(callback_modulo) callback_modulo(false);
	}
    if (typeof id_sistema != 'undefined') {
        select.setarValor($campoSistema, id_sistema, nome_sistema);
		if(callback_sistema) callback_sistema(true);
    } else {
		if(callback_sistema) callback_sistema(false);
	}
}

function carregarComponentesByTipoFuncionalidade(campoTipoFuncionalidade){
	var $campoTipoFuncionalidade = $(campoTipoFuncionalidade);
	var $campoNomeFuncionalidade = $('#nome');
	var $divRowComponentes = $('#componentes');
	
	var id_tipo_funcionalidade = $campoTipoFuncionalidade.val();
	var nome_funcionalidade = $campoNomeFuncionalidade.val();
	
	$divRowComponentes.removeClass('d-none').html('Carregando...');
	var parametros = 'tipo_funcionalidade=' + id_tipo_funcionalidade;
	parametros += '&nome_funcionalidade=' + nome_funcionalidade;
	chamarPagina('funcionalidade_componente_preenche.php?' + parametros, '', function(r){
		$divRowComponentes.html(r);
		instanciarComponentes(null, $divRowComponentes);
		
		$divRowComponentes.find('select.select').trigger('change');
	});
}

function concatenarLabelOptgroupParaTemplateSelection(select){
	var $select = $(select);
	var $opcaoSelecionada = $select.find(':selected');
	var $optgroupOpcaoSelecionada = $opcaoSelecionada.closest('optgroup');
	var $spanSelect2Container = $select.next();
	var $spanSelection = $spanSelect2Container.find('span.select2-selection__rendered');
	
	var label_optgroup = $optgroupOpcaoSelecionada.attr('label');
	var spanSelectionTitle = $spanSelection.attr('title');
	var spanSelectionText = $spanSelection.html();
	
	$spanSelection.attr('title', spanSelectionTitle + ' - ' + label_optgroup);
	$spanSelection.html(spanSelectionText + ' - ' + label_optgroup);
}

function toggleQuantidadeOuNomeCamposArquivosComponente(radio){
	var $radio = $(radio);
	var $divCol = $radio.closest("div[class^='col']");
	var $inputQuantidadeCamposArquivos = $divCol.find("input[name$='[quantidade_campos]'], input[name$='[quantidade_arquivos_referenciados]']");
	var $selectNomesCamposArquivos = $divCol.find("select[name$='[nomes_campos][]'], select[name$='[nomes_arquivos_referenciados][]']");
	var $divTagsinputNomesCamposArquivos = $selectNomesCamposArquivos.prev();
	
	var modo_preenchimento = $radio.val();
	if(modo_preenchimento == 'q'){
		$inputQuantidadeCamposArquivos.show();
		$divTagsinputNomesCamposArquivos.hide();
	} else {
		$inputQuantidadeCamposArquivos.hide();
		
		if($selectNomesCamposArquivos.is("[data-instanciado='true']")){
			$divTagsinputNomesCamposArquivos.show();
		} else {
			$selectNomesCamposArquivos.removeClass('d-input');
			
			instanciarComponenteBootstrapTagsinput($selectNomesCamposArquivos);
		}
	}
}

function instanciarComponenteBootstrapTagsinput(campo, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(campo){
		busca = $(escopo).find(campo).filter(':visible').not("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("select.tagsinput").filter(':visible').not("[data-instanciado='true']");
	}
	busca.each(function(){
		var $select = $(this);
		
		$select.attr('data-role', 'tagsinput');
		
		var id_campo;
		if( $select.is("[id]") ){
			id_campo = $select.attr('id');
		} else {
			id_campo = gerarIdAleatorio($select[0]);
			$select.attr('id', id_campo);
		}
		
		var placeholder = $select.attr('placeholder');

		$select.tagsinput();
		$select.on({
			'itemAdded': function(){
				$(this).next().children('input').removeAttr('placeholder');
			},
			'itemRemoved': function(){
				if($(this).tagsinput('items').length == 0){
					$(this).next().children('input').attr('placeholder', placeholder);
				}
			}
		});
		var $divTagsinput = $select.prev();
		$divTagsinput.insertAfter($select);
		
		$select.attr('data-instanciado', 'true');
	});
}

function calcularComplexidadeEValorComponente(elemento){
	var $divCardBody = $(elemento).closest('div.card-body');
	var $selectTipoComponente = $divCardBody.find("[name='tipo_componente']");
	var $checkboxPossuiAcoes = $divCardBody.find("[name='possui_acoes']");
	var $checkboxPossuiMensagens = $divCardBody.find("[name='possui_mensagens']");
	var $inputsCampos = $divCardBody.find("[name^='campos']");
	var $inputsArquivosReferenciados = $divCardBody.find("[name^='arquivos_referenciados']");
	var $inputComplexidade = $divCardBody.find("[name='complexidade']");
	var $inputValor = $divCardBody.find("[name='valor_pf']");
	
	// Obtendo parâmetros adicionais
	var $opcaoSelecionada = $selectTipoComponente.find(':selected');
	var tipo_funcional = $opcaoSelecionada.attr('data-alias');
	var quantidade_total_tipos_dados = 0, quantidade_total_arquivos_referenciados = 0;
	
	// Caso algum dos checkboxes "possui ações" ou "possui mensagens" estiver
	// marcado, incrementar em um a quantidade total de tipos de dados
	if($checkboxPossuiAcoes.is(':checked')) quantidade_total_tipos_dados++;
	if($checkboxPossuiMensagens.is(':checked')) quantidade_total_tipos_dados++;
	
	// Obter o total de nomes adicionados, com base no componente
	// "Bootstrap Tags Input"
	var quantidade_campos = $inputsCampos.length;
	if(quantidade_campos > 0){
		// Pelo menos um nome foi digitado, logo somar o total de nomes
		// à variável da quantidade total de tipos de dados.
		quantidade_total_tipos_dados += quantidade_campos;
	} else {
		// Nenhum nome digitado, logo zerar a variável da
		// quantidade total de tipos de dados
		quantidade_total_tipos_dados = 0;
	}
	
	// Obter o total de nomes adicionados, com base no componente
	// "Bootstrap Tags Input"
	var quantidade_arquivos_referenciados = $inputsArquivosReferenciados.length;
	if(quantidade_arquivos_referenciados > 0){
		// Pelo menos um nome foi digitado, logo somar o total de nomes
		// à variável da quantidade total de tipos de dados.
		quantidade_total_arquivos_referenciados += quantidade_arquivos_referenciados;
	} else {
		// Nenhum nome digitado, logo zerar a variável da
		// quantidade total de tipos de dados
		quantidade_total_arquivos_referenciados = 0;
	}
	
	// Calculando complexidade e valor do componente (em pontos de função)
	var complexidade, valor, complexidade_formatada;
	if(quantidade_total_tipos_dados > 0 && quantidade_total_arquivos_referenciados > 0){
		complexidade = cpf.calcularComplexidade(tipo_funcional, quantidade_total_tipos_dados, quantidade_total_arquivos_referenciados);
		valor = cpf.calcularValor(tipo_funcional, complexidade);
		complexidade_formatada = cpf.formataNomeComplexidade(complexidade);
	} else {
		complexidade = valor = complexidade_formatada = '---';
	}
	
	// Exibindo informações na página
	$inputComplexidade.val(complexidade_formatada);
	$inputValor.val(valor);
}

function calcularComplexidadeEValorComponenteFuncionalidade(elemento){
	var $divComponenteFuncionalidade = $(elemento).closest('div.componente_funcionalidade');
	var $divComponentesFuncionalidade = $divComponenteFuncionalidade.siblings('div.componente_funcionalidade').addBack();
	var $selectTipoComponente = $divComponenteFuncionalidade.find("[name$='[tipo_componente]']");
	var $checkboxPossuiAcoes = $divComponenteFuncionalidade.find("[name$='[possui_acoes]']");
	var $checkboxPossuiMensagens = $divComponenteFuncionalidade.find("[name$='[possui_mensagens]']");
	var $radiobuttonModoPreenchimentoCampos = $divComponenteFuncionalidade.find("[name$='[modo_preenchimento_campos]']:checked");
	var $inputQuantidadeCampos = $divComponenteFuncionalidade.find("[name$='[quantidade_campos]']");
	var $selectNomesCampos = $divComponenteFuncionalidade.find("[name$='[nomes_campos][]']");
	var $radiobuttonModoPreenchimentoArquivosReferenciados = $divComponenteFuncionalidade.find("[name$='[modo_preenchimento_arquivos_referenciados]']:checked");
	var $inputQuantidadeArquivosReferenciados = $divComponenteFuncionalidade.find("[name$='[quantidade_arquivos_referenciados]']");
	var $selectNomesArquivosReferenciados = $divComponenteFuncionalidade.find("[name$='[nomes_arquivos_referenciados][]']");
	var $divComplexidade = $divComponenteFuncionalidade.find('div.complexidade');
	var $divValor = $divComponenteFuncionalidade.find('div.valor');
	var $bValorTotal = $('#valor_total');
	
	// Obtendo parâmetros adicionais
	var $opcaoSelecionada = $selectTipoComponente.find(':selected');
	var tipo_funcional = $opcaoSelecionada.attr('data-alias');
	var modo_preenchimento_campos = $radiobuttonModoPreenchimentoCampos.val();
	var modo_preenchimento_arquivos_referenciados = $radiobuttonModoPreenchimentoArquivosReferenciados.val();
	var quantidade_total_tipos_dados = 0, quantidade_total_arquivos_referenciados = 0;
	
	// Caso algum dos checkboxes "possui ações" ou "possui mensagens" estiver
	// marcado, incrementar em um a quantidade total de tipos de dados
	if($checkboxPossuiAcoes.is(':checked')) quantidade_total_tipos_dados++;
	if($checkboxPossuiMensagens.is(':checked')) quantidade_total_tipos_dados++;
	
	// Contando a quantidade de campos, em função do modo de preenchimento
	if(modo_preenchimento_campos == 'q'){
		// Modo de preenchimento "Quantidade"
		var quantidade_campos = parseInt($inputQuantidadeCampos.val(), 10);
		if(!isNaN(quantidade_campos) && quantidade_campos > 0){
			// Quantidade válida, logo somá-la à variável da quantidade total
			// de tipos de dados.
			quantidade_total_tipos_dados += quantidade_campos;
		} else {
			// Quantidade inválida, logo zerar a variável da
			// quantidade total de tipos de dados
			quantidade_total_tipos_dados = 0;
		}
	} else {
		// Modo de preenchimento "Nomes", logo obter o total de nomes adicionados,
		// com base no componente "Bootstrap Tags Input"
		var nomes_campos = $selectNomesCampos.tagsinput('items');
		if(nomes_campos.length > 0){
			// Pelo menos um nome foi digitado, logo somar o total de nomes
			// à variável da quantidade total de tipos de dados.
			quantidade_total_tipos_dados += nomes_campos.length;
		} else {
			// Nenhum nome digitado, logo zerar a variável da
			// quantidade total de tipos de dados
			quantidade_total_tipos_dados = 0;
		}
	}
	
	// Contando a quantidade de arquivos referenciados, em função do
	// modo de preenchimento
	if(modo_preenchimento_arquivos_referenciados == 'q'){
		// Modo de preenchimento "Quantidade"
		var quantidade_arquivos_referenciados = parseInt($inputQuantidadeArquivosReferenciados.val(), 10);
		if(!isNaN(quantidade_arquivos_referenciados) && quantidade_arquivos_referenciados > 0){
			// Quantidade válida, logo somá-la à variável da quantidade total
			// de arquivos referenciados.
			quantidade_total_arquivos_referenciados += quantidade_arquivos_referenciados;
		} else {
			// Quantidade inválida, logo zerar a variável da
			// quantidade total de arquivos referenciados
			quantidade_total_arquivos_referenciados = 0;
		}
	} else {
		// Modo de preenchimento "Nomes", logo obter o total de nomes adicionados,
		// com base no componente "Bootstrap Tags Input"
		var nomes_arquivos_referenciados = $selectNomesArquivosReferenciados.tagsinput('items');
		if(nomes_arquivos_referenciados.length > 0){
			// Pelo menos um nome foi digitado, logo somar o total de nomes
			// à variável da quantidade total de tipos de dados.
			quantidade_total_arquivos_referenciados += nomes_arquivos_referenciados.length;
		} else {
			// Nenhum nome digitado, logo zerar a variável da
			// quantidade total de tipos de dados
			quantidade_total_arquivos_referenciados = 0;
		}
	}
	
	// Calculando complexidade e valor do componente (em pontos de função)
	var complexidade, valor, complexidade_formatada;
	if(quantidade_total_tipos_dados > 0 && quantidade_total_arquivos_referenciados > 0){
		complexidade = cpf.calcularComplexidade(tipo_funcional, quantidade_total_tipos_dados, quantidade_total_arquivos_referenciados);
		valor = cpf.calcularValor(tipo_funcional, complexidade);
		complexidade_formatada = cpf.formataNomeComplexidade(complexidade);
	} else {
		complexidade = valor = complexidade_formatada = '---';
	}
	
	// Exibindo informações na página
	$divComplexidade.html(complexidade_formatada);
	$divValor.html(valor);
	
	// Contabilizando total de pontos de função, no rodapé.
	var valor_total = 0;
	$divComponentesFuncionalidade.each(function(){
		var $divComponenteFuncionalidade = $(this);
		var $divValor = $divComponenteFuncionalidade.find('div.valor');
		
		var valor = parseInt($divValor.html(), 10);
		if(isNaN(valor)) valor = 0;
		
		valor_total += valor;
	});
	$bValorTotal.html(valor_total);
}