<?php
class Notification_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
    public function getNotificationList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('notification_types_search') != ''){
            $this->db->where('notification_type', $this->input->post('notification_types_search'));
        }
        if($this->input->post('notification_contents_search') != ''){
            $this->db->like('notification_contents', $this->input->post('notification_contents_search'));
        }
        if($this->input->post('notification_date_search') != ''){
            $this->db->where('notification_date', $this->input->post('notification_date_search'));
        }
        $result['total'] = $this->db->count_all_results('notifications');
        if($sortFieldName != '')
        {   
            if($sortFieldName == "notification_date")
            {
                $this->db->order_by("($sortFieldName IS NULL),created_date $sortOrder,$sortFieldName $sortOrder");
            }   
            else
            {
                $this->db->order_by($sortFieldName, $sortOrder);
            }      
        }        
        if($this->input->post('notification_types_search') != ''){
            $this->db->where('notification_type', $this->input->post('notification_types_search'));
        }
        if($this->input->post('notification_contents_search') != ''){
            $this->db->like('notification_contents', $this->input->post('notification_contents_search'));
        }
        if($this->input->post('notification_date_search') != ''){
            $this->db->where('notification_date', $this->input->post('notification_date_search'));
        }

        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('notifications')->result();  
        return $result;
    }  
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
    public function deleteRecord($notification_id){          
        $this->db->where('notification_id',$notification_id);
        $this->db->delete('notifications');
        return $this->db->affected_rows();
    }

    // Get DeviceID
    public function getUserDevices($user_ids)
    {
        $this->db->select('push_notification_token');
        $this->db->where('status',1); // ACTIVE
        $this->db->where('push_notification_token !=',""); // session token - user must be logged in
        if(!empty($user_ids))
        {
            $this->db->where_in('user_id',$user_ids);            
        }
        return $this->db->get('user_master')->result_array();
    }
    public function getUserList()
    {
        $this->db->select('user_id,first_name,last_name,email');
        $this->db->where('status',1); // ACTIVE
        $this->db->where('push_notification_token !=',""); // session token - user must be logged in
        return $this->db->get('user_master')->result();
    }
    public function getTodaysQuestion($question_date)
    {
        $this->db->where('question_date',$question_date);
        $this->db->where('status',1);
        return $this->db->get('question_master')->result_array();
    }
    public function getTodaysQuestionAns($question_date)
    {
        $this->db->select("count(question_answer_master.question_id) as totalquestion");
        $this->db->join("question_master",'question_master.question_id = question_answer_master.question_id');
        $this->db->where('question_master.question_date',$question_date);
        $this->db->where('question_answer_master.is_correct_answer >=',0);
        $this->db->where('question_master.status',1);
        $this->db->group_by('question_answer_master.question_id');
        return $this->db->get('question_answer_master')->num_rows();
    }
    public function getQuizResultData($question_date,$perPage,$Starting = 1)
    {
        $Starting = ($Starting > 0)?$Starting-1:0;

        $this->db->select('question_id');
        $this->db->where('question_date',$question_date);
        $quiz_questions = $this->db->get('question_master')->result();

        $quizQuestionArray = array();
        foreach ($quiz_questions as $value) 
        {
            $quizQuestionArray[] = $value->question_id;
        }

        $currentquiz_questions_correct_ans = array();
        if(!empty($quizQuestionArray))
        {
            $this->db->select("question_id,question_answer_id");
            $this->db->where_in('question_id',$quizQuestionArray);
            $this->db->where('is_correct_answer >',0);
            $currentquiz_questions_correct_ans = $this->db->get('question_answer_master')->result_array();            
        }
        if(!empty($currentquiz_questions_correct_ans))
        {
            $question_answer = array();
            foreach ($currentquiz_questions_correct_ans as $key => $ques_ans_detail) 
            {
                $question_answer[$ques_ans_detail["question_id"]][] = $ques_ans_detail['question_answer_id'];            
            }

            $correctQuesAnsString = '';
            foreach ($question_answer as $key => $value) 
            {
                $question_answer[$key] = implode(',', $value);       
                if(count($value) >1)
                {
                    $combinationResult = $this->wordcombos($value);
                    unset($question_answer[$key]);
                    foreach ($combinationResult as $v) 
                    {
                        $question_answer[] = $v;
                        $correctQuesAnsString.= "'".$v."',";
                    }
                }
                else
                {
                    $correctQuesAnsString.= "'".$question_answer[$key]."',";                    
                }
            }
        }

        $userList = array();
        if($correctQuesAnsString !="")
        {
            $correctQuesAnsString = rtrim($correctQuesAnsString,',');
            $this->db->select('user_answer_master.user_id,SUM(CASE when user_answer_master.question_answer_id in('.$correctQuesAnsString.') then 1 else 0 end) as points,(select first_name as name from user_master where user_id = user_answer_master.user_id) as name');
        }
        else
        {
            $this->db->select('user_answer_master.user_id,0 as points,(select first_name as name from user_master where user_id = user_answer_master.user_id) as name');            
        }

        $this->db->join('user_answer_master','question_master.question_id = user_answer_master.question_id');
        $this->db->join('user_master','user_master.user_id = user_answer_master.user_id');
        $this->db->where('question_master.question_date',$question_date);
        $this->db->group_by('user_answer_master.user_id');
        $this->db->order_by('points','DESC');
        $this->db->limit($perPage,$Starting*$perPage);
        return $this->db->get('question_master')->result_array();            
    }    
    public function getCheckQuizResultData($question_date)
    {
        $this->db->select('question_id');
        $this->db->where('question_date',$question_date);
        $quiz_questions = $this->db->get('question_master')->result();

        $quizQuestionArray = array();
        foreach ($quiz_questions as $value) 
        {
            $quizQuestionArray[] = $value->question_id;
        }

        $currentquiz_questions_correct_ans = array();
        if(!empty($quizQuestionArray))
        {
            $this->db->select("question_id,question_answer_id");
            $this->db->where_in('question_id',$quizQuestionArray);
            $this->db->where('is_correct_answer >',0);
            $currentquiz_questions_correct_ans = $this->db->get('question_answer_master')->result_array();            
        }
        if(!empty($currentquiz_questions_correct_ans))
        {
            $question_answer = array();
            foreach ($currentquiz_questions_correct_ans as $key => $ques_ans_detail) 
            {
                $question_answer[$ques_ans_detail["question_id"]][] = $ques_ans_detail['question_answer_id'];            
            }

            $correctQuesAnsString = '';
            foreach ($question_answer as $key => $value) 
            {
                $question_answer[$key] = implode(',', $value);       
                if(count($value) >1)
                {
                    $combinationResult = $this->wordcombos($value);
                    unset($question_answer[$key]);
                    foreach ($combinationResult as $v) 
                    {
                        $question_answer[] = $v;
                        $correctQuesAnsString.= "'".$v."',";
                    }
                }
                else
                {
                    $correctQuesAnsString.= "'".$question_answer[$key]."',";                    
                }
            }
        }

        $userList = array();
        if($correctQuesAnsString !="")
        {
            $correctQuesAnsString = rtrim($correctQuesAnsString,',');
            $this->db->select('user_answer_master.user_id,SUM(CASE when user_answer_master.question_answer_id in('.$correctQuesAnsString.') then 1 else 0 end) as points,(select first_name as name from user_master where user_id = user_answer_master.user_id) as name');
            $this->db->join('user_answer_master','question_master.question_id = user_answer_master.question_id');
            $this->db->join('user_master','user_master.user_id = user_answer_master.user_id');
            $this->db->where('question_master.question_date',$question_date);
            $this->db->group_by('user_answer_master.user_id');
            $this->db->order_by('points','DESC');
            $userList = $this->db->get('question_master')->first_row();            
        }
        return $userList;
    }    
}
?>