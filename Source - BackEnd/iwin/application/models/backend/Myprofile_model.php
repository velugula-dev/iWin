<?php
class Myprofile_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();      
    }            
    public function getEditUserDetail($UserID)
    {
        return $this->db->get_where('user_master',array('user_id'=>$UserID))->first_row();
    }
    public function updateUserModel($UserData,$UserID)
    {        
        $this->db->where('user_id',$UserID);
        $this->db->update('user_master',$UserData);            
        return $this->db->affected_rows();
    }
    public function CheckExists($Email,$UserID=NULL)
    {
        $this->db->where('email',$Email);
        $this->db->where('user_id !=',$UserID);
        return $this->db->get('user_master')->num_rows();        
    }
}
?>