/* Biblioteca javascript englobando funções específicas do Dashboard
 * da tela inicial do sistema
 */

function dashboard(){
	
	// Propriedades
	
	// Métodos	
	this.instanciarTabelaValoresSistemas = function(){
		var $tabelaValoresSistemas = $('#valores_sistemas');
		
		$tabelaValoresSistemas.DataTable({
			scrollY: "105px",
			scrollCollapse: true,
			paging: false,
			info: false,
			searching: false
		});
	}
	
	this.calcularValorComponenteFuncionalidade = function(elemento){
		var $tr = $(elemento).closest('tr');
		var $selectTipoComponente = $tr.find("[name$='[tipo_componente]']");
		var $opcaoSelecionada = $selectTipoComponente.find(':selected');
		var $checkboxPossuiAcoes = $tr.find("[name$='[possui_acoes]']");
		var $checkboxPossuiMensagens = $tr.find("[name$='[possui_mensagens]']");
		var $campoCampos = $tr.find("[name$='[campos]']");
		var $campoArquivosReferenciados = $tr.find("[name$='[arquivos_referenciados]']");
		var $divComplexidade = $tr.find('div.complexidade');
		var $divValorPF = $tr.find('div.valor_pf');
		var $tabelaComponentes = $tr.closest('table');
		var $tbody = $tabelaComponentes.children('tbody');
		var $tfoot = $tabelaComponentes.children('tfoot');
		var $divTotalPF = $tfoot.find('div.total_pf');
		
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
			quantidade_total_tipos_dados = -1;
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
			quantidade_total_arquivos_referenciados = -1;
		}
		
		// Calculando complexidade e valor do componente (em pontos de função)
		var complexidade = cpf.calcularComplexidade(tipo_funcional, quantidade_total_tipos_dados, quantidade_total_arquivos_referenciados);
		var valor = cpf.calcularValor(tipo_funcional, complexidade);
		if(valor == '') valor = '---';
		complexidade = cpf.formataNomeComplexidade(complexidade);
		
		// Exibindo complexidade e valor, no campo
		$divComplexidade.html(complexidade);
		$divValorPF.html(valor);
		
		// Atualizando total de PF no rodapé
		var total_pf = 0;
		$tbody.children('tr').each(function(){
			var $trAtual = $(this);
			var $divValorPFAtual = $trAtual.find('div.valor_pf');
			
			var valor_pf = parseInt($divValorPFAtual.html(), 10);
			if(isNaN(valor_pf)) valor_pf = 0;
			
			total_pf += valor_pf;
		});
		if(total_pf == 0) total_pf = '---';
		$divTotalPF.html(total_pf);
	}
}

// Instanciando objeto da classe acima
var dashboard = new dashboard();