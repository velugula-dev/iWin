<?php $this->load->view(config_item('admin_directory').'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/select2/select2.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->

<div class="page-container">

    <!-- BEGIN sidebar -->
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>
    <!-- END sidebar -->

    <!-- BEGIN CONTENT -->

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    CMS Management
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            CMS Management 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                CMS Management
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url().config_item('admin_directory');?>/cms/view" method="post" id="cms_mgmt" name="cms_mgmt" class="form-horizontal">
                                <div class="form-body">
                                    <?php 
                                    if($this->session->flashdata('cmsMSG'))
                                    {?>
                                        <div class="alert alert-success">
                                            <strong>Success!</strong> <?php echo $this->session->flashdata('cmsMSG');?>
                                        </div>
                                    <?php } ?>                                    
                                    <?php
                                    foreach ($cmsList as $key => $value) 
                                    {?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $value->cms_title;?></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="cms_id[<?php echo $key;?>]" value="<?php echo $value->cms_id;?>">
                                                <textarea class="ckeditor form-control" name="cms_contents[<?php echo $key;?>]" rows="6" data-required="1" ><?php echo $value->cms_contents;?></textarea>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-2 col-md-9">
                                        <input type="submit" name="SubmitCMS" id="SubmitCMS" class="btn danger-btn" value="Submit">
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-markdown/js/bootstrap-markdown.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-markdown/lib/markdown.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL STYLES -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
</script>

<?php $this->load->view(config_item('admin_directory').'/footer');?>
