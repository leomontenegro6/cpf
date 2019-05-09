/* Biblioteca javascript englobando funções específicas do
 * relatório de Orçamento de Manutenção
 */

function orcamentoManutencao(){
	
	// Propriedades
	this.conteinerTabelaOrcamentoManutencao = $();
	this.tabelaOrcamentoManutencao = $();
	this.parametros = {
		'metodoEstimativaPrazo': 'e',
		'recursos': 1,
		'tempoDedicacao': 4,
		'indiceProdutividade': 0.6,
		'expoenteCapersJones': 0.36,
		'metodoCalculoOrcamento': 'vht',
		'valorPontoFuncao': 1061.00,
		'valorHoraTrabalhada': 40.00,
		'percentualReducao': 100,
		'formatoTempo': 'hhm',
		'arredondarZerosParaCima': false
	};
	this.templateLinhaTabela = '';
	
	// Métodos
	this.carregarRotinasPrincipais = function(){
		this.conteinerTabelaOrcamentoManutencao = $('#tabela_orcamento_manutencao');
		this.tabelaOrcamentoManutencao = this.conteinerTabelaOrcamentoManutencao.find('table');
		
		this.salvarParametros();
		
		chamarPagina('rel_orcamento_manutencao_tabela_linha.html', '', (r) => {
			this.templateLinhaTabela = r;
		})
	}
	
	this.salvarParametros = function(){
		var recursos = parseInt($('#recursos_lista').val(), 10);
		var tempoDedicacao = parseInt($('#tempo_dedicacao_lista').val(), 10);
		var indiceProdutividade = parseFloat($('#indice_produtividade_lista').val());
		var expoenteCapersJones = parseFloat($('#expoente_capers_jones_lista').val());
		var valorPontoFuncao = decodeMonetario($('#valor_ponto_funcao_lista').val());
		var valorHoraTrabalhada = decodeMonetario($('#valor_hora_trabalhada_lista').val());
		var percentualReducao = parseFloat($('#percentual_reducao').val());
		
		if(isNaN(recursos)) recursos = 0;
		if(isNaN(tempoDedicacao)) tempoDedicacao = 0;
		if(isNaN(indiceProdutividade)) indiceProdutividade = 0;
		if(isNaN(expoenteCapersJones)) expoenteCapersJones = 0;
		if(isNaN(valorPontoFuncao)) valorPontoFuncao = 0;
		if(isNaN(valorHoraTrabalhada)) valorHoraTrabalhada = 0;
		if(isNaN(percentualReducao)) percentualReducao = 0;
		
		this.parametros.metodoEstimativaPrazo = $('#metodo_estimativa_prazo_lista').val();
		this.parametros.recursos = recursos;
		this.parametros.tempoDedicacao = tempoDedicacao;
		this.parametros.indiceProdutividade = indiceProdutividade;
		this.parametros.expoenteCapersJones = expoenteCapersJones;
		this.parametros.metodoCalculoOrcamento = $('#metodo_calculo_orcamento_lista').val();
		this.parametros.valorPontoFuncao = valorPontoFuncao;
		this.parametros.valorHoraTrabalhada = valorHoraTrabalhada;

		this.parametros.percentualReducao = percentualReducao;
		this.parametros.formatoTempo = $('#formato_tempo').val();
		this.parametros.arredondarZerosParaCima = $('#arredondar_zeros').is(':checked');
	}
	
	this.configurarRelatorio = function(selectSistema){
		var $selectSistema = $(selectSistema);
		var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
		var $tbody = $tabelaOrcamentoManutencao.children('tbody');
		
		var opcao_anterior = select.obterOpcaoAnterior($selectSistema);
		var opcao_atual = $selectSistema.val();
		var metadados_opcao_atual = select.obterMetadadosOpcaoSelecionada($selectSistema);
		var nome_sistema = metadados_opcao_atual['text'];
		
		var checkPeloMenosUmaFuncionalidadeAdicionada = ($tbody.children('tr').length > 0);
		
		if(opcao_anterior == '' && opcao_atual != ''){
			// Escolhendo o sistema pela primeira vez, então exibir a tabela e
			// atualizar funcionalidades na aba "Alterações / Exclusões"
			this.toggleVisibilidadeTabela(true, nome_sistema);
			this.atualizarFuncionalidadesAbaAlteracoesExclusoes(opcao_atual);
		} else if(opcao_anterior != '' && opcao_atual != ''){
			// Mudando de um sistema pra outro, e já consta pelo menos uma
			// funcionalidade na tabela. Logo pedir confirmação final do
			// usuário, antes de confirmar a operação, antes de limpar a tabela e atualizar as funcionalidades
			// na aba "Alterações / Exclusões"
			if(checkPeloMenosUmaFuncionalidadeAdicionada){
				jConfirm('Há pelo menos uma funcionalidade na tabela. Se prosseguir, alterações serão perdidas.<br /><br />Tem certeza que quer continuar?', undefined, (r) => {
					if(r){
						// Usuário confirmou a operação, logo limpar a tabela e
						// atualizar as funcionalidades na aba "Alterações / Exclusões"
						this.limparTabela();
						this.toggleVisibilidadeTabela(true, nome_sistema);
						this.atualizarFuncionalidadesAbaAlteracoesExclusoes(opcao_atual);
					} else {
						// Usuário cancelou a operação, logo voltar campo "Sistema"
						// para a sua opção anterior
						select.setarValor($selectSistema, opcao_anterior);
					}
				});
			} else {
				// Nenhuma funcionalidade na tabela, logo limpá-la e atualizar as
				// funcionalidades na aba "Alterações / Exclusões"
				this.limparTabela();
				this.toggleVisibilidadeTabela(true, nome_sistema);
				this.atualizarFuncionalidadesAbaAlteracoesExclusoes(opcao_atual);
			}
		} else if(opcao_anterior != '' && opcao_atual == ''){
			// Limpando o campo, logo ocultar a tabela e limpar as funcionalidades
			// exibidas na aba "Alterações / Exclusões"
			this.toggleVisibilidadeTabela(false);
			this.limparFuncionalidadesAbaAlteracoesExclusoes();
		}
	}
	
	this.validaSistema = function(){
		var $selectSistema = $('#sistema_lista');
		var $labelSelectSistema = $selectSistema.parent();
		
		var id_sistema = $selectSistema.val();
		
		if(id_sistema == ''){
			$labelSelectSistema.addClass('was-validated');
			$selectSistema.next().after(
				$('<div />').addClass('invalid-feedback').html('Escolha o sistema antes.')
			);
			
			return false;
		} else {
			$labelSelectSistema.removeClass('was-validated');
			$selectSistema.siblings('div.invalid-feedback').remove();
			
			return true;
		}
	}
	
	this.toggleVisibilidadeTabela = function(exibir, nome_sistema){
		if(typeof exibir == 'undefined') exibir = true;
		
		var $conteinerTabelaOrcamentoManutencao = this.conteinerTabelaOrcamentoManutencao;
		var $spanNomeSistema = $('#nome_sistema');
		
		if(exibir){
			$conteinerTabelaOrcamentoManutencao.show();
			$spanNomeSistema.html(nome_sistema);
		} else {
			$conteinerTabelaOrcamentoManutencao.hide();
			$spanNomeSistema.html('...');
		}
	}
	
	this.limparTabela = function(){
		var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
		var $tbody = $tabelaOrcamentoManutencao.children('tbody');
		
		return $tbody.html('');
	}
	
	this.atualizarFuncionalidadesAbaAlteracoesExclusoes = function(id_sistema){
		var $divFuncionalidadesAbaAlteracoesExclusoes = $('#funcionalidades_alteracoes_exclusoes');
		
		chamarPagina('rel_orcamento_manutencao_funcionalidade_preenche.php?sistema=' + id_sistema, '', function(r){
			$divFuncionalidadesAbaAlteracoesExclusoes.html(r);
		});
	}
	
	this.limparFuncionalidadesAbaAlteracoesExclusoes = function(){
		var $divFuncionalidadesAbaAlteracoesExclusoes = $('#funcionalidades_alteracoes_exclusoes');
		$divFuncionalidadesAbaAlteracoesExclusoes.html('');
	}
	
	this.calcularValorComponenteFuncionalidade = function(elemento){
		var $tr = $(elemento).closest('tr');
		var $selectTipoComponente = $tr.find("[name$='[tipo_componente]']");
		var $opcaoSelecionada = $selectTipoComponente.find(':selected');
		var $checkboxPossuiAcoes = $tr.find("[name$='[possui_acoes]']");
		var $checkboxPossuiMensagens = $tr.find("[name$='[possui_mensagens]']");
		var $campoCampos = $tr.find("[name$='[campos]']");
		var $campoArquivosReferenciados = $tr.find("[name$='[arquivos_referenciados]']");
		var $divValorPF = $tr.find('div.valor_pf');
		
		// Obtendo parâmetros adicionais
		var tipo_funcional = $opcaoSelecionada.attr('data-alias');
		var quantidade_total_tipos_dados = 0, quantidade_total_arquivos_referenciados = 0;
		
		// Caso algum dos checkboxes "possui ações" ou "possui mensagens" estiver
		// marcado, incrementar em um a quantidade total de tipos de dados
		if($checkboxPossuiAcoes.is(':checked')) quantidade_total_tipos_dados++;
		if($checkboxPossuiMensagens.is(':checked')) quantidade_total_tipos_dados++;
		
		// Contando a quantidade de campos
		var quantidade_campos = parseInt($campoCampos.val(), 10);
		if(!isNaN(quantidade_campos) && quantidade_campos > 0){
			// Quantidade válida, logo somá-la à variável da quantidade total
			// de tipos de dados.
			quantidade_total_tipos_dados += quantidade_campos;
		} else {
			// Quantidade inválida, logo zerar a variável da
			// quantidade total de tipos de dados
			quantidade_total_tipos_dados = 0;
		}
		
		// Contando a quantidade de arquivos referenciados
		var quantidade_arquivos_referenciados = parseInt($campoArquivosReferenciados.val(), 10);
		if(!isNaN(quantidade_arquivos_referenciados) && quantidade_arquivos_referenciados > 0){
			// Quantidade válida, logo somá-la à variável da quantidade total
			// de arquivos referenciados.
			quantidade_total_arquivos_referenciados += quantidade_arquivos_referenciados;
		} else {
			// Quantidade inválida, logo zerar a variável da
			// quantidade total de arquivos referenciados
			quantidade_total_arquivos_referenciados = 0;
		}
		
		// Calculando complexidade e valor do componente (em pontos de função)
		var complexidade = cpf.calcularComplexidade(tipo_funcional, quantidade_total_tipos_dados, quantidade_total_arquivos_referenciados);
		var valor = cpf.calcularValor(tipo_funcional, complexidade);
		if(valor == '') valor = '---';
		
		// Exibindo valor da funcionalidade no formulário
		$divValorPF.html(valor);
	}
	
	this.limparValoresComponenteFuncionalidade = function(){
		var $camposComponentes = $('#campos_componentes');
		
		return $camposComponentes.find('div.valor_pf').html('---');
	}
	
	this.calcularTempoDesenvolvimento = function(valor_pf){
		var parametros = this.parametros;
		var metodo_estimativa_prazo = parametros.metodoEstimativaPrazo;
		var recursos = parametros.recursos;
		var tempo_dedicacao = parametros.tempoDedicacao;
		var indice_produtividade = parametros.indiceProdutividade;
		var expoente_capers_jones = parametros.expoenteCapersJones;
		var percentual_reducao = parametros.percentualReducao;
		var formato_tempo = parametros.formatoTempo;
		
		var tempo;
		if(metodo_estimativa_prazo == 'e'){
			tempo = cpf.calcularTempoDesenvolvimentoPorEstimativaEsforco(valor_pf, recursos, tempo_dedicacao, indice_produtividade, formato_tempo);
			tempo = (tempo * percentual_reducao) / 100;
		} else {
			tempo = cpf.calcularTempoDesenvolvimentoPorFormulaCapersJones(valor_pf, expoente_capers_jones, formato_tempo);
		}
		
		return tempo;
	}
	
	this.formatarTempo = function(tempo){
		var checkArredondarZerosParaCima = this.parametros.arredondarZerosParaCima;
		var formato_tempo = this.parametros.formatoTempo;
		
		return cpf.encodarTempoByFormato(tempo, formato_tempo, checkArredondarZerosParaCima);
	}
	
	this.calcularCustoDesenvolvimento = function(valor_pf){
		var parametros = this.parametros;
		var metodo_estimativa_prazo = parametros.metodoEstimativaPrazo;
		var metodo_calculo_orcamento = parametros.metodoCalculoOrcamento
		var valor_ponto_funcao = parametros.valorPontoFuncao;
		var valor_hora_trabalhada = parametros.valorHoraTrabalhada;
		var formato_tempo = parametros.formatoTempo;
		
		var tempo = this.calcularTempoDesenvolvimento(valor_pf);
		var custo;
		if(metodo_estimativa_prazo == 'e'){
			custo = cpf.calcularCustoDesenvolvimentoPorEstimativaEsforco(metodo_calculo_orcamento, tempo, valor_ponto_funcao, valor_hora_trabalhada, formato_tempo);
		} else {
			var quantidade_pontos_funcao = valor_pf;
			custo = cpf.calcularCustoDesenvolvimentoPorFormulaCapersJones(metodo_calculo_orcamento, quantidade_pontos_funcao, valor_ponto_funcao, valor_hora_trabalhada);
		}
		
		return custo;
	}
	
	this.incluirFuncionalidade = function(formInclusaoFuncionalidades){
		if(this.validaSistema() && validaForm(formInclusaoFuncionalidades, false)){
			var $formInclusaoFuncionalidades = $(formInclusaoFuncionalidades);
			var $campoModulo = $formInclusaoFuncionalidades.find("[name='modulo_lista']");
			var $campoFuncionalidade = $formInclusaoFuncionalidades.find("[name='funcionalidade_lista']");
			var $camposComponentes = $('#campos_componentes');
			
			var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
			var $tbody = $tabelaOrcamentoManutencao.children('tbody');
			
			// Obtendo parâmetros da funcionalidade
			var that = this;
			var nome_modulo = $campoModulo.val();
			var nome_funcionalidade = $campoFuncionalidade.val();
			var iterador_funcionalidades = parseInt($tabelaOrcamentoManutencao.attr('data-iterador-funcionalidades'), 10);
			var iterador_componentes = parseInt($tabelaOrcamentoManutencao.attr('data-iterador-componentes'), 10);
			var id_funcionalidade = 'if_' + iterador_funcionalidades;
			
			// Obtendo parâmetros dos componentes da funcionalidade
			var componentes = [];
			$camposComponentes.children('table').children('tbody').children('tr').each(function(){
				var $tr = $(this);
				var $selectTipoComponente = $tr.find("[name$='[tipo_componente]']");
				var $checkboxPossuiAcoes = $tr.find("[name$='[possui_acoes]']");
				var $checkboxPossuiMensagens = $tr.find("[name$='[possui_mensagens]']");
				var $campoCampos = $tr.find("[name$='[campos]']");
				var $campoArquivosReferenciados = $tr.find("[name$='[arquivos_referenciados]']");
				var $divValorPF = $tr.find('div.valor_pf');
				
				var id_componente = 'ic_' + iterador_componentes;
				var metadados_tipo_componente = select.obterMetadadosOpcaoSelecionada($selectTipoComponente);
				var nome_tipo_componente = $.trim( metadados_tipo_componente['text'] );
				var valor_pf_original = parseInt($divValorPF.html(), 10);
				var valor_pf_ajustado = valor_pf_original;
				var tempo = that.calcularTempoDesenvolvimento(valor_pf_ajustado);
				var custo = that.calcularCustoDesenvolvimento(valor_pf_ajustado);
				
				componentes.push({
					'id': id_componente,
					'componente': nome_tipo_componente,
					'tipo_manutencao': 'Inclusão',
					'possui_acoes': $checkboxPossuiAcoes.is(':checked'),
					'possui_mensagens': $checkboxPossuiMensagens.is(':checked'),
					'campos': parseInt($campoCampos.val(), 10),
					'arquivos_referenciados': parseInt($campoArquivosReferenciados.val(), 10),
					'valor_pf_original': valor_pf_original,
					'valor_pf_ajustado': valor_pf_ajustado,
					'tempo': tempo,
					'custo': custo
				});
				
				iterador_componentes++;
			});
			
			// Renderizando template da linha, em função dos parâmetros fornecidos
			var funcionalidade = {
				'id': id_funcionalidade,
				'modulo': nome_modulo,
				'funcionalidade': nome_funcionalidade,
				'componentes': componentes
			};
			var template = $.templates(this.templateLinhaTabela);
			var trs = template.render(funcionalidade, true);

			// Adicionando, na tabela, a linha da funcionalidade e do primeiro componente
			$tbody.append(trs);

			// Atualizando iteradores de funcionalidades e componentes, de modo a
			// garantir a unicidade de IDs para uso posterior
			iterador_funcionalidades++;
			$tabelaOrcamentoManutencao.attr('data-iterador-funcionalidades', iterador_funcionalidades);
			$tabelaOrcamentoManutencao.attr('data-iterador-componentes', iterador_componentes);

			// Formatar valores de tempo e custo, nas últimas colunas da tabela
			this.formatarValoresCorpoTabela();

			// Atualizando totais no rodapé da tabela
			this.calcularTotaisRodapeTabela();

			// Resetando formulário após a inclusão da funcionalidade
			$formInclusaoFuncionalidades.trigger('reset');
			this.limparValoresComponenteFuncionalidade();
		}
		
		return false;
	}
	
	this.toggleFuncionalidadesFilhasModulo = function(checkboxModulo){
		var $checkboxModulo = $(checkboxModulo);
		var $labelCheckboxModulo = $checkboxModulo.closest('label.checkbox-menu');
		var $checkboxesFuncionalidadesModulo = $labelCheckboxModulo.nextUntil('label.checkbox-menu.modulo').find("input[type='checkbox']");
		
		if($checkboxModulo.is(':checked')){
			$checkboxesFuncionalidadesModulo.prop('checked', true);
		} else {
			$checkboxesFuncionalidadesModulo.prop('checked', false);
		}
		
		this.atualizarTotalFuncionalidadesMarcadasParaAlteracaoOuExclusao();
	}
	
	this.toggleModuloPaiFuncionalidade = function(checkboxFuncionalidade){
		var $checkboxFuncionalidade = $(checkboxFuncionalidade);
		var $labelCheckboxFuncionalidade = $checkboxFuncionalidade.closest('label.checkbox-menu');
		var $labelCheckboxModulo = $labelCheckboxFuncionalidade.prevUntil('label.checkbox-menu.modulo').last().prev();
		if($labelCheckboxModulo.length == 0) $labelCheckboxModulo = $labelCheckboxFuncionalidade.prev();
		var $checkboxModulo = $labelCheckboxModulo.find("input[type='checkbox']");
		
		if($checkboxFuncionalidade.is(':checked')){
			$checkboxModulo.prop('checked', true);
		}
		
		this.atualizarTotalFuncionalidadesMarcadasParaAlteracaoOuExclusao();
	}
	
	this.atualizarTotalFuncionalidadesMarcadasParaAlteracaoOuExclusao = function(){
		var $divFuncionalidadesAlteracoesExclusoes = $('#funcionalidades_alteracoes_exclusoes');
		var $divAcoesLoteFuncionalidadesMarcadas = $('div.acoes_lote_funcionalidades_marcadas');
		var $botaoAcoesLoteFuncionalidadesMarcadas = $divAcoesLoteFuncionalidadesMarcadas.find('button.dropdown-toggle');
		var $spanTotalFuncionalidadesMarcadasAlteracaoExclusao = $botaoAcoesLoteFuncionalidadesMarcadas.children('span.total_funcionalidades_marcadas_alteracao_exclusao');
				
		var total_funcionalidades_selecionadas = $divFuncionalidadesAlteracoesExclusoes.find("input[name^='funcionalidades']").filter(':checked').length;
		var msg;
		if(total_funcionalidades_selecionadas > 0){
			if(total_funcionalidades_selecionadas > 1){
				msg = total_funcionalidades_selecionadas + ' funcionalidades selecionadas';
			} else {
				msg = total_funcionalidades_selecionadas + ' funcionalidade selecionadas';
			}
			$botaoAcoesLoteFuncionalidadesMarcadas.removeAttr('disabled');
		} else {
			msg = '---';
			$botaoAcoesLoteFuncionalidadesMarcadas.attr('disabled', 'disabled');
		}
		
		$spanTotalFuncionalidadesMarcadasAlteracaoExclusao.html(msg);
	}
	
	this.alterarExcluirFuncionalidadesMarcadas = function(tipo_manutencao, fator_impacto, evento){
		var $divFuncionalidadesAlteracoesExclusoes = $('#funcionalidades_alteracoes_exclusoes');
		var $checkboxesFuncionalidadesMarcadas = $divFuncionalidadesAlteracoesExclusoes.find("input[name^='funcionalidades']").filter(':checked');
		
		var that = this;
		
		$checkboxesFuncionalidadesMarcadas.each(function(){
			var $checkboxFuncionalidade = $(this);
			var $labelFuncionalidade = $checkboxFuncionalidade.closest('label.funcionalidade');
			var id_funcionalidade_banco = $checkboxFuncionalidade.val();
			var $inputNomeModulo = $labelFuncionalidade.find("[name^='funcionalidades[" + id_funcionalidade_banco + "][modulo]']");
			var $inputNomeFuncionalidade = $labelFuncionalidade.find("[name^='funcionalidades[" + id_funcionalidade_banco + "][nome]']");
			var $divInputsComponentes = $labelFuncionalidade.find('div.inputs_componentes');
			
			var $tabelaOrcamentoManutencao = that.tabelaOrcamentoManutencao;
			var $tbody = $tabelaOrcamentoManutencao.children('tbody');
			
			// Obtendo parâmetros da funcionalidade
			var nome_modulo = $inputNomeModulo.val();
			var nome_funcionalidade = $inputNomeFuncionalidade.val();
			var id_funcionalidade = (tipo_manutencao + 'f_' + id_funcionalidade_banco);
			
			// Obtendo parâmetros dos componentes da funcionalidade
			var componentes = [];
			$divInputsComponentes.each(function(){
				var $div = $(this);
				var $inputTipoComponente = $div.find("[name$='[tipo_componente]']");
				var $inputValorPFComponente = $div.find("[name$='[valor_pf]']");
				
				var id_componente_banco = $div.attr('data-id-componente-banco');
				var id_componente = (tipo_manutencao + 'c_' + id_componente_banco);
				var nome_tipo_componente = $inputTipoComponente.val();
				var nome_tipo_manutencao = ((tipo_manutencao == 'a') ? ('Alteração') : ('Exclusão')) + ' (' + fator_impacto + '%' + ')';
				var valor_pf_original = parseInt($inputValorPFComponente.val(), 10);
				var valor_pf_ajustado = ((valor_pf_original * fator_impacto) / 100);
				var tempo = that.calcularTempoDesenvolvimento(valor_pf_ajustado);
				var custo = that.calcularCustoDesenvolvimento(valor_pf_ajustado);
				
				componentes.push({
					'id': id_componente,
					'componente': nome_tipo_componente,
					'tipo_manutencao': nome_tipo_manutencao,
					'valor_pf_original': valor_pf_original,
					'valor_pf_ajustado': valor_pf_ajustado,
					'tempo': tempo,
					'custo': custo
				});
			})
			
			// Renderizando template da linha, em função dos parâmetros fornecidos
			var funcionalidade = {
				'id': id_funcionalidade,
				'modulo': nome_modulo,
				'funcionalidade': nome_funcionalidade,
				'componentes': componentes
			};
			var template = $.templates(that.templateLinhaTabela);
			var trs = template.render(funcionalidade, true);

			// Adicionando, na tabela, a linha da funcionalidade e do primeiro componente
			$tbody.append(trs);

			// Formatar valores de tempo e custo, nas últimas colunas da tabela
			that.formatarValoresCorpoTabela();

			// Atualizando totais no rodapé da tabela
			that.calcularTotaisRodapeTabela();

			// Resetando formulário após a inclusão da funcionalidade
			$divFuncionalidadesAlteracoesExclusoes.find("input[type='checkbox']").prop('checked', false);
			that.atualizarTotalFuncionalidadesMarcadasParaAlteracaoOuExclusao();
		})
		
		evento.preventDefault();
	}
	
	this.formatarValoresCorpoTabela = function(){
		var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
		var $tbody = $tabelaOrcamentoManutencao.children('tbody');
		
		var that = this;
		
		$tbody.children('tr').each(function(){
			var $tr = $(this);
			var $inputValorPFOriginal = $tr.find('input.valor_pf_original');
			var $inputValorPFAjustado = $tr.find('input.valor_pf_ajustado');
			var $inputTempo = $tr.find('input.tempo');
			var $inputCusto = $tr.find('input.custo');
			var $tdValorPFOriginal = $tr.children('td.valor_pf_original_formatado');
			var $tdValorPFAjustado = $tr.children('td.valor_pf_ajustado_formatado');
			var $tdTempo = $tr.children('td.tempo_formatado');
			var $tdCusto = $tr.children('td.custo_formatado');
			
			var valor_pf_original = parseFloat( $inputValorPFOriginal.val() );
			var valor_pf_ajustado = parseFloat( $inputValorPFAjustado.val() );
			var valor_tempo = parseFloat( $inputTempo.val() );
			var valor_custo = parseFloat( $inputCusto.val() );
			
			if(isFloat(valor_pf_original)) valor_pf_original = encodeMonetario( valor_pf_original );
			if(isFloat(valor_pf_ajustado)) valor_pf_ajustado = encodeMonetario( valor_pf_ajustado );
			valor_tempo = that.formatarTempo( valor_tempo );
			valor_custo = encodeMonetario( valor_custo );
			
			$tdValorPFOriginal.html(valor_pf_original);
			$tdValorPFAjustado.html(valor_pf_ajustado);
			$tdTempo.html(valor_tempo);
			$tdCusto.html(valor_custo);
		});
	}
	
	this.atualizarValoresCorpoTabela = function(){
		this.salvarParametros();
		
		var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
		var $tbody = $tabelaOrcamentoManutencao.children('tbody');
		
		var that = this;
		
		$tbody.children('tr').each(function(){
			var $tr = $(this);
			var $inputValorPFAjustado = $tr.find('input.valor_pf_ajustado');
			var $inputTempo = $tr.find('input.tempo');
			var $inputCusto = $tr.find('input.custo');
			
			var valor_pf_ajustado = parseInt($inputValorPFAjustado.val(), 10);
			var tempo = that.calcularTempoDesenvolvimento(valor_pf_ajustado);
			var custo = that.calcularCustoDesenvolvimento(valor_pf_ajustado);
			
			$inputTempo.val(tempo);
			$inputCusto.val(custo);
		});
		
		this.formatarValoresCorpoTabela();
		this.calcularTotaisRodapeTabela();
	}
	
	this.calcularTotaisRodapeTabela = function(){
		var $conteinerTabelaOrcamentoManutencao = this.conteinerTabelaOrcamentoManutencao;
		var $tabelaOrcamentoManutencao = this.tabelaOrcamentoManutencao;
		var $tbody = $tabelaOrcamentoManutencao.children('tbody');
		var $tfoot = $tabelaOrcamentoManutencao.children('tfoot');
		var $inputTotalValorPFOriginal = $conteinerTabelaOrcamentoManutencao.find('input.total_valor_pf_original');
		var $inputTotalValorPFAjustado = $conteinerTabelaOrcamentoManutencao.find('input.total_valor_pf_ajustado');
		var $inputTotalTempo = $conteinerTabelaOrcamentoManutencao.find('input.total_tempo');
		var $inputTotalCusto = $conteinerTabelaOrcamentoManutencao.find('input.total_custo');
		var $thTotalValorPFOriginal = $tfoot.find('th.total_valor_pf_original_formatado');
		var $thTotalValorPFAjustado = $tfoot.find('th.total_valor_pf_ajustado_formatado');
		var $thTotalTempo = $tfoot.find('th.total_tempo_formatado');
		var $thTotalCusto = $tfoot.find('th.total_custo_formatado');
		
		var totais = {
			'pfOriginal': 0,
			'pfAjustado': 0,
			'tempo': 0,
			'custo': 0
		};
		
		$tbody.children('tr').each(function(){
			var $tr = $(this);
			var $inputValorPFOriginal = $tr.find('input.valor_pf_original');
			var $inputValorPFAjustado = $tr.find('input.valor_pf_ajustado');
			var $inputTempo = $tr.find('input.tempo');
			var $inputCusto = $tr.find('input.custo');
			
			var valor_pf_original = parseFloat( $inputValorPFOriginal.val() );
			var valor_pf_ajustado = parseFloat( $inputValorPFAjustado.val() );
			var valor_tempo = parseFloat( $inputTempo.val() );
			var valor_custo = parseFloat( $inputCusto.val() );
			
			totais.pfOriginal += valor_pf_original;
			totais.pfAjustado += valor_pf_ajustado;
			totais.tempo += valor_tempo;
			totais.custo += valor_custo;
		})
		
		$inputTotalValorPFOriginal.val(totais.pfOriginal);
		$inputTotalValorPFAjustado.val(totais.pfAjustado);
		$inputTotalTempo.val(totais.tempo);
		$inputTotalCusto.val(totais.custo);
		
		if(totais.pfOriginal > 0){
			if(isFloat(totais.pfOriginal)) totais.pfOriginal = encodeMonetario( totais.pfOriginal );
		} else {
			totais.pfOriginal = '---';
		}
		if(totais.pfAjustado > 0){
			if(isFloat(totais.pfAjustado)) totais.pfAjustado = encodeMonetario( totais.pfAjustado );
		} else {
			totais.pfAjustado = '---';
		}
		if(totais.tempo > 0){
			totais.tempo = this.formatarTempo( totais.tempo );
		} else {
			totais.tempo = '---';
		}
		if(totais.custo > 0){
			totais.custo = encodeMonetario( totais.custo );
		} else {
			totais.custo = '---';
		}
		
		$thTotalValorPFOriginal.html(totais.pfOriginal);
		$thTotalValorPFAjustado.html(totais.pfAjustado);
		$thTotalTempo.html(totais.tempo);
		$thTotalCusto.html(totais.custo);
	}
	
	this.excluirComponenteFuncionalidade = function(botao){
		var $botao = $(botao);
		var $trAtual = $botao.closest('tr');
		var $tabela = $trAtual.closest('table');
		
		var id_funcionalidade = $trAtual.attr('data-id-funcionalidade');
		var $trsFuncionalidade = $tabela.find("[data-id-funcionalidade='" + id_funcionalidade + "']");
		var $primeiroTrFuncionalidade = $trsFuncionalidade.first();
		var $tdOrdem = $primeiroTrFuncionalidade.children('td.ordem');
		var $tdModulo = $primeiroTrFuncionalidade.children('td.modulo');
		var $tdFuncionalidade = $primeiroTrFuncionalidade.children('td.funcionalidade');
		
		var checkExcluindoPrimeiraFuncionalidade = false;
		$trsFuncionalidade.each(function(i){
			var $tr = $(this);
			if($tr[0] == $trAtual[0]){
				if(i == 0){
					checkExcluindoPrimeiraFuncionalidade = true;
				}
			}
		})
		
		// Efetuando personalizações nas linhas da tabela, dependendo de qual
		// linha desta funcionalidade tiver sido excluída
		if(checkExcluindoPrimeiraFuncionalidade){
			// Caso a linha excluída seja a primeira desta funcionalidade, mover
			// colunas "ordem", "módulo" e "funcionalidade" para a linha seguinte
			var $trSeguinte = $trAtual.next();
			
			$tdFuncionalidade.prependTo($trSeguinte);
			$tdModulo.prependTo($trSeguinte);
			$tdOrdem.prependTo($trSeguinte);
			
			// Removendo campos ocultos da primeira linha, já que eles se encontram
			// dentro da coluna "ordem", na primeira linha da funcionalidade
			$tdOrdem.find("input[type='hidden']").remove();
		} else {
			// Caso a linha excluída não seja a primeira desta funcionalidade,
			// atualizar rowspans da primeira linha desta funcionalidade
			var rowspan_ordem = parseInt($tdOrdem.attr('rowspan'), 10);
			var rowspan_modulo = parseInt($tdModulo.attr('rowspan'), 10);
			var rowspan_funcionalidade = parseInt($tdFuncionalidade.attr('rowspan'), 10);

			$tdOrdem.attr('rowspan', (rowspan_ordem - 1));
			$tdModulo.attr('rowspan', (rowspan_modulo - 1));
			$tdFuncionalidade.attr('rowspan', (rowspan_funcionalidade - 1));
		}
		
		// Excluindo linha do componente
		$trAtual.remove();
		
		// Caso haja um único componente restante nesta funcionalidade, ajustar
		// alinhamento das ações "Subir / descer funcionalidade"
		$trsFuncionalidade = $tabela.find("[data-id-funcionalidade='" + id_funcionalidade + "']");
		if($trsFuncionalidade.length == 1){
			var $divBtnGroup = $trsFuncionalidade.children('td.ordem').children('div.btn-group-vertical');
			$divBtnGroup.removeClass('btn-group-vertical').addClass('btn-group');
		}
		
		// Calculando totais no rodapé da tabela
		this.calcularTotaisRodapeTabela();
	}
	
	this.toggleOrdemFuncionalidade = function(botao, direcao){
		var $botao = $(botao);
		var $trAtual = $botao.closest('tr');
		var $tabela = $trAtual.closest('table');
		
		var id_funcionalidade_atual = $trAtual.attr('data-id-funcionalidade');
		var $trsFuncionalidade = $tabela.find("[data-id-funcionalidade='" + id_funcionalidade_atual + "']");
		
		if(direcao == true){
			// Subindo ordem
			var $trAnterior = $trAtual.prevAll("[data-id-funcionalidade!='" + id_funcionalidade_atual + "']").first();
			if($trAnterior.length > 0){
				var id_funcionalidade_anterior = $trAnterior.attr('data-id-funcionalidade');
				$trAnterior = $tabela.find("[data-id-funcionalidade='" + id_funcionalidade_anterior + "']").first();
				$trsFuncionalidade.insertBefore($trAnterior);
			}
			
		} else {
			// Descendo ordem
			var $trSeguinte = $trAtual.nextAll("[data-id-funcionalidade!='" + id_funcionalidade_atual + "']").first();
			if($trSeguinte.length > 0){
				var id_funcionalidade_seguinte = $trSeguinte.attr('data-id-funcionalidade');
				$trSeguinte = $tabela.find("[data-id-funcionalidade='" + id_funcionalidade_seguinte + "']").last();
				$trsFuncionalidade.insertAfter($trSeguinte);
			}
		}
	}
}

// Instanciando objeto da classe acima
var orcamentoManutencao = new orcamentoManutencao();