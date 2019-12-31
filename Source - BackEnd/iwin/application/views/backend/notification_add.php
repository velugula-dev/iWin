<?php $this->load->view(config_item('admin_directory').'/header');?>

<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/multiselect/sumoselect.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/css/datepicker.css"/>
<div class="page-container">
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>

<?php
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('notification_id','notification_contents');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editNotificationDetail->$key);
  }
}
$addNotificationLabel    = "Send Notification";       
$cmsFormAction      = config_item('admin_base_url')."notification/add";
?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Notification</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo config_item('admin_base_url');?>/notification/view">Notification</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $addNotificationLabel;?> 
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
                            <div class="caption"><?php echo $addNotificationLabel;?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $cmsFormAction;?>" id="form_add_notification" name="form_add_notification" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Select Notification Type<span class="required">*</span></label>
                                        <div class="col-md-6">                                            
                                            <select name="notification_type" class="form-control" id="notification_type">                                            
                                                <option value="">Select Notification Type</option>
                                            <?php 
                                            foreach (notification_types() as $not_key => $not_val) 
                                            {?>
                                                <option value="<?php echo $not_key?>"><?php echo $not_val;?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Select User(s)<span class="required">*</span></label>
                                        <div class="col-md-6">                                            
                                            <select name="Users[]" placeholder="Select Users" multiple="multiple" class="form-control" id="Users">                                            
                                                <?php 
                                                foreach ($users as $key => $user) {?>
                                                    <option value="<?php echo $user->user_id?>"><?php echo $user->first_name.' '.$user->last_name;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Notification Message<span class="required">*</span></label>
                                        <div class="col-md-6">
                                            <input type="hidden" name="notification_id" id="notification_id" value="<?php echo $notification_id;?>" />
                                            <input type="text" name="notification_contents" id="notification_contents" value="<?php echo $notification_contents;?>" maxlength="249" data-required="1" class="form-control"/>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Notification Date<span class="required">*</span></label>
                                        <div class="col-md-6">                                            
                                            <input type="text" name="notification_date" id="notification_date" class="form-control" readonly value="<?php echo ($notification_date)?date('Y-m-d',strtotime($notification_date)):date("Y-m-d");?>">                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <input type="submit" name="submitNotification" id="submitNotification" value="Submit" class="btn btn-success danger-btn">
                                        <a class="btn btn-danger danger-btn" href="<?php echo config_item('admin_base_url');?>notification/view">Cancel</a>
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
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/multiselect/jquery.sumoselect.min.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    $( "#Users" ).SumoSelect({search: true,selectAll:true});
});
$('#notification_date').datepicker({               
        autoclose:true,
        format: 'yyyy-mm-dd',
        endDate: '+0d'
});

</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>