<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $MetaTitle;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo config_item('admin_assets_url');?>css/components.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo config_item('admin_assets_url');?>css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo config_item('admin_assets_url');?>layout/css/custom.css" rel="stylesheet">
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?php echo config_item('admin_assets_url');?>img/favicon.png"/>
<script>
    var BASEURL = '<?php echo config_item('admin_base_url');?>';
</script>
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
<script src="<?php echo config_item('admin_assets_url');?>plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

</head>
<body class="page-header-fixed">
<!-- BEGIN header -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN header INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo config_item('admin_base_url');?>">
            <img src="<?php echo config_item('admin_assets_url');?>img/header_logo.png" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </div>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <span class="username">
                    <?php echo $this->session->userdata('adminFirstname')." ".$this->session->userdata('adminLastname');?> </span>
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">                        
                        <li>
                            <a href="<?php echo config_item('admin_base_url');?>myprofile/getUserProfile">
                            <i class="fa fa-user"></i> My Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo config_item('admin_base_url');?>home/logout">
                            <i class="fa fa-key"></i> Log Out </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
        </div>
    
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END header INNER -->
</div>
<!-- END header -->
<div class="clearfix">
</div>
