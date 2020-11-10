// Variáveis de escopo global
var navegador_IE = eval('/*@cc_on !@*/false');
var versao_jscript = (navegador_IE) ? eval("/*@cc_on @_jscript_version @*/") : (0);
var versao_IE = (navegador_IE) ? (getVersaoIE()) : (0);

// Adicionando funções como membro do protótipo do objeto String
String.prototype.strtr = function (replacePairs) {
    var str = this.toString(), key, re;
    for (key in replacePairs) {
        if (replacePairs.hasOwnProperty(key)) {
            if (key == '*') {
                re = /\*/g;
            } else if (key == '.') {
                re = /\./g;
            } else {
                re = new RegExp(key, 'g');
            }
            str = str.replace(re, replacePairs[key]);
        }
    }
    return str;
};

//Funções JavaScript
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

function gerarIdAleatorio(el) {
    var nome, numero, id;
    do {
        nome = ($(el).attr('name') === undefined) ? ('sem_nome') : $(el).attr('name');
        numero = parseInt(Math.random() * 1000, 10);
        id = (nome + numero).replace(/\[/g, "").replace(/\]/g, "");
    } while ($('#' + id).length > 0);
    return id;
}

function isInt(n){
	return Number(n) === n && n % 1 === 0;
}

function isFloat(n){
	return Number(n) === n && n % 1 !== 0;
}

