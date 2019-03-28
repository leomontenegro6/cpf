<?php
class tipoFuncionalidade extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getComponentesFormularioCadastroFuncionalidades($id, $nome_funcionalidade){
		$componenteTipoFuncionalidade_rs = array();
		
		if($id == 1){
			// CRUD Simples
			$componenteTipoFuncionalidade_rs = array(
				0 => array(
					'tipo_componente' => 1, // Formulário de Pesquisa
					'possui_acoes' => true,
					'possui_mensagens' => false,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				1 => array(
					'tipo_componente' => 2, // Tabela de Listagem
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				2 => array(
					'tipo_componente' => 3, // Formulário de Cadastro / Edição
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				)
			);
		} elseif($id == 2){
			// CRUD Complexo
			$componenteTipoFuncionalidade_rs = array(
				0 => array(
					'tipo_componente' => 1, // Formulário de Pesquisa
					'possui_acoes' => true,
					'possui_mensagens' => false,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				1 => array(
					'tipo_componente' => 6, // Página de Detalhes
					'possui_acoes' => true,
					'possui_mensagens' => false,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				2 => array(
					'tipo_componente' => 2, // Tabela de Listagem
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				3 => array(
					'tipo_componente' => 3, // Formulário de Cadastro / Edição
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				)
			);
		} elseif($id == 3){
			// Processo	
			$componenteTipoFuncionalidade_rs = array(
				0 => array(
					'tipo_componente' => 9, // Formulário de Execução
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				)
			);
		} elseif($id == 4){
			// Relatório
			$componenteTipoFuncionalidade_rs = array(
				0 => array(
					'tipo_componente' => 1, // Formulário de Pesquisa
					'possui_acoes' => true,
					'possui_mensagens' => false,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				1 => array(
					'tipo_componente' => 2, // Tabela de Listagem
					'possui_acoes' => true,
					'possui_mensagens' => true,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				),
				2 => array(
					'tipo_componente' => 7, // Exportação para PDF
					'possui_acoes' => false,
					'possui_mensagens' => false,
					'campos' => array('Descrição'),
					'arquivos_referenciados' => array($nome_funcionalidade)
				)
			);
		}
		
		return $componenteTipoFuncionalidade_rs;
	}
	
	private function formataSQLByListagem($busca){
		$sql_where = 'TRUE';
		
		if(!empty($busca)){
			$busca = str_replace(' ', '%', $busca);
            $sql_where .= " AND descricao LIKE '%$busca%'";
		}
		
		return $sql_where;
	}
	
	public function getTotalByListagem($busca){
		$sql_where = $this->formataSQLByListagem($busca);
		
		return $this->getTotal("WHERE $sql_where");
	}
	
	public function getByListagem($busca, $ordenacao='descricao', $filtragem='ASC', $limit=15, $offset=0){
		$sql_where = $this->formataSQLByListagem($busca);
		
		$tipoFuncionalidade_rs = $this->getFieldsByParameter("descricao, id", "WHERE $sql_where ORDER BY $ordenacao $filtragem LIMIT $limit OFFSET $offset");
		foreach($tipoFuncionalidade_rs as $i=>$tipoFuncionalidade_row){
			$tipoFuncionalidade_rs[$i]['descricao'] = funcoes::capitaliza( $tipoFuncionalidade_row['descricao'] );
		}
		return $tipoFuncionalidade_rs;
	}
}