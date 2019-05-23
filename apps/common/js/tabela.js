/* Plugin de tabelas de listagem de registros
 * 
 * Baseado no componente "jQuery Datatables" (https://www.datatables.net/),
 * em conjunto com estilizações nos padrões do Bootstrap.
 * 
 * Componentes Bootstrap utilizados em conjunto:
 * - Modal;
 * - Button Groups;
 * - Button Dropdowns
 * 
 * Dependências:
 * - dataTables.bootstrap.min.css
 * - responsive.bootstrap.min.css
 * - buttons.dataTables.min.css
 * - dataTables.bootstrap.min.js
 * - dataTables.fixedHeader.min.js
 * - dataTables.responsive.min.js
 * - dataTables.buttons.min.js
 * - buttons.print.min.js
 * - buttons.html5.min.js
 * - buttons.flash.min.js
 * - jszip.js
 * - moment.min.js
 * 
 * 
 * Funções adicionadas:
 *	tabela.instanciar( [ seletor_tabela ] , [ escopo ] )
 *	tabela.pesquisar( form , [ seletor_tabela ] )
 *	tabela.atualizar( [ seletor_tabela ] )
 *	tabela.removerLinhaById( id , [ seletor_tabela ] )
 *	tabela.imprimir( [ seletor_tabela ] )
 */

function tabela(){}

// Propriedades
tabela.parametros = [];

