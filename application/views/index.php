<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="<?php echo base_url(); ?>images/kcp_logo.png" type="image/x-icon">
		<title><?php echo $title; ?></title>
		<meta name="description" content="King's College of the Philippines Asset Management Office">
		<meta name="author" content="Jaypee E. Ayawan">

		<?php $this->load->view('includes/css'); ?>

	</head>

	<body class="nav-md">
		<div class="container body">
			<div class="main_container">
				<div class="col-md-3 left_col">
				<div class="left_col scroll-view">
				<div class="navbar nav_title" style="border: 0;">
				  <a href="<?php echo base_url().''.$getController; ?>/dashboard/" class="site_title"><i class="fa fa-paw"></i> <span>KCP</span></a>
				</div>

				<div class="clearfix"></div>

				<?php echo br(); ?>

				<!-- sidebar menu -->
				<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
					<div class="menu_section">
						<ul class="nav side-menu">
							<li><a href="<?php echo base_url().''.$getController; ?>/dashboard/"><i class="fa fa-home"></i> Dashboard </a></li>
							<li><a><i class="fa fa-university"></i> School Section <span class="fa fa-chevron-down"></span></a>
								<ul class="nav child_menu">
									<li><a href="<?php echo base_url().''.$getController; ?>/departmentManager/">Department Manager</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/userManager/"> User Manager</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/employeeManager/"> Employee Manager</a></li>
								</ul>
							</li>
							<li><a><i class="fa fa-desktop"></i> Item Manager <span class="fa fa-chevron-down"></span></a>
								<ul class="nav child_menu">
									<li><a href="<?php echo base_url().''.$getController; ?>/supplierManager/">Supplier Manager</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/categoryManager/">Category Manager</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/itemManager/">Item Manager</a></li>
								</ul>
							</li>
							<li><a><i class="fa fa-shopping-cart"></i> Item Status <span class="fa fa-chevron-down"></span></a>
								<ul class="nav child_menu">
									<li><a>Assigned Item<span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu">
											<li><a href="<?php echo base_url().''.$getController; ?>/assignedNonConsumableItemManager/">Non Consumable Items</a></li>
											<li><a href="<?php echo base_url().''.$getController; ?>/assignedConsumableItemManager/">Consumable Items</a></li>
										</ul>
									</li>
									<li><a href="<?php echo base_url().''.$getController; ?>/stockManager/">Stock Items</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/disposedItemManager/">Disposed Items</a></li>
								</ul>
							</li>
							<li><a><i class="fa fa-cog"></i> System Section <span class="fa fa-chevron-down"></span></a>
								<ul class="nav child_menu">
									<li><a href="<?php echo base_url().''.$getController; ?>/trashedItemManager/">Trashed Items</a></li>
									<li><a href="<?php echo base_url().''.$getController; ?>/systemLogsManager/">System Logs</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<!-- /sidebar menu -->
			  </div>
			</div>

			<!-- top navigation -->
			<div class="top_nav">
				<div class="nav_menu">
					<nav>
						<div class="nav toggle">
							<a id="menu_toggle"><i class="fa fa-bars"></i></a>
						</div>

						<ul class="nav navbar-nav navbar-right">
							<li class="">
								<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<?php $this->load->view($getController.'/userLoggedIn'); ?>
									<span class=" fa fa-angle-down"></span>
								</a>
								<ul class="dropdown-menu dropdown-usermenu pull-right">
									<li><a href="<?php echo base_url().''.$getController.'/generalSettings/'; ?>"><i class="fa fa-cog"></i> Settings</a></li>
									<li><a href="<?php echo base_url().''.$getController.'/logout/'; ?>"><i class="fa fa-sign-out"></i> Log Out</a></li>
								</ul>
							</li>

							<!-- <li role="presentation" class="dropdown">
							<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-envelope-o"></i>
								<span class="badge bg-green"><?php echo $activeUsersCount; ?></span>
							</a>
								<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
								<li>
									<a><span class="message">Stock Items</span></a>
								</li>
								<li>
									<a><span class="message">Assigned Items</span></a>
								</li>
								<li>
									<a><span class="message">Disposed Items</span></a>
								</li>
								<li>
									<a><span class="message">Trashed Items</span></a>
								</li>
								</ul>
							</li> -->
						</ul>
					</nav>
				</div>
			</div>
			<!-- /top navigation -->

			<!-- page content -->
			<div class="right_col" role="main">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="page-title">
							<div class="title-left">
								<?php echo $pageTitle; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php echo $content; ?>
					</div>
				</div>
			</div>
			<!-- /page content -->

			<!-- footer content -->
			<footer>
				<div class="pull-right">&copy; 2017 All Rights Reserved.<a href="http://kcp.edu.ph/"> King's College of the Philippines</a>.</div>
				<div class="clearfix"></div>
			</footer>
			<!-- /footer content -->
		</div><!-- /container body -->
    </div><!-- /main-container -->

	<?php $this->load->view('includes/js'); ?>
	<?php $this->load->view($getController.'/'.$js); ?>
	<script type="text/javascript">
		$(document).ready(function(){
			setFavicon();
			function setFavicon() {
				var link = $('link[type="image/x-icon"]').remove().attr("href");
				$('<link href="'+ link +'" rel="shortcut icon" type="image/x-icon" />').appendTo('head');
			}
	});
	</script>

	</body>
</html>
