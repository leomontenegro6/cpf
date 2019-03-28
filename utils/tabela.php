<?php
class tabela{	
	
	public static function instanciar($pagina, $parametros, $oculta=false, $id_tabela='', $temPaginacao=true, $limite=10){
		$_GET['montar_tabela'] = true;
		$_GET['pagina'] = $pagina;
		$_GET['parametros'] = $parametros;
		$_GET['oculta'] = $oculta;
		$_GET['id_tabela'] = $id_tabela;
		$_GET['tem_paginacao'] = $temPaginacao;
		$_GET['limite'] = $limite;
		include($pagina);
	}
	
	public static function obterValorCampoBusca($get){
		return $get['search']['value'];
	}
	
	public static function interpretarDados($get, $total_consulta){
		$numero_de_pesquisas = $get['draw'];
		$ordenacao_filtragem = $get['order'];
		$total_ordenacao_filtragem = count($ordenacao_filtragem);
		$colunas = $get['columns'];
		
		$ordenacao = $filtragem = '';
		if($total_ordenacao_filtragem > 1){
			foreach($ordenacao_filtragem as $i=>$ord){
				$coluna = (string)((int)$ord['column'] + 1);
				$direcao = $ord['dir'];
				
				if($i > 0){
					$ordenacao .= ', ';
				}
				$ordenacao .= "$coluna $direcao";
			}
		} elseif($total_ordenacao_filtragem == 1){
			$ordenacao = (string)((int)$ordenacao_filtragem[0]['column'] + 1);
			$filtragem = $ordenacao_filtragem[0]['dir'];
		} else {
			$ordenacao = '1';
			$filtragem = 'asc';
		}
		
		if($get['length'] == -1){
			$limit = $total_consulta;
		} else {
			$limit = $get['length'];
		}
		$offset = $get['start'];
		return array(
			'numero_de_pesquisas'=>$numero_de_pesquisas,
			'colunas'=>$colunas,
			'ordenacao'=>$ordenacao,
			'filtragem'=>$filtragem,
			'limit'=>$limit,
			'offset'=>$offset
		);
    }
	
	public static function formatarColunasCliente($colunas){
		$colunas_formatadas = array();
		foreach($colunas as $coluna){
			array_push($colunas_formatadas, $coluna['data']);
		}
		return $colunas_formatadas;
	}
	
	public static function formatarDadosTabela($rs, $colunas_cliente, $rowdata=array()){
		$dados_tabela = array();
		$colunas_cliente = self::formatarColunasCliente($colunas_cliente);
		foreach($rs as $row){
			$dados_coluna = array();
			$i = 0;
			foreach($row as $nome_coluna=>$valor_coluna){
				if(in_array($nome_coluna, $colunas_cliente)){
					$dados_coluna[$nome_coluna] = $valor_coluna;
				} elseif(in_array(($i + 1), $colunas_cliente)){
					$dados_coluna[$i + 1] = $valor_coluna;
				}
				if( (count($rowdata) > 0 && in_array($nome_coluna, $rowdata)) || ($nome_coluna == 'id' && !in_array($nome_coluna, $rowdata)) ){
					$dados_coluna['DT_RowData'][$nome_coluna] = $valor_coluna;
				}
				$i++;
			}
			array_push($dados_tabela, $dados_coluna);
		}
		return $dados_tabela;
	}
	
	public static function encodarRetornoJSON($numero_de_pesquisas, $total_geral, $total_consulta, $dados_tabela, $busca='', $ordenacao='', $filtragem='', $limit='', $offset=''){
		$array_retorno = array(
			'draw'=>$numero_de_pesquisas,
			'recordsTotal'=>$total_geral,
			'recordsFiltered'=>$total_consulta,
			'data'=>$dados_tabela
		);
		$array_retorno = self::gerarInformacoesDepuracao($array_retorno, $busca, $ordenacao, $filtragem, $limit, $offset);
		return json_encode($array_retorno);
	}
	
	public static function gerarInformacoesDepuracao($array_retorno, $busca, $ordenacao, $filtragem, $limit, $offset){
		$ambiente = funcoes::getAmbienteDesenvolvimento();
		if($ambiente == 'D' || $ambiente == 'H'){
			$array_retorno['_busca'] = $busca;
			$array_retorno['_ordenacao'] = $ordenacao;
			$array_retorno['_filtragem'] = $filtragem;
			$array_retorno['_limit'] = $limit;
			$array_retorno['_offset'] = $offset;
		}
		return $array_retorno;
	}
}