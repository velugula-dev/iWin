<?php $this->load->view(config_item('admin_directory').'/header');?>

<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/multiselect/sumoselect.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/css/datepicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<div class="page-container">
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>

<?php
if($this->input->post()){
  foreach ($this->input->post() as $key => $value) {
    $$key = @htmlspecialchars($this->input->post($key));
  } 
} else {
  $FieldsArray = array('question_id','question_type','question_name','question_date','answer_restrict_time','question_detail');
  foreach ($FieldsArray as $key) {
    $$key = @htmlspecialchars($editQuestionDetail->$key);
  }
}
if(!empty($editQuestionDetail))
{
    $addEditQuestionLable    = "Edit Question";       
}
else
{
    $addEditQuestionLable    = "Add Question";       
}
if($answer_restrict_time >0)
{
    $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
    $answer_restrict_time = ($answer_restrict_time > 0)?$answer_restrict_time:'00:00:00';

    $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone($user_local_timezone));
    $question_date = $dt->format('Y-m-d');
    $answer_restrict_time = $dt->format('H:i:s');    
}?>
    <div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Question</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>question/view">
                            Question Management </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $addEditQuestionLable;?> 
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
                            <div class="caption"><?php echo $addEditQuestionLable;?></div>
                        </div>
                        <div class="portlet-body form">
                            <form action="" id="form_add_question" name="form_add_question" method="post" class="form-horizontal">
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
                                        <label class="control-label col-md-3">Question<span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <input type="hidden" name="question_id" id="question_id" value="<?php echo $question_id;?>" />
                                            <input type="hidden" name="user_local_timezone" id="user_local_timezone">
                                            <textarea class="form-control" name="question_name" id="question_name" rows="6" data-required="1" ><?php echo $question_name;?></textarea>
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Question Type<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <select name="question_type" id="question_type" class="form-control" <?php echo ($editQuestionDetail)?"disabled":"";?>>
                                                <option value="">Select a Question Type</option>
                                                <option <?php echo ($question_type == "YesNo")?"selected":"";?> value="YesNo">Yes/No</option>
                                                <option <?php echo ($question_type == "SingleChoice")?"selected":"";?> value="SingleChoice">Single Choice</option>
                                                <option <?php echo ($question_type == "MultiChoice")?"selected":"";?> value="MultiChoice">Multi Choice</option>
                                            </select>
                                        </div>                                        
                                    </div>        
                                    <div class="portlet box blue" id="question_answer_main" style="display: <?php echo ($question_type !='')?'block':'none';?>">
                                        <div class="portlet-title">
                                            <div class="caption">Answers</div>
                                        </div>   
                                        <div class="portlet-body form clearfix">
                                            <div class="form-body"> 
                                            <div class="col-md-12">
                                                <div id="my_alert_container"></div>
                                                <div class="YesNo box">
                                                    <?php 
                                                    if($question_type == "YesNo")
                                                    {
                                                        $this->load->view(config_item('admin_directory').'/question_tf_add');
                                                    }?>
                                                </div>
                                                <div class="SingleChoice box">
                                                    <?php 
                                                    if($question_type == "SingleChoice")
                                                    {
                                                        $this->load->view(config_item('admin_directory').'/question_mc_add');
                                                    }?>
                                                </div>
                                                <div class="MultiChoice box">
                                                    <?php 
                                                    if($question_type == "MultiChoice")
                                                    {
                                                        $this->load->view(config_item('admin_directory').'/question_mr_add');
                                                    }?>
                                                </div>

                                                <div id="newChoice"></div>

                                                <div class="add-more-btn right">                                               
                                                    <div class="form-group"> 
                                                        <div class="col-md-8"></div>
                                                        <div class="col-md-4">
                                                            <button class="btn danger-btn" type="button" id="btnAdd">Add another Answer to the question</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="newChoiceMR"></div>
                                                <div class="add-more-btn right">                                               
                                                    <div class="form-group"> 
                                                        <div class="col-md-8"></div>
                                                        <div class="col-md-4">
                                                            <button class="btn danger-btn" type="button" id="btnMR">Add another Answer to the question</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>                  
                                            </div>             
                                        </div>
                                    </div>
                                    <div class="form-group" id="question_date_main" style="display: <?php echo ($question_type !='')?'block':'none';?>">
                                        <label class="control-label col-md-3">Question Date<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="date date-picker" data-date-format="yyyy-mm-dd">
                                                    <input id="question_date" type="text" class="form-control" readonly name="question_date" value="<?php echo ($question_date)?date('Y-m-d',strtotime($question_date)):date("Y-m-d");?>">                                            
                                            </div>
                                        </div>
                                    </div>                                                                                
                                    <div class="form-group" id="answer_restrict_main" style="display: <?php echo ($question_type !='')?'block':'none';?>">
                                        <label class="control-label col-md-3">Answer Restrict Time<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" class="form-control timepicker timepicker-24" name="answer_restrict_time" id="answer_restrict_time" value="<?php echo ($answer_restrict_time > 0)?$answer_restrict_time:'';?>">
                                                <span class="input-group-btn">
                                                <button class="btn default" type="button"><i class="fa fa-clock-o"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>                                                                                
                                    <div class="form-group" id="question_detail_main" style="display: <?php echo ($question_type !='')?'block':'none';?>">
                                        <label class="control-label col-md-3">Question Detail</label>
                                        <div class="col-md-9">
                                            <textarea class="ckeditor form-control" name="question_detail" id="question_detail" rows="6" data-required="1" ><?php echo $question_detail;?></textarea>
                                        </div>
                                    </div>   
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <input type="submit" name="submitQuestion" id="submitQuestion" value="Submit" class="btn btn-success danger-btn">
                                        <a class="btn btn-danger danger-btn" href="<?php echo config_item('admin_base_url');?>question/view">Cancel</a>
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
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/jstz.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo config_item('admin_assets_url');?>pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>scripts/clone-form-question-multichoice.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>scripts/clone-form-question-multiresponse.js"></script>

