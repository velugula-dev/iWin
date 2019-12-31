<?php
class User_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
    public function getUserList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('user_name_search') != ''){
            $this->db->like('first_name', $this->input->post('user_name_search'));
        }

        if($this->input->post('user_email_search') != ''){
            $this->db->like('email', $this->input->post('user_email_search'));
        }

        if($this->input->post('last_login_search') != ''){
            $this->db->where('last_login >', $this->input->post('last_login_search')." 00:00:00");
            $this->db->where('last_login <', $this->input->post('last_login_search')." 23:59:59");
        }
        if($this->input->post('created_date_search') != ''){
            $this->db->where('created_date >', $this->input->post('created_date_search')." 00:00:00");
            $this->db->where('created_date <', $this->input->post('created_date_search')." 23:59:59");
        }

        $this->db->where('user_type','User');
        $this->db->group_by('user_id');
        $result['total'] = $this->db->get('user_master')->num_rows();


        if($this->input->post('user_name_search') != ''){
            $this->db->like('first_name', $this->input->post('user_name_search'));
        }

        if($this->input->post('user_email_search') != ''){
            $this->db->like('email', $this->input->post('user_email_search'));
        }

        if($this->input->post('last_login_search') != ''){
            $this->db->where('last_login >', $this->input->post('last_login_search')." 00:00:00");
            $this->db->where('last_login <', $this->input->post('last_login_search')." 23:59:59");
        }
        if($this->input->post('created_date_search') != ''){
            $this->db->where('created_date >', $this->input->post('created_date_search')." 00:00:00");
            $this->db->where('created_date <', $this->input->post('created_date_search')." 23:59:59");
        }

        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        

        if($sortFieldName != '')
        {   
            if($sortFieldName =="last_login")
            {
                if($sortOrder =="desc")
                {
                    $this->db->order_by("(last_login IS NULL),last_login DESC,$sortFieldName ASC");                    
                }
                else
                {
                    $this->db->order_by("(last_login IS NULL),last_login ASC,$sortFieldName DESC");                    
                }
                //$this->db->order_by("last_login", "DESC");
                $this->db->order_by("created_date", "DESC");
                $this->db->order_by("last_name", "ASC");
            }
            else
            {
                $this->db->order_by($sortFieldName, $sortOrder);                    
            }
        }  

        $this->db->where('user_type','User');
        $this->db->group_by('user_id');
        $result['data'] = $this->db->get('user_master')->result();  
        return $result;
    }    
}
?>