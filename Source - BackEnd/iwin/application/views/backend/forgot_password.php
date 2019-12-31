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
    <img src="<?php echo config_item('admin_assets_url');?>img/header_logo.png" alt=""/>
</div>
<!-- END LOGO -->
<!-- END sidebar TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <?php if(validation_errors()){?>
        <div class="alert alert-danger">
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
    <form class="forget-password-form isautovalid" id="forget-password-form" action="<?php echo config_item('admin_base_url');?>home/forgotpassword" method="post">
        <h3>Forgot Password ?</h3>
        <p>
             Enter your e-mail address below to reset your password.
        </p>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control placeholder-no-fix required emailcustome" type="text" autocomplete="off" placeholder="Email" name="email_address" id="email_address" />
            </div>
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
     <?php echo date('Y');?> &copy; <?php echo $this->lang->line('site_title');?>
</div>
<!--[if lt IE 9]>
    <script src="<?php echo config_item('admin_assets_url');?>plugins/respond.min.js"></script>
    <script src="<?php echo config_item('admin_assets_url');?>plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js" type="text/javascript"></script>
</body>
</html>