function round(value, decimals) {
	if(typeof decimals == 'undefined') decimals = 2;
	return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function fmod(a, b){
	return Number((a - (Math.floor(a / b) * b)).toPrecision(8));
}

/* Função que retorna o dispositivo utilizado pelo usuário, para acessar o sistema
 * Valores possíveis de retorno:
 *	- xs: Extra small (Celulares, com largura de tela menor que 576px);
 *	- sm: Small (Tablets, com largura de tela entre 576px e 767px);
 *	- md: Medium (Desktops de monitor antigo, com largura entre 768px e 991px);
 *	- lg: Large (Desktops de monitor widescreen, com largura entre 992px e 1199px).
 *	- xl: Extra-Large (Desktops de monitor widescreen, com largura maior ou igual a 1200px).
 * */
function getDispositivo() {
	return breakpoint;
}

function normalize(texto) {
    var table_caracteres_especiais = {
        'Š': 'S', 'š': 's', 'Đ': 'Dj', 'đ': 'dj', 'Ž': 'Z', 'ž': 'z', 'Č': 'C', 'č': 'c', 'Ć': 'C', 'ć': 'c',
        'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'A', 'Ç': 'C', 'È': 'E', 'É': 'E',
        'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O',
        'Õ': 'O', 'Ö': 'O', 'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ý': 'Y', 'Þ': 'B', 'ß': 'Ss',
        'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'a', 'ç': 'c', 'è': 'e', 'é': 'e',
        'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i', 'ð': 'o', 'ñ': 'n', 'ò': 'o', 'ó': 'o',
        'ô': 'o', 'õ': 'o', 'ö': 'o', 'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ý': 'y', 'ý':'y', 'þ': 'b',
        'ÿ': 'y', 'Ŕ': 'R', 'ŕ': 'r', '*': '', ';': '', '.': '', '\'': '', '´': '', '_': ' '
    };
    return texto.strtr(table_caracteres_especiais);
}

function instanciarComponentes(campo, escopo){
	campoMultiplo.instanciar(campo, escopo);
	tabela.instanciar(campo, escopo);
	select.instanciar(campo, escopo);
	calendario.instanciar(campo, escopo);
	timepicker.instanciar(campo, escopo);
	fileUploader.instanciar(campo, escopo);
	mascara.instanciar(campo, escopo);
	instanciarComponenteBootstrapTagsinput(campo, escopo);
	instanciarComponenteBootstrapSlider(campo, escopo);
	aba.instanciar(campo, escopo);
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
		var checkCampoNumerico = ($campo.is("input[type='number']"));
		var checkCampoObrigatorio = ((($campo.is('[required]')) || ($campo.is('[data-required]'))) && (!$campo.is("[data-desativar-validacao='true']")) );
		
		if (!gE($campo.attr("id"))) {
			$campo.attr('id', gerarIdAleatorio(this));
		}
		
		if(checkCampoObrigatorio){
			if ($campo.is("input[type='number']")) {
				if($.trim( $campo.val() ) == ''){
					camposAlvo[i] = {
						'id': $campo.attr("id"),
						'mensagem': 'Este campo é requerido.'
					};
					i++;
					status = false;
				}
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
		if(checkCampoNumerico){
			var valor = parseFloat($campo.val());
			var minimo = parseFloat($campo.attr('min'));
			var maximo = parseFloat($campo.attr('max'));
			if(isNaN(minimo)) minimo = valor;
			if(isNaN(maximo)) maximo = valor;
			
			var checkValorInvalido = (true && (isNaN(valor)));
			var checkValorMenorQueMinimo = (($campo.is("[min]")) && (valor < minimo));
			var checkValorMaiorQueMaximo = (($campo.is("[max]")) && (valor > maximo));

			if(checkValorInvalido){
				camposAlvo[i] = {
					'id': $campo.attr("id"),
					'mensagem': 'O valor digitado é inválido.'
				};
				i++;
				status = false;
			} else if(checkValorMenorQueMinimo || checkValorMaiorQueMaximo){
				if(isFloat(minimo)) minimo = minimo.toString().replace('.', ',');
				if(isFloat(maximo)) maximo = maximo.toString().replace('.', ',');
				var mensagem;
				if($campo.is("[min]") && $campo.is("[max]")){
					mensagem = 'O valor deste campo deve estar entre ' + minimo + ' e ' + maximo + '.';
				} else if(checkValorMenorQueMinimo){
					mensagem = 'O valor deste campo não pode ser menor que ' + minimo + '.';
				} else {
					mensagem = 'O valor deste campo não pode ser maior que ' + maximo + '.';
				}

				camposAlvo[i] = {
					'id': $campo.attr("id"),
					'mensagem': mensagem
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
	var elementos_adicionados = [];
	var setouFoco = false;
	for (var a = 0; a < camposAlvo.length; a++) {
		var alvo = camposAlvo[a];
		var mensagem = alvo['mensagem'];
		var posicao = alvo['posicao'];
		var id_alvo = alvo.id;
		
		if($.inArray(id_alvo, elementos_adicionados) === -1){
			elementos_adicionados.push(id_alvo);
		
			var $elemento = $('#' + id_alvo);
			$elemento.siblings('div.invalid-feedback').remove();

			if($elemento.is("li.nav-item")){
				// Abas
				aviso($elemento, mensagem, 8, posicao);
			} else if($elemento.is("[data-role='tagsinput']")){
				// Bootstrap Tagsinput
				$elemento.siblings('div.bootstrap-tagsinput').after(
					$('<div />').addClass('invalid-feedback').html(mensagem)
				);
			} else if($elemento.parent().hasClass('input-group')){
				// Campos agrupados do Bootstrap
				$elemento.parent().append(
					$('<div />').addClass('invalid-feedback').html(mensagem)
				);
			} else {
				$elemento.after(
					$('<div />').addClass('invalid-feedback').html(mensagem)
				);
			}

			if(!setouFoco && !$elemento.is("[li]")){
				$elemento.focus();
				setouFoco = true;
			}
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
			$modalRetorno = jError('Sessão expirada', undefined, function(){
				abrirPagina('logoff.php?sessao_expirada=true');
			});
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

function resetaForm(form){
	setTimeout(function(){
		var $form = $(form);

		$form.find('select.select, div.campo_multiplo, input[type="file"].fileuploader').each(function(){
			var $campo = $(this);
			
			if($campo.is('select.select')){
				// Select2
				select.atualizar($campo);
			} else if($campo.is('div.campo_multiplo')){
				// Campos múltiplos
				campoMultiplo.limpar($campo.attr('id'));
			} else {
				// FileUploader
				fileUploader.limpar($campo);
			}
		});
	}, 25);
}

function limparCampos(id_conteiner) {
    var busca;
    if ((typeof id_conteiner != 'undefined') && $('#' + id_conteiner).length > 0) {
        busca = $('#' + id_conteiner);
    } else {
        busca = $('body').find("form");
    }
    busca.find("input:not([hidden]), textarea, select").not("[data-desativar-limpar='true']").each(function () {
        var $campo = $(this);

        if (($campo.filter("input").filter("[type='text'], [type='password'], [type='url'], [type='number']").filter(":not([readonly])")).length > 0) {
            // Campos de texto
            $campo.val('');
        } else if ($campo.is("textarea")) {
            // Campos textarea
            $campo.val('');
        } else if ($campo.is("input:checkbox")) {
            // Checkboxes
            $campo.prop("checked", false);
        } else if ($campo.is("select:not([disabled])")) {
            // Campos Select (Combobox)
            if ($campo.hasClass("select")) {
                // Campos <select> gerados pelo componente Select 2
                select.limpar($campo);
            } else {
                // Campos <select> comuns
                var valor_primeira_opcao = $campo.find('option').first().val();
                $campo.val(valor_primeira_opcao);
            }
        } else if ($campo.is("input[type='file']")) {
            // Campos de arquivo
            if ($campo.hasClass("fileuploader")) {
                // Campos de arquivo gerados pelo componente FileUploader
                fileUploader.limpar($campo);
            } else {
                // Campos de arquivo comuns
                try {
                    $campo.val('')
                } catch (e) {
					
                };
            }
        }
    });
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

function validaFormAbas(form, mostra_modal, mostra_aviso){
	mostra_modal = (typeof mostra_modal !== 'undefined') ? (mostra_modal) : (true);
	mostra_aviso = (typeof mostra_aviso !== 'undefined') ? (mostra_aviso) : (true);
	var $form = $(form);
	var $listasAbas = $form.find('ul.nav-pills');
	var retorno = true;
	var camposAbas = camposAlvoAbaVisivel = [];
	$listasAbas.each(function(){
		var $listaAbas = $(this);
		var $lis = $listaAbas.children('li');
		var $divs = $listaAbas.siblings('div.tab-content').children('div.tab-pane');
		
		$divs.each(function(i){
			var $div = $(this);
			var $li = $lis.eq(i);
			var $a = $li.children('a');
			if(!$li.is("[id]") || $.trim( $li.attr('id') ) == ''){
				$li.attr('id', gerarIdAleatorio($li));
			}
			
			if(!validaElementos($div)){
				retorno = false;
				if($a.hasClass('active')){
					$.merge(camposAlvoAbaVisivel, camposAlvo);
				} else {
					camposAbas.push({
						'id': $li.attr('id'),
						'mensagem': 'Há campos em branco /<br />inválidos nesta aba!',
						'posicao': 't'
					});
				}
			}
		})
	});
	
	if(retorno){
		if(mostra_aviso) $form.removeClass('was-validated');
		if(mostra_modal) mostraCarregando();
		return true;
	} else {
		if(mostra_aviso){
			$form.addClass('was-validated');
			$.merge(camposAlvoAbaVisivel, camposAbas);
			mostrarAvisosValidaForm(camposAlvoAbaVisivel);
		}
		return false;
	}
}

function confirma(pagina, parametros, mensagem, ajax, callback_sucesso, callback_erro, tem_modal, tem_animacao) {
    if (!mensagem)
        mensagem = 'Deseja realizar esta operação?';
    ajax = (typeof ajax != 'undefined' && (ajax == 'true' || ajax === true));
    return jConfirmSimNao(mensagem, 'Confirmação', function (valor) {
        if (valor) {
            if (ajax) {
                mostraCarregando(tem_modal, tem_animacao);
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

function trocaColspanTotal(seletor_celulas, escopo) {
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_celulas){
		busca = $(escopo).find(seletor_celulas);
	} else {
		busca = $(escopo).find("th, td").filter("[colspan='100%']");
	}
	
    busca.each(function () {
		var $thd = $(this);
        var maximo = 0;
        $thd.closest("table").children("thead, tbody, tfoot").children("tr").each(function () {
            var $tr = $(this);
            var total = $tr.children("td, th").length;
            if (total > maximo) {
                maximo = total;
            }
        });
        if (maximo > 0) {
            $thd.attr("colspan", maximo);
        }
    });
}

function salvarPersistenciaMenuMinimizado(){
	if(getDispositivo() != 'xs'){
		setTimeout(function(){
			var $body = $('body');
			var opcao_menu_minimizado;
			if($body.hasClass('sidebar-collapse')){
				opcao_menu_minimizado = 'true';
			} else {
				opcao_menu_minimizado = 'false';
			}
			chamarPagina('sessao_crud.php', 'acao=set_menu_minimizado&menu_minimizado=' + opcao_menu_minimizado, function(){
				// Atualizando variável global de menu minimizado
				menu_minimizado = opcao_menu_minimizado;
			});
		}, 25);
	}
}

function salvarSistemaModuloFuncionalidadeSessao(){
	setTimeout(function(){
		var $selectSistemaSessao = $('#sistema_sessao');
		var $selectModuloSessao = $('#modulo_sessao');
		var $selectFuncionalidadeSessao = $('#funcionalidade_sessao');

		var id_sistema_sessao = $selectSistemaSessao.val();
		var id_modulo_sessao = $selectModuloSessao.val();
		var id_funcionalidade_sessao = $selectFuncionalidadeSessao.val();

		var parametros = 'sistema=' + id_sistema_sessao;
		parametros += '&modulo=' + id_modulo_sessao;
		parametros += '&funcionalidade=' + id_funcionalidade_sessao;
		parametros += '&acao=set_sistema_modulo_funcionalidade_sessao';
		chamarPagina('sessao_crud.php', parametros);
	}, 25);
}

function validaFormAlterarSenha(form){
	return validaForm(form, undefined, undefined, undefined, function(){
		jInfo('Senha redefinida com sucesso!<br />Realize o login com a nova senha!', undefined, function(){
			location.href='logoff.php';
		})
	});
}

// Se for ambiente de desenvolvimento ou homologação, exibir consultas salvas na sessão
// Útil para depuração
function obterConsultasSessao(callback) {
    if ((typeof ambiente != 'undefined') && (ambiente == 'D' || ambiente == 'H')) {
        chamarPagina('../common/obterConsultasSessao.php', '', function (r) {
            if (callback)
                callback(r);
        });
    }
}

function mostrarConsultasSessao() {
    obterConsultasSessao(function (r) {
        jInfo(r);
    });
}

function encodeMonetario(n, c, d, t, casas_decimais){
	if(typeof casas_decimais == 'undefined') casas_decimais = 2;
    c = isNaN(c = Math.abs(c)) ? casas_decimais : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(casas_decimais) : "");
}

function decodeMonetario(valor){
	valor = valor.replace('R$', '');
	valor = valor.replace('.', '');
	valor = valor.replace(',', '.');
	valor = $.trim(valor);
	
	return parseFloat(valor);
}

function encodeFloatToTime(float, arredondarSegundos){
	if(typeof arredondarSegundos == 'undefined') arredondarSegundos = false;
	var fraction;
	if(arredondarSegundos){
		fraction = Math.round(fmod(float, 1) * 60);
	} else {
		fraction = fmod(float, 1) * 60;
	}
	
	var hora = parseInt(float, 10);
	var minutos = parseInt(fraction, 10);
	
	if(hora < 10) hora = '0' + hora;
	if(minutos < 10) minutos = '0' + minutos;

	return hora + ':' + minutos;
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
    var item_selecionado = $campo.select2('data')[0];
	if(typeof id_campo_modulo == 'undefined') id_campo_modulo = 'modulo';
	if(typeof id_campo_sistema == 'undefined') id_campo_sistema = 'sistema';

    var $campoModulo = $('#' + id_campo_modulo);
    var $campoSistema = $('#' + id_campo_sistema);

    var id_modulo = item_selecionado['id_modulo'];
    var id_sistema = item_selecionado['id_sistema'];

    var nome_modulo = item_selecionado['nome_modulo'];
    var nome_sistema = item_selecionado['nome_sistema'];

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

function reduzirTamanhoTabelaNoMobile(tabela){
	if(getDispositivo() == 'xs') $(tabela).addClass('table-sm');
}

function cadastrarNovaFuncionalidade(){
	var $divFiltros = $('#filtros');
	var $selectSistema = $divFiltros.find("select[name='sistema_lista']");
	var $selectModulo = $divFiltros.find("select[name='modulo_lista']");
	
	var id_sistema = $selectSistema.val();
	var id_modulo = $selectModulo.val();
	
	var parametros = '';
	if(id_sistema != '') parametros += '&sistema=' + id_sistema;
	if(id_modulo != '') parametros += '&modulo=' + id_modulo;
	return jFormGrande('funcionalidade_form.php?' + parametros);
}

function definirProximaOrdemFuncionalidade(selectModulo){
	var $selectModulo = $(selectModulo);
	var $inputOrdem = $('#ordem');
	
    var item_selecionado = $selectModulo.select2('data')[0];
	var proxima_ordem = item_selecionado['proxima_ordem'];
	
	$inputOrdem.val(proxima_ordem);
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
		
		if(getDispositivo() == 'xs') $divRowComponentes.find('div.cabecalho_componente_funcionalidade').hide();
	});
}

function concatenarLabelOptgroupParaTemplateSelection(select, prepend){
	if(typeof prepend == 'undefined') prepend = false;
	var $select = $(select);
	
	$select.trigger('estiliza');
	
	var $opcaoSelecionada = $select.find(':selected');
	var $optgroupOpcaoSelecionada = $opcaoSelecionada.closest('optgroup');
	var $spanSelect2Container = $select.next();
	var $spanSelection = $spanSelect2Container.find('span.select2-selection__rendered');
	
	var label_optgroup = $optgroupOpcaoSelecionada.attr('label');
	var spanSelectionTitle = $spanSelection.attr('title');
	//var spanSelectionText = $spanSelection.html();
	
	var titulo_concatenado;
	if(typeof label_optgroup != 'undefined'){
		if(prepend){
			titulo_concatenado = label_optgroup + ' - ' + spanSelectionTitle;
		} else {
			titulo_concatenado = spanSelectionTitle + ' - ' + label_optgroup;
		}
	} else {
		titulo_concatenado = spanSelectionTitle;
	}
	
	$spanSelection.attr('title', titulo_concatenado);
	$spanSelection.html(titulo_concatenado);
}

function toggleQuantidadeOuNomeCamposArquivosComponente(radio){
	var $radio = $(radio);
	var $divCol = $radio.closest("div[class^='col']");
	var $inputQuantidadeCamposArquivos = $divCol.find("input[name$='[quantidade_campos]'], input[name$='[quantidade_arquivos_referenciados]']");
	var $selectNomesCamposArquivos = $divCol.find("select[name$='[nomes_campos][]'], select[name$='[nomes_arquivos_referenciados][]']");
	var $divTagsinputNomesCamposArquivos = $selectNomesCamposArquivos.next();
	
	var modo_preenchimento = $radio.val();
	if(modo_preenchimento == 'q'){
		$inputQuantidadeCamposArquivos.show();
		$divTagsinputNomesCamposArquivos.hide();
	} else {
		$inputQuantidadeCamposArquivos.hide();
		
		if($selectNomesCamposArquivos.is("[data-instanciado='true']")){
			$divTagsinputNomesCamposArquivos.show();
		} else {
			$selectNomesCamposArquivos.removeClass('d-none');
			
			instanciarComponenteBootstrapTagsinput($selectNomesCamposArquivos);
		}
	}
}

function instanciarComponenteBootstrapSlider(campo, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(campo){
		busca = $(escopo).find(campo).filter(':visible').not("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("input.slider").filter(':visible').not("[data-instanciado='true']");
	}
	busca.each(function(){
		var $input = $(this);
		
		var min, max, step;
		
		if($input.is('[data-min]')){
			min = parseFloat($input.attr('data-min'));
		} else {
			min = 0;
		}
		
		if($input.is('[data-max]')){
			max = parseFloat($input.attr('data-max'));
		} else {
			max = 10;
		}
		
		if($input.is('[data-step]')){
			step = parseFloat($input.attr('data-step'));
		} else {
			step = 1;
		}
		
		var value = parseFloat($input.val());
		if(isNaN(value)) value = min;
		
		$input.attr('data-slider-id', 'blue');
		
		$input.slider({
			min: min,
			max: max,
			step: step,
			value: value,
			tooltip: 'hide'
		});
		
		$input.attr('data-instanciado', 'true');
	});
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

function cadastrarNovoComponente(){
	var $divFiltros = $('#filtros');
	var $selectSistema = $divFiltros.find("select[name='sistema_lista']");
	var $selectModulo = $divFiltros.find("select[name='modulo_lista']");
	var $selectFuncionalidade = $divFiltros.find("select[name='funcionalidade_lista']");
	
	var id_sistema = $selectSistema.val();
	var id_modulo = $selectModulo.val();
	var id_funcionalidade = $selectFuncionalidade.val();
	
	var parametros = '';
	if(id_sistema != '') parametros += '&sistema=' + id_sistema;
	if(id_modulo != '') parametros += '&modulo=' + id_modulo;
	if(id_funcionalidade != '') parametros += '&funcionalidade=' + id_funcionalidade;
	return jFormGrande('componente_form.php?' + parametros);
}

function definirProximaOrdemComponente(selectFuncionalidade){
	var $selectFuncionalidade = $(selectFuncionalidade);
	var $inputOrdem = $('#ordem');
	
    var item_selecionado = $selectFuncionalidade.select2('data')[0];
	var proxima_ordem = item_selecionado['proxima_ordem'];
	
	$inputOrdem.val(proxima_ordem);
}

function calcularComplexidadeEValorComponente(elemento){
	var $divCardBody = $(elemento).closest('div.card-body');
	var $selectTipoComponente = $divCardBody.find("[name='tipo_componente']");
	var $checkboxPossuiAcoes = $divCardBody.find("[name='possui_acoes']");
	var $checkboxPossuiMensagens = $divCardBody.find("[name='possui_mensagens']");
	var $inputsCampos = $divCardBody.find("[name^='campos']").not('[disabled]');
	var $inputsArquivosReferenciados = $divCardBody.find("[name^='arquivos_referenciados']").not('[disabled]');
	var $inputComplexidade = $divCardBody.find("[name='complexidade']");
	var $inputValor = $divCardBody.find("[name='valor_pf']");
	
	// Obtendo parâmetros adicionais
	var $opcaoSelecionada = $selectTipoComponente.find(':selected');
	var tipo_funcional = $opcaoSelecionada.attr('data-alias');
	var quantidade_total_tipos_dados = 0, quantidade_total_arquivos_referenciados = 0;
	
	// Obtendo total de campos, contabilizando apenas campos de texto não-vazios
	$inputsCampos.each(function(){
		var $input = $(this);
		
		var texto = $.trim( $input.val() );
		
		if(texto != '') quantidade_total_tipos_dados++;
	});
	
	// Obtendo total de arquivos referenciados, contabilizando apenas
	// campos de texto não-vazios
	$inputsArquivosReferenciados.each(function(){
		var $input = $(this);
		
		var texto = $.trim( $input.val() );
		
		if(texto != '') quantidade_total_arquivos_referenciados++;
	});
	
	// Caso algum dos checkboxes "possui ações" ou "possui mensagens" estiver
	// marcado, incrementar em um a quantidade total de tipos de dados
	if($checkboxPossuiAcoes.is(':checked')) quantidade_total_tipos_dados++;
	if($checkboxPossuiMensagens.is(':checked')) quantidade_total_tipos_dados++;
	
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
	var $spanComplexidade = $divComponenteFuncionalidade.find('span.complexidade');
	var $spanValor = $divComponenteFuncionalidade.find('span.valor');
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
	$spanComplexidade.html(complexidade_formatada);
	$spanValor.html(valor);
	
	// Contabilizando total de pontos de função, no rodapé.
	var valor_total = 0;
	$divComponentesFuncionalidade.each(function(){
		var $divComponenteFuncionalidade = $(this);
		var $spanValor = $divComponenteFuncionalidade.find('span.valor');
		
		var valor = parseInt($spanValor.html(), 10);
		if(isNaN(valor)) valor = 0;
		
		valor_total += valor;
	});
	$bValorTotal.html(valor_total);
}

function toggleCamposEstimativaPrazo(selectMetodoEstimativaPrazo){
	var $selectMetodoEstimativaPrazo = $(selectMetodoEstimativaPrazo);
	var $inputRecursos = $('#recursos_lista');
	var $inputTempoDedicacao = $('#tempo_dedicacao_lista');
	var $inputIndiceProdutividade = $('#indice_produtividade_lista');
	var $selectTipoSistema = $('#tipo_sistema_lista');
	var $inputExpoenteCapersJones = $('#expoente_capers_jones_lista');
	var $divColRecursos = $inputRecursos.closest("div[class^='col']");
	var $divColTempoDedicacao = $inputTempoDedicacao.closest("div[class^='col']");
	var $divColIndiceProdutividade = $inputIndiceProdutividade.closest("div[class^='col']");
	var $divColTipoSistema = $selectTipoSistema.closest("div[class^='col']");
	var $divColExpoenteCapersJones = $inputExpoenteCapersJones.closest("div[class^='col']");
	
	var metodo_estimativa_prazo = $selectMetodoEstimativaPrazo.val();
	
	if(metodo_estimativa_prazo == 'e'){
		// Estimativa de Esforço
		$inputRecursos.add($inputTempoDedicacao).add($inputIndiceProdutividade).attr('required', 'required');
		$selectTipoSistema.add($inputExpoenteCapersJones).removeAttr('required');
		$divColRecursos.add($divColTempoDedicacao).add($divColIndiceProdutividade).show();
		$divColTipoSistema.add($divColExpoenteCapersJones).hide();
	} else {
		// Fórmula de Capers Jones
		$inputRecursos.add($inputTempoDedicacao).add($inputIndiceProdutividade).removeAttr('required');
		$selectTipoSistema.add($inputExpoenteCapersJones).attr('required', 'required');
		$divColRecursos.add($divColTempoDedicacao).add($divColIndiceProdutividade).hide();
		$divColTipoSistema.add($divColExpoenteCapersJones).show();
		
		select.instanciar($selectTipoSistema);
		select.limpar($selectTipoSistema);
	}
}

function toggleCampoExpoenteCapersJones(selectTipoSistema){
	var $selectTipoSistema = $(selectTipoSistema);
	var $inputExpoenteCapersJones = $('#expoente_capers_jones_lista');
	
	var tipo_sistema = $selectTipoSistema.val();
	if(tipo_sistema != ''){
		var item_selecionado = $selectTipoSistema.select2('data')[0];
		var expoente_minimo = item_selecionado['expoente_minimo'];
		var expoente_maximo = item_selecionado['expoente_maximo'];
		
		if(expoente_minimo == expoente_maximo){
			$inputExpoenteCapersJones.removeAttr('disabled min max').val(expoente_minimo).attr('readonly', 'readonly');
		} else {
			$inputExpoenteCapersJones.removeAttr('disabled readonly').val(expoente_minimo).attr({
				'min': expoente_minimo,
				'max': expoente_maximo,
				'placeholder': 'Digite um valor entre ' + expoente_minimo.replace('.', ',') + ' e ' + expoente_maximo.replace('.', ',')
			});
		}
	} else {
		$inputExpoenteCapersJones.removeAttr('readonly').val('').attr({
			'min': '0.36',
			'max': '0.45',
			'placeholder': 'Digite um valor entre 0,36 e 0,45',
			'disabled': 'disabled'
		});
	}
}

function toggleCheckboxArredondarZeros(selectFormatoTempo){
	var $selectFormatoTempo = $(selectFormatoTempo);
	var $checkboxArredondarZeros = $('#arredondar_zeros');
	
	var formato_tempo = $selectFormatoTempo.val();
	
	if(formato_tempo == 'hni' || formato_tempo == 'dni'){
		$checkboxArredondarZeros.removeAttr('disabled');
	} else {
		$checkboxArredondarZeros.prop('checked', false).attr('disabled', 'disabled');
	}
}

function toggleCamposModoExibicao(selectModoExibicao){
	var $selectModoExibicao = $(selectModoExibicao);
	var $divModoExibicaoTempoUnico = $('#modo_exibicao_tempo_unico');
	var $divModoExibicaoTemposDivididos = $('#modo_exibicao_tempos_divididos');
	
	var modo_exibicao = $selectModoExibicao.val();
	
	if(modo_exibicao == 'u'){
		$divModoExibicaoTempoUnico.show();
		$divModoExibicaoTemposDivididos.hide();
	} else {
		$divModoExibicaoTempoUnico.hide();
		$divModoExibicaoTemposDivididos.show();
		
		instanciarComponenteBootstrapSlider(null, $divModoExibicaoTemposDivididos);
	}
}

function toggleSliderEsforcoDisciplina(checkboxAtivar){
	var $checkboxAtivar = $(checkboxAtivar);
	var $divGridTableAtual = $checkboxAtivar.closest('div.grid-table');
	var $inputPercentualEsforco = $divGridTableAtual.find('input.slider');
	
	if($checkboxAtivar.is(':checked')){
		$inputPercentualEsforco.slider('enable');
	} else {
		$inputPercentualEsforco.slider('setValue', 0, false, true);
		$inputPercentualEsforco.slider('disable');
	}
}

function balancearPercentuaisEsforcoDisciplinas(inputAtual, evento){
	var $inputAtual = $(inputAtual);
	var $divPercentuaisDisciplinas = $('#modo_exibicao_tempos_divididos');
	var $divGridTableAtual = $inputAtual.closest('div.grid-table');
	var $spanRotuloPercentualAtual = $divGridTableAtual.find('span.badge');
	var $inputsPercentuaisEsforco = $divPercentuaisDisciplinas.find('div.slider').not('.slider-disabled').next();
	
	var total_disciplinas = $inputsPercentuaisEsforco.length;
	var percentual_anterior = evento.value.oldValue;
	var percentual_atual = evento.value.newValue;
	var percentual_dividir = (percentual_atual - percentual_anterior) / (total_disciplinas - 1);
	
	$spanRotuloPercentualAtual.html(Math.round(percentual_atual) + '%');
	
	$inputsPercentuaisEsforco.not($inputAtual).each(function(){
		var $input = $(this);
		var $divGridTable = $input.closest('div.grid-table');
		var $spanRotuloPercentual = $divGridTable.find('span.badge');

		var percentual = $input.slider('getValue');
		if(percentual_dividir > 0){
			percentual -= Math.abs(percentual_dividir);
		} else {
			percentual += Math.abs(percentual_dividir);
		}
		if(percentual < 0){
			var $inputPercentualMaior = $inputsPercentuaisEsforco.not($inputAtual).sort(function(a, b){
				return b.value - a.value;
			}).first();
			var $divGridTablePercentualMaior = $inputPercentualMaior.closest('div.grid-table');
			var $spanRotuloPercentualMaior = $divGridTablePercentualMaior.find('span.badge');
			
			var percentual_maior = $inputPercentualMaior.slider('getValue') - Math.abs(percentual);
			if(percentual_maior < 0) percentual_maior = 0;
			
			$inputPercentualMaior.slider('setValue', percentual_maior);
			$spanRotuloPercentualMaior.html(Math.round(percentual_maior) + '%');
			
			percentual = 0;
		}

		$input.slider('setValue', percentual);
		$spanRotuloPercentual.html(Math.round(percentual) + '%');
	});
}

function validaFormPrazosDesenvolvimento(form){
	if(validaFormAbas(form, false)){
		var $form = $(form);
		var $divTabelaPrazosDesenvolvimento = $('#conteiner_tabela_prazos_desenvolvimento');
		
		mostraCarregando(true, false);
		
		var parametros_formulario = $form.serialize();
		var parametros_historico = parametros_formulario;
		parametros_formulario += '&ajax=true';
		parametros_historico += '&Submit=true';
		chamarPagina('rel_prazos_desenvolvimento_tabela.php?' + parametros_formulario, '', function(r){
			ocultaCarregando();
			$divTabelaPrazosDesenvolvimento.html(r);
			history.pushState(parametros_historico, '', '?' + parametros_historico);
		});
	}
	
	return false;
}

function toggleCamposValorOrcamento(selectMetodoCalculoOrcamento){
	var $selectMetodoCalculoOrcamento = $(selectMetodoCalculoOrcamento);
	var $inputValorPontoFuncao = $('#valor_ponto_funcao_lista');
	var $inputValorHoraTrabalhada = $('#valor_hora_trabalhada_lista');
	var $divColValorPontoFuncao = $inputValorPontoFuncao.closest("div[class^='col-']");
	var $divColValorHoraTrabalhada = $inputValorHoraTrabalhada.closest("div[class^='col-']");
	var $divColBotaoDetalhesCalculoVPF = $divColValorPontoFuncao.next();
	var $divColBotaoDetalhesCalculoVHT = $divColValorHoraTrabalhada.next();
	
	var metodo_calculo_orcamento = $selectMetodoCalculoOrcamento.val();
	
	if(metodo_calculo_orcamento == 'vpf'){
		// Valor do Ponto de Função
		$inputValorPontoFuncao.removeAttr('disabled');
		$inputValorHoraTrabalhada.attr('disabled', 'disabled');
		
		$divColValorPontoFuncao.add($divColBotaoDetalhesCalculoVPF).show();
		$divColValorHoraTrabalhada.add($divColBotaoDetalhesCalculoVHT).hide();
	} else if(metodo_calculo_orcamento == 'vht'){
		// Valor da Hora Trabalhada
		$inputValorPontoFuncao.attr('disabled', 'disabled');
		$inputValorHoraTrabalhada.removeAttr('disabled');
		
		$divColValorPontoFuncao.add($divColBotaoDetalhesCalculoVPF).hide();
		$divColValorHoraTrabalhada.add($divColBotaoDetalhesCalculoVHT).show();
	}
}

function validaFormOrcamentoDesenvolvimento(form){
	if(validaFormAbas(form, false)){
		var $form = $(form);
		var $divTabelaOrcamentoDesenvolvimento = $('#conteiner_tabela_orcamento_desenvolvimento');
		
		mostraCarregando(true, false);
		
		var parametros_formulario = $form.serialize();
		var parametros_historico = parametros_formulario;
		parametros_formulario += '&ajax=true';
		parametros_historico += '&Submit=true';
		chamarPagina('rel_orcamento_desenvolvimento_tabela.php?' + parametros_formulario, '', function(r){
			ocultaCarregando();
			$divTabelaOrcamentoDesenvolvimento.html(r);
			history.pushState(parametros_historico, '', '?' + parametros_historico);
		});
	}
	
	return false;
}

function validaFormCronogramaDesenvolvimento(form){
	if(validaFormAbas(form, false)){
		var $form = $(form);
		var $divTabelaCronogramaDesenvolvimento = $('#conteiner_tabela_cronograma_desenvolvimento');
		
		mostraCarregando(true, false);
		
		var parametros_formulario = $form.serialize();
		var parametros_historico = parametros_formulario;
		parametros_formulario += '&ajax=true';
		parametros_historico += '&Submit=true';
		chamarPagina('rel_cronograma_desenvolvimento_tabela.php?' + parametros_formulario, '', function(r){
			ocultaCarregando();
			$divTabelaCronogramaDesenvolvimento.html(r);
			history.pushState(parametros_historico, '', '?' + parametros_historico);
		});
	}
	
	return false;
}

function filtrarOpcoesCheckboxRadioMenu(input){
	var $input = $(input);
	var $divCheckboxMenuParent = $input.parent().next();
	
	setTimeout(function(){
		var busca = $input.val();
		
		if($.trim(busca) != ''){
			$divCheckboxMenuParent.children('label').each(function(){
				var $label = $(this);
				var $spanCheckboxLabelBlock = $label.find('span.checkbox-label-block');

				var texto = $spanCheckboxLabelBlock.html().toLowerCase();

				if(texto.indexOf(busca) !== -1){
					$label.show();
				} else {
					$label.hide();
				}
			});
		} else {
			$divCheckboxMenuParent.children('label').show();
		}
	}, 25);
}

function marcarCheckboxMenuNoEnterOuEspaco(campo, evento){
	var $campo = $(campo);
	
	// Se teclar ENTER ou Espaço, marcar radiobutton equivalente
	if(evento.which == 13 || evento.which == 32){
		var $checkbox = $campo.find("input[type='checkbox']");
		if($checkbox.is(':checked')){
			$checkbox.prop('checked', false);
		} else {
			$checkbox.prop('checked', true);
		}
		$checkbox.trigger('change');
	}
}

function cadastrarNovoFeriadoCustomizado(){
	var $divFiltros = $('#filtros');
	var $selectAno = $divFiltros.find("select[name='ano_lista']");
	
	var ano = $selectAno.val();
	
	var parametros = '';
	if(ano != '') parametros += '&ano=' + ano;
	return jForm('feriado_customizado_form.php?' + parametros);
}

function pesquisarTabelaFeriados(form){
	if(validaForm(form)){
		atualizarTabelaFeriados();
	}
	
	return false;
}

function atualizarTabelaFeriados(){
	var $formularioPesquisaFeriados = $('#form_lista');
	var $divFeriadoTabela = $('#feriado_tabela');
	
	var parametros = $formularioPesquisaFeriados.serialize();
	
	chamarPagina('feriado_tabela.php?' + parametros, '', function(r){
		$divFeriadoTabela.html(r);
		instanciarComponentes(null, $divFeriadoTabela);
		ocultaCarregando();
	});
}

function toggleCampoDataParaIntervalo(){
	var $checkboxIntervalo = $('#intervalo');
	var $campoData = $('#data');
	var $labelData = $("label[for='data']");
	var $divConteinerCalendario = $campoData.closest('div.conteiner_calendario');
	var $divFormGroup = $divConteinerCalendario.parent();
	
	var data = $campoData.val();
	
	$campoData.insertAfter($divConteinerCalendario);
	
	$divConteinerCalendario.remove();
	
	$campoData.removeAttr('placeholder maxlength data-instanciado data-intervalo data-rotulo-flutuante-inicial data-rotulo-flutuante-final value').val('').off('change keydown');
	
	if($checkboxIntervalo.is(':checked')){
		$campoData.attr({
			'data-intervalo': 'true',
			'data-rotulo-flutuante-inicial': 'Período Inicial:',
			'data-rotulo-flutuante-final': 'Período Final:',
			'data-valor-inicial': data,
			'data-valor-final': data
		});
		$divFormGroup.removeClass('has-float-label');
		$labelData.hide();
	} else {
		var nome = $campoData.attr('name');
		nome = nome.replace('[inicial]', '');
		$campoData.attr('name', nome).val(data);
		$divFormGroup.addClass('has-float-label');
		$labelData.show();
	}
	
	instanciarComponentes(null, $('#form_feriado'));
}

function validaFormFeriadoCustomizado(form){
	return validaForm(form, undefined, undefined, function(){
		mostraCarregando(true, false);
		atualizarTabelaFeriados();
	});
}

function apagarFeriadoCustomizado(id){
	var pagina = 'feriado_customizado_crud.php';
	var parametros = 'id=' + id + '&acao=excluir';
	var $modalConfirmacao = confirma(pagina, parametros, 'Deseja excluir este registro?', true, function(dados){
		dados = interpretarJSON(dados);
		if(dados.tipo_modal == '' || dados.tipo_modal == 'informacao'){
			exibirAvisoNotify(dados.msg_modal, 'info');
			atualizarTabelaFeriados();
		} else {
			ocultaCarregando();
			jError(dados.msg_modal);
		}
	}, function(){
		ocultaCarregando();
		jError('Erro ao excluir registro!');
	}, true, false);
	
	$modalConfirmacao.find('div.modal-header').removeClass('bg-primary').addClass('bg-danger');
	$modalConfirmacao.find('button.btn-ok').removeClass('btn-primary').addClass('btn-danger');
	
	return $modalConfirmacao;
}

function setTemaVisual(tema, event){
	var $body = $('body');
	var $a = $('#toggle_tema_escuro');
	var $i = $a.children('i');
	var $navbar = $a.closest('nav.main-header');
	
	localStorage.setItem('cpf.tema', tema);
	if(tema == 'escuro'){	
		$body.addClass('tema_escuro');
		$a.attr('title', 'Mudar para tema claro');
		$i.addClass('fas text-warning');
		$navbar.removeClass('bg-white navbar-light').addClass('navbar-dark');
	} else {
		$body.removeClass('tema_escuro');
		$a.attr('title', 'Mudar para tema escuro');
		$i.removeClass('text-warning');
		$navbar.removeClass('navbar-dark').addClass('bg-white navbar-light');
	}
	
	if(event) event.preventDefault();
}

function toggleTemaVisual(event){
	var tema = localStorage.getItem('cpf.tema');
	
	if(tema == 'escuro'){
		setTemaVisual('claro', event);
	} else {
		setTemaVisual('escuro', event);
	}
}
