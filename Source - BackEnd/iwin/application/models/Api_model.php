<?php

class Api_model extends CI_Model {

    function __construct()

    {
        parent::__construct();      
        //$this->db = $this->load->database('reader', TRUE);
    }

    public function getRecord($table,$fieldName,$where)

    {

        $this->db->where($fieldName,$where);

        return $this->db->get($table)->first_row();

    }

    public function getRecordMultipleWhere($table,$whereArray)

    {

        $this->db->where($whereArray);

        return $this->db->get($table)->first_row();

    }

    public function getRecordsCount($table,$fieldName,$where)

    {

        $this->db->where($fieldName,$where);

        return $this->db->get($table)->num_rows();

    }

    public function getRecordsWithLimit($table,$fieldName,$where,$perPage,$Starting = 1)

    {

        $Starting = ($Starting > 0)?$Starting-1:0;

        $this->db->where($fieldName,$where);

        $this->db->limit($perPage,$Starting*$perPage);

        return $this->db->get($table)->result();

    }

    public function getMultipleRecord($table,$fieldName,$where)

    {

        $this->db->where($fieldName,$where);

        return $this->db->get($table)->result();

    }

    // Login

    public function getLogin($email, $password)

    {        

        $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';

        $enc_pass  = md5($salt.$password);



        $this->db->where('email',$email);

        $this->db->where('password',$enc_pass);

        $this->db->where('user_type','User');

        $this->db->where('status',1);

        return $this->db->get('user_master')->first_row(); 

    }

    public function getAllRecord($tableName,$fieldName)

    {

        $this->db->select($fieldName);

        return $this->db->get($tableName)->result();

    }

    public function getAllRecordCount($tableName,$fieldName)

    {

        $this->db->select($fieldName);

        return $this->db->get($tableName)->num_rows();

    }

    public function getAllRecordLimit($tableName,$fieldName,$perPage,$Starting = 1)

    {

        $Starting = ($Starting > 0)?$Starting-1:0;

        $this->db->select($fieldName);

        $this->db->limit($perPage,$Starting*$perPage);

        return $this->db->get($tableName)->result();

    }



    // Update User

    public function updateUser($tableName,$data,$fieldName,$UserID)

    {

        $this->db->where($fieldName,$UserID);

        $this->db->update($tableName,$data);

    }

    // Common Add Records

    public function addRecord($table,$data)

    {

        $this->db->insert($table,$data);

        return $this->db->insert_id();

    }

    // Common Add Records Batch

    public function addRecordBatch($table,$data)

    {

        return $this->db->insert_batch($table, $data);

    }

    public function deleteInsertRecord($table,$data,$whereArray)

    {

        $this->db->where($whereArray);

        $this->db->delete($table);



        $this->db->insert_batch($table,$data);

    }

    public function getQuizQuestionsCount($date)

    {

        $this->db->where('question_date',$date);

        $this->db->where('status',1);

        return $this->db->get('question_master')->num_rows();        

    }

    public function getQuizQuestionsLimit($user_id,$date)

    {

        $this->db->select('question_master.question_id,question_master.question_type,question_master.question_name,question_master.question_date,question_master.answer_restrict_time,question_master.question_detail,question_answer_master.question_answer_id,question_answer_master.answer,question_answer_master.is_correct_answer,user_answer_master.user_answer_id,user_answer_master.question_answer_id as user_selected_answer');

        $this->db->where('question_master.question_date',$date);

        $this->db->where('question_master.status',1);

        $this->db->join('question_answer_master','question_answer_master.question_id = question_master.question_id','left');

        $this->db->join('user_answer_master',"(user_answer_master.question_id = question_master.question_id AND user_answer_master.user_id = $user_id)",'left');
        $this->db->order_by('question_master.question_id','ASC');
        return $this->db->get('question_master')->result_array();        

    }

    public function checkUserAnswerExist($user_id,$question_id)

    {

        $this->db->where('user_id',$user_id);

        $this->db->where('question_id',$question_id);

        return $this->db->get('user_answer_master')->first_row();

    }



//user quiz history

    public function getUserQuizHistoryCount($user_id)

    {

        $this->db->select('question_master.question_date');

        $this->db->where('user_answer_master.user_id',$user_id);

        $this->db->join('question_master','question_master.question_id = user_answer_master.question_id');

        $this->db->group_by('question_master.question_date');
        $this->db->where('question_master.status',1);

        return $this->db->get('user_answer_master')->num_rows();

    }

    //public function userQuizHistoryData($user_id,$perPage,$Starting = 1)
    public function userQuizHistoryData($user_id)
    {

        //$Starting = ($Starting > 0)?$Starting-1:0;



        $this->db->select('question_master.question_date,(select count(question_id) from question_master as qm where qm.question_date = question_master.question_date and qm.status =1) as totalQuestions');

        $this->db->where('user_answer_master.user_id',$user_id);

        $this->db->join('question_master','question_master.question_id = user_answer_master.question_id');

        $this->db->group_by('question_master.question_date');

        $this->db->order_by('question_master.question_date','desc');
        $this->db->where('question_master.status',1);

        //$this->db->limit($perPage,$Starting*$perPage);

        return $this->db->get('user_answer_master')->result();

    }

//all users for perticular quiz

    public function getQuizResultCount($question_date)

