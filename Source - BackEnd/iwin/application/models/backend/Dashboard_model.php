<?php
class Dashboard_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		        
    }
    public function getusercount()
    {
    	$this->db->where('user_type','User');
    	$this->db->where('status',1);
    	return $this->db->get('user_master')->num_rows();
    }
    public function getTotalQuizzes()
    {
    	$this->db->select('question_date');
    	$this->db->group_by('question_date');
    	return $this->db->get('question_master')->num_rows();
    }					
    public function getUserAttemptedQuizzes()
    {
    	$this->db->select('question_master.question_date,count(user_answer_master.user_id) as totalUsers');
    	$this->db->join('question_master','question_master.question_id = user_answer_master.question_id');
    	$this->db->group_by('question_master.question_date');
    	return $this->db->get('user_answer_master')->num_rows();
    }					
    public function get_total_questions()
    {
    	$this->db->where('status',1);
    	return $this->db->get('question_master')->num_rows();
    }
    public function get_total_attempted_questions()
    {
    	return $this->db->get('user_answer_master')->num_rows();
    }
    public function get_total_correct_attempted_ques()
    {
		$this->db->select("question_id,question_answer_id");
        $this->db->where('is_correct_answer >',0);
        $currentquiz_questions_correct_ans = $this->db->get('question_answer_master')->result_array();

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
        $this->db->where_in('question_answer_id',$question_answer);
        return $this->db->get('user_answer_master')->num_rows();
    }
    function wordcombos($words) {
        if ( count($words) <= 1 ) {
            $result = $words;
        } else {
            $result = array();
            for ( $i = 0; $i < count($words); ++$i ) {
                $firstword = $words[$i];
                $remainingwords = array();
                for ( $j = 0; $j < count($words); ++$j ) {
                    if ( $i <> $j ) $remainingwords[] = $words[$j];
                }
                $combos = $this->wordcombos($remainingwords);
                for ( $j = 0; $j < count($combos); ++$j ) {
                    $result[] = $firstword.','.$combos[$j];
                }
            }
        }
        return $result;
    }
    public function get_todays_questions()
    {
    	$this->db->where('question_date',date('Y-m-d'));
        $this->db->order_by('question_id','ASC');
    	return $this->db->get('question_master')->result();
    }
    public function get_quiz_attempting_users()
    {
    	$this->db->select('user_master.first_name,user_master.last_name,question_master.question_name,user_master.current_quiz_question_date');
    	$this->db->join('question_master','question_master.question_id = user_master.current_quiz_question');
        $this->db->order_by("user_master.current_quiz_question_date","DESC");
    	return $this->db->get('user_master')->result();
    }
}
?>