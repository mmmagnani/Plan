<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Sistema de Planejamento de Obtenções">
	<meta name="author" content="Magnani">
	<!-- Favicon icon -->
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png'); ?>">
	<title>SPO - Sistema de Planejamento de Obtenções</title>
	<!-- Bootstrap Core CSS -->
	<link href="<?= base_url('assets/css/lib/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="<?= base_url('assets/css/helper.css'); ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/jquery-ui.min.css'); ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/lib/toastr/toastr.min.css'); ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/lib/data-table/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/icons/themify-icons/themify-icons.css'); ?>" rel="stylesheet">
	<link href="<?= base_url('assets/css/lib/datepicker/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">	
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
	<!--[if lt IE 9]>
		<script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<!-- Jquery -->
	<script src="<?= base_url('assets/js/lib/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/lib/jquery-ui/jquery-ui.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/lib/toastr/toastr.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/lib/datepicker/bootstrap-datepicker.min.js'); ?>"></script>
	<script src="<?= base_url('assets/locales/bootstrap-datepicker.pt-BR.min.js'); ?>" charset='UTF-8'></script>
    <script src="<?= base_url('assets/js/lib/jquery.mask/jquery.mask.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/tip-top.js'); ?>"></script>
    <script src="<?= base_url('assets/js/custom.js'); ?>"></script>
    <script src="<?= base_url('assets/js/lib/highchart/highcharts.js'); ?>"></script>
	<script src="<?= base_url('assets/js/lib/highchart/highcharts-more.js'); ?>"></script>
	<script src="<?= base_url('assets/js/lib/highchart/highcharts-modules-exporting.js'); ?>"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".datepicker").mask("##/##/####");
			$('.money').mask('###.###.##0,00', {reverse: true});
			$('.number').mask('##############0,00', {reverse: true});
			$(".cpf").mask("###.###.###-##");
			$(".phone").mask("#####-0000");
		})
	</script>
</head>