// Métodos
tabela.instanciar = function(seletor_tabela, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_tabela){
		busca = $(escopo).find(seletor_tabela).not("[data-instanciado='true']").not('.oculta');
	} else {
		busca = $(escopo).find("div.tabelaaberta").not("[data-instanciado='true']").not('.oculta');
	}
	var dispositivo = getDispositivo();
	busca.each(function(){
		var $conteiner_tabela = $(this);
		var $tabela = $conteiner_tabela.children('table');
		var temPaginaAjax, pagina, parametros_pagina, pagina_com_parametros, ordenacao, filtragem, limite_registros, temPaginacao;
		var relatorio, titulo_relatorio, responsividade, cabecalhoFixo, ordenarPorPadrao = true;
		var eventos = [];
		var temColunaAcoes = ( $tabela.children('thead').children('tr').children('th').last().hasClass('acoes') );
		
		// Obtenção da página ao qual o componente fará requisições Ajax, para obter registros
		if(($conteiner_tabela.is("[data-pagina]") && ($.trim( $conteiner_tabela.attr('data-pagina') ) != ''))){
			temPaginaAjax = true;
			pagina = $conteiner_tabela.attr('data-pagina');
		} else {
			temPaginaAjax = false;
			pagina = '';
		}
		// Obtenção dos parâmetros que o componente enviará na requisição, via método GET
		if((temPaginaAjax) && ($conteiner_tabela.is("[data-parametros]") && ($.trim( $conteiner_tabela.attr('data-parametros') ) != ''))){
			parametros_pagina = $conteiner_tabela.attr('data-parametros');
			pagina_com_parametros = pagina + '?' + parametros_pagina;
		} else {
			parametros_pagina = '';
			pagina_com_parametros = '';
		}

		// Obtenção da ordenação padrão do componente, que pode ser o nome ou o número da coluna
		if(($conteiner_tabela.is("[data-ordenacao]") && ($.trim( $conteiner_tabela.attr('data-ordenacao') ) != ''))){
			if($conteiner_tabela.attr('data-ordenacao') == 'false'){
				ordenarPorPadrao = false;
			}
			ordenacao = ( $conteiner_tabela.attr('data-ordenacao') ).split(',');
		} else {
			ordenacao = ['1'];
		}

		// Obtenção da filtragem padrão do componente, que pode ser ascendente (asc) ou descendente (desc)
		if(($conteiner_tabela.is("[data-filtragem]") && ($.trim( $conteiner_tabela.attr('data-filtragem') ) != ''))){
			filtragem = ( $conteiner_tabela.attr('data-filtragem') ).split(',');
		} else {
			filtragem = ['asc'];
		}
		
		// Criação de objeto contendo as opções de ordenação e filtragem combinados.
		var ordenacao_filtragem = [];
		var total_ordenacao = ordenacao.length;
		for(var i=0; i<total_ordenacao; i++){
			var o = ordenacao[i];
			var f;
			if(typeof filtragem[i] != 'undefined'){
				f = filtragem[i];
			} else {
				f = filtragem[i - 1];
			}
			ordenacao_filtragem.push({
				'ordenacao': $.trim(o),
				'filtragem': $.trim( (f).toLowerCase() )
			});
		}
		
		// Obtenção do limite de registros a ser exibidos na tabela
		if(($conteiner_tabela.is("[data-limite]") && ($.trim( $conteiner_tabela.attr('data-limite') ) != ''))){
			limite_registros = $conteiner_tabela.attr('data-limite');
		} else {
			limite_registros = 15;
		}
		
		// Obtenção do limite de registros a ser exibidos na tabela
		if(($conteiner_tabela.is("[data-paginacao]") && ($.trim( $conteiner_tabela.attr('data-paginacao') ) == 'true'))){
			temPaginacao = true;
		} else {
			temPaginacao = false;
		}
		
		// Obtenção do parâmetro que determina se a tabela é específica de relatório ou não
		if(($conteiner_tabela.is("[data-relatorio]") && ($.trim( $conteiner_tabela.attr('data-relatorio') ) == 'true'))){
			relatorio = true;
		} else {
			relatorio = false;
		}
		
		// Obtenção do parâmetro que determina se a tabela é específica de relatório ou não
		if(($conteiner_tabela.is("[data-titulo-relatorio]") && ($.trim( $conteiner_tabela.attr('data-titulo-relatorio') ) != ''))){
			titulo_relatorio = $conteiner_tabela.attr('data-titulo-relatorio');
		} else {
			titulo_relatorio = $('title').html();
		}
		
		// Obtenção do parâmetro que determina se o plugin de responsividade de tables será ativado ou não
		// Se não for fornecido, será ativado por padrão.
		if($conteiner_tabela.is("[data-responsividade]")){
			responsividade = ($.trim( $conteiner_tabela.attr('data-responsividade') ) == 'true');
		} else {
			responsividade = true;
		}
		
		// Obtenção do parâmetro que determina se o plugin de cabeçalho fixo será ativado ou não.
		// Se não for fornecido, será ativado por padrão.
		if($conteiner_tabela.is("[data-cabecalho-fixo]")){
			cabecalhoFixo = ($.trim( $conteiner_tabela.attr('data-cabecalho-fixo') ) == 'true');
		} else {
			cabecalhoFixo = false;
		}
		
		// Obtenção do parâmetro referente ao evento "drawCallback", chamado a cada busca na tabela
		if(($conteiner_tabela.is("[data-ondraw]") && ($.trim( $conteiner_tabela.attr('data-ondraw') ) != ''))){
			eventos['ondraw'] = $.trim( $conteiner_tabela.attr('data-ondraw') );
		} else {
			eventos['ondraw'] = '';
		}
		
		// Obtenção do parâmetro referente ao evento "drawCallback", chamado a cada busca na tabela
		if(($conteiner_tabela.is("[data-onpopstate]") && ($.trim( $conteiner_tabela.attr('data-onpopstate') ) != ''))){
			eventos['onpopstate'] = $.trim( $conteiner_tabela.attr('data-onpopstate') );
		} else {
			eventos['onpopstate'] = '';
		}
		
		// Setando atributo "id" nos elementos principais, caso não exista
		if(!$tabela.is('[id]')){
			$tabela.attr('id', gerarIdAleatorio($tabela[0]));
		}
		if(!$conteiner_tabela.is('[id]')){
			$conteiner_tabela.attr('id', gerarIdAleatorio($conteiner_tabela[0]));
		}
		
		// Montagem das opções padrão do campo "Exibir"
		var opcoes_exibir = [ [1, 2, 3, 5, 7, 10, 15, 25, 50, 100, -1], [1, 2, 3, 5, 7, 10, 15, 25, 50, 100, 'Todos'] ];
		
		// Variáveis necessárias para implementar a solicitaçao de confirmação, quando o usuario tentar exibir todos os registros
		var desativarPesquisa = false;
		var confirmarPesquisaCustosa = false;
		var pagina_original = 0;
		var limite_registros_original = limite_registros;
		
		// Adição de classes específicas do Bootstrap na tabela, de modo a deixá-la com bordas e efeitos de zebra e hover
		$tabela.addClass('table table-striped table-hover table-bordered');
		
		// Parâmetros de instanciação do jQuery Datatables
		var parametros = {
			// Define se a tabela tera paginação ou não
			'paging': temPaginacao
		}
		
		// Torna a table com layout responsivo
		if(responsividade){
			parametros.responsive = true;
		}
		
		// Se tiver cabeçalho fixo, adicionar alguns parâmetros na instanciação
		if(cabecalhoFixo){
			// Ativa cabeçalho fixo de tables.
			parametros['fixedHeader'] = true;
			
			// Ativa cálculo automático da largura das colunas da table.
			// Necessário para o cabeçalho fixo funcionar corretamente.
			parametros['autoWidth'] = true;
		}
		
		// Se for relatório, exibir botão de imprimir nativo do jQuery DataTables
		if(relatorio){
			// Inserção de botões de exportação da tabela para formatos PDF, XLS e CSV
			parametros['buttons'] = [
				{
					extend: 'pdf',
					text: '<i class="fa fa-file-pdf-o"></i> PDF',
					className: 'btn btn-danger',
					titleAttr: 'Gerar PDF',
					title: titulo_relatorio,
					footer: true,
					customize: function(doc){
						// Adicionando imagem no topo do PDF
						doc.content.splice( 1, 0, {
							margin: [ 0, 0, 0, 12 ],
							alignment: 'center',
							image: 'data:image/jpeg;base64,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'
						} );
						
						// Evitando repetição de linhas no rodapé
						var total_linhas = (doc.content[2].table.body.length) - 1;
						var total_colunas = doc.content[2].table.body[total_linhas].length;
						for(var i=0; i<total_colunas; i++){
							if(i > 0){
								doc.content[2].table.body[total_linhas][i].text = '';
							}
						}
						
						// Definindo 100% de largura da tabela
						var colCount = new Array();
						$tabela.find('tbody tr:first-child td').each(function () {
							if ($(this).attr('colspan')) {
								for (var i = 1; i <= $(this).attr('colspan'); i++) {
									colCount.push('*');
								}
							} else {
								colCount.push('*');
							}
						});
						doc.content[2].table.widths = colCount;
						
						// Adicionando rodapé no PDF, com nome do sistema e número da página
						doc['footer'] = (function (page, pages) {
							return {
								columns: [
									{
										text: 'CPF - Contador de Pontos de Função',
										color: '#666666'
									},
									{
										alignment: 'right',
										text: [
											'Página ',
											{text: page.toString(), italics: true},
											' de ',
											{text: pages.toString(), italics: true}
										],
										color: '#666666'
									}
								],
								margin: [10, 0]
							}
						});
					}
				},
				{
					extend: 'excel',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					className: 'btn btn-success',
					titleAttr: 'Gerar Planilha Excel',
					title: titulo_relatorio
				}
			];
		} else {
			parametros['buttons'] = [];
		}
		
		// Alterando parâmetros em função da existência da paginação ou não
		if(temPaginacao){
			// Fazer paginação exibir todos os botões, inclusive o "Primeiro" e "Último", porém excluindo as elipses
			parametros['pagingType'] = (dispositivo == 'xs') ? ('full_numbers_no_ellipses') : ('full_numbers');
			// Personalizar itens do campo "Exibir", na paginação
			parametros['lengthMenu'] = opcoes_exibir;
			// Define o limite de registros exibidos na tabela, caso o total de registros da tabela ultrapasse este valor
			parametros['pageLength'] = limite_registros;
			// Redefine o DOM do cabeçalho do componente, de modo a facilitar
			// a re-estilização dos filtros "Exibir" e "Pesquisar"
			parametros['dom'] = "<'row'<'col-sm-12'l<'conteiner_dataTables_filter'f>>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";
		} else {
			$.fn.dataTable.Buttons.swfPath = '../common/datatables.net-buttons/swf/flashExport.swf';
			
			if(relatorio){
				parametros['dom'] = 'B';
			}
			// Desativa exibição de informações, na paginação
			parametros['info'] = false;
		}
		
		// Número de botões exibidos na paginação (desconsiderando botões "Primeiro", "Anterior", "Próximo" e "Último)
		var numero_botoes_paginacao;
		if(dispositivo == 'xs'){
			numero_botoes_paginacao = 3;
		} else {
			numero_botoes_paginacao = 10;
		}
		$.fn.DataTable.ext.pager.numbers_length = numero_botoes_paginacao;
		
		// Diz ao componente que os registros serão obtidos via Ajax, através da página fornecida no atributo "data-pagina"
		if(temPaginaAjax){
			parametros['processing'] = true;
			parametros['serverSide'] = true;
			parametros['ajax'] = {
				'url': pagina_com_parametros,
				'method': 'GET',
				'timeout': (60 * 1000) // Expirar requisição em 60 segundos
			};
		}
		
		// Obtendo informações das colunas da tabela, a partir de seu cabeçalho
		var colunas = [], ordem = [];
		$tabela.children('thead').children('tr').children('th').each(function(i){
			var $th = $(this);
			var coluna = {};
			
			// Obtendo colspan da coluna, para realizar a devida inserção do número
			// de colunas na instanciação do componente
			var colspan;
			if($th.is('[colspan]')){
				colspan = parseInt($th.attr('colspan'), 10);
			} else {
				colspan = 0;
			}
			
			// Definindo nomes das colunas, caso o atributo "data-coluna" não exista.
			// A definição é feita adicionando o numero das colunas
			if(!$th.is('[data-coluna]')){
				$th.attr('data-coluna', (i+1));
			}
			// Obtendo nomes das colunas, em função do atributo "data-coluna"
			var nome_coluna;
			if($th.hasClass('acoes')){
				nome_coluna = 'acoes';
				// Adicionando classe para tornar a coluna de ações sempre visível,
				// mesmo em dispositivos de pouca largura
				$th.addClass('all');
				
				// Se estiver em dispositivos com pouca largura de tela,
				// remover nome "Ações" e diminuir largura da célula do cabecalho
				if(dispositivo == 'xs'){
					$th.html('&nbsp;').css('width', '5%');
				}
			} else {
				nome_coluna = ( $th.attr('data-coluna') ).toString();
			}
			coluna.data = nome_coluna;
			
			// Desativando ordenação na coluna, se esta tiver o atributo 
			// "data-ordenavel" definido como false
			if($th.is('[data-ordenavel]') && $th.attr('data-ordenavel') == 'false'){
				coluna.orderable = false;
			}
			
			// Definindo tipo de ordenação.
			// Tipos customizados de ordenação estão no final desse script
			if($th.is('[data-tipo-ordenacao]')){
				var tipo_ordenacao = $th.attr('data-tipo-ordenacao');
				coluna.type = tipo_ordenacao;
			}
			
			// Aplicar formatação de textos nas colunas da tabela, se existir
			// o atributo "data-formato" na coluna <th> do cabeçalho
			if($th.is('[data-formato]') && $.trim( $th.attr('data-formato') ) != ''){
				var formato = $th.attr('data-formato');
				if(typeof window[formato] == 'function'){
					coluna.render = function(texto){
						return new Function('return ' + formato + "('" + texto + "')")();
					}
				}
			}
			
			// Adicionando informações da coluna ao array de parâmetros
			if(colspan > 1){
				for(var j = 0; j < colspan; j++){
					if(j > 0){
						delete coluna.data;
					}
					colunas.push(coluna);
				}
			} else {
				colunas.push(coluna);
			}
			
			// Removendo atributo "data-ordenavel" das células do cabecalho da tabela
			$th.removeAttr('data-ordenavel');
		});
		
		// Obtendo colunas da tabela para serem usadas como base na ordenação, em função
		// do nome da coluna, e da variável "ordenacao_filtragem", se aplicável.
		var ordem = [];
		var $thsCabecalho = $tabela.children('thead').children('tr').children('th');
		for(var i in ordenacao_filtragem){
			$thsCabecalho.each(function(){
				var $th = $(this);
				if($th.attr('data-coluna') == ordenacao_filtragem[i]['ordenacao']){
					var numero_ordem = $th.index();
					var direcao_ordem = ordenacao_filtragem[i]['filtragem'];
					ordem.push([numero_ordem, direcao_ordem]);
				}
			});
		}
		
		// Removendo atributo "data-coluna" das células do cabeçalho da tabela
		$thsCabecalho.removeAttr('data-coluna');
		
		// Formatando rodapé da table
		$tabela.children('tfoot').children('tr').children('th, td').each(function(i){
			var $celula = $(this);
			
			// Trocar atributo "colspan='100%'" para um número ao invés de porcentagem.
			// Necessário para evitar bugs visuais no rodapé.
			if($celula.is("[colspan='100%']")){
				var colspan_rodape = colunas.length;
				$celula.attr('colspan', colspan_rodape);
			}
			
			// Se estiver em dispositivos com pouca largura de tela, remover nome "Ações"
			if($celula.hasClass('acoes')){
				if(dispositivo == 'xs'){
					$celula.html('&nbsp;');
				}
			}
		});
		
		// Se não foi possível obter a ordenação a partir das colunas, defini-la
		// como padrão para usar a primeira coluna da tabela, em ordem ascendente
		if(ordem.length == 0){
			ordem = [[0, 'asc']];
		}
		if(!ordenarPorPadrao){
			ordem = [];
		}
		
		parametros['columns'] = colunas;
		parametros['order'] = ordem;
		
		// Desativa ordenação para a última coluna da tabela (para ações)
		if(temColunaAcoes){
			parametros['columnDefs'] = [
				{
					'orderable': false,
					'targets': -1
				}
			];
			if(temPaginaAjax){
				parametros['columnDefs'][0]['data'] = null;
				parametros['columnDefs'][0]['defaultContent'] = '';
			}
		}
		
		// Chamada de eventos e callbacks do componente
		$tabela.on({
			// Init: Chamado após o componente ser instanciado pela primeira vez
			'init.dt': function(){
				if(temPaginacao){
					var id_tabela = $tabela.attr('id');
					
					var $campoExibir = $conteiner_tabela.find("select[name='" + id_tabela + "_length']");
					var $campoPesquisar = $conteiner_tabela.find('#' + id_tabela + '_filter').find("input[type='search']");
					var $labelCampoExibir = $campoExibir.parent();
					var $labelCampoPesquisar = $campoPesquisar.parent();
					
					$campoExibir.insertBefore($labelCampoExibir);
					$labelCampoExibir.wrapInner('<span></span>');
					$campoExibir.prependTo($labelCampoExibir);
					$labelCampoExibir.addClass('form-group has-float-label');
					$campoExibir.removeClass('custom-select custom-select-sm form-control-sm').addClass('select');
					select.instanciar($campoExibir);
					
					$campoPesquisar.insertBefore($labelCampoPesquisar);
					$labelCampoPesquisar.wrapInner('<span></span>');
					$campoPesquisar.prependTo($labelCampoPesquisar);
					$campoPesquisar.attr({
						'type': 'text',
						'placeholder': 'Digite o texto da busca'
					}).removeClass('form-control-sm');
					$labelCampoPesquisar.addClass('has-float-label').wrap("<div class='form-group input-group with-float-label'></div>");
					$labelCampoPesquisar.after(
						$('<div />').addClass('input-group-append').html(
							$('<span />').addClass('input-group-text').html(
								$('<i />').addClass('fas fa-search')
							)
						)
					);
				}
			},
			// Processing: Chamado quando o componente está processando algo
			// (ex.: carregando dados da tabela)
			'processing.dt': function(e, s, processando){
				if(processando == true){
					var $div_processando = $conteiner_tabela.find('div.dataTables_processing');
					var $caption = $conteiner_tabela.children('div.caption');
					
					// Estilizando modal de carregando
					if(!$div_processando.is("[data-estilizado='true']")){
						var texto_processando = $div_processando.html();
						var $modal_carregando = $("<div />").addClass('modal_carregando').prepend(
							$("<i />").addClass('fas fa-circle-notch fa-spin fa-3x fa-fw') // Indicador de carregamento
						).append(
							$("<div />").html(texto_processando) // Texto de "processando"
						).append(
							$("<button />", {'type': 'button'}).addClass('btn btn-xs cancelar preto').html('Cancelar') // Botão "Cancelar"
						);
						var $modal_carregando_baixo = $modal_carregando.clone().addClass('baixo').hide();
						$div_processando.html( $modal_carregando ).append( $modal_carregando_baixo ).find('button.cancelar').click(function(){
							// Abortar requisição ajax, se o botão "Cancelar" for clicado
							var ajax_tabela = s['jqXHR'];
							ajax_tabela.abort();
						});
						$div_processando.attr('data-estilizado', 'true');
					}
					
					// Exibindo modal de carregando no topo e rodapé da tabela
					var $modal_carregando = $div_processando.children('div.modal_carregando');
					if($conteiner_tabela.height() > getAltura()){
						$modal_carregando.first().addClass('cima');
						$modal_carregando.last().show();
					} else {
						$modal_carregando.first().removeClass('cima');
						$modal_carregando.last().hide();
					}
					
					// Definindo altura do modal, de modo a ocupar toda a área da tabela
					var altura_modal = $conteiner_tabela.outerHeight() - $caption.outerHeight();
					$div_processando.css('height', altura_modal);
				}
			},
			// Length: Chamado quando o valor do campo "Exibir" é alterado
			'length.dt': function(e, s){
				if(temPaginacao){
					var limite_registros_campo = s._iDisplayLength;
					var total_consulta = s['_iRecordsDisplay'];
					var $campoExibir = $conteiner_tabela.find('div.dataTables_length select');

					// Se a tabela for via Ajax, a consulta retornar mais de 1000 resultados, e o usuário tentar exibir todos os registros,
					// perguntar ao usuário se ele realmente deseja realizar esta operação.
					if(temPaginaAjax && total_consulta > 1000 && s._iDisplayLength == -1){
						desativarPesquisa = true;
						jConfirmSimNao('Esta consulta retornará muitos registros e pode demorar um pouco. Deseja continuar?', '', function(r){
							desativarPesquisa = false;
							if(r){
								confirmarPesquisaCustosa = true;
								pagina_original = s._iDisplayStart;
								limite_registros_original = limite_registros_campo;
								objeto_tabela.page.len(-1).draw();
							} else {
								// Resetar a página e tamanho atuais da paginação
								confirmarPesquisaCustosa = false;
								s._iDisplayStart = pagina_original;
								s._iDisplayLength = limite_registros_original;
								$campoExibir.val(limite_registros_original);
								select.atualizar($campoExibir);
							}
						})
					}
				}
			}
		});
		// preDrawCallback: Chamado antes do componente ter concluído uma pesquisa
		parametros['preDrawCallback'] = function(){
			var info_paginacao = $tabela.DataTable().page.info();
			
			// Cancelar pesquisa, se a variável "desativarPesquisa" for alterada para true
			if(desativarPesquisa){
				desativarPesquisa = false;
				return false; // Cancela pesquisa
			}
			
			// Efetuar customizações nos campos <select> da tabela, se tiver paginação
			if(temPaginacao){
				// Obtenção do campo <select>
				var $campoExibir = $conteiner_tabela.find('div.dataTables_length select');

				// Desativar filtro de busca, para campos estilizados com o componente Select2
				$campoExibir.attr('data-filtro', 'false');

				// Montagem das opções customizadas do campo "Exibir", em função do total da consulta
				var total_consulta = info_paginacao.recordsDisplay;
				var limite_registros_pesquisa = info_paginacao.length;
				var temResultados = (total_consulta > 0);
				if(temResultados){
					var opcoes_exibir_customizado = opcoes_exibir[0].slice(0, -1);
					var ultima_opcao = opcoes_exibir_customizado.slice().pop();
					if((total_consulta < limite_registros) || (total_consulta < ultima_opcao)){
						opcoes_exibir_customizado.push(total_consulta);
						opcoes_exibir_customizado = sortNumber(opcoes_exibir_customizado);
						var posicao = opcoes_exibir_customizado.indexOf(total_consulta);
						opcoes_exibir_customizado = opcoes_exibir_customizado.slice(0, posicao+1);
						opcoes_exibir_customizado = removeDuplicates(opcoes_exibir_customizado);
					}
					ultima_opcao = opcoes_exibir_customizado.slice().pop();
					var total_opcoes = opcoes_exibir_customizado.length;
					if(total_opcoes > 0){
						$campoExibir.html('');
						var tem_selecionado = false;
						for(var i=0; i<total_opcoes; i++){
							var opcao = opcoes_exibir_customizado[i];
							var selected;
							if((!tem_selecionado) && ( ((i+1) == total_opcoes) || (opcao == limite_registros_pesquisa) )){
								selected = 'selected';
								tem_selecionado = true;
							} else {
								selected = '';
							}
							$campoExibir.append('<option value="' + opcao + '" ' + selected + '>' + opcao + '</option>');
						}
						if(ultima_opcao < 100){
							$campoExibir.each(function(){
								var $campo = $(this);
								$campo.children('option').last().attr('value', '-1').html('Todos');
							})
						} else {
							$campoExibir.append('<option value="-1">Todos</option>');
						}
					}
				}
			}
			
			// Remover foco de todos os botões da tabela, antes de exibir o modal de carregamento sobre a mesma
			$conteiner_tabela.find('button:focus').blur();
		}
		// drawCallback: Chamado após o componente ter concluído uma pesquisa
		parametros['drawCallback'] = function(){
			var objeto_tabela = $tabela.DataTable();
			var info_paginacao = objeto_tabela.page.info();
			
			// Obtendo totais
			var total_consulta = info_paginacao.recordsDisplay;
			var quantidade_exibir = parseInt(info_paginacao.length, 10);
			
			// Criando flags de validação, para uso posterior
			var checkDentroDeModal = ($conteiner_tabela.closest('div.janela_modal').length > 0);
			var checkAtualizacaoProgramatica = ($conteiner_tabela.is("[data-atualizacao-programatica='true']"));
			var temResultados = (total_consulta > 0);
			
			if(!checkDentroDeModal && !checkAtualizacaoProgramatica){
				// Scrollando página até a table, se for a primeira pesquisa
				if(temPaginaAjax && dispositivo == 'xs'){
					scrollarElemento($conteiner_tabela);
				}
			}

			// Efetuando personalizações adicionais, para tabelas com paginação
			if(temPaginacao){
				// Atualizando valor do campo "Exibir", após a busca ser realizada.
				// Feito para evitar bug do campo ficar vazio, após uma pesquisa
				// feita numa tabela já instanciada.
				var valor_campo_exibir;
				if(total_consulta > quantidade_exibir){
					valor_campo_exibir = quantidade_exibir;
				} else {
					valor_campo_exibir = -1;
				}
				objeto_tabela.page.len(valor_campo_exibir);
			}
			
			// Se a consulta retornar resultados, prosseguir com a formatação da tabela
			if(temResultados){
				// Exibir os botões de ações na sua respectiva célula, para tabelas
				// com página Ajax e pelo menos um resultado.
				// Isso é feito com base no elemento <div class="acoes"> fora da tabela,
				// de modo a substituir os índices entre chaves, por seus respectivos
				// valores, conforme retornado pela propriedade "DT_RowData"
				if(temColunaAcoes){
					if(temPaginaAjax){
						var $conteiner_acoes = $conteiner_tabela.children('div.acoes');
						
						$tabela.children('tbody').children('tr').each(function(){
							var $tr = $(this);
							var dados_coluna = $tr.data();
							var $coluna_acoes = $tr.children('td:last-child').addClass('acoes');

							var $conteiner_acoes_clonado = $conteiner_acoes.clone();
							$conteiner_acoes_clonado.removeClass('acoes');

							tabela.instanciarConteinerAcoes($conteiner_acoes_clonado, dispositivo);
							$conteiner_acoes_clonado.children('button').not('.dropdown-toggle').each(function(){
								var $acao = $(this);
								if($acao.is('[data-pagina]')){
									var pagina = $acao.attr('data-pagina');
									var parametros, target;
									if($acao.is('[data-parametros]')){
										parametros = $acao.attr('data-parametros');
									} else {
										parametros = '';
									}
									if($acao.is('[data-target]')){
										target = $acao.attr('data-target');
									} else {
										target = '';
									}

									// Substituição dos índices pelos valores
									pagina = tabela.substituirIndiceValor(pagina, dados_coluna);
									parametros = tabela.substituirIndiceValor(parametros, dados_coluna);
									
									// Criação do evento de clique, em função dos parâmetros acima
									onclick = function(){
										abrirPagina(pagina, parametros, target);
									};
								} else if( $acao.is('[data-onclick]') ){
									var onclick = $acao.attr('data-onclick');

									onclick = tabela.substituirIndiceValor(onclick, dados_coluna);
									
									// Criação do evento de clique, em função do atributo "data-onclick"
									onclick = new Function('return ' + onclick);							
								}
								
								tabela.estilizarAcao($acao, dispositivo, onclick, temPaginaAjax);
							});
							$conteiner_acoes_clonado.appendTo($coluna_acoes);
						});
					} else {
						$tabela.children('tbody').children('tr').each(function(){
							var $tr = $(this);
							var $coluna_acoes = $tr.children('td:last-child').addClass('acoes');
							
							$coluna_acoes.children('button').wrapAll( $('<div />').addClass('acoes') );
							
							$conteiner_acoes = $coluna_acoes.children('div.acoes');
							$conteiner_acoes.removeClass('acoes');
							
							tabela.instanciarConteinerAcoes($conteiner_acoes, dispositivo);
							$conteiner_acoes.children('button').not('.dropdown-toggle').each(function(){
								var $acao = $(this);
								var onclick = new Function('return ' + $acao.attr('onclick'));
								tabela.estilizarAcao($acao, dispositivo, onclick, temPaginaAjax);
							});
						});
					}
				}
			}
			
			// Executando evento ondraw, se fornecido com atributo
			if(eventos['ondraw'] != ''){
				var ondraw = eventos['ondraw'];
				var id_tabela = $tabela.attr('id');
				ondraw = ondraw.replace('this', '$("#' + id_tabela + '")');
				new Function('return ' + ondraw)();
			}
		}
		// Ajax Error: Chamado quando há erros na requisição Ajax, seja do cliente ou do servidor
		if(temPaginaAjax){
			parametros['ajax']['error'] = function(jqXHR, textStatus, err){
				var $div_processando = $conteiner_tabela.find('div.dataTables_processing');
				$div_processando.hide();
				var $modalRetorno = jError('Erro ao listar registros.');
				
				tabela.mostrarDetalhesErro($modalRetorno, jqXHR.responseText);
			}
		}
		// Error: Chamado quando o componente em si retorna erro
		$.fn.dataTable.ext.errMode = 'throw';
		$tabela.on('error.dt', function(e, s, t, m){
			// Checando se o erro retornado corresponde a sessão expirada
			if(m.indexOf('expired') > -1){
				var $div_processando = $conteiner_tabela.find('div.dataTables_processing');
				$div_processando.hide();

				abrirPagina('logoff.php?sessao_expirada=true');
			} else {
				var $modalRetorno = jError('Erro ao listar registros.');
				
				tabela.mostrarDetalhesErro($modalRetorno, m);
			}
		});
		
		// Strings de tradução do componente para português
		parametros['language'] = {
			'sEmptyTable': 'Nenhum registro encontrado',
			'sInfo': 'Total exibido: _TOTAL_ - Total cadastrado: _MAX_',
			'sInfoEmpty': 'Total exibido: _TOTAL_ - Total cadastrado: _MAX_',
			'sInfoFiltered': '',
			'sInfoPostFix': '',
			'sInfoThousands': '.',
			'sLengthMenu': 'Exibir: _MENU_',
			'sLoadingRecords': 'Carregando...',
			'sProcessing': 'Processando...',
			'sZeroRecords': 'Nenhum registro encontrado',
			'sSearch': 'Pesquisar',
			'oPaginate': {
				'sFirst': '<i class="fa fa-step-backward"></i>',
				'sPrevious': '<i class="fa fa-backward"></i>',
				'sNext': '<i class="fa fa-forward"></i>',
				'sLast': '<i class="fa fa-step-forward"></i>'
			},
			'oAria': {
				'sSortAscending': ': Ordenar colunas de forma ascendente',
				'sSortDescending': ': Ordenar colunas de forma descendente'
			},
			'buttons': {
				'print': 'Imprimir'
			}
		};
		
		// Instanciação do componente
		var objeto_tabela = $tabela.DataTable(parametros);
		
		// Salvando parâmetros da instanciação dessa tabela na propriedade do componente.
		// Útil para customizações posteriores.
		var id = $tabela.attr('id');
		tabela.parametros[id] = parametros;
		
		// Setando evento de controle de estados de histórico
		if(temPaginaAjax){
			window.addEventListener("popstate", function (e) {
				var id = $conteiner_tabela.attr('id');
				var parametros = e.state;
				tabela.atualizarFormularioPesquisa(id, parametros);
				if(e.state != null){
					var limite = parseInt($conteiner_tabela.attr('data-limite'), 10);
					var pagina_com_parametros = pagina + '?' + parametros;
					objeto_tabela.page.len(limite).ajax.url(pagina_com_parametros).load();
				}
				
				if(eventos['onpopstate'] != ''){
					var onpopstate = eventos['onpopstate'];
					onpopstate = onpopstate.replace('this', '$("#' + id + '")');
					new Function('return ' + onpopstate)();
				}
			});
		}
		
		// Correção de largura da tabela, ao ativar o plugin de responsividade
		objeto_tabela.on('responsive-resize', function(){
			tabela.atualizarLargura($conteiner_tabela);
		})
		
		// Inserir atributo que impede desta função atuar sobre esta tabela
		// duas vezes, de modo a evitar bugs.
		$conteiner_tabela.attr('data-instanciado', 'true');
	});
}

