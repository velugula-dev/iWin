<?php
class Question_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }
    public function getQuestionList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('question_name_search') != ''){
            $this->db->like('question_name', $this->input->post('question_name_search'));
        }
        if($this->input->post('question_type_search') != ''){
            $this->db->where('question_type', $this->input->post('question_type_search'));
        }
        if($this->input->post('question_date_search') != ''){
            $this->db->where('question_date', $this->input->post('question_date_search'));
        }
        if($this->input->post('answer_restrict_time') != ''){
            $this->db->where('answer_restrict_time >=', date("H:i:s",strtotime($this->input->post('answer_restrict_time'))));
        }
        if($this->input->post('question_detail_search') != ''){
            $this->db->like('question_detail', $this->input->post('question_detail_search'));
        }
        if($this->input->post('status_search') != ''){
            $this->db->where('status', $this->input->post('status_search'));
        }

        $result['total'] = $this->db->count_all_results('question_master');

        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('question_name_search') != ''){
            $this->db->like('question_name', $this->input->post('question_name_search'));
        }
        if($this->input->post('question_type_search') != ''){
            $this->db->where('question_type', $this->input->post('question_type_search'));
        }
        if($this->input->post('question_date_search') != ''){
            $this->db->where('question_date', $this->input->post('question_date_search'));
        }
        if($this->input->post('answer_restrict_time') != ''){
            $this->db->where('answer_restrict_time >=', date("H:i:s",strtotime($this->input->post('answer_restrict_time'))));
        }
        if($this->input->post('question_detail_search') != ''){
            $this->db->like('question_detail', $this->input->post('question_detail_search'));
        }
        if($this->input->post('status_search') != ''){
            $this->db->where('status', $this->input->post('status_search'));
        }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        
        $result['data'] = $this->db->get('question_master')->result();        
        return $result;
    }  
    public function deleteQuestionAns($question_id,$repeated_question_ans)
    {
        $this->db->where('question_id',$question_id);
        $this->db->where_not_in("question_answer_id",$repeated_question_ans);
        $this->db->delete('question_answer_master');
    }
}
?>