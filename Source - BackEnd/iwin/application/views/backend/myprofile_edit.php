<?php $this->load->view(config_item('admin_directory').'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>
<!-- END sidebar -->
<?php
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {    
  $FieldsArray = array('user_id','first_name','last_name','email','phone','address');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editUserDetail->$key);
  }
}?>   
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">My Profile</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>dashboard">Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>                        
                        <li>My Profile</li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE header-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <ul id="myTab" class="nav nav-tabs">
                        <li <?php echo ($selected_tab == "" || $selected_tab == "UserInfo")?"class='active'":"";?>><a href="#UserInfo" data-toggle="tab">User Information</a></li>
                        <li <?php echo ($selected_tab == "ChangePassword")?"class='active'":"";?>><a href="#ChangePass" data-toggle="tab">Change Password</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade <?php echo ($selected_tab == "" || $selected_tab == "UserInfo")?"in active":"";?>" id="UserInfo">
                    <!-- BEGIN VALIDATION STATES-->
                        <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">My Profile</div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo config_item('admin_base_url')."myprofile/getUserProfile";?>" id="form_edit_editor" name="form_edit_editor" method="post" class="form-horizontal isautovalid" enctype="multipart/form-data">
                                <div class="form-body">
                                	<?php if($this->session->flashdata('myProfileMSG')){?>
	                                <div class="alert alert-success">
	                                    <strong>Success!</strong> <?php echo $this->session->flashdata('myProfileMSG');?>
	                                </div>
                            		<?php } ?>
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger"><?php echo validation_errors();?></div>
                                    <?php } ?>
                                    <div class="form-group">  
                                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" />
                                        <label class="control-label col-md-3">First Name <span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="first_name" id="first_name" value="<?php echo htmlentities($first_name);?>" maxlength="240" class="form-control required"/>
                                        </div>                                        
                                    </div>                                                                                                                
                                    <div class="form-group">                                        
                                        <label class="control-label col-md-3">Last Name <span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="last_name" id="last_name" value="<?php echo htmlentities($last_name);?>" maxlength="240" class="form-control required"/>
                                        </div>                                        
                                    </div>                                    
                                    <div class="form-group">                                        
                                        <label class="control-label col-md-3">Email <span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="email" id="email" value="<?php echo htmlentities($email);?>" onblur="checkEmailExist(this.value,<?php echo $this->session->userdata("adminID");?>);" maxlength="240" class="form-control required emailcustom"/>
                                            <div style="display:none;" class="error" id="EmailExist"></div>
                                        </div>                                        
                                    </div>                                                                                                         
                                    <div class="form-group">                                        
                                        <label class="control-label col-md-3">Phone </label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone" id="phone" value="<?php echo htmlentities($phone);?>" maxlength="15" data-required="1" class="form-control"/>
                                        </div>                                        
                                    </div>
                                    <div class="form-group">                                        
                                        <label class="control-label col-md-3">Address </label>
                                        <div class="col-md-4">
                                            <textarea name="address" id="address" class="form-control"><?php echo htmlentities($address);?></textarea>
                                        </div>                                        
                                    </div>                                    
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <input type="submit" class="btn danger-btn btn-sm" name="submitEditUser" id="submitEditUser" value="Submit">
                                        <a href="<?php echo config_item('admin_base_url');?>dashboard" class="btn danger-btn btn-sm">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                        </div>
                    </div>
                        <div class="tab-pane fade <?php echo ($selected_tab == "ChangePassword")?"in active":"";?>" id="ChangePass">
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    <div class="caption">Change Password</div>
                                </div>
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="<?php echo config_item('admin_base_url')."myprofile/getUserProfile";?>" method="post" name="userChangePass" id="userChangePass" class="form-horizontal isautovalid" enctype="multipart/form-data">
                                        <div class="form-body">
                                            <?php if(validation_errors()){?>
                                                <div class="alert alert-danger"><?php echo validation_errors();?></div>
                                            <?php } ?>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">New Password<span class="require">*</span></label>
                                                <div class="col-md-4">                                                	
                                                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id;?>">
                                                    <input type="password" name="Newpass" id="Newpass" class="form-control required" placeholder="New Password">
                                                    <label id="password_error" class="productFieldsError_custom"><?php echo $this->lang->line('password_repeat_error'); ?></label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Confirm Password<span class="require">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="password" name="confirmPass" id="confirmPass" class="form-control required" placeholder="Confirm Password" equalTo="#Newpass">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <input type="submit" class="btn danger-btn btn-sm" value="Submit" name="ChangePassword" id="ChangePassword">
                                                <a href="<?php echo config_item('admin_base_url');?>dashboard" class="btn danger-btn btn-sm">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>                              
                        </div>
                    <!-- END VALIDATION STATES-->
                </div>
                </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>pages/scripts/pwstrength.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script>
jQuery(document).ready(function() {           
    Layout.init(); // init current layout
    var options = {
        onLoad: function () {
            $('#messages').text('Start typing password');
        }
    };
    $('#Newpass').pwstrength(options);    
});
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>