tabela.pesquisar = function(form, seletor_tabela){
	if(validaForm(form, false) == true){
		// Obtendo dados a partir do formulário
		var $form = $(form);
		var seletor_tabela = $form.attr('data-tabela');
		
		// Obtendo contêiner da tabela, em função de seu seletor no formulário.
		// Se não for encontrado, assumir como seletor padrão o primeiro elemento
		// <div class="tabelaaberta" />
		var $conteiner_tabela;
		if(typeof seletor_tabela == 'undefined'){
			$conteiner_tabela = $('div.tabelaaberta').first();
			if(!$conteiner_tabela.is('[id]')){
				$conteiner_tabela.attr('id', gerarIdAleatorio($conteiner_tabela[0]));
			}
			
			var id_conteiner_tabela = $conteiner_tabela.attr('id');
			$form.attr('data-tabela', id_conteiner_tabela);
		} else {
			$conteiner_tabela = $('#' + seletor_tabela);
		}
		
		// Verificando se a tabela tem página ajax, para realizar tratamentos
		// posteriores necessários.
		var temPaginaAjax;
		if(($conteiner_tabela.is("[data-pagina]") && ($.trim( $conteiner_tabela.attr('data-pagina') ) != ''))){
			temPaginaAjax = true;
		} else {
			temPaginaAjax = false;
		}
		
		// Obtendo parâmetros adicionais da tabela e do seu formulário
		var limite = parseInt($conteiner_tabela.attr('data-limite'), 10);
		var objeto_tabela = $conteiner_tabela.find('table.dataTable').DataTable();
		var $form = $(form);
		var pagina = $conteiner_tabela.attr('data-pagina').split('?').shift();
		var parametros_formulario = decodeURIComponent( $form.serialize() );
		parametros_formulario += '&Submit=';
		
		// Desocultando a tabela e atualizando seus parâmetros de página
		// e parâmetros
		$conteiner_tabela.removeClass('oculta').attr({
			'data-pagina': pagina,
			'data-parametros': parametros_formulario
		});
		
		// Verificando se a tabela já foi instanciada antes.
		// Necessário para saber como prosseguir com a pesquisa.
		var tabelaInstanciada = $conteiner_tabela.is("[data-instanciado='true']");
		if(tabelaInstanciada){
			// Obtendo página com parâmetros
			var pagina_com_parametros;
			if($.trim(parametros_formulario) != ''){
				pagina_com_parametros = pagina + '?' + parametros_formulario;
			} else {
				pagina_com_parametros = pagina;
			}
			
			// Efetuando atualização da tabela
			objeto_tabela.page.len(limite).ajax.url(pagina_com_parametros).load();
		} else {
			// Apenas instanciar a tabela
			tabela.instanciar($conteiner_tabela);
		}
		
		// Inserindo estado da página no histórico, para ser possível avançar e voltar devidamente no navegador
		if(temPaginaAjax){
			history.pushState(parametros_formulario, '', '?' + parametros_formulario);
		}
		
		// Removendo foco dos botões e campos do formulário
		$form.find('input, button, textarea, select').blur();
		
		// Scrollando até a tabela
		if(getDispositivo() == 'xs') scrollarElemento($conteiner_tabela);
	}
	return false; 
}

