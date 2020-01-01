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
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>
                        <div class="modal-body">
                             Widget settings form goes here
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn blue">Save changes</button>
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    System Options
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            System Options 
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
                                System Options
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url().config_item('admin_directory');?>/system_option/view" method="post" id="SystemOption" name="SystemOption" class="form-horizontal">
                                <div class="form-body">
                            <?php 
                            if($this->session->flashdata('SystemOptionMSG'))
                            {?>
                                <div class="alert alert-success">
                                    <strong>Success!</strong> <?php echo $this->session->flashdata('SystemOptionMSG');?>
                                </div>
                            <?php } ?>
                                    
                                    <?php
                                    foreach ($SystemOptionList as $key => $OptionDet) 
                                    { ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><?php echo $OptionDet->option_name;?></label>
                                            <div class="col-md-8">
                                                <input type="hidden" name="system_option_id[]" value="<?php echo $OptionDet->system_option_id; ?>">
                                                <input type="text" name="option_value[]" value="<?php echo htmlentities($OptionDet->option_value); ?>" maxlength="250" class="form-control">
                                            </div>
                                        </div>

                                    <?php } ?>                              
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-2 col-md-9">
                                        <input type="submit" name="SubmitSystemSetting" id="SubmitSystemSetting" class="btn danger-btn" value="Submit">
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
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
</script>

<?php $this->load->view(config_item('admin_directory').'/footer');?>
