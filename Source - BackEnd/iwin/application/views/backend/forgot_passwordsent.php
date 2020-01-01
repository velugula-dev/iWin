<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $this->lang->line('site_title');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo config_item('admin_assets_url');?>css/login.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo config_item('admin_assets_url');?>css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?php echo config_item('admin_assets_url');?>img/favicon.png"/>
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="<?php echo config_item('admin_base_url');?>">
      <img src="<?php echo config_item('admin_assets_url');?>img/header_logo.png" alt="<?php $this->lang->line('site_title') ?>"/>
    </a>
</div>
<!-- END LOGO -->
<div class="main">
  <div class="container">
    <!-- BEGIN SIDEBAR & CONTENT -->
    <div class="row">
      <!-- BEGIN CONTENT -->
        <div class="col-md-8 front-from">
          <div class="informatoin-content">
              <h2 class="title-type1 text-center">Password Assistance</h2>
              <strong>Check your e-mail.</strong>
              <hr class="red">
              <p>You will receive an email from us with instructions for resetting your password. If you don't receive this email, please check your junk mail folder.</p>
              <p><a href="<?php echo config_item('admin_base_url');?>">Return home </a></p>
          </div>  
      </div>
    </div>
  </div>
</div>
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    <?php echo date('Y'); ?> &copy; <?php echo $this->lang->line('site_title');?>
</div>
</body>
</html>