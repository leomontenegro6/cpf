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

		<?php
		include_once('bibliotecas_js.php');
		
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