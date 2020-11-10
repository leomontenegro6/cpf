<!-- jQuery -->
<script src="../common/js/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../common/js/bootstrap.bundle.min.js"></script>
<!-- If Bootstrap 4 Breakpoint - Para detecção de ambientes -->
<script src="../common/js/if-b4-breakpoint.min.js"></script>
<!-- Funções da própria aplicação -->
<script src="../common/js/funcoes.js?<?php echo filemtime('../common/js/funcoes.js') ?>"></script>
<script type="text/javascript">
	var tema = localStorage.getItem('cpf.tema');
	$(function(){
		setTemaVisual(tema, event);
	})
</script>