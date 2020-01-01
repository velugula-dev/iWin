<?php
class User_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
    public function updateData($userid)
    {
    	$data = array('status'=>1,'verification_code'=>"");
    	$this->db->where('verification_code',$userid);
        $this->db->update('user_master',$data);
    }

    public function resetPassword($actcode,$data)
    {    	
    	$this->db->where('verification_code',$actcode);
        $this->db->update('user_master',$data);
    }
    public function check_verfication_code($actcode)
    {
        $this->db->where('verification_code',$actcode);
        return $this->db->get('user_master')->first_row();        
    }
}
?>