<script>
$(document).ready(function(){
    Layout.init(); // init current layout

    var timezone = jstz.determine().name();
    $('#user_local_timezone').val(timezone);

    var question_type = '<?php echo $question_type;?>';
    $('.timepicker-24').timepicker({
        autoclose: true,
        minuteStep: 5,
        showSeconds: false,
        showMeridian: false,
        defaultTime:''
    });
    $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function(e){
        e.preventDefault();
        $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
    });

    if(question_type == "YesNo")
    {
        $("#btnAdd").hide();
        $("#btnMR").hide();
        var formUrl = '<?php echo base_url()?>'+"backend/question/editTFQuestion";
        $("#form_add_question").attr('action', formUrl);
        return false;
    }
    else if(question_type == 'SingleChoice')
    {
        var formUrl = '<?php echo base_url()?>'+"backend/question/editSinglChoiceQuestion";
        $("#form_add_question").attr('action', formUrl);

         $("#btnAdd").show();
         $("#btnMR").hide();    
         return false;
    }
    else if(question_type == 'MultiChoice')
    {
        var formUrl = '<?php echo base_url()?>'+"backend/question/editMultiChoiceQuestion";
        $("#form_add_question").attr('action', formUrl);

        $("#btnMR").show();
        $("#btnAdd").hide();   
        return false;
    } 
    $('#question_date').datepicker({               
            autoclose:true,
            format: 'yyyy-mm-dd'
    });
});

