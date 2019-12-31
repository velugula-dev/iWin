<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Feedback extends CI_Controller { 
    public $module_name = "Feedback";
    public $controller_name = "feedback";
    public $addedit_viewfile = "feedback_add";
    public $list_viewfile = "feedback";

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }

        date_default_timezone_set('UTC');
        $this->load->library('form_validation');
        $this->load->model(config_item('admin_directory').'/feedback_model');        
    }
    public function view() {
        $data['MetaTitle'] = $this->lang->line('title_admin_feedback').' | '.$this->lang->line('site_title');        
        $this->load->view(config_item('admin_directory').'/'.$this->list_viewfile,$data);
    }
    public function add() {
        $data['MetaTitle'] = $this->lang->line('title_admin_feedback_add').' | '.$this->lang->line('site_title');
        if($this->input->post('submitFeedback') == "Submit")
        {
            $this->form_validation->set_rules('message', 'Feedback Message', 'trim|required');
            if ($this->form_validation->run())
            {
                $checkFeedbackMSG = $this->common_model->getSingleRow('feedback_message_master',1,1);

                if(!empty($checkFeedbackMSG))
                {
                    $updateFeedbackMSGData = array(                   
                        'message'=>$this->input->post('message'),
                        'updated_by'=>$this->session->userdata("adminID"),
                        'updated_date'=>date("Y-m-d H:i:s")
                    );                                            
                    $this->common_model->updateData('feedback_message_master',$updateFeedbackMSGData,'feedback_message_id',$checkFeedbackMSG->feedback_message_id);
                }
                else
                {
                    $addFeedbackMSGData = array(                   
                        'message'=>$this->input->post('message'),
                        'created_by'=>$this->session->userdata("adminID"),
                        'created_date'=>date("Y-m-d H:i:s")
                    );                                            
                    $this->common_model->addData('feedback_message_master',$addFeedbackMSGData);
                }
                if($this->input->post("feedback_question"))
                {
                    $addFeedbackQues = array();
                    $updateFeedbackQues = array();
                    $feedback_question_id_array = array();
                    foreach ($this->input->post("feedback_question") as $key => $value) 
                    {
                        if($this->input->post("feedback_question_id")[$key] !="" && $this->input->post('feedback_question_id')[$key] >0)
                        {
                            $updateFeedbackQues[$key] = array(
                                'feedback_question_id'=>$this->input->post("feedback_question_id")[$key],
                                'feedback_question'=>$this->input->post("feedback_question")[$key],
                                'feedback_question_type'=>$this->input->post("feedback_question_type")[$key],
                                'updated_by'=>$this->session->userdata("adminID"),
                                'updated_date'=>date("Y-m-d H:i:s")
                            );       
                            $feedback_question_id_array[] = $this->input->post('feedback_question_id')[$key];                                     
                        }
                        else
                        {
                            $addFeedbackQues[$key] = array(
                                'feedback_question'=>$this->input->post("feedback_question")[$key],
                                'feedback_question_type'=>$this->input->post("feedback_question_type")[$key],
                                'created_by'=>$this->session->userdata("adminID"),
                                'updated_date'=>date("Y-m-d H:i:s")
                            );                                            
                        }
                    }

                    if(!empty($updateFeedbackQues))
                    {
                        $updateFeedbackQues = array_values($updateFeedbackQues);
                        $this->common_model->updateBatch('feedback_question_master',$updateFeedbackQues,'feedback_question_id');                            
                    }
                    if(!empty($feedback_question_id_array))
                    {
                        $this->feedback_model->deleteFeedbackQuestion($feedback_question_id_array);
                    }
                    if(!empty($addFeedbackQues))
                    {
                        $arrayValues = array_values($addFeedbackQues);
                        $this->common_model->insertBatch('feedback_question_master',$arrayValues);                            
                    }
                }                         

                $this->session->set_flashdata('feedback_ques_msg_MSG', $this->lang->line('success_add'));
                redirect(config_item('admin_base_url').$this->controller_name."/view");                 
            }
        }
        $data['editFeedbackDetail'] = $this->common_model->getSingleRow('feedback_message_master',1,1);
        $data['editFeedbackQuestionDetail'] = $this->common_model->getMultipleRows('feedback_question_master',1,1);
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }    
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'first_name',2=>'feedback_question',3=>'feedback_user_answer',4=>'feedback_question_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        
        //Get Recored from model
        $feedbackData = $this->feedback_model->getUserFeedbackAnswerList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo "<pre> ";print_r($feedbackData);exit;
        $totalRecords = $feedbackData['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
        foreach ($feedbackData['data'] as $key => $feedbackDetail) {
            $dt = new DateTime($feedbackDetail->feedback_question_date." ".date("H:i:s"), new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone($user_local_timezone));
            $feedback_question_date = $dt->format('Y-m-d');

            $abc = rawurldecode($feedbackDetail->feedback_user_answer);
            //$abc = str_replace('&nbps;', '%u', $abc);
            $records["aaData"][] = array(
                $nCount,
                $feedbackDetail->first_name." ".$feedbackDetail->last_name,                
                $feedbackDetail->feedback_question,                
                $abc,                
                $feedback_question_date,                
                ''
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxdeleteNotification() {
        $notification_id = ($this->input->post('notification_id') != '')?$this->input->post('notification_id'):'';
        if($notification_id != ''){
            $this->feedback_model->deleteRecord($notification_id);
        }
    }
}