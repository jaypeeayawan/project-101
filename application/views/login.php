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
		<meta name="author" content="Jaypee E. Ayawan, BSIT">

		<!-- Bootstrap -->
		<link href="<?php echo base_url(); ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="<?php echo base_url(); ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<!-- NProgress -->
		<link href="<?php echo base_url(); ?>vendors/nprogress/nprogress.css" rel="stylesheet">
		<!-- Animate.css -->
		<link href="<?php echo base_url(); ?>vendors/animate.css/animate.min.css" rel="stylesheet">
		<!-- Custom Theme Style -->
		<link href="<?php echo base_url(); ?>build/css/custom.min.css" rel="stylesheet">
	</head>
	
	<body class="login">
		<div>
			<div class="login_wrapper">
				<div id="login_div" class="animate form login_form">
					<section class="login_content">
						<?php echo form_open('', 'class="login-form" id="login-form"'); ?>
							<h1>Login Form</h1>
							<div>
								<?php echo form_input('username', '', 'class="form-control" placeholder="Username" data-parsley-required-message="Username field is required!" required'); ?>
							</div>
							<div>
								<?php echo form_password('password', '', 'class="form-control" placeholder="Password" data-parsley-required-message="Password field is required!" required'); ?>
							</div>
							<div>
								<?php echo form_submit('login', 'Log In', 'class="btn btn-default submit"'); ?>
								<a href="#" class="lost_password">Lost your password?</a>
							</div>
							<div class="clearfix"></div>
							<div class="separator">
								<?php echo br(); ?>
								<div>
									<h1><i class="fa fa-paw"></i> KCP</h1>
									<p>&copy; 2017.All Rights Reserved.<a href="http://kcp.edu.ph/">King's College of the Philippines.</a></p>
								</div>
							</div>
						<?php echo form_close(); ?>
					</section>
				</div>	
			
				<div id="forgot_pass_div" class="animate form forgot_pass_form">
					<section class="login_content">
						<?php echo form_open(base_url().'lostpassword/index/', 'class="forgot-pass-form" id="forgot-pass-form"'); ?>
							<h1>Forgot Password</h1>
							<div>
								<?php echo form_input('username', '', 'class="form-control" placeholder="Username" data-parsley-required-message="Username field is required!" required'); ?>
							</div>
							<div>
								<?php echo form_input('email', '', 'class="form-control" placeholder="Your Email Address" id="email" data-parsley-type="email" data-parsley-required-message="Email Address field is required" required="required"'); ?>
							</div>
							<div>
								<?php echo form_submit('forgotPass', 'Submit', 'class="btn btn-default submit"'); ?>
								<a href="#" class="to_login"> Log in here</a>
							</div>
							<div class="clearfix"></div>						
							<div class="separator">
								<?php echo br(); ?>
								<div>
									<h1><i class="fa fa-paw"></i> KCP</h1>
									<p>&copy; 2017.All Rights Reserved.<a href="http://kcp.edu.ph/">King's College of the Philippines.</a></p>
								</div>
							</div>
						<?php echo form_close(); ?>
					</section>
				</div>	
			</div>
		</div>
		<script type="text/javascript" src="<?php echo base_url(); ?>vendors/jquery/dist/jquery.min.js"></script>
		<!-- Parsley -->
		<script type="text/javascript" src="<?php echo base_url(); ?>vendors/parsleyjs/dist/parsley.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				
				setFavicon();
				
				function setFavicon() {
					var link = $('link[type="image/x-icon"]').remove().attr("href");
					$('<link href="'+ link +'" rel="shortcut icon" type="image/x-icon" />').appendTo('head');
				}

				$('.submit').on('click', function(){
					$('#login-form').parsley().validate();
				});
				
				$('#forgot_pass_div').hide();
				$('.lost_password').on('click', function(){
					$('#login_div').fadeOut('fast', function(){
						$('#forgot_pass_div').fadeIn('slow', function(){
												
						});	
					});
				});
				
				$('.to_login').on('click', function(){
					$('#forgot_pass_div').fadeOut('fast', function(){
						$('#login_div').fadeIn('slow', function(){
							
						});	
					});
				});
				
				$('.submit').on('click', function(){
					$('#forgot-pass-form').parsley().validate();
				});				
				
			});
		</script>
	</body>
</html>
