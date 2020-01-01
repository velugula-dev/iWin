<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Question extends CI_Controller { 
    public $module_name = "Question";
    public $controller_name = "question";
    public $list_viewfile = "question";
    public $addedit_viewfile = "question_add";

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }

        $this->load->library('form_validation');
        $this->load->model(config_item('admin_directory').'/question_model');        
    }
    public function view() {
        $data['MetaTitle'] = $this->lang->line('title_admin_question').' | '.$this->lang->line('site_title');        
        $this->load->view(config_item('admin_directory').'/'.$this->list_viewfile,$data);
    }
    public function add() 
    {
        $data['MetaTitle'] = $this->lang->line('title_admin_question_add').' | '.$this->lang->line('site_title');
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }  
    public function TFQuestion()
    {
        $this->load->view(config_item('admin_directory').'/question_tf_add');
    }
    public function addTFQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   
            $this->form_validation->set_rules('question_type', 'Question Type', 'trim|required');

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $addQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_type'=>$this->input->post('question_type'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'status'=>1,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $question_id = $this->common_model->addData('question_master',$addQuestion);

                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_AnswerChoice"))
                    {
                        $questionAns = array();
                        $i=1;
                        foreach ($this->input->post("QA_AnswerChoice") as $key => $value) 
                        {
                            $questionAns[$key] = array(
                                'question_id'=>$question_id,
                                'answer'=>$value
                            );
                            if(isset($this->input->post('QA_Choice')[$i]))
                            {
                                $questionAns[$key]['is_correct_answer'] =$this->input->post('QA_Choice')[$i];
                            }
                            $i++;
                        }
                        $this->common_model->insertBatch('question_answer_master',$questionAns);
                    }
                    $this->common_model->deleteData('quiz_master','quiz_date',$this->input->post('question_date'));

                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_add'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question add fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "YesNo";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }

    public function MCQuestion()
    {
        $this->load->view(config_item('admin_directory').'/question_mc_add');
    }
    public function addMCQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   
            $this->form_validation->set_rules('question_type', 'Question Type', 'trim|required');

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $addQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_type'=>$this->input->post('question_type'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'status'=>1,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $question_id = $this->common_model->addData('question_master',$addQuestion);

                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_Option"))
                    {
                        $i=1;
                        foreach ($this->input->post("QA_Option") as $key => $value) 
                        {
                            $questionAns = array();
                            $questionAns = array(
                                'question_id'=>$question_id,
                                'answer'=>$value
                            );
                            if(isset($this->input->post('QA_Choice')[$i]))
                            {
                                $questionAns['is_correct_answer'] =$this->input->post('QA_Choice')[$i];
                            }
                            if(!empty($questionAns))
                            {
                                $this->common_model->addData('question_answer_master',$questionAns);                                
                            }
                            $i++;
                        }
                    }
                    $this->common_model->deleteData('quiz_master','quiz_date',$this->input->post('question_date'));

                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_add'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question add fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "SingleChoice";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }
    public function MRQuestion()
    {
        $this->load->view(config_item('admin_directory').'/question_mr_add');
    }
    public function addMRQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   
            $this->form_validation->set_rules('question_type', 'Question Type', 'trim|required');

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $addQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_type'=>$this->input->post('question_type'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'status'=>1,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $question_id = $this->common_model->addData('question_master',$addQuestion);

                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_Option"))
                    {
                        $i=1;
                        foreach ($this->input->post("QA_Option") as $key => $value) 
                        {
                            $questionAns = array();
                            $questionAns = array(
                                'question_id'=>$question_id,
                                'answer'=>$value
                            );
                            if(isset($this->input->post('QA_Choice')[$i]))
                            {
                                $questionAns['is_correct_answer'] = $this->input->post('QA_Choice')[$i];
                            }
                            if(!empty($questionAns))
                            {
                                $this->common_model->addData('question_answer_master',$questionAns);                                
                            }
                            $i++;
                        }
                    }
                    $this->common_model->deleteData('quiz_master','quiz_date',$this->input->post('question_date'));
                    
                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_add'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question add fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "MultiChoice";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }



    public function edit() 
    {
        $data['MetaTitle'] = $this->lang->line('title_admin_question_edit').' | '.$this->lang->line('site_title');
        $question_id = ($this->uri->segment('4'))?$this->uri->segment('4'):$this->input->post('question_id');
        $data['editQuestionDetail'] = $this->common_model->getSingleRow('question_master','question_id',$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='),$question_id)));
        $data['editAnswerDetail'] = $this->common_model->getMultipleRows('question_answer_master','question_id',$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='),$question_id)));
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }  
    public function editTFQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $updateQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $this->common_model->updateData('question_master',$updateQuestion,'question_id',$this->input->post('question_id'));
                $question_id = $this->input->post('question_id');

                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_AnswerChoice"))
                    {
                        $updatequestionAns = array();
                        $questionAns = array();
                        $i=1;
                        foreach ($this->input->post("QA_AnswerChoice") as $key => $value) 
                        {
                            if(isset($this->input->post('QA_Choice')[$i]))
                            {
                                if($this->input->post('answer_autoid')[$i] !="")
                                {
                                    $updatequestionAns[] = array(
                                        'question_answer_id'=>$this->input->post('answer_autoid')[$i],
                                        'is_correct_answer' =>$this->input->post('QA_Choice')[$i]
                                    );                                    
                                }
                                else
                                {
                                    $questionAns[$key] = array(
                                        'question_id'=>$question_id,
                                        'answer'=>$value
                                    );
                                    if(isset($this->input->post('QA_Choice')[$i]))
                                    {
                                        $questionAns[$key]['is_correct_answer'] =$this->input->post('QA_Choice')[$i];
                                    }
                                }
                            }

                            $i++;
                        }
                        if(!empty($questionAns))
                        {
                            $this->common_model->insertBatch('question_answer_master',$questionAns);
                        }
                        if(!empty($updatequestionAns))
                        {
                            $this->common_model->updateBatch('question_answer_master',$updatequestionAns,'question_answer_id');                            
                        }
                    }
                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_update'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question Edit fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "YesNo";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }
    public function editSinglChoiceQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $updateQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $this->common_model->updateData('question_master',$updateQuestion,'question_id',$this->input->post('question_id'));
                $question_id = $this->input->post('question_id');

                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_Option"))
                    {
                        $updatequestionAns = array();
                        $i=1;
                        $repeated_question_ans = array();
                        foreach ($this->input->post("QA_Option") as $key => $value) 
                        {
                            if($this->input->post('answer_autoid')[$i] !="" && $this->input->post('answer_autoid')[$i] >0)
                            {
                                $updatequestionAns[$key] = array(
                                    'question_answer_id'=>$this->input->post('answer_autoid')[$i],
                                    'answer'=>$value
                                );
                                if(isset($this->input->post('QA_Choice')[$i]))
                                {
                                    $updatequestionAns[$key]['is_correct_answer'] = $this->input->post('QA_Choice')[$i];
                                }
                                $repeated_question_ans[] = $this->input->post('answer_autoid')[$i];
                            }
                            else
                            {
                                $addequestionAns = array();
                                $addequestionAns = array(
                                    'question_id'=>$question_id,
                                    'answer'=>$value
                                );
                                if(isset($this->input->post('QA_Choice')[$i]))
                                {
                                    $addequestionAns['is_correct_answer'] = $this->input->post('QA_Choice')[$i];
                                }
                                if(!empty($addequestionAns))
                                {
                                    $repeated_question_ans[] = $this->common_model->addData('question_answer_master',$addequestionAns);                            
                                }
                            }
                            $i++;
                        }
                        if(!empty($updatequestionAns))
                        {
                            $updatequestionAns = array_values($updatequestionAns);
                            $this->common_model->updateBatch('question_answer_master',$updatequestionAns,'question_answer_id');                            
                        }
                        if(!empty($repeated_question_ans))
                        {
                            $this->question_model->deleteQuestionAns($question_id,$repeated_question_ans);
                        }
                    }
                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_update'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question Edit fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "SingleChoice";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }
    public function editMultiChoiceQuestion()
    {
        if($this->input->post('submitQuestion') == "Submit")
        {
            $this->form_validation->set_rules('question_name', 'Question', 'trim|required');   

            if ($this->form_validation->run())
            {       
                $question_date = ($this->input->post('question_date'))?$this->input->post('question_date'):date("Y-m-d");
                $answer_restrict_time = ($this->input->post('answer_restrict_time'))?date("H:i:s",strtotime($this->input->post('answer_restrict_time'))):date("H:i:s");
                $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

                $dt = new DateTime($question_date." ".$answer_restrict_time, new DateTimeZone($user_local_timezone));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $question_utc_date = $dt->format('Y-m-d');
                $question_utc_time = $dt->format('H:i:s');

                $updateQuestion = array(
                    'question_name'=>$this->input->post('question_name'),
                    'question_date'=>$question_utc_date,
                    'answer_restrict_time'=>$question_utc_time,
                    'question_detail'=>$this->input->post('question_detail')
                );
                $this->common_model->updateData('question_master',$updateQuestion,'question_id',$this->input->post('question_id'));
                $question_id = $this->input->post('question_id');
                if($question_id !="" && $question_id > 0)
                {
                    if($this->input->post("QA_Option"))
                    {
                        $updatequestionAns = array();
                        $addequestionAns = array();
                        $i=1;
                        $repeated_question_ans = array();
                        foreach ($this->input->post("QA_Option") as $key => $value) 
                        {
                            if($this->input->post('answer_autoid')[$i] !="" && $this->input->post('answer_autoid')[$i] >0)
                            {
                                $updatequestionAns[$key] = array(
                                    'question_answer_id'=>$this->input->post('answer_autoid')[$i],
                                    'answer'=>$value
                                );
                                if(isset($this->input->post('QA_Choice')[$i]))
                                {
                                    $updatequestionAns[$key]['is_correct_answer']=$this->input->post('QA_Choice')[$i];
                                }
                                $repeated_question_ans[] = $this->input->post('answer_autoid')[$i];
                            }
                            else
                            {
                                $addequestionAns = array();
                                $addequestionAns = array(
                                    'question_id'=>$question_id,
                                    'answer'=>$value
                                );
                                if(isset($this->input->post('QA_Choice')[$i]))
                                {
                                    $addequestionAns['is_correct_answer'] = $this->input->post('QA_Choice')[$i];
                                }
                                if(!empty($addequestionAns))
                                {
                                    $repeated_question_ans[] = $this->common_model->addData('question_answer_master',$addequestionAns);                                                         
                                }
                            }
                            $i++;
                        }
                        if(!empty($updatequestionAns))
                        {
                            $updatequestionAns = array_values($updatequestionAns);
                            $this->common_model->updateBatch('question_answer_master',$updatequestionAns,'question_answer_id');                            
                        }
                        if(!empty($repeated_question_ans))
                        {
                            $this->question_model->deleteQuestionAns($question_id,$repeated_question_ans);
                        }
                    }
                    $this->session->set_flashdata('PageMSG', $this->lang->line('success_update'));
                    redirect(config_item('admin_base_url').$this->controller_name."/view");                 
                }
                else
                {
                    $data["Error"] = "Question Edit fail! please try again.";
                    $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
                }
            }
        }

        $data['question_type'] = "SingleChoice";
        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);
    }

    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'question_name',2=>'question_type',3=>'question_date',4=>'answer_restrict_time',5=>'question_detail',6=>'status',7=>'question_id');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        
        //Get Recored from model
        $questionData = $this->question_model->getQuestionList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo "<pre> ";print_r($questionData);exit;
        $totalRecords = $questionData['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
        foreach ($questionData['data'] as $key => $questionDetail) {

            $answer_restrict_time = ($questionDetail->answer_restrict_time > 0)?$questionDetail->answer_restrict_time:'00:00:00';

            $dt = new DateTime($questionDetail->question_date." ".$answer_restrict_time, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone($user_local_timezone));
            $question_utc_date = $dt->format('Y-m-d');
            $question_utc_time = $dt->format('h:i A');

            $records["aaData"][] = array(
                $nCount,
                $questionDetail->question_name,                
                $questionDetail->question_type,                
                $question_utc_date,                
                $question_utc_time,                
                $questionDetail->question_detail,                
                ($questionDetail->status == 1)?'Active':'Deactive',
                '<a class="btn btn-sm danger-btn margin-bottom" href="'.config_item('admin_base_url').$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($questionDetail->question_id)).'"><i class="fa fa-edit"></i> Edit</a><button onclick="disableRecord('.$questionDetail->question_id.','.$questionDetail->status.')"  title="Click here for '.($questionDetail->status?'Deactivate':'Activate').' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($questionDetail->status?'times':'check').'"></i> '.($questionDetail->status?'Deactivate':'Activate').'</button> <button onclick="deleteQuestion('.$questionDetail->question_id.')"  title="Click here for delete question" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> Delete</button>'
                //($questionDetail->question_type == "YesNo")?'<a class="btn btn-sm danger-btn margin-bottom" href="'.config_item('admin_base_url').$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($questionDetail->question_id)).'"><i class="fa fa-edit"></i> Edit</a><button onclick="disableRecord('.$questionDetail->question_id.','.$questionDetail->status.')"  title="Click here for '.($questionDetail->status?'Deactivate':'Activate').' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-'.($questionDetail->status?'times':'check').'"></i> '.($questionDetail->status?'Deactivate':'Activate').'</button> <button onclick="deleteQuestion('.$questionDetail->question_id.')"  title="Click here for delete question" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> Delete</button>':''
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxDisable() 
    {
        $question_id = ($this->input->post('question_id') != '')?$this->input->post('question_id'):'';
        if($question_id != ''){
            if($this->input->post('status')==0){
                $data = array('status' => 1);
            } else {
                $data = array('status' => 0);
            }
            $this->common_model->updateData('question_master',$data,'question_id',$question_id);
        }
    }
    public function check_question_attempt_user()
    {
        $question_id = ($this->input->post('question_id') != '')?$this->input->post('question_id'):'';
        $checkRecord = $this->common_model->getSingleRow('user_answer_master','question_id',$question_id);
        echo count($checkRecord);
    }
    public function delete_question()
    {
        $question_id = ($this->input->post('question_id') != '')?$this->input->post('question_id'):'';
        $this->common_model->deleteData('question_answer_master','question_id',$question_id);        
        echo $this->common_model->deleteData('question_master','question_id',$question_id);        
    }
}