tabela.atualizar = function(seletor_tabela){
	var $conteineres_tabela;
	if(typeof seletor_tabela == 'undefined'){
		$conteineres_tabela = $('div.tabelaaberta');
	} else {
		$conteineres_tabela = $('#' + seletor_tabela);
	}
	$conteineres_tabela.each(function(){
		var $conteiner_tabela = $(this);
		var temPaginaAjax;
		if(($conteiner_tabela.is("[data-pagina]") && ($.trim( $conteiner_tabela.attr('data-pagina') ) != ''))){
			temPaginaAjax = true;
		} else {
			temPaginaAjax = false;
		}
		var $tabela = $conteiner_tabela.find('table.dataTable');
		var objeto_tabela = $tabela.DataTable();
		if(temPaginaAjax){
			$conteiner_tabela.attr('data-dentro-modal', 'true');
			objeto_tabela.ajax.reload(function(){
				$conteiner_tabela.removeAttr('data-dentro-modal');
			}, false);
		} else {
			var ordenacao, filtragem;
			if($conteiner_tabela.is('[data-ordenacao]')){
				ordenacao = $conteiner_tabela.attr('data-ordenacao') - 1;
			} else {
				ordenacao = '1';
			}
			if($conteiner_tabela.is('[data-filtragem]')){
				filtragem = $conteiner_tabela.attr('data-filtragem');
			} else {
				filtragem = 'asc';
			}
			
			try{
				objeto_tabela.order([ordenacao, filtragem]).draw(false);
			} catch(e){
				
			}
		}
	});
}

