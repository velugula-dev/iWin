<?php
class Home_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }	
    public function checkemailExist($email){   
        $this->db->where('email',$email); 	    	
        $this->db->where('user_type','Admin');            
    	return $this->db->get_where('user_master')->first_row();
    } 
    public function updatePassword($emailaddress,$updpsw)  
    {  
        $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
        $enc_pass  = md5($salt.$updpsw);    	    	    	
        $this->db->set('password', $enc_pass)  
            ->where('email', $emailaddress)
            ->update('user_master');  
        return $this->db->affected_rows();  
    } 	
    public function updateVerificationCode($emailaddress,$verificationCode,$UserID)  
    {  
        $this->db->set('verification_code', $verificationCode)  
            ->where('email', $emailaddress)
            ->where('user_id',$UserID)
            ->update('user_master');
        return $this->db->affected_rows();  
    } 
    public function forgotEmailVerify($verificationCode)  
    {       
        return $this->db->get_where('user_master',array('verification_code'=>$verificationCode))->first_row();
    }
    public function updateData($data,$table,$where){
        $this->db->where('verification_code',$where);
        $this->db->update($table, $data);
    }
    public function verifyEmailAddress($verificationCode)
    {
        $this->db->set(array('Status'=> 1,'verification_code'=>''))  
        ->where('verification_code', $verificationCode)  
        ->update('user_master');  
        return $this->db->affected_rows();  
    }
}   
?>