    {

        $this->db->select('question_id');

        $this->db->where('question_date',$question_date);

        $this->db->get('question_master')->result();



        $this->db->select('user_answer_master.user_id');

        $this->db->where('question_master.question_date',$question_date);
        $this->db->where('question_master.status',1);
        $this->db->where('user_master.status',1);


        $this->db->join('user_master','user_master.user_id = user_answer_master.user_id');
       $this->db->join('question_master','question_master.question_id = user_answer_master.question_id');

        $this->db->group_by('user_answer_master.user_id');

        return $this->db->get('user_answer_master')->num_rows();

    }

    public function getQuizResultData($question_date,$perPage,$Starting = 1)

    {

        $Starting = ($Starting > 0)?$Starting-1:0;



        $this->db->select('question_id');

        $this->db->where('question_date',$question_date);
        $this->db->where('question_master.status',1);

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



        $this->db->select('question_id,question_answer_id');

        //$this->db->where_in('question_id',$quizQuestionArray);

        $this->db->where('is_correct_answer >',0);

        $quiz_questions_correct_ans = $this->db->get('question_answer_master')->result();



        if(!empty($quiz_questions_correct_ans))

        {

            foreach ($quiz_questions_correct_ans as $key => $value) 

            {

                $quizQuestionAnsArray[$value->question_id][] = $value->question_answer_id;

            }

        }



        //,(select count(qam.question_id) from question_answer_master as qam where qam.question_id = user_answer_master.user_id) as userScore



/*        $this->db->select('user_master.user_id,user_master.first_name,user_master.last_name');

        $this->db->where('question_master.question_date',$question_date);

        $this->db->join('question_master','question_master.question_id = user_answer_master.question_id');

        $this->db->join('user_master','user_master.user_id = user_answer_master.user_id');

        $this->db->group_by('user_master.user_id');

        $this->db->limit($perPage,$Starting*$perPage);

        $userList = $this->db->get('user_answer_master')->result();

*/

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
        $this->db->where('question_master.status',1);

        $this->db->group_by('user_answer_master.user_id');

        $this->db->order_by('points','DESC');

        $this->db->limit($perPage,$Starting*$perPage);

        $userList = $this->db->get('question_master')->result();            



        $userScores = array();

        if(!empty($userList))

        {

            foreach ($userList as $key => $userDetail) 

            {

                $this->db->where('user_id',$userDetail->user_id);

                $allUserAns = $this->db->get('user_answer_master')->result();



                $userQuizQuestionAnsArray = array();

                foreach ($allUserAns as $key => $value) 

                {

                    if($value->question_answer_id !="" && $value->question_answer_id >0)

                    {

                        $stringToArray = explode(',', $value->question_answer_id); 

                        sort($stringToArray);                       

                        $userQuizQuestionAnsArray[$value->question_id] = $stringToArray;

                    }

                }



                $totalScore = 0;

                foreach ($userQuizQuestionAnsArray as $key => $value) 

                {

                    if(!empty($value))

                    {

                        if($value==$quizQuestionAnsArray[$key])

                        {

                            $totalScore = $totalScore +1;

                        }

                    }

                }

                $userScores[] = array(

                    'user_id'=>$userDetail->user_id,

                    'name' =>$userDetail->name,

                    'points'=>$userDetail->points,

                    'total'=>$totalScore

                );

            }

        }

        return $userScores;

    }

    public function delete_user($tableName,$fieldName,$whereVal)

    {

        $this->db->where($fieldName,$whereVal);

        return $this->db->delete($tableName);

    }

    public function check_old_assword($user_id,$old_password)

    {

        $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';

        $enc_pass  = md5($salt.$old_password);



        $this->db->where('user_id',$user_id);

        $this->db->where('password',$enc_pass);

        $this->db->where('user_type','User');

        $this->db->where('status',1);

        return $this->db->get('user_master')->first_row();         

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
    public function getFeedbackMSG()
    {
        $this->db->select('feedback_message_id,message');
        return $this->db->get('feedback_message_master')->first_row();
    }
    public function getFeedbackQuestionList($user_id,$feedback_question_date)
    {
        $this->db->select('feedback_question_master.feedback_question_id,feedback_question_master.feedback_question,feedback_question_master.feedback_question_type,feedback_user_answer.feedback_user_answer');
        $this->db->join('feedback_user_answer',"(feedback_user_answer.feedback_question_id = feedback_question_master.feedback_question_id AND feedback_user_answer.user_id = $user_id AND feedback_user_answer.feedback_question_date = '".$feedback_question_date."')",'left');
        $this->db->order_by('feedback_question_master.feedback_question_id','ASC');
        return $this->db->get('feedback_question_master')->result();
    }
    public function checkFeedbackAnsGiven($user_id,$feedback_question_date)
    {
        $this->db->where(array('user_id'=>$user_id,'feedback_question_date'=>$feedback_question_date));
        return $this->db->get('feedback_user_answer')->num_rows();
    }
    public function getUserResultData($question_date,$user_id)
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

            $correctQuesAnsString = array();
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
                        $correctQuesAnsString[] = $v;
                    }
                }
                else
                {
                    $correctQuesAnsString[] = $question_answer[$key];
                }
            }

            $this->db->where('user_id',$user_id);
            $this->db->where_in('question_id',$quizQuestionArray);
            $UserAnswer = $this->db->get('user_answer_master')->result_array();
            $userCorrectWrongArray = array();
            if(!empty($UserAnswer))
            {
                foreach ($UserAnswer as $key => $value) 
                {
                    $userCorrectWrongArray[$value['question_id']] = 0;
                    if(in_array($value['question_answer_id'], $correctQuesAnsString))
                    {
                        $userCorrectWrongArray[$value['question_id']] = 1;
                    }
                }
            }
            return $userCorrectWrongArray;
        }

    }    

}

?>