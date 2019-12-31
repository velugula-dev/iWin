<?php $this->load->view(config_item('admin_directory').'/header');?>

<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/multiselect/sumoselect.min.css"/>
<div class="page-container">
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>

<?php
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('feedback_message_id','message');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editFeedbackDetail->$key);
  }
}
$addNotificationLabel    = "Feedback";       
$feedbackFormAction      = config_item('admin_base_url')."feedback/add";
?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Feedback</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
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
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">Manage Feedback</div>
                        </div>
                        <div class="portlet-body form">
                            <form action="<?php echo $feedbackFormAction;?>" id="form_add_feedback" name="form_add_feedback" method="post" class="form-horizontal" enctype="multipart/form-data" >
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
                                        <label class="control-label col-md-2">Feedback Message<span class="required">*</span></label>
                                        <div class="col-md-10">
                                            <input type="hidden" name="feedback_message_id" id="feedback_message_id" value="<?php echo $feedback_message_id;?>" />
                                            <textarea class="ckeditor form-control" name="message" id="message" rows="6" data-required="1" ><?php echo $message;?></textarea>
                                        </div>
                                    </div>                                    

                                    <div class="portlet box blue">
                                        <div class="portlet-title">
                                            <div class="caption">Feedback Questions</div>
                                        </div>
                                        <div class="portlet-body form clearfix">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label col-md-8">Question Name</label>
                                                    <label class="control-label col-md-2">Question Type</label>                                                    
                                                </div>
                                                <?php if(!empty($editFeedbackQuestionDetail))
                                                {
                                                    $j = 0;
                                                    foreach($editFeedbackQuestionDetail as $feedQuesKey =>$feedQuesDet)
                                                    {
                                                        $j++?>
                                                        <div class="form-group clonedInput" id="entry<?php echo $j;?>">
                                                            <div class="col-md-7">
                                                                <input type="hidden" name="feedback_question_id[<?php echo $j;?>]" id="feedback_question_id<?php echo $j;?>" value="<?php echo $feedQuesDet->feedback_question_id;?>">
                                                                <input type="text" class="form-control feedback_question" name="feedback_question[<?php echo $j;?>]" id="feedback_question<?php echo $j;?>" value="<?php echo $feedQuesDet->feedback_question;?>">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select name="feedback_question_type[<?php echo $j;?>]" id="feedback_question_type<?php echo $j;?>" class="form-control feedback_question_type">                                                   
                                                                    <option value="">Select Type</option>
                                                                    <option <?php echo ($feedQuesDet->feedback_question_type == 'Thumbs-up-down')?"selected":"";?> value="Thumbs-up-down">Thumbs Up/Down</option>
                                                                    <option <?php echo ($feedQuesDet->feedback_question_type == 'Text-input')?"selected":"";?> value="Text-input">Text Input</option>
                                                                </select>
                                                            </div>
                                                            <?php if($j == 1)
                                                            {?>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-danger danger-btn" id="btnAddFeedbackQues">+Add</button>
                                                                </div>                                        
                                                            <?php }?>
                                                            <div class="col-md-2">
                                                                <div class="right">
                                                                    <input type="button" value="Remove" id="btnDelFeedbackQues<?php echo $j;?>" class="btnDelFeedbackQues btn btn-danger danger-btn" name="btnDelFeedbackQues" data-num="<?php echo $j;?>">                                                        
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                }
                                                else
                                                {?>
                                                    <div class="form-group clonedInput" id="entry1">
                                                        <div class="col-md-7">
                                                            <input type="hidden" name="feedback_question_id[1]" id="feedback_question_id1" value="">
                                                            <input type="text" class="form-control feedback_question" name="feedback_question[1]" id="feedback_question1">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select name="feedback_question_type[1]" id="feedback_question_type1" class="form-control feedback_question_type">                                                   
                                                                <option value="">Select Type</option>
                                                                <option value="Thumbs-up-down">Thumbs Up/Down</option>
                                                                <option value="Text-input">Text Input</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger danger-btn" id="btnAddFeedbackQues">+Add</button>
                                                        </div>                                        
                                                        <div class="col-md-2">
                                                            <div class="right">
                                                                <input type="button" value="Remove" id="btnDelFeedbackQues" class="btnDelFeedbackQues hide btn btn-danger danger-btn" name="btnDelFeedbackQues">                                                        
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <div id="newFeedbackQuestion">                                        
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <input type="submit" name="submitFeedback" id="submitFeedback" value="Submit" class="btn btn-success danger-btn">
                                        <a class="btn btn-danger danger-btn" href="<?php echo config_item('admin_base_url');?>feedback/view">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>scripts/clone-form-feedback-question.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
});
$('#submitFeedback').click(function() 
{                                      
    $('.clonedInput .feedback_question').each(function () 
    {   
        $(this).rules('add', {
            required: true
        });
    });
    $('.clonedInput .feedback_question_type').each(function () 
    {   
        $(this).rules('add', {
            required: true
        });
    });
});
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>