<body class="fix-header fix-sidebar">
	<!-- Preloader - style you can find in spinners.css -->
	<div class="preloader">
		<svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
	</div>
	<!-- Main wrapper  -->
	<div id="main-wrapper">
		<!-- header header  -->
		<div class="header">
			<nav class="navbar top-navbar navbar-expand-md navbar-light">
				<!-- Logo -->
				<div class="navbar-header">
					<a class="navbar-brand" href="<?= site_url() ?> ">
						<!-- Logo icon -->
						<b>
							<img style="max-height: 24px; max-width: 60px" src="<?= base_url('assets/images/logo.png'); ?>" class="dark-logo img-responsive" />
						</b>
						<!--End Logo icon -->
						<!-- Logo text -->
						<span>
							<img style="max-height: 24px; max-width: 120px" src="<?= base_url('assets/images/logo-text.png'); ?>" alt="Plan" class="dark-logo img-responsive"
							/>
						</span>
					</a>
				</div>
				<!-- End Logo -->
				<div class="navbar-collapse">
					<!-- toggle and nav items -->
					<ul class="navbar-nav mr-auto mt-md-0">
						<!-- This is  -->
						<li class="nav-item">
							<a class="nav-link nav-toggler hidden-md-up text-muted  " href="javascript:void(0)">
								<i class="mdi mdi-menu"></i>
							</a>
						</li>
						<li class="nav-item m-l-10">
							<a class="nav-link sidebartoggler hidden-sm-down text-muted  " href="javascript:void(0)">
								<i class="ti-menu"></i>
							</a>
						</li>
						<!-- Messages -->
						<li class="nav-item dropdown mega-dropdown">
							<a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?= $this->lang->line('app_quick_access') ?>">
								<i class="fa fa-th-large"></i>
							</a>
							<div class="dropdown-menu animated zoomIn">
								<ul class="mega-dropdown-menu row">


									<li class="col-lg-2 m-b-30">
										<h4 class="m-b-20"><?= mb_strtoupper($this->lang->line('quick_access')); ?></h4>
										<ul>
										<?php if ($this->permission->check($this->session->userdata('permissao'), 'aObjetivos')) { ?>
											<li>
												<a href="<?= site_url('objetivos/create') ?>" class="btn btn-primary col-12"><i class="fa fa-plus"></i> <?= ucfirst($this->lang->line('targets')); ?></a>
											</li>
										<?php } ?>
										<?php if ($this->permission->check($this->session->userdata('permissao'), 'aProjetos')) {	 ?>
											<li>
												<a href="<?= site_url('projetos/create') ?>" class="btn btn-primary col-12"><i class="fa fa-plus"></i> <?= ucfirst($this->lang->line('projects')); ?></a>
											</li>
										<?php } ?>
										<?php if ($this->permission->check($this->session->userdata('permissao'), 'aTarefas')) { ?>
											<li>
												<a href="<?= site_url('tarefas/create') ?>" class="btn btn-primary col-12"><i class="fa fa-plus"></i> <?= ucfirst($this->lang->line('tasks')); ?></a>
											</li>
										<?php } ?>
										</ul>
									</li>
									<li class="col-lg-3 col-xlg-3 m-b-30">
										<h4 class="m-b-20"><?= ucfirst($this->lang->line('targets')); ?></h4>

										<form class="app-search" action="<?= site_url('objetivos/pesquisar'); ?>" method="GET" >
											<div class="form-group">
												<input type="text" class="form-control" name="termo" id="" placeholder="<?= ucfirst($this->lang->line('target_placeholder')); ?>"> </div>
											<button type="submit" class="btn btn-info"><?= ucfirst($this->lang->line('search_targets')); ?></button>
										</form>

									</li>
									<li class="col-lg-3 col-xlg-3 m-b-30">
										<h4 class="m-b-20"><?= ucfirst($this->lang->line('projects')); ?></h4>

										<form class="app-search" action="<?= site_url('projetos/pesquisar'); ?>" method="GET" >
											<div class="form-group">
												<input type="text" class="form-control" name="termo" id="" placeholder="<?= ucfirst($this->lang->line('project_placeholder')); ?>"> </div>
											<button type="submit" class="btn btn-info"><?= ucfirst($this->lang->line('search_projects')); ?></button>
										</form>
									</li>
									<li class="col-lg-3 col-xlg-3 m-b-30">
										<h4 class="m-b-20"><?= ucfirst($this->lang->line('tasks')); ?></h4>

										<form class="app-search" action="<?= site_url('tarefas/pesquisar'); ?>" method="GET" >
											<div class="form-group">
												<input type="text" class="form-control" name="termo" id="" placeholder="<?= ucfirst($this->lang->line('task_placeholder')); ?>"> </div>
											<button type="submit" class="btn btn-info"><?= ucfirst($this->lang->line('search_tasks')); ?></button>
										</form>
									</li>
								</ul>
							</div>
						</li>
						<!-- End Messages -->						
					</ul>
					<!-- User profile and search -->
					<ul class="navbar-nav my-lg-0">
						<li class="nav-item hidden-sm-down change-box">
							<a class="nav-link hidden-sm-down text-muted" href="javascript:void(0)" title="Clique para alterar o ano">Planejamento <?= $this->session->userdata('anofiscal'); ?>	
							</a>
							<form class="app-change" action="<?= site_url('plan/changeyear'); ?>" method="GET" >
								<input type="text" name="novoano" class="form-control" placeholder="<?= ucfirst($this->lang->line('newyear_placeholder')); ?>">
								<a class="chg-btn">
									<i class="ti-close"></i>
								</a>
							</form>
						</li>
						<!-- Search -->
						<li class="nav-item hidden-sm-down search-box">
							<a class="nav-link hidden-sm-down text-muted" href="javascript:void(0)">
								<i class="ti-search"></i>
							</a>
							<form class="app-search" action="<?= site_url('plan/pesquisar'); ?>" method="GET" >
								<input type="text" name="termo" class="form-control" placeholder="<?= ucfirst($this->lang->line('search_placeholder')); ?>">
								<a class="srh-btn">
									<i class="ti-close"></i>
								</a>
							</form>
						</li>
						<!-- Help -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle text-muted" href="#" id="3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title ="<?= $this->lang->line('app_help'); ?>">
								<i class="fa fa-question"></i>
								
							</a>
							<div class="dropdown-menu dropdown-menu-right mailbox animated zoomIn">
								<ul class="dropdown-user">
									<li>
										<a href="<?php echo base_url(); ?>assets/arquivos/SPO_Manual_Utilizacao.pdf" target="_blank"><i class="fa fa-book"></i> <?= ucfirst($this->lang->line('app_utilization_manual')); ?>
										</a>
									</li>
								</ul>
							</div>
						</li>
						<!-- End Help -->
						<!-- Profile -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="<?= base_url('assets/images/user.png'); ?>" alt="user" class="profile-pic" />
							</a>
							<div class="dropdown-menu dropdown-menu-right animated zoomIn">
								<ul class="dropdown-user">
									<li>
										<a href="<?= site_url('plan/conta'); ?>">
											<i class="ti-user"></i> <?= ucfirst($this->lang->line('profile')); ?></a>
									</li>
									<li>
										<a href="<?= site_url('plan/sair'); ?>">
											<i class="fa fa-power-off"></i> <?= ucfirst($this->lang->line('logout')); ?></a>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</nav>
		</div>
		<!-- End header header -->
		<!-- Left Sidebar  -->
		<div class="left-sidebar">
			<!-- Sidebar scroll-->
			<div class="scroll-sidebar">
				<!-- Sidebar navigation-->
				<nav class="sidebar-nav">
					<ul id="sidebarnav">
						<li class="nav-devider"></li>

						<li class="nav-label">Menu</li>

						<li>
							<a href="<?= site_url() ?>" aria-expanded="false">
								<i class="fa fa-tachometer-alt"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('dashboard')); ?></span>
							</a>
						</li>
                        <?php if ($this->permission->check($this->session->userdata('permissao'), 'vCalendario')) { ?>
                        <li>
							<a href="<?= site_url('calendario') ?>" aria-expanded="false">
								<i class="fa fa-calendar-alt"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('calendar_menu')); ?></span>
							</a>
						</li>
						<?php } ?>
                        <?php if ($this->permission->check($this->session->userdata('permissao'), 'vObjetivos')) { ?>
						<li>
							<a href="<?= site_url('objetivos') ?>" aria-expanded="false">
								<i class="fa fa-bullseye"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('targets')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if ($this->permission->check($this->session->userdata('permissao'), 'vProjetos')) { ?>
						<li>
							<a href="<?= site_url('projetos') ?>" aria-expanded="false">
								<i class="fa fa-project-diagram"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('projects')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if ($this->permission->check($this->session->userdata('permissao'), 'vTarefas')) { ?>
						<li>
							<a href="<?= site_url('tarefas') ?>" aria-expanded="false">
								<i class="fa fa-tasks"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('tasks')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if ($this->permission->check($this->session->userdata('permissao'), 'vProjetos')) { ?>
						<li>
							<a href="<?= site_url('priorizar') ?>" aria-expanded="false">
								<i class="fa fa-sort"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('prioritize_tasks')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if (($this->permission->check($this->session->userdata('permissao'), 'cIndicadores')) && ($this->permission->check($this->session->userdata('permissao'), 'vRegistros'))) { ?>
						<li>
							<a href="<?= site_url('indicadores') ?>" aria-expanded="false">
								<i class="fa fa-chart-bar"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('indicators')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if ($this->permission->check($this->session->userdata('permissao'), 'fGerset')) { ?>
                        <li>
							<a href="<?= site_url('registros') ?>" aria-expanded="false">
								<i class="fa fa-edit"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('register_indicators')); ?></span>
							</a>
						</li>
						<?php } ?>
						<?php if(($this->permission->check($this->session->userdata('permissao'),'cUsuario')) || ($this->permission->check($this->session->userdata('permissao'), 'cGerset')) || ($this->permission->check($this->session->userdata('permissao'), 'cOm')) || ($this->permission->check($this->session->userdata('permissao'), 'cSetores')) || ($this->permission->check($this->session->userdata('permissao'), 'cSistema')) || ($this->permission->check($this->session->userdata('permissao'), 'cStatus')) || ($this->permission->check($this->session->userdata('permissao'), 'cPermissao')) || ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup'))) { ?>
						<li>
							<a class="has-arrow  " href="#" aria-expanded="false">
								<i class="fa fa-cogs"></i>
								<span class="hide-menu"><?= ucfirst($this->lang->line('app_configs')); ?></span>
							</a>
							<ul aria-expanded="false" class="collapse">
								<?php if($this->permission->check($this->session->userdata('permissao'),'cUsuario')){ ?>
								<li>
									<a href="<?= site_url('usuarios'); ?>"><i class="fa fa-users"></i> <?= ucfirst($this->lang->line('users')); ?></a>
								</li>
                                <?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cGerset')) { ?>
                                <li>
									<a href="<?= site_url('gersets'); ?>"><i class="fa fa-diagnoses"></i> <?= ucfirst($this->lang->line('gersets')); ?></a>
								</li>
								<?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cOm')) { ?>
								<li>
									<a href="<?= site_url('om'); ?>"><i class="fa fa-fighter-jet"></i> <?= ucfirst($this->lang->line('om')); ?></a>
								</li>
                                <?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cSetores')) { ?>
                                <li>
									<a href="<?= site_url('setores'); ?>"><i class="fa fa-bezier-curve"></i> <?= ucfirst($this->lang->line('sectors')); ?></a>
								</li>
                                <?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cSistema')) { ?>
                                <li>
									<a href="<?= site_url('sistema'); ?>"><i class="fa fa-cog"></i> <?= ucfirst($this->lang->line('system')); ?></a>
								</li>
                                <?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cStatus')) { ?>
                                <li>
									<a href="<?= site_url('status'); ?>"><i class="fa fa-thumbs-up"></i> <?= ucfirst($this->lang->line('perm_status')); ?></a>
								</li>
                                <?php } ?>
								<?php if ($this->permission->check($this->session->userdata('permissao'), 'cPermissao')) { ?>
								<li>
									<a href="<?= site_url('permissoes'); ?>"><i class="fa fa-check-double"></i> <?= ucfirst($this->lang->line('permissions')); ?></a>
								</li>
								<?php } ?>
								<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'cBackup')) { ?>                                
								<li>
									<a href="<?= site_url('plan/backup'); ?>"><i class="fa fa-file-archive"></i> Backup</a>
								</li>
								<?php } ?>                                
			
							</ul>
						</li>
						<?php } ?>
					</ul>
				</nav>
				<!-- End Sidebar navigation -->
			</div>
			<!-- End Sidebar scroll-->
		</div>
		<!-- End Left Sidebar  -->
		<!-- Page wrapper  -->
		<div class="page-wrapper">
			<!-- Bread crumb -->
			<div class="row page-titles">
				<div class="col-md-5 align-self-center">
					<h3 class="text-primary">
					<?php if(($this->uri->segment(1) != null)&&($this->uri->segment(1)!= 'plan'))
					{ 
						echo ucfirst($this->uri->segment(1));
					} 
					else if($this->uri->segment(2) != null) 
					{ 
						echo ucfirst($this->uri->segment(2));
					} 
					else 
					{
					?>
                    <?= ucfirst($this->lang->line('dashboard')); ?>
					<?php 
					} 
					?></h3>
				</div>
				<div class="col-md-7 align-self-center">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="javascript:void(0)" class="tip-bottom">Home</a>						</li>
                        
    					<li class="breadcrumb-item"> <a href="<?= base_url()?>" title="Painel de Controle" class="tip-bottom"> <?= ucfirst($this->lang->line('dashboard')); ?></a></li> 
	                    <?php
		if (($this->uri->segment(1) != null)&&($this->uri->segment(1)!= 'fedd')){ 
	?>
                      <li class="breadcrumb-item active"><a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) ?>" class="tip-bottom" title="<?= ucfirst($this->uri->segment(1));?>"><?= ucfirst($this->uri->segment(1)); ?></a></li>
   	<?php
		}
		if ($this->uri->segment(2) != null){
	?>
    					<li class="breadcrumb-item active"><a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>" class="current tip-bottom" title="<?= ucfirst($this->uri->segment(2)); ?>"><?= ucfirst($this->uri->segment(2));?></a></li>
    <?php
		}
	?>
					</ol>
			  </div>
			</div>
			<!-- End Bread crumb -->
			<!-- Container fluid  -->
			<div class="container-fluid">
				<!-- Start Page Content -->
				<div class="row">
					<div class="col-12">					
						<?php if(isset($view)){echo $this->load->view($view, null, true);}?>
					</div>
				</div>
				<!-- End PAge Content -->
			</div>
			<!-- End Container fluid  -->
			<!-- footer -->
			<footer class="footer text-center fixed-bottom" style="margin: 0">
				
				&copy; SPO - <?= ucfirst($this->lang->line('app_version')); ?>: <?= $this->config->item('app_version'); ?>
			</footer>

			<!-- End footer -->
		</div>
		<!-- End Page wrapper  -->
	</div>
	<!-- End Wrapper -->
	
	<!-- Bootstrap tether Core JavaScript -->
	<script src="<?= base_url('assets/js/lib/bootstrap/js/popper.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/lib/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<!-- slimscrollbar scrollbar JavaScript -->
	<script src="<?= base_url('assets/js/jquery.slimscroll.js'); ?>"></script>
	<!--Menu sidebar -->
	<script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
	<!--stickey kit -->
	<script src="<?= base_url('assets/js/lib/sticky-kit-master/dist/sticky-kit.min.js'); ?>"></script>
	<!--Custom JavaScript -->
	<script src="<?= base_url('assets/js/scripts.js'); ?>"></script>

	<script type="text/javascript">  
		$(document).ready(function () {
			
			<?php if($this->session->flashdata('success') != null){ ?>
		
				toastr.success('<?= $this->session->flashdata('success');?>','Atenção',{
					timeOut: 8000,
					"closeButton": true,
					"newestOnTop": true,
					"progressBar": true,
					"positionClass": "toast-top-right",
					"onclick": null,
				});

			<?php } ?>

			<?php if($this->session->flashdata('error') != null){?>
		
				toastr.error('<?= $this->session->flashdata('error');?>','Atenção',{
					timeOut: 8000,
					"closeButton": true,
					"newestOnTop": true,
					"progressBar": true,
					"positionClass": "toast-top-right",
					"onclick": null,
				});

			<?php } ?>

		});  
	</script>

</body>

</html>