tabela.atualizarFormularioPesquisa = function(seletor_tabela, parametros){
	var $conteiner_tabela;
	if(typeof seletor_tabela == 'undefined'){
		$conteiner_tabela = $('div.tabelaaberta');
	} else {
		$conteiner_tabela = $('#' + seletor_tabela);
	}
	var $form = $('body').find("form[data-tabela='" + seletor_tabela + "']");
	if($form.length == 0) $form = $conteiner_tabela.prev();
	
	if(parametros == null){
		$form.trigger('reset').find('select.select').each(function(){
			var $campo = $(this);
			select.atualizar($campo);
		});
		return;
	}
	
	var array_parametros = parametros.split('&');
	parametros = {};
	for(var i in array_parametros){
		var parametro = array_parametros[i].split('=');
		var nome = parametro[0];
		var valor = parametro[1];

		parametros[nome] = valor;
	}
	
	$form.find("input, select, textarea").each(function(){
		var $campo = $(this);
		var nome = $campo.attr('name');
		
		if(parametros.hasOwnProperty(nome)){
			var valor_parametro = parametros[nome].replace('+', ' ');
			
			if($campo.is("input[type='checkbox']")){
				var valor_checkbox = $campo.val();
				if(valor_parametro == valor_checkbox){
					$campo.prop('checked', true);
				} else {
					$campo.prop('checked', false);
				}
			} else if($campo.is("input[type='radio']")){
				var nome_tmp = ( $campo.attr('name') ).replace(/\[.*?\]/g, '');
				$form.find("input[type='radio'][name=^'"+nome_tmp+"']").each(function(){
					var $campoRadio = $(this);
					var valor_radio = $campoRadio.val();
					if(valor_parametro == valor_radio){
						$campoRadio.prop('checked', true);
					} else {
						$campoRadio.prop('checked', false);
					}
				});
			} else if($campo.is("select.select")){
				$campo.val(valor_parametro);
				select.atualizar($campo);
			} else {
				$campo.val(valor_parametro);
			}
		}
	});
}

