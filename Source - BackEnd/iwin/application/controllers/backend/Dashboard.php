<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Dashboard extends CI_Controller {    
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
		$this->load->model(config_item('admin_directory').'/dashboard_model');
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }
    }
    public function index() {
        $arr['MetaTitle'] = $this->lang->line('title_admin_dashboard').' | '.$this->lang->line('site_title');        
        $arr['total_users'] = $this->dashboard_model->getusercount();
        $arr['total_quizzes'] = $this->dashboard_model->getTotalQuizzes();
        $arr['user_attempted_quizzes'] = $this->dashboard_model->getUserAttemptedQuizzes();
        $arr['total_questions'] = $this->dashboard_model->get_total_questions();
        $arr['total_attempted_questions'] = $this->dashboard_model->get_total_attempted_questions();
        $arr['total_correct_attempted_ques'] = $this->dashboard_model->get_total_correct_attempted_ques();

        $arr['todays_questions'] = $this->dashboard_model->get_todays_questions();
        $arr['quiz_attempting_users'] = $this->dashboard_model->get_quiz_attempting_users();

        $this->load->view(config_item('admin_directory').'/dashboard',$arr);
    }
    public function getAdminDashboardCount()
    {
        echo json_encode(array(
                'total_users'=>$this->dashboard_model->getusercount(),
                'total_quizzes'=>$this->dashboard_model->getTotalQuizzes(),
                'user_attempted_quizzes'=>$this->dashboard_model->getUserAttemptedQuizzes(),
                'total_questions'=>$this->dashboard_model->get_total_questions(),
                'total_attempted_questions'=>$this->dashboard_model->get_total_attempted_questions()
            )
        );
    }
    public function getCorrectAttemptedQuesCount()
    {
        echo json_encode($this->dashboard_model->get_total_correct_attempted_ques());
    }
    public function getTodaysQuizQuestionsList()
    {
        $todays_questions = $this->dashboard_model->get_todays_questions();
        $questionTableHtml = '';
        if(!empty($todays_questions))
        {
            $ij = 1;
            foreach ($todays_questions as $runningQues) 
            {
                $questioinStatus = ($runningQues->status ==1)?"Active":"Inactive";
                $questionTableHtml.='<tr class="odd gradeX">
                    <td>'.$ij.'</td>
                    <td>'.$runningQues->question_name.'</td>
                    <td>'.$questioinStatus.'</td>
                </tr>';
                $ij++;
            }
        }
        else
        {
            $questionTableHtml ='<tr class="odd gradeX">
                <td colspan="2">No Questions found</td>
            </tr>';
        }
        echo json_encode($questionTableHtml);
    }    
    public function getUserAttemptingQuizList()
    {
        $quiz_attempting_users = $this->dashboard_model->get_quiz_attempting_users();
        $questionTableHtml = '';
        if(!empty($quiz_attempting_users))
        {
            $ij = 1;
            $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
            foreach ($quiz_attempting_users as $runningQues) 
            {
                $dt = new DateTime($runningQues->current_quiz_question_date, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone($user_local_timezone));
                $questionTableHtml.='<tr class="odd gradeX">
                    <td>'.$ij.'</td>
                    <td>'.$runningQues->first_name.' '.$runningQues->last_name.'</td>
                    <td>'.$runningQues->question_name.'</td>
                    <td>'.$dt->format('Y-m-d h:i A').'</td>
                </tr>';
                $ij++;
            }
        }
        else
        {
            $questionTableHtml ='<tr class="odd gradeX">
                <td colspan="2">No User found</td>
            </tr>';
        }
        echo json_encode($questionTableHtml);
    }    
}