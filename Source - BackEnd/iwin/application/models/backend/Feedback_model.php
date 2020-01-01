<?php
class Feedback_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
    public function getUserFeedbackAnswerList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('feedback_user_search') != ''){
            $this->db->like('user_master.first_name', $this->input->post('feedback_user_search'));
        }
        if($this->input->post('feedback_question_search') != ''){
            $this->db->like('feedback_user_answer.feedback_question', $this->input->post('feedback_question_search'));
        }
        if($this->input->post('feedback_user_ans_search') != ''){
            $this->db->like('feedback_user_answer.feedback_user_answer', $this->input->post('feedback_user_ans_search'));
        }
        if($this->input->post('question_date_search') != ''){
            $this->db->where('feedback_user_answer.feedback_question_date', $this->input->post('question_date_search'));
        }


        $this->db->select('feedback_user_answer.*,user_master.first_name,user_master.last_name');
        $this->db->join('user_master','user_master.user_id = feedback_user_answer.user_id');
        $result['total'] = $this->db->count_all_results('feedback_user_answer');

        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('feedback_user_search') != ''){
            $this->db->like('user_master.first_name', $this->input->post('feedback_user_search'));
        }
        if($this->input->post('feedback_question_search') != ''){
            $this->db->like('feedback_user_answer.feedback_question', $this->input->post('feedback_question_search'));
        }
        if($this->input->post('feedback_user_ans_search') != ''){
            $this->db->like('feedback_user_answer.feedback_user_answer', $this->input->post('feedback_user_ans_search'));
        }
        if($this->input->post('question_date_search') != ''){
            $this->db->where('feedback_user_answer.feedback_question_date', $this->input->post('question_date_search'));
        }

        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $this->db->select('feedback_user_answer.*,user_master.first_name,user_master.last_name');
        $this->db->join('user_master','user_master.user_id = feedback_user_answer.user_id');
        $result['data'] = $this->db->get('feedback_user_answer')->result();        
        return $result;
    }  
    public function deleteFeedbackQuestion($repeated_question_ans)
    {
        $this->db->where_not_in("feedback_question_id",$repeated_question_ans);
        $this->db->delete('feedback_question_master');
    }    
}
?>