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
<link href="<?php echo config_item('admin_assets_url');?>plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo config_item('admin_assets_url');?>plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/select2/select2-metronic.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/login.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo config_item('admin_assets_url');?>css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="<?php echo config_item('admin_assets_url');?>css/themes/default.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.png"/>
</head>
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
    <img src="<?php echo config_item('admin_assets_url');?>img/logo-big.jpg" alt=""/>
</div>
<!-- END LOGO -->
<!-- END sidebar TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <?php if(validation_errors()){?>
        <div class="alert alert-success">
            <?php echo $error; echo validation_errors();?>
        </div>
    <?php } ?>
    <?php if($this->session->flashdata('emailNotExist')){ ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('emailNotExist');?>
        </div>
    <?php } ?>
    <?php if($this->session->flashdata('verifyerr')){ ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('verifyerr');?>
        </div>
    <?php } ?>
    <form class="forget-password-form" action="<?php echo config_item('admin_base_url');?>home/newPassword" method="post" id="newPasswordform">
        <h3>New Password</h3>
        <p>
             Create your new password.
        </p>
        <div class="form-group">
            <input type="hidden" value="<?php echo $verification_code?>" name="verification_code" id="verification_code">
            <input class="form-control placeholder-no-fix" type="password" placeholder="Password" id="password" name="password"/>
        </div>
        <div class="form-group">
            <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Confirm Password">
        </div>
        <div class="form-actions">
            <a id="back-btn" class="btn default" href="<?php echo config_item('admin_base_url');?>home"><i class="m-icon-swapleft"></i> Back </a>
            <input type="submit" class="btn danger-btn pull-right" value="Submit" name="Submit" id="Submit">
        </div>
    </form>
    <!-- END FORGOT PASSWORD FORM -->
</div>
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
     2016 &copy; <?php echo $this->lang->line('site_title');?>
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
    <script src="<?php echo config_item('admin_assets_url');?>plugins/respond.min.js"></script>
    <script src="<?php echo config_item('admin_assets_url');?>plugins/excanvas.min.js"></script> 
    <![endif]-->
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-login-forgot-validation.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/select2/select2.min.js"></script>
</body>
<!-- END BODY -->
</html>

