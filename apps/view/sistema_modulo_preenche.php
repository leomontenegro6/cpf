<?php
$_GET['ajax'] = true;
require_once('cabecalho.php');

$modulo = new modulo();

$i = $_GET['i'];

if(isset($modulo_row) && (is_numeric($modulo_row['id']))){
	$atributo_tr = "data-linha-existente='true'";
	$acao_modulo = 'editar';
} else {
	$atributo_tr = '';
	$acao_modulo = 'cadastrar';
	$modulo_row = array(
		'id' => '',
		'nome' => ''
	);
}

$id_modulo = $modulo_row['id'];
$nome = $modulo_row['nome'];
?>
<tr <?php echo $atributo_tr ?>>
	<td>
		<input type="text" id="modulos_<?php echo $i ?>_nome" class="form-control" required
			name="modulos[<?php echo $i ?>][nome]" value="<?php echo $nome ?>" />
	</td>
	<td class="acoes" valign="top">
		<input type="hidden" name="modulos[<?php echo $i ?>][id]" value="<?php echo $id_modulo ?>" />
		<input type="hidden" name="modulos[<?php echo $i ?>][acao]" class="acao" value="<?php echo $acao_modulo ?>" />
		
		<button type="button" class="btn btn-danger" title="Remover" onclick="removerModuloSistema(this)">
			<i class="fas fa-minus" style="margin-right: 0"></i>
		</button>
	</td>
</tr>