tabela.removerLinhaById = function(id, seletor_tabela){
	var $conteiner_tabela;
	if(typeof seletor_tabela == 'undefined'){
		$conteiner_tabela = $('div.tabelaaberta');
	} else {
		$conteiner_tabela = $('#' + seletor_tabela);
	}
	$conteiner_tabela.find('table.dataTable').each(function(){
		var $tabela = $(this);
		var removeu = false;
		$tabela.children('tbody').children('tr').each(function(){
			var $tr = $(this);
			var dados_coluna = $tr.data();
			if(id == dados_coluna['id']){
				var $tr_seguinte = $tr.next();
				
				// Se a linha seguinte for uma extensão da atual (comum em celulares), removê-la também
				if($tr_seguinte.hasClass('child') && !$tr_seguinte.is("[role='row']")){
					$tr_seguinte.remove();
				}
				
				// Removendo linha propriamente dita
				$tr.remove();
				
				// Atualizando flag de remoção de linha da tabela
				removeu = true;
				return false; // Sair do $.each
			}
		});
		if(removeu){
			$tabela.find('span.total_consulta, span.total_geral').each(function(){
				var $span = $(this);
				var total = parseInt($span.html(), 10);
				$span.html(total - 1);
			});
		}
	});
}

// Função que instancia contêiner de ações, em função do dispositivo
// usado (desktop ou mobile). Se for mobile, o componente do Bootstrap
// "Button Dropdown" é utilizado. Do contrário, é usado o componente
// Bootstrap "Button Group"
tabela.instanciarConteinerAcoes = function(conteiner_acoes, dispositivo){
	if(typeof dispositivo == 'undefined') dispositivo = getDispositivo();
	var $conteiner_acoes_instanciado = $(conteiner_acoes);
	if(dispositivo == 'xs'){
		var $botao_dropdown = $('<button />').addClass('btn btn-secondary dropdown-toggle').attr({
			'type': 'button',
			'data-toggle': 'dropdown',
			'aria-haspopup': 'true',
			'aria-expanded': 'false'
		}).html(
			$('<i />').addClass('fa fa-ellipsis-v')
		);
		var id_botao_dropdown = gerarIdAleatorio($botao_dropdown);
		$botao_dropdown.attr('id', id_botao_dropdown);
		var $conteiner_dropdown = $('<div />').addClass('acoes_mobile dropdown-menu dropdown-menu-right').attr('aria-labelledby', id_botao_dropdown).append(
			$("<h6 />").addClass('dropdown-header').html('Ações')
		).append(
			$("<div />").addClass('dropdown-divider')
		);
		$conteiner_acoes_instanciado.prepend( $botao_dropdown.add($conteiner_dropdown) ).addClass('btn-group');
	} else {
		$conteiner_acoes_instanciado.attr({
			"role": "group",
			"aria-label": "Ações"
		}).addClass('btn-group').children('button').each(function(){
			var $botao = $(this);
			if(!$botao.hasClass('btn')){
				$botao.addClass('btn');
			}
			if(! ($botao.hasClass('btn-default') || $botao.hasClass('btn-primary') || $botao.hasClass('btn-success') || $botao.hasClass('btn-warning') || $botao.hasClass('btn-danger') || $botao.hasClass('btn-info')) ){
				$botao.addClass('btn-default');
			}
		});
	}
	return $conteiner_acoes_instanciado;
}

