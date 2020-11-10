<?php
class feriado extends abstractBusiness{
	public function __construct(){
		parent::__construct(get_class($this));
    }
	
	// Métodos de listagem de dados
	public function getFromFrameworkYasumiByAno($ano){
		$feriado_rs = array();
		
		$feriados = Yasumi\Yasumi::create('Brazil', $ano, 'pt_BR');
		foreach ($feriados->getHolidays() as $f) {
			$nome = funcoes::capitaliza($f->getName());
			$data_desformatada = trim(`echo $f`);
			$data = funcoes::encodeData($data_desformatada);
			$dia_semana = funcoes::formataDiaSemanaPorData($data_desformatada, true);

			$feriado_rs[] = array(
				'nome' => $nome,
				'data' => $data,
				'data_desformatada' => $data_desformatada,
				'dia_semana' => $dia_semana,
				'customizado' => 'Não',
				'id' => ''
			);
		}
		
		return $feriado_rs;
	}
	
	public function getCustomizadosByAno($ano){
		$feriadoCustomizado_rs = $this->getFieldsByParameter("nome, data, id", "WHERE DATE_FORMAT(data, '%Y') = '$ano'");
		
		foreach($feriadoCustomizado_rs as $i=>$feriadoCustomizado_row){
			$feriadoCustomizado_rs[$i]['data_desformatada'] = $feriadoCustomizado_row['data'];
			$feriadoCustomizado_rs[$i]['data'] = funcoes::encodeData($feriadoCustomizado_row['data']);
			$feriadoCustomizado_rs[$i]['dia_semana'] = funcoes::formataDiaSemanaPorData($feriadoCustomizado_row['data'], true);
			$feriadoCustomizado_rs[$i]['customizado'] = 'Sim';
		}
		
		return $feriadoCustomizado_rs;
	}
	
	public function getAllByAno($ano, $customizado=''){
		$feriado_rs = array();
		
		if($customizado == 'false' || $customizado == ''){
			$feriadoFrameworkYasumi_rs = $this->getFromFrameworkYasumiByAno($ano);
			$feriado_rs = array_merge($feriado_rs, $feriadoFrameworkYasumi_rs);
		}
		
		if($customizado == 'true' || $customizado == ''){
			$feriadoCustomizado_rs = $this->getCustomizadosByAno($ano);
			$feriado_rs = array_merge($feriado_rs, $feriadoCustomizado_rs);
		}
		
		return $feriado_rs;
	}
	
	public function getDiasNaoUteisByAnos($anos){
		$feriados_cronograma = array();
		
		// Iterando cada um dos anos fornecidos
		foreach($anos as $ano){
			$ano_seguinte = $ano + 1;
			$feriado_rs = $this->getAllByAno($ano);
			
			// Incluindo feriados, para o ano atual
			foreach($feriado_rs as $feriado_row){
				$data_desformatada = $feriado_row['data_desformatada'];
				$nome = $feriado_row['nome'];
				
				if(!array_key_exists($data_desformatada, $feriados_cronograma)){
					$feriados_cronograma[$data_desformatada] = $nome;
				}
			}
			
			// Incluindo finais de semana, para o ano atual
			$objeto_data_inicial = new DateTime("{$ano}-01-01");
			$objeto_data_final = new DateTime("{$ano_seguinte}-01-01");

			$objeto_data_final = $objeto_data_final->modify('+1 day');

			$intervalo = DateInterval::createFromDateString('1 day');
			$periodo = new DatePeriod($objeto_data_inicial, $intervalo, $objeto_data_final);

			foreach ($periodo as $objeto_data) {
				$data = $objeto_data->format("Y-m-d");
				$dia_semana = $objeto_data->format("N");

				if(in_array($dia_semana, array(6, 7))){
					if(!array_key_exists($data, $feriados_cronograma)){
						$feriados_cronograma[$data] = 'Fim de Semana';
					}
				}
			}
			
		}
		
		// Ordenando array pela data, e retornando-o em seguida
		ksort($feriados_cronograma);
		return $feriados_cronograma;
	}
	
	// Métodos de checagens e validações
	public function checkDataFeriado($data){
		$data_desformatada = funcoes::decodeData($data);
		$objeto_data = new DateTime($data_desformatada);
		$ano = $objeto_data->format('Y');
		
		// Verificando se a data fornecida consta nos feriados gerados pelo framework Yasumi
		$feriadoFrameworkYasumi_rs = $this->getFromFrameworkYasumiByAno($ano);
		foreach($feriadoFrameworkYasumi_rs as $feriadoFrameworkYasumi_row){
			$data_framework_desformatada = $feriadoFrameworkYasumi_row['data_desformatada'];
			if($data_desformatada == $data_framework_desformatada){
				return true;
			}
		}
		
		// Verificando se a data fornecida consta nos feriados customizados
		$feriadoCustomizado_rs = $this->getCustomizadosByAno($ano);
		foreach($feriadoCustomizado_rs as $feriadoCustomizado_row){
			$data_customizada_desformatada = $feriadoCustomizado_row['data_desformatada'];
			if($data_desformatada == $data_customizada_desformatada){
				return true;
			}
		}
		
		// Se não constar em nenhum deles, assumir que não é feriado
		return false;
	}
	
	// Métodos de escrita de dados
	public function set($post, $commit=true){
		$intervalo = (isset($post['intervalo']) && ($post['intervalo'] == 'true'));
		
		if($intervalo){
			$data_inicial = funcoes::decodeData($post['data']['inicial']);
			$data_final = funcoes::decodeData($post['data']['final']);
			
			$objeto_data_inicial = new DateTime($data_inicial);
			$objeto_data_final = new DateTime($data_final);
			
			$objeto_data_final = $objeto_data_final->modify('+1 day');

			$intervalo = DateInterval::createFromDateString('1 day');
			$periodo = new DatePeriod($objeto_data_inicial, $intervalo, $objeto_data_final);

			foreach ($periodo as $objeto_data) {
				$data = $objeto_data->format("d/m/Y");
				
				$post_inserir = array(
					'nome' => $post['nome'],
					'data' => $data
				);
				$retorno = parent::set($post_inserir, false);
				if($retorno !== true){
					return $retorno;
				}
			}

			return $this->commit($commit);
		} else {
			return parent::set($post, $commit);
		}
	}
}
