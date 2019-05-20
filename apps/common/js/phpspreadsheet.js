/* Plugin de geração de planilhas XLS, com o componente PHPSpreadsheet
 * 
 * Dependências:
 * - jszip.min.js
 * - base64.min.js
 * - jQuery Alerts
 * - Funções adicionais do arquivo "funcoes.js":
 *   - abrirPagina
 *   - gE
 *   - getTamanhoObjeto
 *   - trocaColspanTotal
 */

function phpspreadsheet(){
	
	// Propriedades
	this.titulo = 'Planilha';
	this.subtitulo = '';
	this.elemento_tabela = gE('tabela');
	this.nome_arquivo = '';
	this.xhr = '';
	this.barra_progresso = '';
	this.coordenadas_linhas_tabela = {
		'titulo_subtitulo': [],
		'cabecalho': [],
		'corpo': [],
		'rodape': []
	};
	this.classes_celulas = {};
	
	// Métodos	
	this.gerar = function(botao, callback){
		var $botao = $(botao);

		var titulo = $botao.attr('data-titulo');
		var subtitulo = $botao.attr('data-subtitulo');
		var id_tabela = $botao.attr('data-tabela');
		var nome_arquivo = $botao.attr('data-nome-arquivo');
		
		mostraCarregando(true, false);
		setTimeout(() => {
			this.salvarPropriedades(titulo, subtitulo, id_tabela, nome_arquivo);
			
			ocultaCarregando();
			this.enviarDadosServidorViaArquivo(callback);
		}, 250);
	}
	
	this.salvarPropriedades = function(titulo, subtitulo, id_tabela, nome_arquivo){
		if(typeof titulo != 'undefined') this.titulo = titulo;
		if(typeof subtitulo != 'undefined') this.subtitulo = subtitulo;
		if(typeof id_tabela != 'undefined'){
			this.elemento_tabela = gE(id_tabela);
			if(typeof this.elemento_tabela == 'undefined'){
				this.elemento_tabela = gE('tabela');
			}
		}
		if(typeof nome_arquivo != 'undefined') this.nome_arquivo = nome_arquivo;
	}
	
	this.enviarDadosServidorViaArquivo = function(callback){
		var that = this;
		
		// Realizando operações antes da geração do relatório
		this.formatarPagina();
		
		// Obtendo os números das linhas da tabela, para determinar quais
		// são de cabeçalho, corpo e rodapé
		this.obterNumerosLinhasTabela();
		
		// Obtendo classes de estilização das células da tabela, para posteriormente
		// enviá-las ao servidor, na geração da planilha
		this.obterClassesEstilizacaoCelulasTabela();

		// Instanciação do componente de andamento
		this.instanciarComponenteAndamento();

		// Gerando nome do arquivo HTML, baseado no timestamp atual
		var timestamp_atual = Date.now();
		var nome_arquivo_html = 'planilha_' + timestamp_atual + '.html';

		// Gerando arquivo ZIP, através da biblioteca JSZip
		var zip = new JSZip();

		// Criando, dentro do ZIP, um arquivo HTML contendo a tabela selecionada
		zip.file(nome_arquivo_html, (this.elemento_tabela).innerHTML);

		// Gerando arquivo de forma assíncrona
		zip.generateAsync({type:"blob"}).then((arquivo) => {
			// Criando objeto de requisição ajax, para envio do arquivo ZIP gerado
			this.xhr = new XMLHttpRequest();

			// Eventos do objeto de requisição ajax
			this.xhr.upload.onerror = function(e){
				that.encerrarAndamento(false, 'Erro ao gerar planilha.');
				if(callback) callback(false);
			}
			this.xhr.upload.onprogress = function(e){
				// Atualizar barra de progresso no modal
				var valor = parseInt(((e.loaded * 100) / e.total), 10);
				that.atualizarAndamento(valor);
			}
			this.xhr.onreadystatechange = function(){
				if (that.xhr.readyState == 4 && that.xhr.status == 200) {
					var resposta = that.xhr.responseText;
					var info_arquivo_servidor;
					try{
						info_arquivo_servidor = $.parseJSON(resposta);
					} catch(e){
						info_arquivo_servidor = [{"error": "1", "error_msg": "Não foi possível obter retorno do servidor!"}];
					}
					var erro_xhr = (info_arquivo_servidor[0]['error'] == "1");
					if(!erro_xhr){
						var nome_servidor = atob(info_arquivo_servidor[0]['name']);
						var caminho_servidor = atob(info_arquivo_servidor[0]['tmp_name']);

						chamarPagina('../common/relatorios/phpspreadsheet_avisos_modal.html', '', function(html){
							var parametros = 'titulo=' + that.titulo + '&subtitulo=' + that.subtitulo;
							parametros += '&arquivo[nome]=' + nome_servidor + '&arquivo[caminho]=' + caminho_servidor;
							parametros += '&' + decodeURIComponent( $.param({'coordenadas_linhas_tabela': that.coordenadas_linhas_tabela}) );
							if(getTamanhoObjeto(that.classes_celulas) > 0){
								parametros += '&' + decodeURIComponent( $.param({'classes_celulas': that.classes_celulas}) );
							}
							console.log(parametros);
							abrirPagina('../common/relatorios/gerar_xls.php', parametros, '_blank');

							var $html = $(html);
							$html.find('a.link_gerar_xls').click(function(e){
								e.preventDefault();
								abrirPagina('../common/relatorios/gerar_xls.php', parametros, '_blank');
							});
							that.encerrarAndamento(true, $html);
							
							if(callback) callback(true);
						}, function(r){
							that.encerrarAndamento(false, 'Erro ao gerar planilha.<br />' + r);
							if(callback) callback(false);
						});
					} else {
						var mensagem = info_arquivo_servidor[0]['error_msg'];
						that.encerrarAndamento(false, 'Erro ao gerar planilha.<br />' + mensagem);
						if(callback) callback(false);
					}
				}
			}

			// Parâmetros da requisição, referente ao upload do arquivo zipado ao servidor
			this.xhr.open("POST", "upload_arquivo.php", true);
			var formData = new FormData();
			formData.append('arquivo', arquivo, that.nome_arquivo + '.zip');
			formData.append("name", 'arquivo');
			formData.append("tamanho_limite", 10485760);

			// Disparando requisição ao servidor
			this.xhr.send(formData);

			// Desfazendo operações feitas antes da geração do relatório
			this.desformatarPagina();
		});
	}
	
	this.instanciarComponenteAndamento = function(){
		this.barra_progresso = $("<div />").append(
			$("<h4 />").html('Preparando dados da planilha...')
		).append(
			$("<div />").addClass("progress").css('marginBottom', '10px').html(
				$("<div />").addClass("progress-bar progress-bar-striped progress-bar-animated").attr({
					'role': 'progressbar',
					'aria-valuenow': '0',
					'aria-valuemin': '0',
					'aria-valuemax': '100',
				}).css('width', '0%').html('0%')
			)
		).append(
			$('<div />').addClass('text-center').html(
				$('<button />').attr('type', 'button').addClass('btn btn-secondary').html('Cancelar').on('click.cancelar', this.cancelarGeracao)
			)
		);

		jModalLocal(this.barra_progresso, 'Geração da Planilha', function(){}, true, false);
	}

	this.atualizarAndamento = function(valor, rotulo){
		var $barraProgresso = this.barra_progresso;
		var $divBarraProgresso = $barraProgresso.find('div.progress-bar');

		if(typeof valor == 'undefined') valor = 0;
		var porcentagem = valor + '%';
		if(typeof rotulo == 'undefined') rotulo = porcentagem;
		
		$divBarraProgresso.attr('aria-valuenow', valor).css('width', porcentagem).html(rotulo);
	}

	this.encerrarAndamento = function(sucesso, msg){
		if(typeof sucesso == 'undefined') sucesso = true;
		var $barraProgresso = this.barra_progresso;
		var $h4 = $barraProgresso.find('h4');
		var $divBarraProgresso = $barraProgresso.find('div.progress-bar');
		var $botao = $barraProgresso.find('button');
		var porcentagem;
		if(sucesso){
			porcentagem = 100;
		} else {
			porcentagem = 0;
		}

		this.atualizarAndamento(porcentagem);
		$divBarraProgresso.removeClass('progress-bar-striped progress-bar-animated');

		$h4.html(msg);
		$botao.html('Fechar').off('click.cancelar').on('click.fechar', function(){
			jModalRemove();
		});
	}

	this.cancelarGeracao = function(){
		if(this.xhr != ''){
			this.xhr.abort();
		}
		jModalRemove();
	}
	
	this.limparCoordenadasLinhasTabela = function(){
		this.coordenadas_linhas_tabela = {
			'titulo_subtitulo': [],
			'cabecalho': [],
			'corpo': [],
			'rodape': []
		};
	}
	
	this.obterNumerosLinhasTabela = function(){
		var $tabela = $(this.elemento_tabela);
		
		var that = this;
		
		that.limparCoordenadasLinhasTabela();
		
		$tabela.children('thead, tbody, tfoot').children('tr').each(function(i){
			var $pai = $(this).parent();
			
			var numero = (i + 1);
			
			if($pai.is('thead')){
				// Título, Subtitulo ou cabeçalho
				if(numero == 1 || numero == 2){
					// Título / Subtítulo
					that.coordenadas_linhas_tabela.titulo_subtitulo.push(numero);
				} else {
					// Cabeçalho
					that.coordenadas_linhas_tabela.cabecalho.push(numero);
				}
			} else if($pai.is('tbody')){
				// Corpo
				that.coordenadas_linhas_tabela.corpo.push(numero);
			} else {
				// Rodapé
				that.coordenadas_linhas_tabela.rodape.push(numero);
			}
		});
		
		for(var i in this.coordenadas_linhas_tabela){
			this.coordenadas_linhas_tabela[i] = this.coordenadas_linhas_tabela[i].join();
		}
	}
	
	this.formatarPagina = function(){
		var $tabela = $(this.elemento_tabela);
		var $thead = $tabela.children('thead');

		// Criando caption movida e jogando-a no cabeçalho da tabela
		var $trTitulo = $('<tr />').addClass('titulo').html(
			$('<th />').attr('colspan', '100%').html(this.titulo)
		);
		var $trSubtitulo = $('<tr />').addClass('subtitulo').html(
			$('<th />').attr('colspan', '100%').html(this.subtitulo)
		);
		$thead.prepend($trSubtitulo).prepend($trTitulo);
		trocaColspanTotal($trTitulo.children('th'));
		trocaColspanTotal($trSubtitulo.children('th'));
		
		
	}

	this.desformatarPagina = function(){
		var $tabela = $(this.elemento_tabela);
		var $thead = $tabela.children('thead');
		
		$thead.children('tr.titulo, tr.subtitulo').remove();
	}
	
	this.obterClassesEstilizacaoCelulasTabela = function(){
		var $tabela = $(this.elemento_tabela);
		var $thead = $tabela.children('thead');
		var $tbody = $tabela.children('tbody');
		var $tfoot = $tabela.children('tfoot');
		
		var that = this;
		
		$thead.add($tbody).add($tfoot).children('tr').each(function(){
			var $tr = $(this);
			
			$tr.children('td, th').each(function(){
				var $thd = $(this);
				
				var coordenadas_coluna = that.obterCoordenadasCelulaTabela(this);
				
				if($thd.is('[data-phpspreadsheet-classe]')){
					var classe = $thd.attr('data-phpspreadsheet-classe');
					
					that.classes_celulas[coordenadas_coluna] = classe;
				}
			})
		})
	}
	
	this.obterCoordenadasCelulaTabela = function(elem){
		var $td = $(elem).closest("td, th");
		var coluna = 0;
		$td.prevAll().each(function () {
			coluna += $(this).prop('colspan');
		});
		var linha = $td.closest("tr").index();
		var $conteiner_pai = $td.closest('thead, tbody, tfoot');
		var $celulas_rowspan = $conteiner_pai.find("td[rowspan], th[rowspan]");
		$celulas_rowspan.each(function () {
			var $thd = $(this);
			var indice = $thd.closest("tr").index();
			var rowspan = $thd.prop("rowspan");
			if (linha > indice && linha <= indice + rowspan - 1) {
				var rsCol = 0;
				$thd.prevAll().each(function () {
					rsCol += $(this).prop('colspan');
				});
				if (rsCol <= coluna) coluna += $thd.prop('colspan');
			}
		});
		
		var $thead, $tbody;
		var total_linhas_cabecalho = 0;
		var total_linhas_corpo = 0;
		
		linha++;
		if($td.closest('tbody').length > 0){
			$thead = $td.closest('table').children('thead');
			
			total_linhas_cabecalho = $thead.children('tr').length;
			
			linha += total_linhas_cabecalho;
		} else if($td.closest('tfoot').length > 0){
			$thead = $td.closest('table').children('thead');
			$tbody = $td.closest('table').children('tbody');
			
			total_linhas_corpo = $tbody.children('tr').length;
			
			linha += total_linhas_corpo;
		}
		linha = linha.toString();
		coluna = String.fromCharCode(97 + coluna).toUpperCase();

		return (coluna + linha);
	}
}

// Instanciando objeto da classe acima
var phpspreadsheet = new phpspreadsheet();