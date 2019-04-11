<?php if($ajax === false){ ?>
			</div>
			<!-- /.content-wrapper -->

			<footer class="main-footer">
				<div class="float-right d-none d-sm-block">
					<b>Versão</b> 1.0.0-alpha
				</div>
				<strong>&copy; 2019</strong> Leonardo Montenegro
			</footer>

			<!-- Control Sidebar -->
			<aside class="control-sidebar control-sidebar-dark">
				<!-- Control sidebar content goes here -->
			</aside>
			<!-- /.control-sidebar -->
		</div>
		<!-- ./wrapper -->

		<!-- jQuery -->
		<script src="../common/js/jquery.min.js"></script>
		<!-- Bootstrap 4 -->
		<script src="../common/js/bootstrap.bundle.min.js"></script>
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
		<!-- jQuery Mask -->
		<script src="../common/js/jquery.mask.min.js"></script>
		<!-- Bootstrap Slider -->
		<script src="../common/js/bootstrap-slider.js?<?php echo filemtime('../common/js/bootstrap-slider.js') ?>"></script>
		<!-- jQuery DataTables -->
		<script src="../common/js/jquery.dataTables.min.js"></script>
		<script src="../common/js/dataTables.bootstrap4.min.js"></script>
		<script src="../common/js/dataTables.responsive.min.js"></script>
		<!-- AdminLTE App -->
		<script src="../common/js/adminlte.js?<?php echo filemtime('../common/js/adminlte.js') ?>"></script>
		<!-- Funções da própria aplicação -->
		<script src="../common/js/modal.js?<?php echo filemtime('../common/js/modal.js') ?>"></script>
		<script src="../common/js/funcoes.js?<?php echo filemtime('../common/js/funcoes.js') ?>"></script>
		<script src="../common/js/tabela.js?<?php echo filemtime('../common/js/tabela.js') ?>"></script>
		<script src="../common/js/aviso.js?<?php echo filemtime('../common/js/aviso.js') ?>"></script>
		<script src="../common/js/aba.js?<?php echo filemtime('../common/js/aba.js') ?>"></script>
		<script src="../common/js/select.js?<?php echo filemtime('../common/js/select.js') ?>"></script>
		<script src="../common/js/fileUploader.js?<?php echo filemtime('../common/js/fileUploader.js') ?>"></script>
		<script src="../common/js/mascara.js?<?php echo filemtime('../common/js/mascara.js') ?>"></script>
		<script src="../common/js/campoMultiplo.js?<?php echo filemtime('../common/js/campoMultiplo.js') ?>"></script>
		<script src="../common/js/cpf.js?<?php echo filemtime('../common/js/cpf.js') ?>"></script>
		<script type="text/javascript">
			var data_servidor = new Date(<?php echo strtotime('now') * 1000 ?>);
			var ambiente = '<?php echo $ambiente ?>';
			var dispositivo = '';
			$(function(){
				dispositivo = getDispositivo(true);
				instanciarBuscaMenu();
				instanciarComponentes();
			})
		</script>
		<?php
		if(isset($_SESSION['crud'])){
			$texto = $_SESSION['crud']['texto'];
			$tipo = $_SESSION['crud']['tipo'];
			
			unset($_SESSION['crud']);
			
			aviso::exibir($texto, $tipo);
		}
		?>
	</body>
</html>
<?php } ?>