// Função que estiliza os botões do contêiner de ações, em função do dispositivo
// usado (desktop ou mobile).
tabela.estilizarAcao = function(acao, dispositivo, onclick, temPaginaAjax){
	var $acao_estilizada = $(acao);
	var temOnclick = (typeof onclick == 'function');
	var $conteiner_dropdown = $acao_estilizada.siblings('div');
	if(dispositivo == 'xs'){
		var $icone = $acao_estilizada.children('i.fa, i.fas, img').first();
		
		var titulo = $acao_estilizada.attr('title');
		var acao = $acao_estilizada.attr('data-acao');
		var cor_acao;
		if($acao_estilizada.hasClass('btn-success')){
			cor_acao = 'mobile-success';
		} else if($acao_estilizada.hasClass('btn-primary')){
			cor_acao = 'mobile-primary';
		} else if($acao_estilizada.hasClass('btn-danger')){
			cor_acao = 'mobile-danger';
		} else {
			cor_acao = 'mobile-default';
		}
		
		var $a = $("<a />").attr('href', '#').addClass('dropdown-item ' + cor_acao).prepend(
			$icone.addClass('fa-fw align-middle')
		).append(
			$('<span />').addClass('align-middle').html(titulo)
		);
		if(temOnclick){
			$a.click(function(e){
				e.preventDefault();
				onclick();
			});
		}
		$conteiner_dropdown.append($a);

		$acao_estilizada.remove();
	} else {
		$acao_estilizada.removeAttr('data-pagina data-parametros data-target data-onclick');
		if(temOnclick && temPaginaAjax) $acao_estilizada.click(onclick);
	}
	return $acao_estilizada;
}

// Função que recebe a variável string "texto", e substitui índices entre chaves pelos valores inclusos no array "dados_coluna"
tabela.substituirIndiceValor = function(texto, dados_coluna){
	if(typeof texto == 'string'){
		var busca = new RegExp('\\{(.*?)\\}', 'g');
		var resultado;
		while(resultado = busca.exec(texto)){
			var coluna = resultado[1];
			$.each(dados_coluna, function(c, v){
				if(c == coluna){
					texto = texto.replace(new RegExp('{' + c + '}', 'g'), v);
					return false; // Sair do $.each
				}
			})
		}
		// Se, após a substituição, ainda existir índices não substituídos, removê-los do texto
		texto = texto.replace(new RegExp('\\{(.*?)\\}', 'g'), '');

		// Retorna texto com valores atualizados
		return texto;
	} else {
		return null;
	}
}

tabela.mostrarDetalhesErro = function(modal_retorno, detalhes_erro){
	// Exibindo detalhes do erro
	var $modalRetorno = $(modal_retorno);
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
	injetarConteudoIframe($iframe[0], detalhes_erro);
}

tabela.exportar = function(seletor_tabela, formato){
	var $conteiner_tabela;
	if(typeof seletor_tabela == 'undefined'){
		$conteiner_tabela = $('div.tabelaaberta');
	} else {
		$conteiner_tabela = $('#' + seletor_tabela);
	}
	mostraCarregando();
	$conteiner_tabela.first().find('table.dataTable').each(function(){
		var $tabela = $(this);
		var objeto_tabela = $tabela.DataTable();
		objeto_tabela.button(formato).trigger();
	});
	setTimeout(ocultaCarregando, 1000);
}

tabela.imprimir = function(seletor_tabela){
	tabela.exportar(seletor_tabela, 0);
}

tabela.desinstanciar = function(seletor_tabela, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_tabela){
		busca = $(escopo).find(seletor_tabela).filter("[data-instanciado='true']");
	} else {
		busca = $(escopo).find("div.tabelaaberta").filter("[data-instanciado='true']");
	}
	
	busca.each(function(){
		var $conteiner_tabela = $(this);
		var $tabela = $conteiner_tabela.find('table');

		// Destruindo instância da tabela
		$tabela.DataTable().destroy();
		
		// Realizando limpeza no DOM, retirando resquícios
		// deixados pelo componente.
		$conteiner_tabela.removeAttr('data-instanciado');
		$tabela.removeClass('dtr-inline collapsed');
		$tabela.children('thead').children('tr').each(function(){
			var $tr = $(this);
			
			$tr.children('td, th').each(function(){
				var $celula = $(this);
				
				if($celula.is('[data-largura-inline]')){
					var largura_inline = $celula.attr('data-largura-inline');

					$celula.css('width', largura_inline);
				} else {
					$celula.css('width', '');
				}
			});
		});
	});
}

