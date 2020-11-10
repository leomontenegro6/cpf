<?php
class menu{

	public static function carregar(){
		return array(
			array(
				'nome' => 'Página Inicial',
				'icone' => 'fa-home',
				'pagina' => 'index.php',
			),
			array(
				'nome' => 'Cadastros',
				'icone' => 'fa-table',
				'filhos' => array(
					array(
						'nome' => 'Sistemas',
						'icone' => 'fa-cog',
						'pagina' => 'sistema_lista.php',
					),
					array(
						'nome' => 'Funcionalidades',
						'icone' => 'fa-cogs',
						'pagina' => 'funcionalidade_lista.php',
					),
					array(
						'nome' => 'Componentes',
						'icone' => 'fa-puzzle-piece',
						'pagina' => 'componente_lista.php',
					),
					array(
						'nome' => 'Feriados',
						'icone' => 'fa-calendar-times',
						'pagina' => 'feriado_lista.php',
					),
					array(
						'nome' => 'Usuários',
						'icone' => 'fa-user',
						'pagina' => 'usuario_lista.php',
					)
				)
			),
			array(
				'nome' => 'Relatórios',
				'icone' => 'fa-paste',
				'filhos' => array(
					array(
						'nome' => 'Contagem de Pontos',
						'icone' => 'fa-dice',
						'pagina' => 'rel_contagem_pontos_lista.php',
					),
					array(
						'nome' => 'Prazos de Desenvolvimento',
						'icone' => 'fa-history',
						'pagina' => 'rel_prazos_desenvolvimento_lista.php',
					),
					array(
						'nome' => 'Cronograma de Desenvolvimento',
						'icone' => 'fa-calendar-alt',
						'pagina' => 'rel_cronograma_desenvolvimento_lista.php',
					),
					array(
						'nome' => 'Orçamento de Desenvolvimento',
						'icone' => 'fa-dollar-sign',
						'pagina' => 'rel_orcamento_desenvolvimento_lista.php',
					),
					array(
						'nome' => 'Orçamento de Manutenção',
						'icone' => 'fa-tools',
						'pagina' => 'rel_orcamento_manutencao_lista.php',
					)
				)
			)
		);
	}
	
	public static function montar($menu, $endereco){
		foreach($menu as $item){
			$nome = $item['nome'];
			$icone = $item['icone'];
			$pagina = (isset($item['pagina'])) ? ($item['pagina']) : ('');
			$filhos = (isset($item['filhos'])) ? ($item['filhos']) : (array());
			
			$checkTemFilhos = (count($filhos) > 0);
			$checkEstaNaPaginaAtual = ($pagina == $endereco);
			
			if($checkTemFilhos){
				foreach($filhos as $subitem){
					$subpagina = (isset($subitem['pagina'])) ? ($subitem['pagina']) : ('');
					$checkPaiEstaNaPaginaAtual = ($subpagina == $endereco);
					if($checkPaiEstaNaPaginaAtual){
						break;
					}
				}
			} else {
				$checkPaiEstaNaPaginaAtual = false;
			}
			?>
			<li class="nav-item <?php if($checkTemFilhos) echo 'has-treeview' ?> <?php if($checkPaiEstaNaPaginaAtual) echo 'menu-open' ?>">
				<a <?php if(!empty($pagina)){ echo "href='$pagina'"; } else { echo "href=''"; } ?> title="<?php echo $nome ?>"
					class="nav-link <?php if($checkEstaNaPaginaAtual || $checkPaiEstaNaPaginaAtual) echo 'active' ?>">
					<i class="nav-icon fa <?php echo $icone ?>"></i>
					<p>
						<?php echo $nome ?>
						<?php if($checkTemFilhos){ ?>
							<i class="fa fa-angle-left right"></i>
						<?php } ?>
					</p>
				</a>
				<?php if($checkTemFilhos){ ?>
					<ul class="nav nav-treeview"><?php echo self::montar($filhos, $endereco) ?></ul>
				<?php } ?>
			</li>
			<?php
		}
	}
	
	public static function getHierarquicamente($menu, $nivel=0){
		global $menu_hierarquico;
		
		if($nivel == 0){
			$menu_hierarquico = array();
		}
		
		// Montando array com menus listados em ordem hierárquica
		foreach($menu as $i=>$item){
			$pagina = (isset($item['pagina'])) ? ($item['pagina']) : ('');
			$filhos = (isset($item['filhos'])) ? ($item['filhos']) : (array());
			
			$item['pagina'] = $pagina;
			$item['nivel'] = $nivel;
			
			$menu_hierarquico[] = $item;
			if(count($filhos) > 0){
				self::getHierarquicamente($filhos, ($nivel + 1));
			}
		}
		
		return $menu_hierarquico;
	}
	
	public static function getHierarquicamenteAutocomplete($menu, $busca=''){
		$menu_hierarquico = self::getHierarquicamente($menu);
		
		$checkPossuiBusca = (!empty(trim($busca)));
		
		// Aplicando filtro de busca, se aplicável
		if($checkPossuiBusca){
			$checkPeloMenosUmExcluido = false;
			foreach($menu_hierarquico as $i=>$item){
				$nome = $item['nome'];
				$icone = $item['icone'];
				$pagina = (isset($item['pagina'])) ? ($item['pagina']) : ('');
				
				$palavras_busca = explode(' ', $busca);
				foreach($palavras_busca as $palavra){
					$descricao = $nome . ' ' . $icone . ' ' . $pagina;
					$descricao = funcoes::lower(funcoes::normalize($descricao));
					$palavra = trim(funcoes::lower(funcoes::normalize($palavra)));
					
					if(!empty($palavra) && (strpos($descricao, $palavra) === false)){
						// Palavra não existe nos menus
						unset($menu_hierarquico[$i]);
						$checkPeloMenosUmExcluido = true;
					}
				}
			}
			
			// Resetando chaves do array
			if($checkPeloMenosUmExcluido){
				$menu_hierarquico = array_values($menu_hierarquico);
			}
		}
		
		return $menu_hierarquico;
	}
}