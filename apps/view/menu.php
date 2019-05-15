<?php if($ajax === false){ ?>
	<body class="hold-transition sidebar-mini <?php echo ($_SESSION['menu_minimizado']) ? ('sidebar-collapse') : ('sidebar-open') ?>">
		<!-- Site wrapper -->
		<div class="wrapper">
			<!-- Navbar -->
			<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
				<!-- Left navbar links -->
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" data-widget="pushmenu" href="#" onclick="salvarPersistenciaMenuMinimizado()"><i class="fa fa-bars"></i></a>
					</li>
				</ul>

				<!-- Right navbar links -->
				<ul class="navbar-nav ml-auto">
					<!-- User Dropdown Menu -->
					<li class="nav-item dropdown user-menu">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<img src="<?php echo $_SESSION['foto'] ?>" class="user-image" alt="User Image">
							<?php echo $_SESSION['nome_exibicao'] ?>
							<span class="fa fa-angle-down"></span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<a href="usuario_logado_dados_pessoais_edita.php" class="dropdown-item">
								<i class="fas fa-user pull-right"></i> Alterar Dados Pessoais
							</a>
							<div class="dropdown-divider"></div>
							<a href="usuario_logado_foto_edita.php" class="dropdown-item">
								<i class="fas fa-camera pull-right"></i> Editar Foto
							</a>
							<div class="dropdown-divider"></div>
							<a href="usuario_logado_senha_edita.php" class="dropdown-item">
								<i class="fas fa-key pull-right"></i> Redefinir Senha
							</a>
							<div class="dropdown-divider"></div>
							<a href="logoff.php" class="dropdown-item dropdown-footer">
								<i class="fas fa-sign-out-alt pull-right"></i> Sair
							</a>
						</div>
					</li>
				</ul>
			</nav>
			<!-- /.navbar -->

			<!-- Main Sidebar Container -->
			<aside class="main-sidebar sidebar-dark-primary elevation-4">
				<!-- Brand Logo -->
				<a href="javascript:void" class="brand-link">
					<img src="../common/img/cpf_logo.png"
						 alt="AdminLTE Logo"
						 class="brand-image img-circle elevation-3"
						 style="opacity: .8">
					<span class="brand-text font-weight-light">Contador de PF</span>
				</a>
				
				<!-- Search menu bar -->
				<form method="get" class="sidebar-form" id="sidebar-form">
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Buscar no menu...">
						<span class="input-group-btn">
							<button type="submit" name="search" id="search-btn" class="btn btn-flat">
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</form>

				<!-- Sidebar -->
				<div class="sidebar">
					<!-- Sidebar Menu -->
					<nav class="mt-2">
						<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" data-instanciado="true">
							<?php menu::montar($_SESSION['menu'], $endereco) ?>
						</ul>
					</nav>
					<!-- /.sidebar-menu -->
				</div>
				<!-- /.sidebar -->
			</aside>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
<?php } ?>