$("#question_type").change(function()
{ 
    var divName;  
    var baseUrl = "<?php echo base_url()?>backend/question/";
    var url;
    var question_type = $(this).val();      
    $('#newChoice').html('');
    $('#newChoiceMR').html('');

    $(this).find("option:selected").each(function()
    {
        $('.YesNo').empty();
        $('.SingleChoice').empty();
        $('.MultiChoice').empty();

        if($(this).attr("value")=="YesNo")
        {
            var formUrl = baseUrl + "addTFQuestion";
            url = baseUrl + "TFQuestion";
            divName = ".YesNo";
            $('#question_answer_main').show();
            $('#question_detail_main').show();
            $('#question_date_main').show();
            $('#answer_restrict_main').show();

            $('.help-block').hide();
            $("#btnAdd").hide();
            $("#btnMR").hide();
            $("#form_add_question").attr('action', formUrl);
        }
        else if($(this).attr("value")=="SingleChoice")
        {    
            var formUrl = baseUrl + "addMCQuestion/";
            url = baseUrl + "MCQuestion";
            divName = ".SingleChoice"; 
            $("#form_add_question").attr('action', formUrl);
            $('#question_answer_main').show();
            $('#question_detail_main').show();
            $('#question_date_main').show();
            $('#answer_restrict_main').show();
            $('.help-block').hide();
            $("#btnAdd").show();
            $("#btnMR").hide();
        }
        else if($(this).attr("value")=="MultiChoice")
        {
            var formUrl = baseUrl + "addMRQuestion/";
            url = baseUrl + "MRQuestion";
            divName = ".MultiChoice";
            $("#form_add_question").attr('action', formUrl);
            $('#question_answer_main').show();
            $('#question_detail_main').show();
            $('#question_date_main').show();
            $('#answer_restrict_main').show();
            $('.help-block').hide();
            $("#btnAdd").hide();
            $("#btnMR").show();
        }
        $.ajax({
            type: "POST",
            url: url,
            success: function(data){            
                $(divName).empty().append(data);
                $(divName).show();          
            }
        }); 
    });
});
$('#submitQuestion').click(function() {                                      
    $('.clonedInput .option').each(function () {   
            $(this).rules('add', {
                required: true
            });
    }); 

    if($('#question_type').val() == "YesNo")
    {
        var b=0;
        $(".radio-choice:checked").each(function() {                                    
            if($(this).val() == 1)
            {
                b++;
            }
        });
        if(b>1){            
          Metronic.alert({
              container: '#my_alert_container', // alerts parent container(by default placed after the page breadcrumbs)
              place: 'prepent', // append or prepent in container 
              type: 'danger',  // alert's type
              message: 'You have more than one correct answer specified. You must have only one correct answer selected with True False questions. If you wish to have more than one choice then choose a question type of Multiple Response.',  // alert's message
              close: 1, // make alert closable
              reset: 1, // close all previouse alerts first
              focus: 1, // auto scroll to the alert after shown
              closeInSeconds: 5, // auto close after defined seconds
              icon: 'warning' // put icon before the message
          });
          return false;
      }           
    }                                                                    
    else if($('#question_type').val() == "SingleChoice")
    {
        var b=0;
        $(".radio-choice:checked").each(function()
        {                                    
            if($(this).val() == 1)
            {
                b++;
            }
        });
        if(b>1)
        {            
            Metronic.alert({
              container: '#my_alert_container', // alerts parent container(by default placed after the page breadcrumbs)
              place: 'prepent', // append or prepent in container 
              type: 'danger',  // alert's type
              message: 'You have more than one correct answer specified. You must have only one correct answer selected with Single Choices questions. If you wish to have more than one choice then choose a question type of Multiple Choice Question.',  // alert's message
              close: 1, // make alert closable
              reset: 1, // close all previouse alerts first
              focus: 1, // auto scroll to the alert after shown
              closeInSeconds: 30, // auto close after defined seconds
              icon: 'warning' // put icon before the message
            });
            return false;
        }           
    }                                                                                                                                        
    if($("#addQuestion").valid()){
        return true;
    }           
    var errorDiv = $('.error:visible').first();
    var scrollPos = errorDiv.offset().top;
    $(window).scrollTop(scrollPos);
});
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>