tabela.toggleAdaptacaoParaPDF = function(seletor_tabela, escopo, acao){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_tabela){
		busca = $(escopo).find(seletor_tabela);
	} else {
		busca = $(escopo).find("div.tabelaaberta");
	}
	
	busca.each(function(){
		var $conteiner_tabela = $(this);
		var $tabela = $conteiner_tabela.find('table');
		
		var id_tabela = $tabela.attr('id');
		var parametros = tabela.parametros[id_tabela];
		
		// Caso a ação não tenha sido fornecida, tentar
		// detectá-la em função dos parâmetros da table
		// atual
		if(typeof acao == 'undefined'){
			if(parametros.responsive === true){
				acao = false;
			} else {
				acao = true;
			}
		}

		// Desinstanciando table, para depois reinstanciá-la
		tabela.desinstanciar($conteiner_tabela);

		// Re-instanciando table, sem as bibliotecas que
		// prejudicam a geração do PDF
		parametros.responsive = acao;
		parametros.fixedHeader = acao;
		parametros.autoWidth = acao;
		parametros.bAutoWidth = acao;
		
		$tabela.DataTable(parametros);
		$conteiner_tabela.attr('data-instanciado', 'true');
	});
}

tabela.atualizarLargura = function(seletor_tabela, escopo){
	var busca;
	if(!escopo) escopo = 'body';
	if(seletor_tabela){
		busca = $(escopo).find(seletor_tabela);
	} else {
		busca = $(escopo).find("div.tabelaaberta");
	}
	
	busca.each(function(){
		var $conteiner_tabela = $(this);
		var $tabela = $conteiner_tabela.find('table');
		
		var largura_conteiner = $conteiner_tabela.width();
		
		$tabela.css('width', largura_conteiner);
	});
}

tabela.obterDadosOrdenacao = function(seletor_tabela){
	var $conteiner_tabela;
	if(typeof seletor_tabela == 'undefined'){
		$conteiner_tabela = $('div.tabelaaberta').first();
	} else {
		$conteiner_tabela = $('#' + seletor_tabela);
	}
	var $tabela = $conteiner_tabela.find('table.dataTable');
	var objeto_tabela = $tabela.DataTable();
	return objeto_tabela.order();
}

/**
 *  Plug-in offers the same functionality as `full_numbers` pagination type 
 *  (see `pagingType` option) but without ellipses.
 *
 *  See [example](http://www.gyrocode.com/articles/jquery-datatables-pagination-without-ellipses) for demonstration.
 *
 *  @name Full Numbers - No Ellipses
 *  @summary Same pagination as 'full_numbers' but without ellipses
 *  @author [Michael Ryvkin](http://www.gyrocode.com)
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable( {
 *            "pagingType": "full_numbers_no_ellipses"
 *        } );
 *    } );
 */

$.fn.DataTable.ext.pager.full_numbers_no_ellipses = function(page, pages){
	var numbers = [];
	var buttons = $.fn.DataTable.ext.pager.numbers_length;
	var half = Math.floor( buttons / 2 );
	var _range = function ( len, start ){
		var end;
		if ( typeof start === "undefined" ){ 
			start = 0;
			end = len;
		} else {
			end = start;
			start = len;
		}
		var out = []; 
		for ( var i = start ; i < end; i++ ){ out.push(i); }
		return out;
	};
	if ( pages <= buttons ) {
		numbers = _range( 0, pages );
	} else if ( page <= half ) {
		numbers = _range( 0, buttons);
	} else if ( page >= pages - 1 - half ) {
		numbers = _range( pages - buttons, pages );
	} else {
		numbers = _range( page - half, page + half + 1);
	}
	numbers.DT_el = 'span';
	return [ 'first', 'previous', numbers, 'next', 'last' ];
};

/* Detecção automática de tipos de dados adicionais */
jQuery.fn.dataTableExt.aTypes.unshift(function ( sData ){
	// Verifica se a coluna contém datas em formato brasileiro
	if(sData !== null){
		// Apenas data
		if( moment(sData, 'DD/MM/YYYY', true).isValid() ){
			return 'data-br';
		}
		// Data e hora, com ou sem segundos
		if( moment(sData, 'DD/MM/YYYY - HH:mm', true).isValid() || moment(sData, 'DD/MM/YYYY - HH:mm:ss', true).isValid() ){
			return 'data-hora-br';
		}
	}
	
	// Se não bater nada, retornar nulo.
	// Isso fará o componente testar o valor para textos ou números.
	return null;
});

/* Plugins de ordenação customizada */
jQuery.extend(jQuery.fn.dataTableExt.oSort, {
	// "data-br": para data em formato brasileiro (01/01/1900)
	"data-br-pre": function ( a ) {
		if (a == null || a == "") {
			return 0;
		}
		var brDatea = a.split('/');
		return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
	},
	"data-br-asc": function ( a, b ) {
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},
	"data-br-desc": function ( a, b ) {
		return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	},
	
	// "data-hora-br": para data e hora em formato brasileiro ("01/02/1903 - 04:05" ou "06/07/1908 - 09:10:20")
	"data-hora-br-pre": function ( a ) {
		var x;

		if ( $.trim(a) !== '' ) {
			var brDatea = $.trim(a).split('-');
			var brTimea = (undefined != brDatea[1]) ? $.trim( brDatea[1] ).split(':') : [00,00,00];
			var brDatea2 = $.trim( brDatea[0] ).split('/');
			x = (brDatea2[2] + brDatea2[1] + brDatea2[0] + brTimea[0] + brTimea[1] + ((undefined != brTimea[2]) ? brTimea[2] : 0)) * 1;
		} else {
			x = Infinity;
		}

		return x;
	},
	"data-hora-br-asc": function ( a, b ) {
		return a - b;
	},
	"data-hora-br-desc": function ( a, b ) {
		return b - a;
	},
	
	// "dia-extenso": para dias escritos por extenso (1 dia, 5 dias, etc)
	"dia-extenso-pre": function ( a ) {
		a = a.replace(/dia(s)*/g, '');
		a = a.replace(/ /g, '');
		
		var dias = parseInt(a, 10);
		
		if(isNaN(dias)) dias = 0;
		
		return dias;
	},
	"dia-extenso-asc": function ( a, b ) {
		return a - b;
	},
	"dia-extenso-desc": function ( a, b ) {
		return b - a;
	},
	
	// "hora-extenso": para horários escritas por extenso (8 horas e 10 minutos, 7 horas e 50 minutos, etc)
	"hora-extenso-pre": function ( a ) {
		a = a.replace(/hora(s)*/g, '');
		a = a.replace('e', '|');
		a = a.replace(/minuto(s)*/g, '');
		a = a.replace(/ /g, '');
		a = a.split('|');
		
		var horas = parseInt(a[0], 10);
		var minutos = parseInt(a[1], 10);
		
		if(isNaN(horas)) horas = 0;
		if(isNaN(minutos)) minutos = 0;
		
		return (horas + (minutos / 60));
	},
	"hora-extenso-asc": function ( a, b ) {
		return a - b;
	},
	"hora-extenso-desc": function ( a, b ) {
		return b - a;
	}
});