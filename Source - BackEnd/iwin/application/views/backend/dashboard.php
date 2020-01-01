<?php $this->load->view(config_item('admin_directory').'/header');?>
<div class="page-container">
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>
    <div class="page-content-wrapper">
        <div class="page-content admin-dashboard">          
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-title">Dashboard</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>Dashboard </li>                        
                    </ul>
                </div>
            </div>  
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="total_users"><?php echo $total_users;?></div>
                            <div class="desc">Total Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat green-haze">
                        <div class="visual">
                            <i class="fa fa-pencil-square-o"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="total_quizzes"><?php echo $total_quizzes;?></div>
                            <div class="desc">Total Quizzes</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-pencil-square-o"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="user_attempted_quizzes"><?php echo $user_attempted_quizzes;?></div>
                            <div class="desc">User's Attempted Quizzes</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat red-intense">
                        <div class="visual">
                            <i class="fa fa-question"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="total_questions"><?php echo $total_questions;?></div>
                            <div class="desc">Total Questions</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat green-haze">
                        <div class="visual">
                            <i class="fa fa-pencil-square-o"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="total_attempted_questions"><?php echo $total_attempted_questions;?></div>
                            <div class="desc">Attempted Questions</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="dashboard-stat blue-madison">
                        <div class="visual">
                            <i class="fa fa-check"></i>
                        </div>
                        <div class="details">
                                <div class="number" id="total_correct_attempted_ques"><?php echo $total_correct_attempted_ques;?></div>
                            <div class="desc">Correct Attempted Questions</div>
                        </div>
                    </div>
                </div>
            </div>                                        
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-question"></i>Today's Quiz Questions
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="todays_questions_list">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                Question Name
                                            </th>
                                            <th>
                                                 Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($todays_questions))
                                        {
                                            $i=1;
                                            foreach ($todays_questions as $key => $value) 
                                            {?>
                                                <tr class="odd gradeX">
                                                    <td><?php echo $i;?></td>
                                                    <td><?php echo $value->question_name;?></td>
                                                    <td><?php echo ($value->status ==1)?"Active":"Inactive";?></td>
                                                </tr>
                                            <?php $i++;
                                            }
                                        }
                                        else
                                        {?>
                                            <tr class="odd gradeX">
                                                <td colspan="3">No Questions found</td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
                <div class="col-md-6 col-sm-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-user"></i>Quiz attempting Users
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="current_user_attempting_quiz">
                            <thead>
                            <tr>
                                <th style="width1:8px;">#</th>
                                <th>
                                    User Name
                                </th>
                                <th>
                                     Question Name
                                </th>
                                <th>
                                     Date
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(!empty($quiz_attempting_users))
                                {
                                    $ij = 1;
                                    $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
                                    foreach ($quiz_attempting_users as $runningQues) 
                                    {
                                        $dt = new DateTime($runningQues->current_quiz_question_date, new DateTimeZone('UTC'));
                                        $dt->setTimezone(new DateTimeZone($user_local_timezone));?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $ij;?></td>
                                            <td><?php echo $runningQues->first_name." ".$runningQues->last_name;?></td>
                                            <td><?php echo $runningQues->question_name;?></td>
                                            <td><?php echo $dt->format('Y-m-d h:i A');?></td>
                                        </tr>
                                    <?php $ij++;
                                    }?>
                                <?php }
                                else
                                {?>
                                    <tr class="odd gradeX">
                                        <td colspan="2">No Users Attempting Quiz</td>
                                    </tr>
                                <?php }?>
                            </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>            
    </div>
</div>
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function() {    
   Metronic.init();
   Layout.init(); // init layout   
    setInterval(getUserAttemptingQuizList, 35000);
    setInterval(getAdminDashboardCount, 40000);
    setInterval(getCorrectAttemptedQuesCount, 60000*2);
    setInterval(getTodaysQuizQuestionsList, 60000*5);
});
function getAdminDashboardCount()
{
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : 'dashboard/getAdminDashboardCount',
      success: function(response) 
      {
        $("#total_users").html(response.total_users);
        $("#total_quizzes").html(response.total_quizzes);
        $("#user_attempted_quizzes").html(response.user_attempted_quizzes);
        $("#total_questions").html(response.total_questions);
        $("#total_attempted_questions").html(response.total_attempted_questions);
      }
    });
}
function getCorrectAttemptedQuesCount()
{
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : 'dashboard/getCorrectAttemptedQuesCount',
      success: function(response) 
      {
        $("#total_correct_attempted_ques").html(response);
      }
    });
}
function getTodaysQuizQuestionsList()
{
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : 'dashboard/getTodaysQuizQuestionsList',
      success: function(response) 
      {
        $("#todays_questions_list tbody").html(response);
      }
    });
}
function getUserAttemptingQuizList()
{
    jQuery.ajax({
      type : "POST",
      dataType : "json",
      url : 'dashboard/getUserAttemptingQuizList',
      success: function(response) 
      {
        $("#current_user_attempting_quiz tbody").html(response);
      }
    });
}
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>