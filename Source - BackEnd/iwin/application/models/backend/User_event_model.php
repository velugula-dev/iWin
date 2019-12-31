<?php
class User_event_model extends CI_Model {

    function __construct()
    {
        parent::__construct();		
    }       
    public function getUserEventList($user_id,$sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if($this->input->post('screen_types_search') != ''){
            $this->db->where('api_name', $this->input->post('screen_types_search'));
        }

        if($this->input->post('event_date_search') != ''){
            $this->db->where('created_date >', $this->input->post('event_date_search')." 00:00:00");
            $this->db->where('created_date <', $this->input->post('event_date_search')." 23:59:59");
        }
        $this->db->where('user_id',$user_id);
        $result['total'] = $this->db->get('user_screen_log')->num_rows();

        if($this->input->post('screen_types_search') != ''){
            $this->db->where('api_name', $this->input->post('screen_types_search'));
        }

        if($this->input->post('event_date_search') != ''){
            $this->db->where('created_date >', $this->input->post('event_date_search')." 00:00:00");
            $this->db->where('created_date <', $this->input->post('event_date_search')." 23:59:59");
        }

        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);        

        if($sortFieldName != '')
        {   
            $this->db->order_by($sortFieldName, $sortOrder);
        }  

        $this->db->where('user_id',$user_id);
        $result['data'] = $this->db->get('user_screen_log')->result();  
        return $result;
    }    
}
?>