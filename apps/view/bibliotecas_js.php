<!-- jQuery -->
<script src="../common/js/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../common/js/bootstrap.bundle.min.js"></script>
<!-- If Bootstrap 4 Breakpoint - Para detecção de ambientes -->
<script src="../common/js/if-b4-breakpoint.min.js"></script>
<!-- Select2 -->
<script src="../common/js/select2.full.min.js"></script>
<!-- SlimScroll -->
<script src="../common/js/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../common/js/fastclick.min.js"></script>
<!-- Hullabaloo.js - Para exibição de notificações com alerts flutuantes -->
<script src="../common/js/hullabaloo.min.js"></script>
<!-- Tagsinput -->
<script src="../common/js/tagsinput.js"></script>
<!-- bootstrap-daterangepicker -->
<script src="../common/js/moment.min.js"></script>
<script src="../common/js/daterangepicker.js"></script>
<!-- bootstrap-timepicker -->
<script src="../common/js/bootstrap-timepicker.js"></script>
<!-- bootstrap-daterangepicker -->
<script src="../common/js/moment.min.js"></script>
<!-- jQuery Mask -->
<script src="../common/js/jquery.mask.min.js"></script>
<!-- Bootstrap Slider -->
<script src="../common/js/bootstrap-slider.js?<?php echo filemtime('../common/js/bootstrap-slider.js') ?>"></script>
<!-- jQuery DataTables -->
<script src="../common/js/jquery.dataTables.min.js"></script>
<script src="../common/js/dataTables.bootstrap4.min.js"></script>
<script src="../common/js/dataTables.responsive.min.js"></script>
<script src="../common/js/dataTables.rowGroup.min.js"></script>
<!-- JSZip.js -->
<script src="../common/js/jszip.min.js"></script>
<!-- jsrender.js -->
<script src="../common/js/jsrender.min.js"></script>
<!-- AdminLTE App -->
<script src="../common/js/adminlte.js?<?php echo filemtime('../common/js/adminlte.js') ?>"></script>
<!-- Funções da própria aplicação -->
<script src="../common/js/modal.js?<?php echo filemtime('../common/js/modal.js') ?>"></script>
<script src="../common/js/funcoes.js?<?php echo filemtime('../common/js/funcoes.js') ?>"></script>
<script src="../common/js/tabela.js?<?php echo filemtime('../common/js/tabela.js') ?>"></script>
<script src="../common/js/aviso.js?<?php echo filemtime('../common/js/aviso.js') ?>"></script>
<script src="../common/js/aba.js?<?php echo filemtime('../common/js/aba.js') ?>"></script>
<script src="../common/js/select.js?<?php echo filemtime('../common/js/select.js') ?>"></script>
<script src="../common/js/calendario.js?<?php echo filemtime('../common/js/calendario.js') ?>"></script>
<script src="../common/js/timepicker.js?<?php echo filemtime('../common/js/timepicker.js') ?>"></script>
<script src="../common/js/fileUploader.js?<?php echo filemtime('../common/js/fileUploader.js') ?>"></script>
<script src="../common/js/mascara.js?<?php echo filemtime('../common/js/mascara.js') ?>"></script>
<script src="../common/js/campoMultiplo.js?<?php echo filemtime('../common/js/campoMultiplo.js') ?>"></script>
<script src="../common/js/cpf.js?<?php echo filemtime('../common/js/cpf.js') ?>"></script>
<script src="../common/js/phpspreadsheet.js?<?php echo filemtime('../common/js/phpspreadsheet.js') ?>"></script>
<script type="text/javascript">
	var menu_minimizado = <?php echo ($_SESSION['menu_minimizado']) ? ('true') : ('false') ?>;
	var data_servidor = new Date(<?php echo strtotime('now') * 1000 ?>);
	var ambiente = '<?php echo $ambiente ?>';
	var tema = localStorage.getItem('cpf.tema');
	$(function(){
		instanciarBuscaMenu();
		instanciarComponentes();
		setTemaVisual(tema, event);
	})
</script>